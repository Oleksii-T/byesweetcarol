<header class="topbar" aria-label="Top navigation">
    <nav class="chip-row" aria-label="Primary">
        <a class="chip{{ Route::currentRouteName() == 'index' ? ' active' : '' }}" href="{{ route('index') }}">BSCNews</a>
        @foreach ($headerCategories as $cat)
            <a class="chip{{ request()->segment(1) == $cat->slug ? ' active' : '' }}" href="{{ route('categories.show', $cat) }}">
                {{ $cat->name }}
            </a>
        @endforeach
        @foreach ($topTags->take(4) as $tag)
            <a class="chip chip--ghost{{ request()->get('tag') == $tag->slug ? ' active' : '' }}"
               href="{{ route('categories.show', ['category' => 'news', 'tag' => $tag->slug]) }}">
                {{ $tag->name }}
            </a>
        @endforeach
    </nav>
    <p class="topbar-note">Fresh Game News</p>
</header>
