<?php

namespace App\Models;

use App\Traits\LogsActivityBasic;
use Illuminate\Database\Eloquent\Model;

class ContentBlock extends Model
{
    use LogsActivityBasic;

    protected $fillable = [
        'name',
        'ident',
        'order',
    ];

    public function items()
    {
        return $this->hasMany(BlockItem::class, 'block_id');
    }

    public function blockable()
    {
        return $this->morphTo();
    }
}
