@extends('layouts.app')

@section('content')
    <div class="page">
        <x-header />

        <h1>{{ $page->title }}</h1>

        <x-content-blocks :blocks="$blocks" type="1" />

        <x-footer />
    </div>
@endsection