<?php

namespace App\Services;

use App\Actions\GenerateSitemap;
use App\Enums\PostStatus;
use App\Enums\PostTCStyle;
use App\Enums\PromptType;
use App\Factories\AiModelFactory;
use App\Models\Category;
use App\Models\Game;
use App\Models\Post;
use App\Models\Prompt;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class PostService
{
    public function __construct(protected AiModelFactory $factory)
    {
        //
    }

    public function aiGenerate(array $data): string
    {
        $promptModel = Prompt::firstOrCreate(
            [
                'value' => $data['prompt'],
                'type' => PromptType::POST_CREATE,
            ],
            [
                'is_saved' => $data['save_prompt'],
                'name' => $data['save_prompt_name'] ?? 'Prompt',
            ]
        );

        $bindedPrompt = $this->bindPrompt($promptModel->value, $data);
        $aiModel = $this->factory->make($data['model']);
        $resultDto = $aiModel->generate($bindedPrompt);

        $promptModel->calls()->create([
            'prompt' => $bindedPrompt,
            'model' => $data['model'],
            'result' => $resultDto->result,
            'cost' => $resultDto->cost,
            'data' => $resultDto->data,
        ]);

        return $resultDto->result;
    }

    public function aiStore(array $data): Post
    {
        $data['status'] = PostStatus::DRAFT;
        $data['user_id'] = auth()->id();
        $data['category_id'] = Category::where('slug', 'reviews')->value('id');
        $data['meta_title'] = $data['title'];
        $data['meta_description'] = $data['title'];
        $data['slug'] = makeSlug($data['title'], Post::pluck('slug')->toArray());
        $data['tc_style'] = PostTCStyle::R_SIDEBAR;
        $data['block_groups'] = ['1'];

        try {
            DB::beginTransaction();
            $post = Post::create($data);
            $block = $post->blocks()->create([
                'ident' => 'content',
                'name' => 'Content',
                'order' => 1,
            ]);

            $block->items()->create([
                'order' => 1,
                'type' => 'text',
                'value' => json_encode([
                    'value' => $data['content'],
                ]),
            ]);

            Post::getAllSlugs(true);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        $post->refresh();

        return $post;
    }

    public function store(array $input, bool $renameThumb = false): Post
    {
        if ($input['status'] == PostStatus::PUBLISHED->value) {
            $input['published_at'] = now();
        }

        $newgame = false;

        if (is_numeric($input['game_id'] ?? false) && (int) $input['game_id'] == $input['game_id']) {
            $game = Game::findOrFail($input['game_id']);
        } elseif ($input['game_id'] ?? false) {
            $newgame = true;
            $game = Game::prepareNewGame($input['game_id']);
            $input['game_id'] = $game->id;
        }

        $input['user_id'] = auth()->id();
        $input['intro'] = sanitizeHtml($input['intro'] ?? '');
        $post = Post::create($input);
        $post->addAttachment($input['thumbnail'] ?? null, 'thumbnail', $renameThumb ? $input['slug'] : null);

        Post::getAllSlugs(true);
        GenerateSitemap::run();

        if ($newgame) {
            \App\Jobs\ScrapeGame::dispatch($game);
        }

        return $post;
    }

    public function attachTags(Post $post, ?string $tags): void
    {
        if (! $tags) {
            return;
        }

        $separator = '|';

        if (strpos($tags, $separator) === false) {
            $separator = ',';
        }

        $tags = array_map('trim', explode($separator, $tags));

        foreach ($tags as $tagName) {
            $tagModel = Tag::query()
                ->whereRaw('LOWER(name) = ?', [strtolower($tagName)])
                ->orWhereJsonContains('alter_names', strtolower($tagName))
                ->first();

            if (! $tagModel) {
                $tagModel = Tag::create([
                    'name' => $tagName,
                    'slug' => makeSlug($tagName, Tag::pluck('slug')->toArray()),
                ]);
            }

            $post->tags()->attach($tagModel);
        }
    }

    private function bindPrompt(string $prompt, array $data): string
    {
        $bindedPrompt = $prompt;
        foreach ($data as $key => $value) {
            $bindedPrompt = str_replace('{{'.$key.'}}', $value, $bindedPrompt);
        }

        return $bindedPrompt;
    }
}
