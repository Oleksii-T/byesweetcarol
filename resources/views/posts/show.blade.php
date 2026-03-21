@extends('layouts.app')

@section('title', $post->meta_title)
@section('description', $post->meta_description)
@section('meta-image', $post->thumbnail()?->url)
@section('meta-type', 'article')
@section('meta')
    <meta property="article:published_time" content="{{ $post->published_at?->toIso8601ZuluString() }}"/>
    <meta property="article:modified_time" content="{{ $post->updated_at->toIso8601ZuluString() }}"/>
    <meta property="article:section" content="{{ $category->name }}"/>
    @if ($author)
        <meta property="article:author" content="{{ $author->name }}"/>
    @endif
@endsection

@section('content')
    <span data-sendview="{{ route('posts.view', $post) }}"></span>

    <div class="page post-page">
        <div class="post-layout">
            <article class="post-article">
                {{-- Draft warning --}}
                @if ($post->status == \App\Enums\PostStatus::DRAFT)
                    @admin
                        <div class="post-draft-notice">This post is a draft. Only admins can see it.</div>
                    @endadmin
                @endif

                {{-- Thumbnail --}}
                @php $thumbnail = $post->thumbnail(); @endphp
                @if ($thumbnail)
                    <div class="post-hero">
                        <img src="{{ $thumbnail->url }}" alt="{{ $thumbnail->alt }}" title="{{ $thumbnail->title }}" class="post-hero__img" />
                    </div>
                @endif

                {{-- Category breadcrumb --}}
                <div class="post-breadcrumb">
                    <a href="{{ route('categories.show', $category) }}">{{ $category->name }}</a>
                </div>

                {{-- Title --}}
                <h1 class="post-title">{{ $post->title }}</h1>
                @if ($post->sub_title)
                    <p class="post-subtitle">{{ $post->sub_title }}</p>
                @endif

                {{-- Author + date --}}
                <div class="post-byline">
                    @php $authorAvatar = $author?->avatar(); @endphp
                    @if ($author)
                        <a class="post-byline__author" href="{{ route('authors.show', $author) }}">
                            <img
                                src="{{ $authorAvatar ? $authorAvatar->url : asset('images/empty.png') }}"
                                alt="{{ $authorAvatar?->alt ?: $author->name }}"
                                class="post-byline__avatar"
                            />
                            <span>{{ $author->name }}</span>
                        </a>
                    @endif
                    <span class="post-byline__date">{{ $post->published_at?->format('M d, Y') }}</span>
                </div>

                {{-- Content blocks --}}
                <div class="post-content">
                    @foreach ($blockGroups as $blocks)
                        <x-content-blocks :blocks="$blocks" />
                    @endforeach
                </div>

                {{-- Tags --}}
                @if ($post->tags->isNotEmpty())
                    <div class="post-tags">
                        @foreach ($post->tags as $tag)
                            <a class="post-tag" href="{{ route('categories.show', ['category' => $category->slug, 'tag' => $tag->slug]) }}">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </article>

            {{-- Sidebar --}}
            <aside class="post-sidebar">
                @if ($otherPostsFromCategory->isNotEmpty())
                    <h3 class="section-label">More from {{ $category->name }}</h3>
                    <div class="sidebar-posts">
                        @foreach ($otherPostsFromCategory as $otherPost)
                            @php $otherThumb = $otherPost->thumbnail(); @endphp
                            <a class="sidebar-post" href="{{ route('posts.show', $otherPost) }}">
                                <div class="sidebar-post__thumb">
                                    <img
                                        src="{{ $otherThumb ? $otherThumb->url : asset('images/empty.png') }}"
                                        alt="{{ $otherPost->title }}"
                                        loading="lazy"
                                    />
                                </div>
                                <div class="sidebar-post__body">
                                    <p class="sidebar-post__title">{{ Str::limit($otherPost->title, 70, '...') }}</p>
                                    <span class="sidebar-post__date">{{ $otherPost->published_at->format('M d, Y') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </aside>
        </div>
    </div>
@endsection
