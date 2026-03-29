<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Game;
use App\Models\Page;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(Request $request, Category $category, $page = 1)
    {
        $perPage = 10;
        $page = abs((int) filter_var($page, FILTER_SANITIZE_NUMBER_INT));
        $posts = $category->posts() 
            ->publised()
            ->latest('published_at')
            ->when($request->search, fn ($q, $v) => $q->where('title', 'like', "%$v%"))
            ->when($request->tag, fn ($q, $v) => $q->whereRelation('tags', 'slug', $v))
            ->when($request->game, fn ($q) => $q->where('game_id', Game::where('slug', request()->game)->value('id')))
            ->paginate($perPage, ['*'], 'page', $page);
        $hasMore = $posts->hasMorePages();
        $game = null;
        if ($request->game) {
            $game = Game::where('slug', $request->game)->first();
        }

        if (! $request->ajax()) {
            $currentPage = $page;
            $page = Page::get('{category}');

            return view('categories.show', compact('category', 'posts', 'game', 'page', 'hasMore', 'currentPage'));
        }

        return $this->jsonSuccess('', [
            'hasMore' => $hasMore,
            'html' => view('components.post-cards-with-pages', [
                'posts' => $posts,
                'model' => $category,
                'includeQueryParams' => ['game'],
            ])->render(),
        ]);
    }
}
