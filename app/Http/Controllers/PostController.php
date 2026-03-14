<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function show(Request $request, Post $post)
    {
        $user = auth()->user();

        if ($post->status == PostStatus::DRAFT && ! $user) {
            abort(404);
        }

        $page = Page::get('{post}');
        $author = $post->author;
        $game = $post->game()->where('status', \App\Enums\GameStatus::PUBLISHED)->first();
        $category = $post->category;
        $relatedPosts = $post->getRelatedPosts();
        $blockGroups = $post->getGroupedBlocks();
        $newsTemplate = ! $game && $category->slug == 'news';
        $otherPostsFromCategory = Post::query()
            ->publised()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->latest()
            ->limit(4)
            ->latest('published_at')
            ->get();
        $sameGamePosts = Post::query()
            ->publised()
            ->whereNull('parent_id')
            ->where('game_id', $game?->id)
            ->whereRelation('category', 'slug', '!=', 'news')
            ->with('childs')
            ->latest('published_at')
            ->get();

        return view('posts.show', compact('newsTemplate', 'post', 'page', 'author', 'game', 'category', 'relatedPosts', 'sameGamePosts', 'blockGroups', 'otherPostsFromCategory'));
    }

    public function view(Request $request, Post $post)
    {
        $post->saveView();
    }
}
