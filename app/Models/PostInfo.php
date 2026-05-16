<?php

namespace App\Models;

use App\Traits\LogsActivityBasic;
use Illuminate\Database\Eloquent\Model;

class PostInfo extends Model
{
    use LogsActivityBasic;

    protected $fillable = [
        'post_id',
        'advantages',
        'disadvantages',
        'rating',
        'game_details',
        'source',
    ];

    protected $casts = [
        'advantages' => 'array',
        'disadvantages' => 'array',
        'game_details' => 'array',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
