<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Author;
use App\Models\Category;
use App\Models\Game;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    protected $games;

    protected $categories;

    protected $authors;

    protected $tags;

    /**
     * Run the database seeds of pages and related blocks.
     *
     * @return void
     */
    public function run()
    {
        $this->games = Game::all();
        $this->categories = Category::all();
        $this->authors = Author::all();
        $this->tags = Tag::all();

        $schemas = [
            [
                'model' => [
                    'category_id' => $this->getCategory('news'),
                    'author_id' => $this->getAuthor('author-yaroslav'),
                    'game_id' => $this->getGame('witcher-3-wild-hunt'),
                    'is_active' => true,
                ],
                'translations' => [
                    'slug' => [
                        'en' => 'gerald-from-rivia-slept-with-a-new-witch',
                        'es' => 'gerald-de-rivia-se-acostó-con-una-nueva-bruja',
                    ],
                    'title' => [
                        'en' => 'Gerald From Rivia slept with a new witch',
                        'es' => 'Gerald de Rivia se acostó con una nueva bruja',
                    ],
                    'content' => [
                        'en' => 'In the new episode, Gerald of Rivia slept with a new witch.
                        Another powerful witch couldn\'t stand Gerald\'s cat eyes. Witcher again pretended he has amnesia and dived into her secret cave.',
                        'es' => 'En el nuevo episodio, Gerald de Rivia se acostó con una nueva bruja.
                        Otra bruja poderosa no podía soportar los ojos de gato de Gerald. Witcher volvió a fingir que tenía amnesia y se sumergió en su cueva secreta.',
                    ],
                ],
                'tags' => [$this->getTag('popular'), $this->getTag('recent')],
                'attachments' => [
                    'thumbnail' => [
                        [
                            'name' => 'witch-post-th-IkScYXATk5hFwz.webp',
                            'original_name' => 'witch-post-th.webp',
                            'type' => 'image',
                            'size' => '598464',
                        ],
                    ],
                    'images' => [
                        [
                            'name' => 'witch-post-th-1.jpeg',
                            'original_name' => 'witch-post-th-1.jpeg',
                            'type' => 'image',
                            'size' => '40673',
                        ],
                        [
                            'name' => 'witch-post-th-2.jpeg',
                            'original_name' => 'witch-post-th-2.jpeg',
                            'type' => 'image',
                            'size' => '5527',
                        ],
                        [
                            'name' => 'witch-post-th-3.jpeg',
                            'original_name' => 'witch-post-th-3.jpeg',
                            'type' => 'image',
                            'size' => '7113',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($schemas as $schema) {
            $model = Post::create($schema['model']);
            $model->saveTranslations($schema['translations']);
            $model->tags()->attach($schema['tags']);
            foreach ($schema['attachments'] as $group => $attachments) {
                foreach ($attachments as $attachment) {
                    Attachment::create($attachment + [
                        'group' => $group,
                        'attachmentable_id' => $model->id,
                        'attachmentable_type' => Post::class,
                    ]);
                }
            }
        }

        $posts = Post::factory()
            ->for($this->categories->random())
            ->for($this->authors->random())
            ->for($this->games->random())
            ->has(
                Attachment::factory()->group('thumbnail')->state(
                    function (array $attributes, Post $post) {
                        return [
                            'attachmentable_id' => $post->id,
                            'attachmentable_type' => Post::class,
                        ];
                    }
                ),
                'thumbnail'
            )
            ->has(
                Translation::factory()->locale('en')->field('title')->text(30)->state(
                    function (array $attributes, Post $post) {
                        return [
                            'translatable_id' => $post->id,
                            'translatable_type' => Post::class,
                        ];
                    }
                )
            )
            ->has(
                Translation::factory()->locale('en')->field('content')->text(1000)->state(
                    function (array $attributes, Post $post) {
                        return [
                            'translatable_id' => $post->id,
                            'translatable_type' => Post::class,
                        ];
                    }
                )
            )
            ->count(300)
            ->create();

        $tagsCount = $this->tags->count();

        foreach ($posts as $post) {
            $translations = [
                'slug' => [
                    'en' => Str::slug($post->title),
                    'es' => 'es-'.Str::slug($post->title),
                ],
                'title' => [
                    'en' => $post->title,
                    'es' => "ES | $post->title",
                ],
                'content' => [
                    'en' => $post->content,
                    'es' => "ES | $post->content",
                ],
            ];
            $post->update([
                'category_id' => $this->categories->random()->id,
                'game_id' => $this->games->random()->id,
                'author_id' => $this->authors->random()->id,
                'views' => rand(0, 500),
            ]);
            $post->created_at = now()->subDays(rand(0, 360));
            $post->save();
            $post->tags()->sync($this->tags->pluck('id')->shuffle()->take(rand(0, $tagsCount)));
            $post->saveTranslations($translations);
        }
    }

    private function getGame($key)
    {
        return $this->games->where('slug', $key)->value('id');
    }

    private function getCategory($key)
    {
        return $this->categories->where('slug', $key)->value('id');
    }

    private function getAuthor($key)
    {
        return $this->authors->where('slug', $key)->value('id');
    }

    private function getTag($key)
    {
        return $this->tags->where('slug', $key)->value('id');
    }
}
