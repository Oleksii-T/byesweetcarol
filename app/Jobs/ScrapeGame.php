<?php

namespace App\Jobs;

use App\Models\Attachment;
use App\Models\Attachmentable;
use App\Models\Game;
use App\Models\Platform;
use App\Services\GameScraperService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ScrapeGame implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Game $game, public bool $overwrite = true)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $game = $this->game;
        $service = new GameScraperService($game->name);
        $scrapedData = $service->scrape();
        $data = [];
        $review = $game->posts()->whereRelation('category', 'slug', 'review')->first();

        if ($review) {
            $data['rating'] = $review->info->rating;
        }

        if ($this->needToBeUpdated($scrapedData, 'metaScore', 'metacritic')) {
            $data['metacritic'] = $scrapedData['metaScore'];
        }

        if ($this->needToBeUpdated($scrapedData, 'userScore', 'users_score')) {
            $data['users_score'] = $scrapedData['userScore'];
        }

        if ($this->needToBeUpdated($scrapedData, 'releaseDate', 'release_date')) {
            try {
                $data['release_date'] = Carbon::parse($scrapedData['releaseDate']);
            } catch (\Throwable $th) {
                report($th);
            }
        }

        if ($this->needToBeUpdated($scrapedData, 'description')) {
            $data['description'] = $scrapedData['description'];
        }

        if ($this->needToBeUpdated($scrapedData, 'developer')) {
            $data['developer'] = $scrapedData['developer'];
        }

        if ($this->needToBeUpdated($scrapedData, 'publisher')) {
            $data['publisher'] = $scrapedData['publisher'];
        }

        if ($this->needToBeUpdated($scrapedData, 'platforms')) {
            $platforms = $this->composePlatforms($scrapedData['platforms']);
            $game->platforms()->sync($platforms);
        }

        if ($this->needToBeUpdated($scrapedData, 'ganres')) {
            $data['ganres'] = $scrapedData['ganres'];
        }

        if ($this->needToBeUpdated($scrapedData, 'esrb')) {
            $data['esbr'] = $scrapedData['esrb'];
        }

        if ($this->needToBeUpdated($scrapedData, 'esrbImage', 'esbr')) {
            $this->attachImage($scrapedData['esrbImage'], 'esbr_image');
        }

        if ($this->needToBeUpdated($scrapedData, 'steam_site', 'steam')) {
            $data['steam'] = $scrapedData['steam_site'];
        }

        if ($this->needToBeUpdated($scrapedData, 'nintendo_site', 'nintendo_store')) {
            $data['nintendo_store'] = $scrapedData['nintendo_site'];
        }

        if ($this->needToBeUpdated($scrapedData, 'xbox_site', 'xbox_store')) {
            $data['xbox_store'] = $scrapedData['xbox_site'];
        }

        if ($this->needToBeUpdated($scrapedData, 'ps_site', 'playstation_store')) {
            $data['playstation_store'] = $scrapedData['ps_site'];
        }

        if ($this->needToBeUpdated($scrapedData, 'official_site')) {
            $data['official_site'] = $scrapedData['official_site'];
        }

        if ($this->needToBeUpdated($scrapedData, 'thumbnail')) {
            $this->attachImage($scrapedData['thumbnail'], 'thumbnail', $game->name, true);
        }

        if ($this->needToBeUpdated($scrapedData, 'screenshots')) {
            foreach ($scrapedData['screenshots'] as $i => $imageUrl) {
                $this->attachImage($imageUrl, 'screenshots', $game->name.' '.($i + 1));
            }
        }

        $game->update($data);
    }

    private function composePlatforms(string $platformsAsString): array
    {
        $allPlatforms = Platform::all();
        $platforms = [];

        $possiblePlatforms = [
            'PlayStation 2' => 'ps2',
            'PlayStation 3' => 'ps3',
            'PlayStation 4' => 'ps4',
            'PlayStation 5' => 'ps5',
            'PlayStation' => 'ps',
            'Xbox One' => 'xbox-one',
            'Xbox Series' => 'xbox-series',
            'Xbox 360' => 'xbox-360',
            'Xbox' => 'xbox',
            'Mac OS X' => 'mac',
            'macOS' => 'mac',
            'iOS' => 'ios',
            'Android' => 'android',
            'Windows' => 'pc',
            'Nintendo Switch' => 'nintencdo-switch',
            'mobile' => 'mobile',
        ];

        foreach ($possiblePlatforms as $fullPlatformName => $slug) {
            if (str_contains($platformsAsString, $fullPlatformName)) {
                $platform = $allPlatforms->where('slug', $slug)->first();
                if (! $platform) {
                    continue;
                }
                $platforms[] = $platform->id;
                $platformsAsString = str_replace($fullPlatformName, '', $platformsAsString);
            }
        }

        return $platforms;
    }

    private function attachImage($url, $group, $fileName = null, $deletePrev = false)
    {
        $extension = pathinfo($url, PATHINFO_EXTENSION);
        $ogFileName = basename($url);
        $fileName = $fileName ?? pathinfo($ogFileName, PATHINFO_FILENAME);
        $fileName = Str::slug($fileName);
        $attachmentModel = Attachment::where('source', $url)->first();

        if ($deletePrev) {
            Attachmentable::where([
                'attachmentable_id' => $this->game->id,
                'attachmentable_type' => Game::class,
                'group' => $group,
            ])->delete();
        }

        if (! $attachmentModel) {
            $disk = Attachment::disk('image');

            try {
                $fileContent = file_get_contents($url);
            } catch (\Throwable $th) {
                report($th);

                return;
            }
            $uniqueFileName = Attachment::makeUniqueName("$fileName.$extension", $disk);
            Storage::disk($disk)->put($uniqueFileName, $fileContent);

            $attachmentModel = Attachment::create([
                'name' => $uniqueFileName,
                'title' => $fileName,
                'alt' => $fileName,
                'original_name' => $ogFileName,
                'type' => 'image',
                'size' => Storage::disk($disk)->size($uniqueFileName),
                'source' => $url,
            ]);
        }

        Attachmentable::updateOrCreate([
            'attachment_id' => $attachmentModel->id,
            'attachmentable_id' => $this->game->id,
            'attachmentable_type' => Game::class,
            'group' => $group,
        ], []);
    }

    private function needToBeUpdated($scrapedData, $field, $modelField = null)
    {
        if (! $scrapedData[$field]) {
            return false;
        }

        if (! $this->overwrite) {
            $modelField ??= $field;

            if ($field == 'thumbnail') {
                if ($this->game->thumbnail()) {
                    return false;
                }
            } elseif ($field == 'platforms') {
                if ($this->game->platforms()->exists()) {
                    return false;
                }
            } elseif ($field == 'screenshots') {
                if ($this->game->screenshots()->exists()) {
                    return false;
                }
            } elseif ($this->game->$modelField) {
                return false;
            }
        }

        return true;
    }
}
