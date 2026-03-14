<?php

namespace App\Services;

use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;

class GameRadarService
{
    private string $html;

    public function __construct()
    {
        $this->html = PageFetchService::get('https://www.gamesradar.com/video-game-release-dates/');
    }

    public function games()
    {
        $result = [];
        $games = [];
        $crawler = new Crawler($this->html);
        $nodes = $crawler->filter('#article-body ul li');

        foreach ($nodes as $i => $node) {
            $games[] = $node->nodeValue ?? null;
        }

        $currentYear = now()->format('Y');
        $nextYear = now()->addYear()->format('Y');
        $months = array_map(fn ($month) => Carbon::create(null, $month)->format('F'), range(1, 12));
        $days = array_reverse(range(1, 31));

        foreach ($games as $i => $gameString) {
            $dateSeparatorAt = strripos($gameString, '–');

            if ($dateSeparatorAt === false) {
                continue;
            }

            $date = substr($gameString, $dateSeparatorAt + 1);
            $nameAndPlatform = substr($gameString, 0, $dateSeparatorAt - 1);
            $platformsSeparatorAt = strripos($gameString, '(');
            $platforms = substr($nameAndPlatform, $platformsSeparatorAt + 1);
            $platforms = str_replace(')', '', $platforms);
            $platforms = str_replace(' ', '', $platforms);
            $platforms = explode(',', $platforms);
            $name = trim(substr($nameAndPlatform, 0, $platformsSeparatorAt));
            $release = [];

            if (str_contains($date, 'TBC')) {
                continue;
            }

            if (str_contains($date, $currentYear)) {
                $release['year'] = $currentYear;
            } elseif (str_contains($date, $nextYear)) {
                $release['year'] = $nextYear;
            } else {
                // if the year is skipped, assume that it is current year
                $release['year'] = $currentYear;
            }

            if (empty($release['year'])) {
                continue;
            }

            $date = str_replace($release['year'], '', $date);

            foreach ($months as $month) {
                if (str_contains($date, $month)) {
                    $release['month'] = $month;
                    $date = str_replace($month, '', $date);

                    break;
                }
            }

            if (empty($release['month'])) {
                continue;
            }

            foreach ($days as $day) {
                if (str_contains($date, $day)) {
                    $release['day'] = $day;
                    $date = str_replace($day, '', $date);

                    break;
                }
            }

            if (empty($release['day'])) {
                continue;
            }

            $result[] = [
                'platforms' => $platforms,
                'name' => $name,
                'released' => $release['month'].' '.$release['day'].', '.$release['year'],
            ];
        }

        return $result;
    }

    public function incommingGames()
    {
        return array_filter($this->games(), fn ($game) => now() < Carbon::parse($game['released']));
    }
}
