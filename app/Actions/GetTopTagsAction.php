<?php

namespace App\Actions;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use App\Models\Tag;
use App\Enums\PostStatus;

class GetTopTagsAction
{
    public static function run(bool $clear = false): Collection
    {
        $cKey = 'topTags';
        $cashTime = 60*60;

        if ($clear) {
            Cache::forget($cKey);
        }

        return Cache::remember(
            $cKey, 
            $cashTime, 
            fn () => Tag::withCount(['posts' => fn ($q) => $q->where('status', PostStatus::PUBLISHED)])->orderByDesc('posts_count')->limit(20)->get()
        );
    }
}
