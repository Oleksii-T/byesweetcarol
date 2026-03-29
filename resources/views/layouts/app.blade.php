<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <title>@yield('title', $page?->meta_title)</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta property="og:image" content="@yield('meta-image', asset('images/og-image.png'))">
    <meta property="og:title" content="@yield('title', $page?->meta_title)">
    <meta property="og:description" content="@yield('description', $page?->meta_description)">
    <meta name="description" content="@yield('description', $page?->meta_description)">
    <meta name="robots" content="@yield('meta-robots', 'index, follow, max-image-preview:large')">
    <meta property="og:url" content="https://localhost.com{{request()->getRequestUri()}}">
    <meta property="og:site_name" content="RCGameClub">
    <meta property="og:type" content="@yield('meta-type', 'website')">
    <meta property="og:locale" content="en_US">
    <meta property="og:image:width" content="1500">
    <meta property="og:image:height" content="844">
    <meta name="google-site-verification" content="FzFoQ7NMeiR9L5ivlCfFfi-GjLrAaFKrr7dCu3QDIEw" />
    @yield('meta')
    <link rel="canonical" href="@yield('meta-canonical', 'https://localhost.com' . request()->getRequestUri())">
    <link rel="shortcut icon" href="{{asset('images/icons/favicon.png')}}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{asset('images/icons/favicon.png')}}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{asset('images/icons/favicon.png')}}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{asset('images/icons/favicon.png')}}">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.0/themes/base/jquery-ui.css">
    
    <meta name="google-site-verification" content="YmuZrawmUJNjV2uLyuI42jFFP0l9FtenQXB2XB1B4K0" />
    <meta name="theme-color" content="rgb(4, 3, 15)" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="rgb(4, 3, 15)" media="(prefers-color-scheme: dark)">

    <link rel="stylesheet" href="{{asset('css/main.css')}}">
    @yield('styles')
</head>

<body>
    <span id="page-top"></span>
    @admin
        @php
            $editUrl = match (Route::currentRouteName()) {
                'games.show' => route('admin.games.edit', $game),
                'posts.show' => route('admin.posts.edit', $post ?? 1),
                'categories.show' => route('admin.categories.edit', $category),
                'authors.show' => route('admin.authors.edit', $author),
                default => null
            };
        @endphp
        <div class="admin-header">
            <div class="admin-header-item">
                <a href="{{route('admin.index')}}">
                    {{config('app.name')}} Admin
                </a>
            </div>
            @if ($editUrl)
                <div class="admin-header-item">
                    <a href="{{$editUrl}}">Edit</a>
                </div>
            @endif
        </div>
    @endadmin

    <x-header />

    @yield('content')

    <x-footer />

    @yield('popups')
</body>

<script src="{{asset('js/jquery.min.js')}}"></script>
{{-- <script src="{{asset('js/jquery-ui-3.min.js')}}"></script> --}}
<script src="https://code.jquery.com/ui/1.14.0/jquery-ui.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script src="{{asset('js/lazysizes.min.js')}}"></script>
<script src="{{asset('js/slick.min.js')}}"></script>
<script src="{{asset('js/custom.js')}}"></script>

<script>
    window.Laravel = {!!$LaravelDataForJS!!};
</script>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{env('GA_MEASUREMENT_ID')}}"></script>
@if (app('env') != 'local')
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', "{{env('GA_MEASUREMENT_ID')}}");
    </script>
@endif

@yield('scripts')

</html>
