@extends('layouts.app')

@section('content')
    <div class="page">
      <section class="hero" aria-label="Introduction">
        <h1 class="hero-title">
          <span class="primary">Fresh game news for players across PC, PlayStation, Xbox, and Switch.</span>
          <span class="muted">
            Daily coverage on launches, patches, studio moves, showcases, and hardware updates.
            Top stories below.
          </span>
        </h1>
      </section>

      <main class="content-grid">
        <section class="work-columns" aria-label="Top stories">
          {{-- Column 1: top tag --}}
          <article class="category-column">
            <h2 class="section-label">{{ $col1Tag?->name ?? 'Latest News' }}</h2>
            <div class="project-list">
              @foreach ($col1Posts as $post)
                <a class="project-card" href="{{ route('posts.show', $post) }}" aria-label="{{ $post->title }}">
                  <div class="project-media-wrap">
                    <img
                      class="project-media"
                      src="{{ $post->thumbnail() ? $post->thumbnail()->url : asset('images/empty.png') }}"
                      alt="{{ $post->title }}"
                      loading="lazy"
                    />
                  </div>
                  <div class="project-body">
                    <h3 class="project-title">{{ Str::limit($post->title, 75, '...') }}</h3>
                    <p class="project-meta">{{ $post->published_at->diffForHumans() }}</p>
                  </div>
                </a>
              @endforeach
            </div>
          </article>

          {{-- Column 2: second top tag --}}
          <article class="category-column">
            <h2 class="section-label">{{ $col2Tag?->name ?? 'More News' }}</h2>
            <div class="project-list">
              @foreach ($col2Posts as $post)
                <a class="project-card" href="{{ route('posts.show', $post) }}" aria-label="{{ $post->title }}">
                  <div class="project-media-wrap">
                    <img
                      class="project-media"
                      src="{{ $post->thumbnail() ? $post->thumbnail()->url : asset('images/empty.png') }}"
                      alt="{{ $post->title }}"
                      loading="lazy"
                    />
                  </div>
                  <div class="project-body">
                    <h3 class="project-title">{{ Str::limit($post->title, 75, '...') }}</h3>
                    <p class="project-meta">{{ $post->published_at->diffForHumans() }}</p>
                  </div>
                </a>
              @endforeach
            </div>
          </article>

          {{-- Column 3: remaining latest news --}}
          <article class="category-column">
            <h2 class="section-label">Latest News</h2>
            <div class="project-list">
              @foreach ($col3Posts as $post)
                <a class="project-card" href="{{ route('posts.show', $post) }}" aria-label="{{ $post->title }}">
                  <div class="project-media-wrap">
                    <img
                      class="project-media"
                      src="{{ $post->thumbnail() ? $post->thumbnail()->url : asset('images/empty.png') }}"
                      alt="{{ $post->title }}"
                      loading="lazy"
                    />
                  </div>
                  <div class="project-body">
                    <h3 class="project-title">{{ Str::limit($post->title, 75, '...') }}</h3>
                    <p class="project-meta">{{ $post->published_at->diffForHumans() }}</p>
                  </div>
                </a>
              @endforeach
            </div>
          </article>
        </section>

        <aside class="recognition" aria-label="Trending topics">
          <h2 class="section-label">Trending Topics</h2>
          <div class="award-list">
            @foreach ($topTags->slice(4)->take(12) as $tag)
              <a class="award-badge" href="{{ route('categories.show', ['category' => $newsCategory->slug, 'tag' => $tag->slug]) }}">
                {{ $tag->name }}
              </a>
            @endforeach
          </div>
        </aside>
      </main>

      <div class="all-news-link-wrap">
        <a class="all-news-link" href="{{ route('categories.show', $newsCategory) }}">All Game News &rsaquo;</a>
      </div>
    </div>
@endsection
