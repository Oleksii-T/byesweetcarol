<?php

namespace App\Http\Controllers\Api;

use App\Actions\SaveContentBlocks;
use App\Enums\PostStatus;
use App\Enums\PostTCStyle;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PostCreateRequest;
use App\Http\Requests\Api\PostUpdateRequest;
use App\Models\Author;
use App\Models\Category;
use App\Models\Post;
use App\Services\PostService;

class PostController extends Controller
{
    public function __construct(
        protected PostService $service,
    ) {}

    public function store(PostCreateRequest $request)
    {
        $data = $request->validated();
        $data = $this->normalizeExternalData($data);
        $toPublish = $data['publish'] ?? false;
        $useAuthor = $data['author'] ?? 'random';
        $categorySlug = $data['category'] ?? 'news';

        // add data which is mission in n8n to simulate a generatl post create
        if ($toPublish) {
            $data['status'] = PostStatus::PUBLISHED;
            $data['published_at'] = now();
        } else {
            $data['status'] = PostStatus::DRAFT;
        }

        if ($useAuthor == 'random') {
            $data['author_id'] = Author::inRandomOrder()->value('id');
        } elseif ($useAuthor == 'first') {
            $data['author_id'] = Author::first()->id;
        } else {
            $data['author_id'] = Author::where('id', $useAuthor)->orWhere('email', $useAuthor)->firstOrFail()->id;
        }

        $data['tc_style'] = PostTCStyle::R_SIDEBAR;
        $data['slug'] = makeSlug($data['title'], Post::pluck('slug')->toArray());
        $data['category_id'] = Category::where('slug', $categorySlug)->value('id');
        $blocks = $this->makeBlocks($data['blocks'] ?? $data['body']);

        $post = \DB::transaction(function () use ($data, $blocks) {
            // call general post create
            $post = $this->service->store($data, true);

            // attach tags
            $this->service->attachTags($post, $data['tags'] ?? null);

            // save content block
            SaveContentBlocks::run($post, $blocks);

            if ($data['source'] ?? null) {
                $post->info()->create(['source' => $data['source']]);
            }

            return $post;
        });

        return response()->json([
            'source_id' => $data['id'] ?? null,
            'post_id' => $post->id,
            'post_url' => route('posts.show', $post),
            'post_admin_url' => route('admin.posts.edit', $post),
        ]);
    }

    public function update(PostUpdateRequest $request, int $id)
    {
        $post = Post::findOrFail($id);
        $data = $request->validated();
        $data = $this->normalizeExternalData($data);
        $blocks = $this->makeBlocks($data['blocks'] ?? $data['body']);

        $post = \DB::transaction(function () use ($post, $data, $blocks) {
            $post->update(collect($data)
                ->only([
                    'title',
                    'meta_title',
                    'meta_description',
                    'external_data',
                ])
                ->toArray());

            if (array_key_exists('tags', $data)) {
                $post->tags()->detach();
                $this->service->attachTags($post, $data['tags']);
            }

            SaveContentBlocks::run($post, $blocks);

            return $post;
        });

        return response()->json([
            'source_id' => $data['id'] ?? null,
            'post_id' => $post->id,
            'post_url' => route('admin.posts.edit', $post),
        ]);
    }

    private function normalizeExternalData(array $data): array
    {
        if (isset($data['external_data']) && is_string($data['external_data'])) {
            $data['external_data'] = json_decode($data['external_data'], true);
        }

        return $data;
    }

    private function makeBlocks(string $data): array
    {
        $blocksRaw = json_decode($data, true);

        // if it is not json, then assume it is just a simple body html - manually creaate 1 content block
        if (! $blocksRaw) {
            $blocksRaw = [
                [
                    'title' => 'block1',
                    'body' => $this->service->parseBlockHtml($data),
                ],
            ];
        }

        $blocks = [
            'blocks' => [],
            'group_blocks' => [count($blocksRaw)],
        ];

        // this should be the same as frontend format for saving data, because we reuse SaveContentBlocks action.
        foreach ($blocksRaw as $i => $blockRaw) {
            $blocks['blocks'][] = [
                'ident' => $blockRaw['title'],
                'name' => $blockRaw['title'],
                'items' => [
                    [
                        'type' => 'text',
                        'order' => 1,
                        'value' => [
                            'value' => $this->service->parseBlockHtml($blockRaw['body']),
                        ],
                    ],
                ],
                'order' => $i + 1,
            ];
        }

        return $blocks;
    }
}
