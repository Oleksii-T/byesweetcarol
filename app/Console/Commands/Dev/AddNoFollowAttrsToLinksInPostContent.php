<?php

namespace App\Console\Commands\Dev;

use App\Models\BlockItem;
use App\Models\Post;
use Illuminate\Console\Command;

class AddNoFollowAttrsToLinksInPostContent extends Command
{
    protected $signature = 'app:AddNoFollowAttrsToLinksInPostContent';

    protected $description = 'AddNoFollowAttrsToLinksInPostContent';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = BlockItem::query()
            ->where('value', 'like', '%<a%')
            ->whereRelation('block', 'blockable_type', Post::class);

        $total = $query->count();
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->chunkById(100, function ($items) use ($bar) {
            foreach ($items as $item) {
                $val = $item->valueSimple;

                if (!is_string($val)) {
                    $bar->advance();
                    continue;
                }

                $new = preg_replace_callback('/<a(\s[^>]*)?>/', function ($matches) {
                    $attrs = $matches[1] ?? '';

                    if (!preg_match('/\brel\s*=/i', $attrs)) {
                        $attrs .= ' rel="nofollow"';
                    }

                    if (!preg_match('/\btarget\s*=/i', $attrs)) {
                        $attrs .= ' target="_blank"';
                    }

                    return '<a' . $attrs . '>';
                }, $val);

                if ($new !== $val) {
                    $item->update(['value' => ['value' => $new]]);
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("Done. Processed $total items.");
    }
}
