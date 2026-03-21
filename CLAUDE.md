# Frontend Blade Guide for Project Clones

This guide is for Claude instances working on brand-skin clones of this project.
The backend (controllers, routes, models, services) is shared. Frontend views are rebuilt per-skin.

**Preserved files that must NOT be deleted in clones:**
- `resources/views/vendor/` (all)
- `resources/views/components/content-blocks.blade.php`

---

## Global View Composer Variables

These are injected into **every view** via `AppServiceProvider` — no need to pass them from controllers:

| Variable | Description |
|---|---|
| `$topTags` | Collection of `Tag` models, ordered by post count (top 20) |
| `$headerCategories` | Collection of `Category` models with `in_menu = true`, ordered by `order` |
| `$header` | `PageBlock` for the header (from `/` page, block name `header`) |
| `$footer` | `PageBlock` for the footer (from `/` page, block name `footer`) |
| `$currentUser` | Authenticated user or `null` |

---

## 1. Landing Page — `resources/views/index.blade.php`

**Controller:** `PageController::index()`

### Variables passed to view
| Variable | Type | Description |
|---|---|---|
| `$page` | `Page` | The `/` page record |
| `$authors` | `Collection<Author>` | All authors |
| `$latestNews` | `Collection<Post>` | Latest published posts from `news` category |
| `$latestIndustryNews` | `Collection<Post>` | Posts tagged `industry` |
| `$latestPcNews` | `Collection<Post>` | Posts tagged `pc` |
| `$latestXboxNews` | `Collection<Post>` | Posts tagged `xbox`/`xbox-one`/`xbox-series-x-s` |
| `$latestPsNews` | `Collection<Post>` | Posts tagged `playstation-4`/`playstation-5` |
| `$newsCategory` | `Category` | The `news` category model |

### Page content blocks (CMS text/rich content)

The `$page->show('block-name:field-name')` method reads editable CMS content from `PageBlock` records.
Use `{{ }}` for plain text, `{!! !!}` for HTML content (rich text fields).

```blade
{{-- Plain text --}}
<h1>{{ $page->show('hero-section:title') }}</h1>

{{-- Rich text / HTML --}}
{!! $page->show('about-us-section:content') !!}
```

### Rendering posts (thumbnail + link + date)

```blade
{{-- Thumbnail: $post->thumbnail() returns an Attachment object or null --}}
<img
  src="{{ $post->thumbnail() ? $post->thumbnail()->url : asset('images/empty.png') }}"
  alt="{{ $post->title }}"
>

{{-- Post URL --}}
<a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>

{{-- Relative date (e.g. "3 hours ago") --}}
<span>{{ $post->published_at->diffForHumans() }}</span>

{{-- Formatted date --}}
<span>{{ $post->published_at->format('M d') }}</span>

{{-- Truncated title --}}
{{ Str::limit($post->title, 55, '...') }}
```

### Rendering authors (avatar + link)

```blade
@foreach ($authors as $author)
    <a href="{{ route('authors.show', $author) }}">
        <img
          src="{{ $author->avatar()->url }}"
          alt="{{ $author->avatar()->alt }}"
          title="{{ $author->avatar()->title }}"
        >
        <span>{{ $author->name }}</span>
    </a>
    <p>{{ $author->title }}</p>  {{-- author job title / role --}}
@endforeach
```

> **Note:** On the landing page `$authors` is guaranteed to have avatars. On other pages
> always guard against null: `$author->avatar()` can return `null` if no avatar is uploaded.

### Link to a category page

```blade
<a href="{{ route('categories.show', $newsCategory) }}">See all news</a>
```

---

## 2. Category Page — `resources/views/categories/show.blade.php`

**Controller:** `CategoryController::show()`

### Variables passed to view
| Variable | Type | Description |
|---|---|---|
| `$category` | `Category` | Current category |
| `$posts` | `LengthAwarePaginator<Post>` | Paginated posts |
| `$game` | `Game\|null` | Set when `?game=slug` query param is present |
| `$currentPage` | `int` | Current page number |
| `$tagSlug` | `string\|null` | Active tag filter slug |
| `$hasMore` | `bool` | Whether there are more pages |

### SEO meta sections

```blade
@section('title', ($game ? "Latest News on $game->name" : $category->meta_title) . ($currentPage != 1 ? " - Page $currentPage" : ''))
@section('description', $category->meta_description)
@section('meta-image', $category->meta_thumbnail()?->url)

{{-- Canonical for paginated pages --}}
@if ($currentPage != 1)
    @section('meta-canonical', $category->paginationLink(1, ['game']))
@endif
```

### Rendering tags (global `$topTags`)

`$topTags` is available on every view. Link a tag to a filtered category URL:

```blade
@if ($topTags->isNotEmpty())
    @foreach ($topTags as $tag)
        <a
          class="{{ $tagSlug == $tag->slug ? 'selected' : '' }}"
          href="{{ route('categories.show', ['news', $tag->slug]) }}"
        >
            {{ $tag->name }}
        </a>
    @endforeach
@endif
```

To link back to "all" (clear tag filter), use `$category->paginationLink()`:

```blade
<a href="{{ $category->paginationLink(1, ['game', 'search']) }}">All</a>
```

### Rendering the posts list with pagination

Use the shared component — it handles the posts grid and pagination links:

```blade
@include('components.category-posts-with-pages', [
    'posts'              => $posts,
    'model'              => $category,
    'includeQueryParams' => ['game', 'tagSlug', 'search'],
])
```

The component renders each post with its thumbnail, `updated_at->diffForHumans()`, title link,
and `$post->intro_cropped` (155-char stripped intro).
Falls back to `asset('images/empty.png')` when no thumbnail.

---

## 3. Author Page — `resources/views/authors/show.blade.php`

**Controller:** `AuthorController::show()`

### Variables passed to view
| Variable | Type | Description |
|---|---|---|
| `$author` | `Author` | The author model |
| `$posts` | `LengthAwarePaginator<Post>` | Author's published posts (paginated, 5 per page) |
| `$blocks` | `Collection<ContentBlock>` | Author's content blocks, sorted by `order` |

### SEO meta

```blade
@section('title', $author->meta_title)
@section('description', $author->meta_description)
@section('meta-image', $author->meta_thumbnail()?->url)
```

### Rendering the author avatar (with null guard)

```blade
@php $avatar = $author->avatar(); @endphp

<img
  src="{{ $avatar ? $avatar->url : asset('images/empty.png') }}"
  alt="{{ $avatar?->alt ?: $author->name }}"
  title="{{ $avatar?->title ?: $author->name }}"
>
<h1>{{ $author->name }}</h1>
@if ($author->title)
    <span>{{ $author->title }}</span>
@endif
```

### Rendering author content blocks

```blade
<x-content-blocks :blocks="$blocks" type="1" />
```

The `type="1"` prop wraps H2 headings in an extra `<span>` (used for styled section markers).
Omit `type` for standard article rendering.

---

## 4. Post Page — `resources/views/posts/show.blade.php`

**Controller:** `PostController::show()`

### Variables passed to view
| Variable | Type | Description |
|---|---|---|
| `$post` | `Post` | The post model |
| `$author` | `Author\|null` | Post author |
| `$category` | `Category` | Post category |
| `$game` | `Game\|null` | Associated game (published only) |
| `$blockGroups` | `array` | Array of ContentBlock collections (grouped for layout) |
| `$otherPostsFromCategory` | `Collection<Post>` | Up to 4 other posts from same category |
| `$relatedPosts` | `Collection<Post>` | Manually curated related posts |
| `$newsTemplate` | `bool` | `true` when category is `news` and no game attached |

### SEO meta

```blade
@section('title', $post->meta_title)
@section('description', $post->meta_description)
@section('meta-image', $post->thumbnail()?->url)
@section('meta-type', 'article')
@section('meta')
    <meta property="article:published_time" content="{{ $post->published_at?->toIso8601ZuluString() }}"/>
    <meta property="article:modified_time" content="{{ $post->updated_at->toIso8601ZuluString() }}"/>
    <meta property="article:section"        content="{{ $category->name }}"/>
    <meta property="article:author"         content="{{ $author->name }}"/>
@endsection
```

### Draft post warning (admin-only visibility)

```blade
@if ($post->status == \App\Enums\PostStatus::DRAFT)
    <p>The post is {{ $post->status->readable() }}. Only admin can see it.</p>
@endif
```

### View tracking (hit counter)

Place this element on the page — JS picks it up and fires a view event:

```blade
<span data-sendview="{{ route('posts.view', $post) }}"></span>
```

### Rendering post thumbnail

```blade
@php $thumbnail = $post->thumbnail(); @endphp

@if ($thumbnail)
    <img src="{{ $thumbnail->url }}" alt="{{ $thumbnail->alt }}" title="{{ $thumbnail->title }}" />
@endif
```

### Rendering author block inside post

```blade
@php $authorAvatar = $author?->avatar(); @endphp

@if ($author)
    <img
      src="{{ $authorAvatar ? $authorAvatar->url : asset('images/empty.png') }}"
      alt="{{ $authorAvatar?->alt ?: $author->name }}"
      title="{{ $authorAvatar?->title ?: $author->name }}"
    >
    <span>By {{ $author->name }}</span>
    @if ($author->title)
        <span>{{ $author->title }}</span>
    @endif
@endif
```

### Post date display

```blade
{{-- Human-relative --}}
<span>Last updated: {{ $post->updated_at->diffForHumans() }}</span>

{{-- Formatted --}}
<span>{{ $post->updated_at->format('M d, Y') }}</span>
<span>{{ $post->published_at->format('M d, Y') }}</span>
```

### Rendering post content blocks

Posts support grouped blocks for multi-column/multi-section layouts.
`$blockGroups` is an array of block collections (see `Post::getGroupedBlocks()`).

```blade
@foreach ($blockGroups as $blocks)
    <x-content-blocks :blocks="$blocks" />
@endforeach
```

### Sidebar: other posts from same category

```blade
@if ($otherPostsFromCategory->isNotEmpty())
    @foreach ($otherPostsFromCategory as $otherPost)
        @php $otherThumbnail = $otherPost->thumbnail(); @endphp
        <a href="{{ route('posts.show', $otherPost) }}">
            <img
              src="{{ $otherThumbnail ? $otherThumbnail->url : asset('images/empty.png') }}"
              alt="{{ $otherPost->title }}"
            >
            <p>{{ $otherPost->title }}</p>
            <span>{{ $otherPost->published_at->format('M d, Y') }}</span>
        </a>
    @endforeach
@endif
```

---

## 5. Template Content Pages — `resources/views/page-with-blocks.blade.php`

Used for static pages: Privacy Policy, Terms of Use, About Us, Cookie Policy.

**Controllers:** `PageController::privacy()`, `terms()`, `aboutUs()`, `cookiePolicy()`

### Variables passed to view
| Variable | Type | Description |
|---|---|---|
| `$page` | `Page` | The page model |
| `$blocks` | `Collection<ContentBlock>` | Content blocks sorted by `order` |

### Rendering

```blade
<h1>{{ $page->title }}</h1>
<x-content-blocks :blocks="$blocks" type="1" />
```

---

## `<x-content-blocks>` Component Reference

**File:** `resources/views/components/content-blocks.blade.php` (preserved in clones)

Iterates over a collection of `ContentBlock` models, each containing ordered `BlockItem` records.

```blade
{{-- Standard article rendering --}}
<x-content-blocks :blocks="$blocks" />

{{-- With type="1": wraps H2 text in <span> for decorative heading styles --}}
<x-content-blocks :blocks="$blocks" type="1" />
```

**Supported block item types** (`App\Enums\BlockItemType`):
- `TITLE_H2`, `TITLE_H3`, `TITLE_H4` — headings via `$item->value_simple`
- `TEXT` — raw HTML via `{!! $item->value_simple !!}`
- `IMAGE` — full-width image via `$item->file()->url/alt/title`
- `IMAGE_SMALL` — smaller image
- `IMAGE_TITLE` — image + title text combination
- `IMAGE_TEXT` — image paired with rich text
- `youtube` — embedded video HTML via `{!! $item->value_simple !!}`
- `CARDS` — card grid, each card has `title`, `text`, optional `image` (url/alt/title)
- `IMAGE_GALLERY` — lightbox gallery (fancybox); 1-2 images use two-up layout, 3+ use a slider

Each block renders inside a `<section id="{{ $block->ident }}">`.

---

## Attachment Object Properties

Returned by `$post->thumbnail()`, `$author->avatar()`, `$author->meta_thumbnail()`,
`$category->meta_thumbnail()`, `$item->file()`.

| Property | Description |
|---|---|
| `->url` | Full public URL |
| `->alt` | Alt text |
| `->title` | Title attribute |

Always guard with null check: `$post->thumbnail()` returns `null` if none is uploaded.

---

## Route Names Reference

| Route | Usage |
|---|---|
| `route('posts.show', $post)` | Post detail page (uses `$post->slug` as key) |
| `route('posts.view', $post)` | View tracking endpoint (AJAX) |
| `route('authors.show', $author)` | Author profile page |
| `route('categories.show', $category)` | Category listing |
| `route('categories.show', ['news', $tag->slug])` | Category filtered by tag |
| `route('contact-us')` | Contact form page |

---

## Custom Blade Directives

```blade
{{-- Render block only for admin users --}}
@admin
    <p>Admin-only content</p>
@endadmin

{{-- Inline SVG from public path --}}
@svg('images/icon.svg', 'css-class')
```
