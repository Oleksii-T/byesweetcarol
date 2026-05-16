<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum BlockItemType: string
{
    use EnumTrait;

    case TITLE_H2 = 'title-h2';
    case TITLE_H3 = 'title-h3';
    case TITLE_H4 = 'title-h4';
    case TITLE_H5 = 'title-h5';
    case TEXT = 'text';
    case IMAGE = 'image';
    case IMAGE_SMALL = 'image-small';
    case IMAGE_TITLE = 'image-title';
    case IMAGE_TEXT = 'image-text';
    case IMAGE_GALLERY = 'image-gallery';
    case CARDS = 'cards';
    case YOUTUBE = 'youtube';
    // case TABLE = 'table';

    public static function getReadable($val)
    {
        return match ($val) {
            self::TITLE_H2 => 'Title h2',
            self::TITLE_H3 => 'Title h3',
            self::TITLE_H4 => 'Title h4',
            self::TITLE_H5 => 'Title h5',
            self::TEXT => 'Rich text',
            self::IMAGE => 'Image',
            self::IMAGE_SMALL => 'Image small',
            self::IMAGE_TITLE => 'Image + title',
            self::IMAGE_TEXT => 'Image + text',
            self::IMAGE_GALLERY => 'Image gallery',
            self::CARDS => 'Cards',
            self::YOUTUBE => 'Youtube',
            // self::TABLE => 'Table',
        };
    }

    public static function getSimpleTextTypes()
    {
        return [
            self::TITLE_H2->value,
            self::TITLE_H3->value,
            self::TITLE_H4->value,
            self::TITLE_H5->value,
            self::TEXT->value,
            self::YOUTUBE->value,
        ];
    }

    public static function getSimpleFileTypes()
    {
        return [
            self::IMAGE->value,
            self::IMAGE_SMALL->value,
        ];
    }
}
