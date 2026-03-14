<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TwitchService
{
    private string $client_id;

    private string $client_secret;

    public function __construct()
    {
        $this->client_id = 'x36sjrxitj4439r1jl9zs969mmv7hu';
        $this->client_secret = 'tw0y1qpm76xw2c84j7ao88x8d4uqf8';
    }

    public function getAccessToken()
    {
        $cKey = 'igdb-aith-token';
        $token = cache()->get($cKey);

        if ($token) {
            return $token;
        }

        $url = 'https://id.twitch.tv/oauth2/token';
        $data = [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type' => 'client_credentials',
        ];

        $response = Http::post($url, $data)->json();

        cache()->put($cKey, $response['access_token'], $response['expires_in']);

        return $response['access_token'];
    }

    public function getClientId()
    {
        return $this->client_id;
    }
}
