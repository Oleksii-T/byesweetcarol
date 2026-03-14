@extends('layouts.admin.app')

@section('title', 'Edit author blocks')

@push('styles')
    <style>
        .my-post-block {
            border: 3px solid #6c757d;
        }
        .card {
            margin-bottom: 2rem;
        }
        .my-block-ident {
            max-width: 200px;
            display:inline-block;
        }
        .my-block-title {
            font-size: 1.5em;
        }
        hr {
            width: 100%;
            color: red;
            border-top: 2px dashed #04030F;
            margin: 0 7.5px 1rem 7.5px;
        }
    </style>
@endpush

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <div class="float-left">
                    <h1 class="m-0">Edit Author #{{$author->id}}</h1>
                </div>
                <x-admin.author-nav active="blocks" :author="$author" />
            </div>
        </div>
    </div>
@stop

@section('content')
    <div id="app">
        <post-content :dataprops="{{$appData}}"/></post-content>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/admin.js')
@endpush
