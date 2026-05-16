<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum PromptType: int
{
    use EnumTrait;

    case POST_CREATE = 1;

    public static function getReadable($val)
    {
        return match ($val) {
            self::POST_CREATE => 'POST_CREATE',
        };
    }
}
