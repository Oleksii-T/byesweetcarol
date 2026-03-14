<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    const EDATABLE_SETTINGS = [
        [
            'name' => 'General',
            'settings' => [
                'debug_emabled_for_users' => 'Debug enabled for users (ids json)',
            ],
        ],
    ];

    public static function get($key, $onlyValue = true)
    {
        try {
            $setting = self::where('key', $key)->first();

            return $onlyValue ? $setting->data['value'] : $setting->data;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function set($key, $value)
    {
        try {
            self::updateOrCreate([
                'key' => $key,
            ], [
                'key' => $key,
                'data' => is_string($value) ? ['value' => $value] : $value,
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
