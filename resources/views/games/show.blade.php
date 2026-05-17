@extends('layouts.app')

@section('title', $game->meta_title ?: $game->name)
@section('description', $game->meta_description ?: Str::limit(strip_tags($game->description), 155, '...'))
@section('meta-image', $game->thumbnail()?->url)
@section('meta-type', 'website')

@section('content')
    <div class="page post-page game-page">
        <div class="post-layout">
            <article class="post-article">
                @if ($game->status == \App\Enums\GameStatus::DRAFT)
                    @admin
                        <div class="post-draft-notice">This game is a draft. Only admins can see it.</div>
                    @endadmin
                @endif

                @php $thumbnail = $game->thumbnail(); @endphp
                @if ($thumbnail)
                    <div class="post-hero">
                        <img src="{{ $thumbnail->url }}" alt="{{ $thumbnail->alt ?: $game->name }}" title="{{ $thumbnail->title }}" class="post-hero__img" />
                    </div>
                @endif

                <div class="post-breadcrumb">
                    <a href="{{ route('index') }}">Games</a>
                </div>

                <h1 class="post-title">{{ $game->name }}</h1>

                @if ($game->description)
                    <p class="post-subtitle">{{ $game->description }}</p>
                @endif

                <div class="game-facts">
                    @if ($game->metacritic)
                        <div class="game-fact">
                            <span class="game-fact__label">Metacritic</span>
                            <span class="game-fact__value">{{ $game->metacritic }}</span>
                        </div>
                    @endif
                    @if ($game->release_date)
                        <div class="game-fact">
                            <span class="game-fact__label">Release date</span>
                            <span class="game-fact__value">{{ $game->release_date->format('M d, Y') }}</span>
                        </div>
                    @endif
                    @if ($game->developer)
                        <div class="game-fact">
                            <span class="game-fact__label">Developer</span>
                            <span class="game-fact__value">{{ $game->developer }}</span>
                        </div>
                    @endif
                    @if ($game->publisher)
                        <div class="game-fact">
                            <span class="game-fact__label">Publisher</span>
                            <span class="game-fact__value">{{ $game->publisher }}</span>
                        </div>
                    @endif
                    @if ($game->ganres)
                        <div class="game-fact">
                            <span class="game-fact__label">Genre</span>
                            <span class="game-fact__value">{{ $game->ganres }}</span>
                        </div>
                    @endif
                    @if ($game->esbr)
                        <div class="game-fact">
                            <span class="game-fact__label">ESRB</span>
                            <span class="game-fact__value">{{ $game->esbr }}</span>
                        </div>
                    @endif
                </div>

                @if ($game->steam || $game->playstation_store)
                    <div class="game-store-links">
                        @if ($game->steam)
                            <a class="game-store-link" href="{{ $game->steam }}" target="_blank" rel="nofollow noopener">Steam</a>
                        @endif
                        @if ($game->playstation_store)
                            <a class="game-store-link" href="{{ $game->playstation_store }}" target="_blank" rel="nofollow noopener">PlayStation Store</a>
                        @endif
                    </div>
                @endif

                @if ($game->summary)
                    <div class="post-content game-summary">
                        {!! $game->summary !!}
                    </div>
                @endif

                @if ($screenshots->isNotEmpty())
                    <section class="game-section">
                        <h2 class="section-label">Screenshots</h2>
                        <div class="game-screenshots">
                            @foreach ($screenshots as $screenshot)
                                <img src="{{ $screenshot->url }}" alt="{{ $screenshot->alt ?: $game->name }}" loading="lazy" />
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($review)
                    <section class="game-section">
                        <h2 class="section-label">Review</h2>
                        <x-post-cards :posts="collect([$review])" />
                    </section>
                @endif

                @if ($guides->isNotEmpty())
                    <section class="game-section">
                        <h2 class="section-label">Guides</h2>
                        <x-post-cards :posts="$guides" />
                    </section>
                @endif

                @if ($topLists->isNotEmpty())
                    <section class="game-section">
                        <h2 class="section-label">Lists and mods</h2>
                        <x-post-cards :posts="$topLists" />
                    </section>
                @endif
            </article>

            <aside class="post-sidebar">
                @if ($news->isNotEmpty())
                    <h3 class="section-label">Latest news</h3>
                    <div class="sidebar-posts">
                        @foreach ($news as $post)
                            @php $postThumb = $post->thumbnail(); @endphp
                            <a class="sidebar-post" href="{{ route('posts.show', $post) }}">
                                <div class="sidebar-post__thumb">
                                    <img
                                        src="{{ $postThumb ? $postThumb->url : asset('images/empty.png') }}"
                                        alt="{{ $post->title }}"
                                        loading="lazy"
                                    />
                                </div>
                                <div class="sidebar-post__body">
                                    <p class="sidebar-post__title">{{ Str::limit($post->title, 70, '...') }}</p>
                                    <span class="sidebar-post__date">{{ $post->published_at->format('M d, Y') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    @if ($hasMoreNewsLink)
                        <a class="game-more-link" href="{{ $hasMoreNewsLink }}">More news</a>
                    @endif
                @endif
            </aside>
        </div>
    </div>
@endsection
