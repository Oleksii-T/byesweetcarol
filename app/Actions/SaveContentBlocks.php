<?php

namespace App\Actions;

use App\Enums\BlockItemType;

class SaveContentBlocks
{
    public static function run($model, array $data, bool $withGroups = true): void
    {
        $simpleValueTypes = BlockItemType::getSimpleTextTypes();
        $simpleFileTypes = BlockItemType::getSimpleFileTypes();

        // delete removed blocks and items
        foreach ($model->blocks()->get() as $b) {
            $requestBlock = collect($data['blocks'])->where('id', $b->id)->first();

            if (! $requestBlock) {
                $b->delete();

                continue;
            }

            foreach ($b->items as $item) {
                $requestItem = collect($requestBlock['items'])->where('id', $item->id)->first();

                if (! $requestItem) {
                    $item->delete();
                }
            }
        }

        // update block group
        if ($data['group_blocks'] ?? false) {
            $model->update([
                'block_groups' => $data['group_blocks'],
            ]);
        }

        // add or update blocks and items
        foreach ($data['blocks'] as $b) {
            $block = $model->blocks()->updateOrcreate(
                [
                    'id' => $b['id'] ?? null,
                ],
                [
                    'ident' => $b['ident'],
                    'order' => $b['order'],
                    'name' => $b['name'],
                ]
            );

            foreach ($b['items'] as $i) {
                $t = $i['type'];
                $v = $i['value'] ?? [];

                $item = $block->items()->updateOrCreate(
                    [
                        'id' => $i['id'] ?? null,
                    ],
                    [
                        'type' => $t,
                        'order' => $i['order'],
                    ]
                );

                if (in_array($t, $simpleValueTypes)) {
                    $value = $v;

                    if ($t == BlockItemType::TEXT->value) {
                        $value = ['value' => sanitizeHtml($v['value'])];
                    }

                    $item->update([
                        'value' => $value,
                    ]);

                    continue;
                }

                if (in_array($t, $simpleFileTypes)) {
                    $item->addAttachment($v['file'], 'files');

                    continue;
                }

                if ($t == BlockItemType::IMAGE_TITLE->value) {
                    $item->update([
                        'value' => [
                            'title' => $v['title'],
                        ],
                    ]);
                    $item->addAttachment($v['file'], 'files');

                    continue;
                }

                if ($t == BlockItemType::IMAGE_TEXT->value) {
                    $item->update([
                        'value' => [
                            'text' => sanitizeHtml($v['text']),
                        ],
                    ]);
                    $item->addAttachment($v['file'], 'files');

                    continue;
                }

                if ($t == BlockItemType::IMAGE_GALLERY->value) {
                    $item->addAttachment($v['images'], 'files');

                    continue;
                }

                if ($t == BlockItemType::CARDS->value) {
                    if (! isset($v['cards']) || ! $v['cards']) {
                        foreach ($item->files as $file) {
                            $file->delete();
                        }
                        $item->update([
                            'value' => [],
                        ]);

                        continue;
                    }

                    $images = array_column($v['cards'], 'image');
                    $savedAttachments = $item->addAttachment($images, 'files');
                    $value = [];
                    $withoutImage = 0;

                    foreach ($v['cards'] as $i => $c) {
                        $card = [
                            'title' => $c['title'],
                            'text' => sanitizeHtml($c['text']),
                        ];

                        if (isset($c['image'])) {
                            $card['attachment_id'] = $savedAttachments[$i - $withoutImage]->id;
                        } else {
                            $withoutImage++;
                        }

                        $value[] = $card;
                    }

                    $item->update([
                        'value' => $value,
                    ]);

                    continue;
                }

                abort(404, 'type not found');
            }
        }
    }
}
