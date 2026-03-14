<?php

namespace App\Console\Commands;

use App\Actions\GenerateSitemap as GenerateSitemapAction;
use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Geneate sitemap';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        GenerateSitemapAction::run();
    }
}
