<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
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
                'model' => [
                ],
                'translations' => [
                    'name' => [
                        'en' => 'Popular',
                        'es' => 'Popular',
                    ],
                    'slug' => [
                        'en' => 'popular',
                        'es' => 'popular',
                    ],
                ],
            ],
            [
                'model' => [
                ],
                'translations' => [
                    'name' => [
                        'en' => 'Verified',
                        'es' => 'Verificada',
                    ],
                    'slug' => [
                        'en' => 'verified',
                        'es' => 'verificada',
                    ],
                ],
            ],
            [
                'model' => [
                ],
                'translations' => [
                    'name' => [
                        'en' => 'Recent',
                        'es' => 'Reciente',
                    ],
                    'slug' => [
                        'en' => 'recent',
                        'es' => 'reciente',
                    ],
                ],
            ],
        ];

        foreach ($schemas as $schema) {
            $model = Tag::create($schema['model']);
            $model->saveTranslations($schema['translations']);
        }
    }
}
