<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum FeedbackStatus: int
{
    use EnumTrait;

    case PENDING = 0;
    case CLIENT = 1;
    case ADV = 2;
    case SPAM = 3;
    case OFFER = 4;
    case SYSTEM = 5;

    public static function getReadable($val)
    {
        return match ($val) {
            self::PENDING => 'Pending',
            self::CLIENT => 'Client',
            self::SPAM => 'Spam',
            self::ADV => 'Ad',
            self::OFFER => 'Offer',
            self::SYSTEM => 'System',
        };
    }
}
