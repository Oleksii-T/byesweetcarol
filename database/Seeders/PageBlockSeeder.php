<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Database\Seeder;

class PageBlockSeeder extends Seeder
{
    public function run()
    {
        $schemas = [
            '/' => [
                'header' => [
                    'site-name' => [
                        'type'  => 'text',
                        'value' => 'BSCNews',
                    ],
                    'tagline' => [
                        'type'  => 'text',
                        'value' => 'Fresh Game News',
                    ],
                ],
                'footer' => [
                    'site-name' => [
                        'type'  => 'text',
                        'value' => 'BSCNews',
                    ],
                ],
                'hero' => [
                    'title' => [
                        'type'  => 'text',
                        'value' => 'Fresh game news for players across PC, PlayStation, Xbox, and Switch.',
                    ],
                    'subtitle' => [
                        'type'  => 'text',
                        'value' => 'Daily coverage on launches, patches, studio moves, showcases, and hardware updates. Top stories below.',
                    ],
                    'trending-label' => [
                        'type'  => 'text',
                        'value' => 'Trending Topics',
                    ],
                    'all-news-cta' => [
                        'type'  => 'text',
                        'value' => 'All Game News',
                    ],
                ],
            ],
        ];

        foreach ($schemas as $pageLink => $blocks) {
            $pageModel = Page::where('link', $pageLink)->firstOrFail();

            foreach ($blocks as $blockName => $block) {
                PageBlock::updateOrCreate(
                    [
                        'page_id' => $pageModel->id,
                        'name'    => $blockName,
                    ],
                    [
                        'data' => $block,
                    ]
                );
            }
        }
    }
}
