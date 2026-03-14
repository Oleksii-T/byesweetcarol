@extends('layouts.admin.app')

@section('title', 'Create Post AI')

@section('content_header')
    <x-admin.title
        text="Create Post AI"
    />
@stop

@section('content')
    {{-- <div>
        {{$appData}}
    </div> --}}
    <div id="app">
        <post-create-ai :dataprops="{{$appData}}"/></post-content>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/admin.js')
@endpush