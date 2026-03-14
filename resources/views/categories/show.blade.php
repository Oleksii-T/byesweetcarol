@extends('layouts.app')

@section('title', ($game ? "Latest News on $game->name" : $category->meta_title) . ($currentPage != 1 ? " - Page $currentPage" : ''))
@section('description', ($game ? "Read the most authoritative and fresh news about the $game->name!" : $category->meta_description) . ($currentPage != 1 ? " | Page $currentPage" : ''))
@section('meta-image', $category->meta_thumbnail()?->url)
@if ($currentPage != 1)
    @section('meta-canonical', $category->paginationLink(1, ['game']))
@endif

@section('content')
    <main>
        Category Page
    </main>
@endsection
