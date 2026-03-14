<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class IgdbService
{
    public function __construct()
    {
    }

    public function incommingGames()
    {
        $url = 'https://api.igdb.com/v4/games/';
        $ts = now()->timestamp;
        $data = 'where first_release_date > '.$ts.'; fields name,cover,first_release_date,involved_companies,genres,platforms,screenshots,storyline,summary,url,websites;';
        $response = $this->post($url, $data, false);

        foreach ($response as $i => $game) {
            $response[$i] = $this->expandGameInfo($game);
        }

        return $response;
    }

    public function game($name)
    {
        $url = 'https://api.igdb.com/v4/games/';
        $data = 'search "'.$name.'"; fields name,cover,first_release_date,involved_companies,genres,platforms,screenshots,storyline,summary,url,websites;';
        $response = $this->post($url, $data);
        $response = $this->expandGameInfo($response);

        return $response;
    }

    public function getInvolvedCompany($id)
    {
        $url = 'https://api.igdb.com/v4/involved_companies';
        $data = "fields *; where id = $id;";

        return $this->post($url, $data);
    }

    public function getCompany($id)
    {
        $url = 'https://api.igdb.com/v4/companies';
        $data = "fields *; where id = $id;";

        return $this->post($url, $data);
    }

    public function getGanre($id)
    {
        $url = 'https://api.igdb.com/v4/genres';
        $data = "fields *; where id = $id;";

        return $this->post($url, $data);
    }

    public function getPlatform($id)
    {
        $url = 'https://api.igdb.com/v4/platforms';
        $data = "fields *; where id = $id;";

        return $this->post($url, $data);
    }

    public function getScreenshot($id)
    {
        $url = 'https://api.igdb.com/v4/screenshots';
        $data = "fields *; where id = $id;";

        return $this->post($url, $data);
    }

    public function getWebsite($id)
    {
        $url = 'https://api.igdb.com/v4/websites';
        $data = "fields *; where id = $id;";

        return $this->post($url, $data);
    }

    public function getCover($id)
    {
        $url = 'https://api.igdb.com/v4/covers';
        $data = "fields game,image_id,url; where id = $id;";
        $response = $this->post($url, $data);

        return $response;
    }

    private function post(string $url, string $data, bool $getFirst = true): array
    {
        $cKey = Str::slug($url.'-'.$data.'-'.($getFirst ? 1 : 0));
        $response = cache()->get($cKey, null);

        if ($response !== null) {
            return $response;
        }

        $twitch = new TwitchService();
        $twitchClientId = $twitch->getClientId();
        $twitchToken = $twitch->getAccessToken();
        $headers = [
            'Client-ID' => $twitchClientId,
            'Authorization' => "Bearer $twitchToken",
            'Accept' => 'application/json',
        ];

        \Log::channel('external_apis')->info('IGBD request', [
            'method' => 'post',
            'url' => $url,
            'data' => $data,
            'headers' => $headers,
        ]);

        $response = Http::withHeaders($headers)->withBody($data)->post($url);

        \Log::channel('external_apis')->info(' response: '.$response->status().' '.$response->body());

        $response = $response->json();

        if ($getFirst) {
            $response = $response[0] ?? [];
        }

        cache()->put($cKey, $response, 60 * 60 * 24);

        return $response;
    }

    private function websitesCategoryEnum($id)
    {
        $map = [
            1 => 'official',
            2 => 'wikia',
            3 => 'wikipedia',
            4 => 'facebook',
            5 => 'twitter',
            6 => 'twitch',
            8 => 'instagram',
            9 => 'youtube',
            10 => 'iphone',
            11 => 'ipad',
            12 => 'android',
            13 => 'steam',
            14 => 'reddit',
            15 => 'itch',
            16 => 'epicgames',
            17 => 'gog',
            18 => 'discord',
        ];

        return $map[$id] ?? '';
    }

    private function expandGameInfo($gameData): array
    {
        if (! $gameData || Arr::get($gameData, 'status') == 400) {
            return [];
        }

        $gameData['cover'] = 'https:'.$this->getCover($gameData['cover'])['url'] ?? null;

        $gameData['first_release_date'] = isset($gameData['first_release_date'])
            ? Carbon::createFromTimestamp($gameData['first_release_date'])->toISOString()
            : null;

        $ganres = [];
        foreach ($gameData['genres'] ?? [] as $ganreId) {
            $ganreData = $this->getGanre($ganreId);

            if ($ganreData) {
                $ganres[] = $ganreData['name'];
            }
        }
        $gameData['genres'] = $ganres;

        $platforms = [];
        foreach ($gameData['platforms'] as $platformId) {
            $platformData = $this->getPlatform($platformId);

            if ($platformData) {
                $platforms[] = $platformData['name'];
            }
        }
        $gameData['platforms'] = $platforms;

        $screenshots = [];
        foreach ($gameData['screenshots'] as $screenshotId) {
            $screenshotData = $this->getScreenshot($screenshotId);

            if ($screenshotData) {
                $screenshots[] = 'https:'.$screenshotData['url'];
            }
        }
        $gameData['screenshots'] = $screenshots;

        $websites = [];
        foreach ($gameData['websites'] as $websiteId) {
            $websiteData = $this->getWebsite($websiteId);

            if ($websiteData) {
                $websiteData['category'] = $this->websitesCategoryEnum($websiteData['category']);
                $websites[] = $websiteData;
            }
        }
        $gameData['websites'] = $websites;

        foreach ($gameData['involved_companies'] ?? [] as $companyId) {
            $companyData = $this->getInvolvedCompany($companyId);

            if ($companyData) {
                if ($companyData['developer']) {
                    $gameData['developer'] = $this->getCompany($companyData['company'])['name'] ?? null;
                }
                if ($companyData['publisher']) {
                    $gameData['publisher'] = $this->getCompany($companyData['company'])['name'] ?? null;
                }
            }
        }

        unset($gameData['involved_companies']);

        return $gameData;
    }
}
