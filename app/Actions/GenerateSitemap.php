<?php

namespace App\Actions;

use App\Enums\PageStatus;
use App\Models\Author;
use App\Models\Game;
use App\Models\Page;
use App\Models\Post;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap
{
    // const CHANGE_FREQUENCY_ALWAYS = 'always';
    // const CHANGE_FREQUENCY_HOURLY = 'hourly';
    // const CHANGE_FREQUENCY_DAILY = 'daily';
    // const CHANGE_FREQUENCY_WEEKLY = 'weekly';
    // const CHANGE_FREQUENCY_MONTHLY = 'monthly';
    // const CHANGE_FREQUENCY_YEARLY = 'yearly';
    // const CHANGE_FREQUENCY_NEVER = 'never';

    public static function run()
    {
        $path = public_path('sitemap.xml');
        $pages = Page::query()
            ->where('status', PageStatus::PUBLISHED)
            ->orWhere('status', PageStatus::STATIC)
            ->get();

        $sm = Sitemap::create();

        foreach ($pages as $page) {
            $sm->add(Url::create(url($page->link))->setLastModificationDate($page->created_at));
        }

        foreach (Post::publised()->get() as $post) {
            $sm->add(Url::create(route('posts.show', $post))->setLastModificationDate($post->updated_at));
        }

        // foreach (Category::where('slug', 'news')->get() as $category) {
        //     $sm->add(Url::create(route('categories.show', $category))->setLastModificationDate($category->updated_at));
        // }

        foreach (Author::all() as $author) {
            $sm->add(Url::create(route('authors.show', $author))->setLastModificationDate($author->updated_at));
        }

        foreach (Game::published()->get() as $game) {
            $sm->add(Url::create(route('games.show', $game))->setLastModificationDate($game->updated_at));
        }

        $sm->writeToFile($path);

        // Read the file into a string
        $content = file_get_contents($path);

        // Remove lines containing '<priority>' using regex
        $content = preg_replace('/.*<priority>.*\n/', '', $content);
        $content = preg_replace('/.*<changefreq>.*\n/', '', $content);

        // Write the modified string back to the file
        file_put_contents($path, $content);
    }
}
