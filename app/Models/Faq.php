<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'post_id',
        'question',
        'answer',
        'order',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
