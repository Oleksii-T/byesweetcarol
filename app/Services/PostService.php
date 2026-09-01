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

    public function parseBlockHtml(string $postText): string
    {
        $youtubeEmbeds = [];

        $postText = preg_replace_callback(
            '~\[[^\]\r\n]*\]\(\s*(https?://[^\s)]+)\s*\)~i',
            function (array $matches) use (&$youtubeEmbeds): string {
                $videoId = $this->youtubeVideoId($matches[1]);

                if ($videoId === null) {
                    return $matches[0];
                }

                $placeholder = '___YOUTUBE_EMBED_'.count($youtubeEmbeds).'___';
                $youtubeEmbeds[$placeholder] = $this->youtubeEmbed($videoId);

                return $placeholder;
            },
            $postText
        );

        $postText = preg_replace_callback(
            '~(?<!["\'=])(https?://(?:(?:(?:www|m|music)\.)?youtube\.com|(?:www\.)?youtube-nocookie\.com|(?:www\.)?youtu\.be)/[^\s<>\[\]()]+)~i',
            function (array $matches): string {
                $url = rtrim($matches[1], '.,!?;:');
                $trailingPunctuation = substr($matches[1], strlen($url));
                $videoId = $this->youtubeVideoId($url);

                if ($videoId === null) {
                    return $matches[0];
                }

                return $this->youtubeEmbed($videoId).$trailingPunctuation;
            },
            $postText
        );

        $postText = strtr($postText, $youtubeEmbeds);

        preg_match_all(
            '~https://t\.co/[A-Za-z0-9]+~i',
            $postText,
            $matches
        );

        $shortUrls = array_unique($matches[0]);

        foreach ($shortUrls as $shortUrl) {
            $postUrl = $this->resolveXPostUrl($shortUrl);

            if ($postUrl === null) {
                continue;
            }

            $blockquote = sprintf(
                '<blockquote class="twitter-tweet"><a href="%s"></a></blockquote>',
                htmlspecialchars($postUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            );

            $postText = str_replace($shortUrl, $blockquote, $postText);
        }

        return $postText;
    }

    private function youtubeVideoId(string $url): ?string
    {
        $parts = parse_url(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if ($parts === false) {
            return null;
        }

        $host = strtolower($parts['host'] ?? '');
        $path = trim($parts['path'] ?? '', '/');
        $videoId = null;

        if (in_array($host, ['youtu.be', 'www.youtu.be'], true)) {
            $videoId = explode('/', $path)[0] ?? null;
        } elseif (in_array($host, [
            'youtube.com',
            'www.youtube.com',
            'm.youtube.com',
            'music.youtube.com',
            'youtube-nocookie.com',
            'www.youtube-nocookie.com',
        ], true)) {
            if ($path === 'watch') {
                parse_str($parts['query'] ?? '', $query);
                $videoId = $query['v'] ?? null;
            } elseif (preg_match('~^(?:embed|shorts|live|v)/([^/]+)~', $path, $matches)) {
                $videoId = $matches[1];
            }
        }

        if (! is_string($videoId) || ! preg_match('~^([A-Za-z0-9_-]{11})~', $videoId, $matches)) {
            return null;
        }

        return $matches[1];
    }

    private function youtubeEmbed(string $videoId): string
    {
        return sprintf(
            '<iframe src="https://www.youtube-nocookie.com/embed/%s" title="YouTube video player" loading="lazy" style="width: 100%%; aspect-ratio: 16 / 9; border: 0;" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
            $videoId
        );
    }

    private function resolveXPostUrl(string $shortUrl): ?string
    {
        try {
            $response = Http::timeout(10)
                ->connectTimeout(5)
                ->withOptions([
                    'allow_redirects' => [
                        'max' => 5,
                        'strict' => true,
                        'referer' => false,
                        'track_redirects' => true,
                    ],

                    // Avoid downloading a potentially large destination response.
                    'stream' => true,
                ])
                ->get($shortUrl);

            if (! $response->successful()) {
                return null;
            }

            $finalUrl = (string) $response->effectiveUri();

            return $this->normalizeXPostUrl($finalUrl);
        } catch (\Throwable $exception) {
            Log::warning('Could not resolve X short URL.', [
                'url' => $shortUrl,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function normalizeXPostUrl(string $url): ?string
    {
        $parts = parse_url($url);

        if ($parts === false) {
            return null;
        }

        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';

        if (! in_array($host, [
            'x.com',
            'www.x.com',
            'twitter.com',
            'www.twitter.com',
        ], true)) {
            return null;
        }

        if (! preg_match(
            '~^/([A-Za-z0-9_]+)/status/(\d+)/?$~',
            $path,
            $matches
        )) {
            return null;
        }

        $username = $matches[1];
        $postId = $matches[2];

        return "https://x.com/{$username}/status/{$postId}";
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
