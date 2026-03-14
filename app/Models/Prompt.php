<?php

namespace App\Models;

use App\Enums\PromptType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prompt extends Model
{
    protected $fillable = [
        'value',
        'name',
        'type',
        'is_saved',
    ];

    protected $casts = [
        'type' => PromptType::class,
    ];

    public function calls(): HasMany
    {
        return $this->hasMany(PromptCall::class);
    }
}
