@extends('layouts.app')

@section('title', 'Not found')
@section('description', 'Not found')

@section('content')
    <main>
        <section class="section section-head">
            <h1>404 PAGE NOT FOUND</h1>
            Oops! Seem like we can not find the resource you are looking for...
            <br>
            <a href="{{route("index")}}" class="button">Go Back Home</a>
        </section>
    </main>
@endsection