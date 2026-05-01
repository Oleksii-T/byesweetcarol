<?php

namespace Database\Seeders;

use App\Enums\PageStatus;
use App\Models\Page;
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
            [
                'status' => PageStatus::STATIC,
                'title' => 'Main Page',
                'link' => '/',
            ],
            [
                'status' => PageStatus::ENTITY,
                'title' => 'Category',
                'link' => '{category}',
            ],
            [
                'status' => PageStatus::STATIC,
                'title' => 'About Us',
                'link' => 'about-us',
            ],
            [
                'status' => PageStatus::STATIC,
                'title' => 'Privacy Policy',
                'link' => 'privacy-policy',
            ],
            [
                'status' => PageStatus::STATIC,
                'title' => 'Contact Us',
                'link' => 'contact',
            ],
            [
                'status' => PageStatus::STATIC,
                'title' => 'Terms of Use',
                'link' => 'terms-of-use',
            ],
            [
                'status' => PageStatus::STATIC,
                'title' => 'Cookie Policy',
                'link' => 'cookie-policy',
            ],
            [
                'status' => PageStatus::ENTITY,
                'title' => 'Post',
                'link' => '{post}',
            ],
        ];

        foreach ($pages as $pageData) {
            Page::updateOrCreate(
                [
                    'link' => $pageData['link'],
                ],
                $pageData
            );
        }
    }
}
