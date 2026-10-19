<?php

namespace App\Console\Commands;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class DeleteOldPosts extends Command
{
    protected $signature = 'posts:delete-older-than
                            {date : Delete posts created before this date (YYYY-MM-DD)}
                            {--dry-run : Show the number of matching posts without deleting them}';

    protected $description = 'Soft delete posts created before a given date in the application timezone';

    public function handle(): int
    {
        $date = $this->argument('date');

        if (Validator::make(['date' => $date], ['date' => ['required', 'date_format:Y-m-d']])->fails()) {
            $this->error('The date must be a valid date in YYYY-MM-DD format.');

            return self::FAILURE;
        }

        $cutoff = Carbon::createFromFormat('!Y-m-d', $date, config('app.timezone'));
        $query = Post::query()->where('created_at', '<', $cutoff);

        if ($this->option('dry-run')) {
            $this->info("Would soft delete {$query->count()} posts created before {$date}.");

            return self::SUCCESS;
        }

        $deleted = 0;

        $query->chunkById(500, function ($posts) use (&$deleted) {
            foreach ($posts as $post) {
                if ($post->delete()) {
                    $deleted++;
                }
            }
        });

        $this->info("Soft deleted {$deleted} posts created before {$date}.");

        return self::SUCCESS;
    }
}
