<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
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
                'name' => 'Game News',
                'description' => '',
                'meta_title' => 'Game News',
                'meta_description' => 'Game News',
                'slug' => 'news',
                'in_menu' => true,
                'order' => 1,
            ],
        ];

        foreach ($schemas as $schema) {
            $model = Category::create($schema);
        }
    }
}
