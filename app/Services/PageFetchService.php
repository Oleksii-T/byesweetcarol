<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PageFetchService
{
    public static function get(string $uri, bool $usingBrowser = false)
    {
        self::log(' Get HTML from url '.$uri);

        $cKey = Str::slug($uri).'';
        $cacheHtml = cache()->get($cKey);

        // return HTML from the cache if it is exists
        if ($cacheHtml) {
            self::log('  found in cache');

            return $cacheHtml;
        }

        if ($usingBrowser) {
            self::log('  load HTML via headless browser...');
            $html = self::browserGet($uri);
        } else {
            self::log('  load HTML via simple HTTP GET...');
            $html = self::simpleGet($uri);
        }

        cache()->put($uri, $html, 60 * 60 * 24);

        self::log('  HTML loaded');

        return $html;
    }

    private static function simpleGet($uri)
    {
        return Http::get($uri)->body();
    }

    private static function browserGet($uri)
    {
        $command = escapeshellcmd('node fetchContent.js '.escapeshellarg($uri));
        $output = shell_exec($command);

        return $output; // This is the fully rendered HTML
    }

    private static function log($text)
    {
    }
}
