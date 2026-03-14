@extends('layouts.app')

@section('title', 'Server Error')
@section('description', 'Server Error')

@section('content')
    <main>
        <section class="section section-head">
            <h1>500 SERVER ERROR</h1>
            Oops! Seem like something goes wrong...
            <br>
            <a href="{{route("index")}}" class="button">Go Back Home</a>
        </section>
    </main>
@endsection