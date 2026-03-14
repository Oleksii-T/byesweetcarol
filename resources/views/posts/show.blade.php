@extends('layouts.app')

@section('title', $post->meta_title)
@section('description', $post->meta_description)
@section('meta-image', $post->thumbnail()?->url)
@section('meta-type', 'article')
@section('meta')
    <meta property="article:published_time" content="{{$post->published_at?->toIso8601ZuluString()}}"/>
    <meta property="article:modified_time" content="{{$post->updated_at->toIso8601ZuluString()}}"/>
    <meta property="article:section" content="{{$category->name}}"/>
    <meta property="article:author" content="{{$author->name}}"/>
@endsection

@section('content')
    <main>
        Post Page
    </main>
@endsection
