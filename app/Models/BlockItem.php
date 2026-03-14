<?php

namespace App\Models;

use App\Enums\BlockItemType;
use App\Traits\HasAttachments;
use App\Traits\LogsActivityBasic;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class BlockItem extends Model
{
    use HasAttachments;
    use LogsActivityBasic;

    protected $fillable = [
        'block_id',
        'type',
        'order',
        'value',
    ];

    protected $casts = [
        'type' => BlockItemType::class,
    ];

    protected $hidden = [
        'translations',
    ];

    public function block()
    {
        return $this->belongsTo(ContentBlock::class);
    }

    public function files()
    {
        return $this->morphToMany(Attachment::class, 'attachmentable');
    }

    public function file()
    {
        return $this->files()->first();
    }

    public function value(): Attribute
    {
        return new Attribute(function ($value) {
            $simpleValueTypes = BlockItemType::getSimpleTextTypes();
            $simpleFileTypes = BlockItemType::getSimpleFileTypes();
            $value = json_decode($value, true);

            if ($this->type == BlockItemType::IMAGE_TITLE) {
                $file = $this->file();

                return [
                    'title' => $value['title'] ?? '',
                    'file' => [
                        'id' => $file?->id,
                        'id_old' => $file?->id,
                        'original_name' => $file?->original_name,
                        'alt' => $file?->alt,
                        'title' => $file?->title,
                        'url' => $file?->url,
                    ],
                ];
            }

            if ($this->type == BlockItemType::IMAGE_TEXT) {
                $file = $this->file();

                return [
                    'text' => $value['text'] ?? '',
                    'file' => [
                        'id' => $file?->id,
                        'id_old' => $file?->id,
                        'original_name' => $file?->original_name,
                        'alt' => $file?->alt,
                        'title' => $file?->title,
                        'url' => $file?->url,
                    ],
                ];
            }

            if ($this->type == BlockItemType::IMAGE_GALLERY) {
                $files = [];
                foreach ($this->files as $file) {
                    $files[] = [
                        'id' => $file->id,
                        'id_old' => $file->id,
                        'original_name' => $file->original_name,
                        'alt' => $file->alt,
                        'title' => $file->title,
                        'url' => $file->url,
                    ];
                }

                return [
                    'images' => $files,
                ];
            }

            if ($this->type == BlockItemType::CARDS) {
                $cards = [];
                $files = $this->files;
                foreach ($value ?? [] as $c) {
                    $file = $files->where('id', $c['attachment_id'] ?? 0)->first();

                    $card = [
                        'title' => $c['title'],
                        'text' => $c['text'],
                    ];

                    if ($file) {
                        $card['image'] = [
                            'id' => $file->id,
                            'id_old' => $file->id,
                            'original_name' => $file->original_name,
                            'alt' => $file->alt,
                            'title' => $file->title,
                            'url' => $file->url,
                        ];
                    }

                    $cards[] = $card;
                }

                return [
                    'cards' => $cards,
                ];
            }

            if (in_array($this->type->value, $simpleFileTypes)) {
                $file = $this->file();

                return [
                    'file' => [
                        'id' => $file->id ?? '',
                        'id_old' => $file->id ?? '',
                        'original_name' => $file->original_name ?? '',
                        'alt' => $file->alt ?? '',
                        'title' => $file->title ?? '',
                        'url' => $file->url ?? '',
                    ],
                ];
            }

            return $value;
        });
    }

    public function valueSimple(): Attribute
    {
        return new Attribute(
            get: function () {
                $value = $this->value;
                $simpleValueTypes = BlockItemType::getSimpleTextTypes();

                if (in_array($this->type->value, $simpleValueTypes)) {
                    return $value['value'] ?? '';
                }

                if ($this->type == BlockItemType::IMAGE_GALLERY) {
                    return $value['images'];
                }

                return $value;
            },
        );
    }
}
