<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds of pages and related blocks.
     *
     * @return void
     */
    public function run()
    {
        $pages = [
        ];

        foreach ($pages as $pageAll) {
            $page = $pageAll['page'];
            $p = Page::updateOrCreate(
                [
                    'link' => $page['link'],
                ],
                $page
            );

            foreach ($pageAll['blocks'] ?? [] as $block) {
                PageBlock::updateOrCreate(
                    [
                        'page_id' => $p->id,
                        'name' => $block['name'],
                    ],
                    [
                        'name' => $block['name'],
                        'page_id' => $p->id,
                        'data' => $block['data'],
                    ]);
            }
        }
    }
}
