@extends('layouts.app')

@section('title', $author->meta_title)
@section('description', $author->meta_description)
@section('meta-image', $author->meta_thumbnail()?->url)

@section('content')
    <main>
        Author Page
    </main>
@endsection
