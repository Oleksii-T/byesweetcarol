<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PageBlock extends Model
{
    protected $fillable = [
        'page_id',
        'name',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public static function getBlockId($pageId, $name)
    {
        return self::where(['page_id' => $pageId])->where(['name' => $name])->value('id');
    }

    public function show($dataName, $default = '')
    {
        if (! isset($this->data[$dataName])) {
            return $default;
        }
        $data = $this->data[$dataName];

        if ($data['type'] == 'dynamic') {
            foreach ($data['blocks'] ?? [] as $i => $block) {
                foreach ($block as $name => $item) {
                    if ($item['type'] == 'image') {
                        $res[$i][$name] = Storage::disk('pages')->url($item['value']);
                    } else {
                        $res[$i][$name] = $item['value'];
                    }
                }
            }

            return $res ?? [];
        }
        if (! $data['value']) {
            return $default;
        }
        if ($data['type'] == 'image') {
            return Storage::disk('pages')->url($data['value']);
        }

        return $data['value'];
    }

    public static function blocks($block_id, $field)
    {
        $block = self::find($block_id);

        return $block->data[$field]['blocks'] ?? [];
    }
}
