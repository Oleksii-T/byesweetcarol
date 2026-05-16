<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromptCall extends Model
{
    protected $fillable = [
        'prompt_id',
        'prompt', // used when parent prompt has bindings
        'result',
        'model',
        'cost',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
