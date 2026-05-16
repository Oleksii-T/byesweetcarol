<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum PageStatus: int
{
    use EnumTrait;

    case DRAFT = 1;
    case PUBLISHED = 2;
    case STATIC = 3;
    case ENTITY = 4;

    public static function getReadable($val)
    {
        return match ($val) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
            self::STATIC => 'Static',
            self::ENTITY => 'Entity',
        };
    }

    public static function getEditables()
    {
        return [
            self::DRAFT->value => self::getReadable(self::DRAFT),
            self::PUBLISHED->value => self::getReadable(self::PUBLISHED),
        ];
    }
}
