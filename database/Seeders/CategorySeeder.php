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
                'model' => [
                    'key' => 'news',
                    'in_menu' => true,
                    'order' => 1,
                ],
                'translations' => [
                    'name' => [
                        'en' => 'News',
                        'es' => 'Noticias',
                    ],
                    'slug' => [
                        'en' => 'news',
                        'es' => 'noticias',
                    ],
                ],
            ],
            [
                'model' => [
                    'key' => 'reviews',
                    'in_menu' => true,
                    'order' => 2,
                ],
                'translations' => [
                    'name' => [
                        'en' => 'Reviews',
                        'es' => 'Reseñas',
                    ],
                    'slug' => [
                        'en' => 'reviews',
                        'es' => 'resenas',
                    ],
                ],
            ],
            [
                'model' => [
                    'key' => 'guides',
                    'in_menu' => true,
                    'order' => 3,
                ],
                'translations' => [
                    'name' => [
                        'en' => 'Guides',
                        'es' => 'Guías',
                    ],
                    'slug' => [
                        'en' => 'guides',
                        'es' => 'guias',
                    ],
                ],
            ],
            [
                'model' => [
                    'key' => 'entertainment',
                    'in_menu' => true,
                    'order' => 4,
                ],
                'translations' => [
                    'name' => [
                        'en' => 'Entertainment',
                        'es' => 'Entretenimiento',
                    ],
                    'slug' => [
                        'en' => 'entertainment',
                        'es' => 'entretenimiento',
                    ],
                ],
            ],
            [
                'model' => [
                    'key' => 'features',
                    'in_menu' => true,
                    'order' => 5,
                ],
                'translations' => [
                    'name' => [
                        'en' => 'Features',
                        'es' => 'Características',
                    ],
                    'slug' => [
                        'en' => 'features',
                        'es' => 'caracteristicas',
                    ],
                ],
            ],
            [
                'model' => [
                    'key' => 'tech',
                    'in_menu' => false,
                    'order' => 6,
                ],
                'translations' => [
                    'name' => [
                        'en' => 'Tech',
                        'es' => 'Tecnología',
                    ],
                    'slug' => [
                        'en' => 'tech',
                        'es' => 'tecnologia',
                    ],
                ],
            ],
            [
                'model' => [
                    'key' => 'video',
                    'in_menu' => false,
                    'order' => 7,
                ],
                'translations' => [
                    'name' => [
                        'en' => 'Video',
                        'es' => 'Video',
                    ],
                    'slug' => [
                        'en' => 'video',
                        'es' => 'video',
                    ],
                ],
            ],
            [
                'model' => [
                    'key' => 'quizzes',
                    'in_menu' => false,
                    'order' => 8,
                ],
                'translations' => [
                    'name' => [
                        'en' => 'Quizzes',
                        'es' => 'Cuestionarios',
                    ],
                    'slug' => [
                        'en' => 'quizzes',
                        'es' => 'cuestionarios',
                    ],
                ],
            ],
        ];

        foreach ($schemas as $schema) {
            $model = Category::create($schema['model']);
            $model->saveTranslations($schema['translations']);
        }
    }
}
