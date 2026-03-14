<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum CommentStatus: int
{
    use EnumTrait;

    case PENDING = 0;
    case APPROVED = 1;
    case REJECTED = 2;

    public static function getReadable($val)
    {
        return match ($val) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
        };
    }
}
