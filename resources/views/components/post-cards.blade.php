@foreach ($posts as $post)
    <a class="post-list-card" href="{{ route('posts.show', $post) }}">
        <div class="post-list-card__thumb">
            <img
                src="{{ $post->thumbnail() ? $post->thumbnail()->url : asset('images/empty.png') }}"
                alt="{{ $post->title }}"
                loading="lazy"
            />
        </div>
        <div class="post-list-card__body">
            <h2 class="post-list-card__title">{{ Str::limit($post->title, 90, '...') }}</h2>
            <p class="post-list-card__meta">{{ $post->published_at->diffForHumans() }}</p>
        </div>
    </a>
@endforeach
