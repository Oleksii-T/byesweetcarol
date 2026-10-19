<?php

namespace App\Services;

use App\Models\BlockItem;
use App\Models\ContentBlock;
use App\Models\Post;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TrashedPostImageReferences
{
    /** Restrict attachment owners to trashed posts and their content. */
    public function trashedOwners($query): void
    {
        $posts = Post::onlyTrashed()->select('id');
        $blocks = DB::table('content_blocks')
            ->whereIn('blockable_type', $this->types(Post::class))
            ->whereIn('blockable_id', $posts)->select('id');

        $query->where(function ($query) use ($posts) {
            $query->whereIn('attachmentable_type', $this->types(Post::class))
                ->whereIn('attachmentable_id', $posts);
        })->orWhere(function ($query) use ($blocks) {
            $query->whereIn('attachmentable_type', $this->types(ContentBlock::class))
                ->whereIn('attachmentable_id', $blocks);
        })->orWhere(function ($query) use ($blocks) {
            $query->whereIn('attachmentable_type', $this->types(BlockItem::class))
                ->whereIn('attachmentable_id', DB::table('block_items')->whereIn('block_id', $blocks)->select('id'));
        });
    }

    /** @return array{trashed: array<string, true>, retained: array<string, true>} */
    public function inlineImages(): array
    {
        $images = ['trashed' => [], 'retained' => []];
        $path = parse_url(Storage::disk('aimages')->url(''), PHP_URL_PATH);
        $pattern = '~(?='.preg_quote(rtrim($path ?: '/storage/attachments/images', '/').'/', '~').'([^"\'<>?#\r\n\\\\]+))~u';

        $scan = function (Builder $query, array $columns) use (&$images, $pattern): void {
            foreach ($query->cursor() as $row) {
                $group = ($row->deleted_at ?? null) === null ? 'retained' : 'trashed';
                foreach ($columns as $column) {
                    $text = (string) ($row->$column ?? '');
                    // Decode JSON values before looking for URLs in rich text.
                    $decoded = json_decode($text, true);
                    if (is_array($decoded)) {
                        $text = implode(' ', array_filter(\Illuminate\Support\Arr::flatten($decoded), 'is_string'));
                    }
                    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    preg_match_all($pattern, $text, $matches);
                    foreach ($matches[1] as $name) {
                        $images[$group][rawurldecode($name)] = true;
                        if ($group === 'retained') {
                            // Conservatively retain URL prefixes in srcset and CSS,
                            // while also supporting literal spaces and punctuation.
                            preg_match_all('/[\s,),]/u', $name, $delimiters, PREG_OFFSET_CAPTURE);
                            foreach ($delimiters[0] as [$delimiter, $offset]) {
                                $images[$group][rawurldecode(substr($name, 0, $offset))] = true;
                            }
                        }
                    }
                }
            }
        };

        $scan(DB::table('posts')->select('intro', 'conclusion', 'deleted_at'), ['intro', 'conclusion']);
        $scan(DB::table('block_items')
            ->join('content_blocks', 'content_blocks.id', '=', 'block_items.block_id')
            ->leftJoin('posts', function ($join) {
                $join->on('posts.id', '=', 'content_blocks.blockable_id')
                    ->whereIn('content_blocks.blockable_type', $this->types(Post::class));
            })->select('block_items.value', 'posts.deleted_at'), ['value']);
        $scan(DB::table('faqs')->leftJoin('posts', 'posts.id', '=', 'faqs.post_id')
            ->select('question', 'answer', 'posts.deleted_at'), ['question', 'answer']);
        $scan(DB::table('pages')->select('content'), ['content']);
        $scan(DB::table('page_blocks')->select('data'), ['data']);
        $scan(DB::table('authors')->select('description', 'description_small'), ['description', 'description_small']);
        $scan(DB::table('categories')->select('description'), ['description']);
        $scan(DB::table('games')->select('description', 'summary'), ['description', 'summary']);

        return $images;
    }

    private function types(string $model): array
    {
        return array_unique([$model, (new $model)->getMorphClass()]);
    }
}
