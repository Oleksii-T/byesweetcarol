@extends('layouts.app')

@section('title', $author->meta_title)
@section('description', $author->meta_description)
@section('meta-image', $author->meta_thumbnail()?->url)

@section('content')
    <div class="page author-page">
        {{-- Author bio --}}
        @php $avatar = $author->avatar(); @endphp
        <div class="author-bio">
            <div class="author-bio__avatar-wrap">
                <img
                    class="author-bio__avatar"
                    src="{{ $avatar ? $avatar->url : asset('images/empty.png') }}"
                    alt="{{ $avatar?->alt ?: $author->name }}"
                    title="{{ $avatar?->title ?: $author->name }}"
                />
            </div>
            <div class="author-bio__info">
                <h1 class="author-bio__name">{{ $author->name }}</h1>
                @if ($author->title)
                    <p class="author-bio__role">{{ $author->title }}</p>
                @endif
                @if ($author->description_small)
                    <p class="author-bio__desc">{{ $author->description_small }}</p>
                @endif
            </div>
        </div>

        {{-- Content blocks (bio / articles) --}}
        @if ($blocks->isNotEmpty())
            <div class="author-blocks">
                <x-content-blocks :blocks="$blocks" type="1" />
            </div>
        @endif

        {{-- Posts --}}
        @if ($posts->isNotEmpty())
            <section class="author-posts">
                <h2 class="section-label">Articles by {{ $author->name }}</h2>
                <div class="posts-grid">
                    <x-post-cards :posts="$posts" />
                </div>
            </section>
        @endif
    </div>
@endsection
