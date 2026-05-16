<?php

namespace App\Http\Controllers;

use App\Enums\GameStatus;
use App\Models\Category;
use App\Models\Game;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function show(Request $request, Game $game)
    {
        $user = auth()->user();

        if ($game->status == GameStatus::DRAFT && ! $user) {
            abort(404);
        }

        $perPage = 4;
        $guides = $game->posts()
            ->publised()
            ->whereHas('category', fn ($q) => $q->whereIn('slug', ['guides', 'cheats']))
            ->latest('published_at')
            ->paginate($perPage);
        $topLists = $game->posts()
            ->publised()
            ->whereHas('category', fn ($q) => $q->whereIn('slug', ['lists', 'mods']))
            ->latest('published_at')
            ->paginate($perPage);

        if (! $request->ajax()) {
            $page = Page::get('{game}');
            $review = $game->posts()->publised()->whereRelation('category', 'slug', 'reviews')->latest('published_at')->first();
            $hasMoreGuides = $guides->hasMorePages();
            $hasMoreTopLists = $topLists->hasMorePages();
            $newsCategory = Category::where('slug', 'news')->first();
            $news = Post::query()
                ->where('category_id', $newsCategory->id)
                ->where('game_id', $game->id)
                ->publised()
                ->latest('published_at')
                ->paginate($perPage);
            $hasMoreNewsLink = $news->hasMorePages() ? route('categories.show', ['category' => $newsCategory, 'game' => $game->slug]) : null;

            return view('games.show', compact('page', 'game', 'review', 'guides', 'topLists', 'hasMoreGuides', 'hasMoreTopLists', 'news', 'hasMoreNewsLink'));
        }

        if ($request->type == 'guides') {
            return $this->jsonSuccess('', [
                'hasMore' => $guides->hasMorePages(),
                'html' => view('components.post-cards-small', ['posts' => $guides])->render(),
            ]);
        }

        if ($request->type == 'top_lists') {
            return $this->jsonSuccess('', [
                'hasMore' => $topLists->hasMorePages(),
                'html' => view('components.post-cards-small', ['posts' => $topLists])->render(),
            ]);
        }
    }
}
