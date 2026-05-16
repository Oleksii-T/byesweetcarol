<?php

namespace App\Traits;

trait EnumTrait
{
    public function readable()
    {
        return self::getReadable($this);
    }

    public static function all()
    {
        $cases = self::cases();
        $res = [];

        foreach ($cases as $case) {
            $res[$case->value] = self::getReadable($case);
        }

        return $res;
    }

    public static function values()
    {
        return array_keys(self::all());
    }

    public static function readables()
    {
        return array_values(self::all());
    }
}
