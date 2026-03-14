<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Attachmentable extends Pivot
{
    protected $table = 'attachmentables';

    protected $fillable = [
        'attachment_id',
        'attachmentable_id',
        'attachmentable_type',
        'group',
    ];

    public static function getByModel($model, $group = null, $get = true): Builder|Collection
    {
        $res = self::query()
            ->where('attachmentable_id', $model->id)
            ->where('attachmentable_type', get_class($model))
            ->where('group', $group);

        return $get ? $res->get() : $res;
    }

    public function attachmentable()
    {
        return $this->morphTo();
    }
}
