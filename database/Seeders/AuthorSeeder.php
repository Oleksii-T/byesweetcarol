<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds of pages and related blocks.
     *
     * @return void
     */
    public function run()
    {
        $schemas = [
            [
                'name' => 'Author #1',
                'meta_title' => 'Author #1',
                'meta_description' => 'Author #1',
                'title' => 'Author #1',
                'slug' => 'author-1',
                'email' => 'author1@mail.com',
            ],
            [
                'name' => 'Author #2',
                'meta_title' => 'Author #2',
                'meta_description' => 'Author #2',
                'title' => 'Author #2',
                'slug' => 'author-2',
                'email' => 'author2@mail.com',
            ],
            [
                'name' => 'Author #3',
                'meta_title' => 'Author #3',
                'meta_description' => 'Author #3',
                'title' => 'Author #3',
                'slug' => 'author-3',
                'email' => 'author3@mail.com',
            ],
        ];

        foreach ($schemas as $schema) {
            Author::create($schema);
        }
    }
}
