<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RawgService
{
    private $key;

    public function __construct()
    {
        $this->key = '740d1abfab69455a8c25088d43955439';
    }

    public function game($name, $strict = true)
    {
        $url = 'https://api.rawg.io/api/games';

        $data = [
            'key' => $this->key,
            'search' => $name,
        ];

        $response = $this->getWithCache($url, $data);
        $response = Arr::get($response, 'results.0');

        if ($response['name'] != $name && $strict) {
            return [];
        }

        $url = 'https://api.rawg.io/api/games/'.$response['id'];
        $data = [
            'key' => $this->key,
        ];
        $response = $this->getWithCache($url, $data);

        $url = 'https://api.rawg.io/api/games/'.$response['id'].'/stores';
        $data = [
            'key' => $this->key,
        ];
        $response['stores_urls'] = $this->getWithCache($url, $data)['results'] ?? [];

        return $this->transform($response);
    }

    public function incommingGames()
    {
        $url = 'https://api.rawg.io/api/games';
        $start = now()->format('Y-m-d');
        $end = now()->addMonths(12)->format('Y-m-d');

        $data = [
            'key' => $this->key,
            'dates' => "$start,$end",
        ];

        $response = $this->getAllPages($url, $data);

        return $response;
    }

    private function getAllPages($url, $data, $page = 1)
    {
        if ($page > 20) {
            return [];
        }

        $data['page'] = $page;
        $response = $this->getWithCache($url, $data);

        if (! $response || empty($response['results'])) {
            \Log::channel('external_apis')->error("RAWG $url (page #$page) returns no result. Data: ".json_encode($data).'. Response: '.json_encode($response));

            return [];
        }

        $games = $response['results'];

        if ($response['next'] ?? null) {
            $nextPageResponse = $this->getAllPages($url, $data, $page + 1);
            $games = array_merge($games, $nextPageResponse);
        }

        return $games;
    }

    private function getWithCache($url, $data)
    {
        $cKey = Str::slug($url.'-'.json_encode($data));

        return cache()->remember($cKey, 60 * 60 * 24, function () use ($url, $data) {
            \Log::channel('external_apis')->info('RAWG request', [
                'method' => 'get',
                'url' => $url,
                'data' => $data,
            ]);

            $response = Http::get($url, $data);

            \Log::channel('external_apis')->info(' response: '.$response->status().' '.$response->body());

            return $response->json();
        });
    }

    /**
     * Transform api data to knows format
     */
    private function transform(array $gameData): array
    {
        $knownStores = [
            'playstation-store' => '',
            'epic-games' => '',
            'steam' => '',
            'xbox-store' => '', // microsoft.com
            'apple-appstore' => '',
            'gog' => '',
            'nintendo' => '',
            'xbox360' => '', // marketplace.xbox.com
            'google-play' => '',
        ];

        if (! empty($gameData['stores']) && ! empty($gameData['stores_urls'])) {
            foreach ($gameData['stores'] as $store) {
                $storeSlug = $store['store']['slug'];

                if (! array_key_exists($storeSlug, $knownStores)) {
                    continue;
                }

                // get game store url from separate data
                $storeData = array_filter($gameData['stores_urls'], fn ($s) => $s['id'] == $store['id']);
                $storeData = array_shift($storeData);

                if (! $storeData) {
                    continue;
                }

                $knownStores[$storeSlug] = $storeData['url'];
            }
        }

        $gameData = [
            'id' => $gameData['id'],
            'name' => $gameData['name'],
            'cover' => $gameData['background_image'],
            'released' => $gameData['released'],
            'genres' => ! empty($gameData['genres']) ? array_column($gameData['genres'], 'name') : [],
            'platforms' => ! empty($gameData['platforms']) ? array_column(array_column($gameData['platforms'], 'platform'), 'slug') : [],
            'summary' => $gameData['description_raw'] ?? strip_tags($gameData['description']),
            'website' => $gameData['website'] ?? '',
            'developer' => ! empty($gameData['developers']) ? implode(', ', array_column($gameData['developers'], 'name')) : '',
            'publisher' => ! empty($gameData['publishers']) ? implode(', ', array_column($gameData['publishers'], 'name')) : '',
            'metacritic' => $gameData['metacritic'] ?? null,
            'esrb_rating' => ! empty($gameData['esrb_rating']) ? $gameData['esrb_rating']['name'] : '',
        ];

        return array_merge($gameData, $knownStores);
    }
}
