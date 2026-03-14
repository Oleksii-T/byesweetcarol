<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Database\Seeder;

class PageBlockSeeder extends Seeder
{
    /**
     * Run the database seeds of pages and related blocks.
     *
     * @return void
     */
    public function run()
    {
        $schemas = [
            '/' => [
                'header' => [
                    //
                ],
                'hero' => [
                    'text-1' => [
                        'type' => 'text',
                        'value' => 'In a world where releases drop fast and rumors spread faster,',
                    ],
                    'text-2' => [
                        'type' => 'text',
                        'value' => 'the real story can be hard to find.',
                    ],
                    'text-3' => [
                        'type' => 'text',
                        'value' => 'So we track updates, patch notes, and launches and break them down to spotlight the wins, the misses, and the moments worth your time.',
                    ],
                    'text-4' => [
                        'type' => 'text',
                        'value' => 'Because gaming news should be clear.',
                    ],
                    'cta' => [
                        'type' => 'text',
                        'value' => 'Enter',
                    ],
                ],
                'footer' => [
                    //
                ],
            ],
            'contact' => [
                'header' => [
                    'title' => [
                        'type' => 'text',
                        'value' => 'Contact Us',
                    ],
                    'text' => [
                        'type' => 'text',
                        'value' => 'We are happy to hear from you.',
                    ],
                ],
                'form' => [
                    'cta' => [
                        'type' => 'text',
                        'value' => 'Send Message',
                    ],
                ],
                'we-offer' => [
                    'title' => [
                        'type' => 'text',
                        'value' => 'What Help Can We Offer?',
                    ],
                    'card-1-title' => [
                        'type' => 'text',
                        'value' => 'News Tips',
                    ],
                    'card-1-text' => [
                        'type' => 'text',
                        'value' => 'Share new findings, stories, or evidence you want verified.',
                    ],
                    'card-2-title' => [
                        'type' => 'text',
                        'value' => 'Partnerships',
                    ],
                    'card-2-text' => [
                        'type' => 'text',
                        'value' => 'Explore collaborations that keep facts accessible and open.',
                    ],
                    'card-3-title' => [
                        'type' => 'text',
                        'value' => 'Press Kits',
                    ],
                    'card-3-text' => [
                        'type' => 'text',
                        'value' => 'Request assets, statements, and media information.',
                    ],
                    'card-4-title' => [
                        'type' => 'text',
                        'value' => 'Support',
                    ],
                    'card-4-text' => [
                        'type' => 'text',
                        'value' => 'Get help with submissions, corrections, or site access.',
                    ],
                ],
                'wait' => [
                    'title' => [
                        'type' => 'text',
                        'value' => 'How Long to Wait for the Answer?',
                    ],
                    'sub-title' => [
                        'type' => 'text',
                        'value' => 'Response times depend on the complexity of your inquiry.',
                    ],
                    'bullet-1' => [
                        'type' => 'text',
                        'value' => 'General questions: 2 business days.',
                    ],
                    'bullet-2' => [
                        'type' => 'text',
                        'value' => 'Press and partnerships: 3 business days.',
                    ],
                    'bullet-3' => [
                        'type' => 'text',
                        'value' => 'Investigations or reviews: up to 4 business days.',
                    ],
                ],
            ],
            '{category}' => [
                'running' => [
                    '1' => [
                        'type' => 'text',
                        'value' => '09 TRUTH DROPS | 07.01.26 at 15:43:10 GMT',
                    ],
                    '2' => [
                        'type' => 'text',
                        'value' => '[09 TRUTH DROPS] 07.01.26 at 15:43:10 GMT',
                    ],
                    '3' => [
                        'type' => 'text',
                        'value' => '09 TRUTH DROPS | 07.01.26 at 15:43:10 GMT',
                    ],
                    '4' => [
                        'type' => 'text',
                        'value' => '[09 TRUTH DROPS] 07.01.26 at 15:43:10 GMT',
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
                        'name' => $blockName,
                    ],
                    [
                        'data' => $block,
                    ]
                );
            }
        }
    }
}
