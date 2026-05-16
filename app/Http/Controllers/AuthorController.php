<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Page;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function show(Request $request, Author $author)
    {
        $perPage = 5;
        $posts = $author->posts()->publised()->latest('published_at')->paginate($perPage);
        $blocks = $author->blocks->sortBy('order');

        if (! $request->ajax()) {
            $page = Page::get('{author}');

            return view('authors.show', compact('author', 'posts', 'page', 'blocks'));
        }

        return $this->jsonSuccess('', [
            'hasMore' => $posts->hasMorePages(),
            'html' => view('components.post-cards', compact('posts'))->render(),
        ]);
    }
}
