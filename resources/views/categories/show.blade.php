@extends('layouts.app')

@section('title', ($game ? "Latest News on $game->name" : (($tag ? ($tag->name.' ') : '') . $category->meta_title)) . ($currentPage != 1 ? " - Page $currentPage" : ''))
@section('description', ($game ? "Read the most authoritative and fresh news about the $game->name!" : $category->meta_description) . ($currentPage != 1 ? " | Page $currentPage" : ''))
@section('meta-image', $category->meta_thumbnail()?->url)
@if ($currentPage != 1)
    @section('meta-canonical', $category->paginationLink(1, ['game']))
@endif

@section('content')
    <div class="page">
        <div class="cat-header">
            <h1 class="cat-title">
                {{ $game ? "Latest News on $game->name" : $category->name }}
                @if ($tag)
                    > {{ $tag->name }}
                @endif
            </h1>
        </div>

        {{-- Tag filters --}}
        @if ($topTags->isNotEmpty())
            <nav class="tag-filter-row" aria-label="Filter by topic">
                <a class="tag-filter{{ !$tagSlug ? ' active' : '' }}"
                   href="{{ route('categories.show', $category) }}">All</a>
                @foreach ($topTags->take(15) as $tag)
                    <a class="tag-filter{{ $tagSlug == $tag->slug ? ' active' : '' }}"
                       href="{{ route('categories.show.tag', ['category' => $category->slug, 'tagSlug' => $tag->slug]) }}">
                        {{ $tag->name }}
                    </a>
                @endforeach
            </nav>
        @endif

        {{-- Posts grid --}}
        <div class="posts-grid pagination-content" id="posts-container">
            @include('components.post-cards-with-pages', [
                'posts' => $posts,
                'model' => $category,
                'includeQueryParams' => ['game', 'search'],
            ])
        </div>
    </div>
@endsection
