<?php

namespace App\Console\Commands;

use App\Enums\GameStatus;
use App\Jobs\ScrapeGame;
use App\Models\Game;
use App\Services\GameRadarService;
use App\Services\RawgService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CalendarGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:games';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get new calendar games';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->log('Start scraping for calendar...');
        $this->getFromRawg();
        $this->getFromGameRadar();
    }

    private function getFromRawg()
    {
        $this->log('_Scrapping rawg...');
        $rawg = new RawgService;
        $games = $rawg->incommingGames();

        $this->log('__found '.count($games).' games');

        foreach ($games ?? [] as $i => $game) {
            $this->saveGame($game, $i);
        }

        $this->log('_Rawg scrapping ended...');
    }

    private function getFromGameRadar()
    {
        $this->log('_Scrapping GamesRadar...');
        $gameRadar = new GameRadarService;
        $games = $gameRadar->games();

        $this->log('__found '.count($games).' games');

        foreach ($games ?? [] as $i => $game) {
            $this->saveGame($game, $i);
        }

        $this->log('_GamesRadar scrapping ended...');
    }

    private function saveGame($game, $i)
    {
        try {
            $this->log("__process games #$i: ".json_encode($game));

            if (empty($game['name'] || empty($game['released']))) {
                return;
            }

            $releaseDate = Carbon::parse($game['released']);
            $exists = Game::where('name', $game['name'])->first();

            if ($exists) {
                $this->log("___exists #$exists->id");
                if ($exists->status == GameStatus::CALENDAR_PUBLISHED) {
                    $this->log('___update release date '.$game['released']);
                    $exists->update(['release_date' => $releaseDate]);
                }

                return;
            }

            $data = [
                'name' => $game['name'],
                'slug' => makeSlug($game['slug'] ?? Str::slug($game['name']), Game::pluck('slug')->toArray()),
                'status' => GameStatus::DRAFT,
                'meta_title' => $game['name'],
                'meta_description' => $game['name'],
                'metacritic' => $game['metacritic'] ?? null,
                'release_date' => $releaseDate,
                'developer' => '',
                'platforms' => '',
                'ganres' => '',
                'hours' => [],
                'description' => '',
            ];

            if ($game['genres'] ?? null) {
                $data['ganres'] = implode(', ', array_column($game['genres'], 'name'));
            }

            if ($game['playtime'] ?? null) {
                $data['hours'] = ['all' => $game['playtime']];
            }

            $model = Game::create($data);

            ScrapeGame::dispatch($model, false); //! should sync

            $model = $model->fresh();

            $model->update([
                'status' => GameStatus::CALENDAR_DRAFT,
            ]);

            $this->log('___done');
        } catch (\Throwable $th) {
            $this->log('___ERROR: '.exceptionAsString($th));
        }
    }

    private function log($text)
    {
        \Log::channel('calendar_scraper')->info($text);
    }
}
