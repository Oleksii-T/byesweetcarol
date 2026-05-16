@extends('layouts.app')

@section('title', 'UNAUTHORIZED')
@section('description', 'UNAUTHORIZED')

@section('content')
    <main>
        <section class="section section-head">
            <h1>403 UNAUTHORIZED</h1>
            Oops! You have no access for this resource...
            <br>
            <a href="{{route("index")}}" class="button">Go Back Home</a>
        </section>
    </main>
@endsection