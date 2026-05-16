@extends('layouts.admin.app')

@section('title', 'Edit post related')

@push('styles')
    <style>
        .select2-container--default .select2-selection--multiple {
            height: auto !important
        }
    </style>
@endpush

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <div class="float-left">
                    <h1 class="m-0">Edit Post #{{$post->id}}</h1>
                </div>
                <x-admin.post-nav active="related" :post="$post" />
            </div>
        </div>
    </div>
@stop

@section('content')
    <form action="{{route('admin.posts.update-related', $post)}}" method="POST" class="general-ajax-submit">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label>Related Posts</label>
                            <select name="related[]" class="form-control select2" multiple>
                                @foreach ($posts as $p)
                                    <option value="{{$p->id}}" @selected(in_array($p->id, $post->related??[]))>{{$p->title}}</option>
                                @endforeach
                            </select>
                            <span data-input="related" class="input-error"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success min-w-100">Save</button>
        <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary text-dark min-w-100">Cancel</a>
    </form>
@endsection

@push('scripts')
    <script src="{{asset('/js/admin/posts.js')}}"></script>
@endpush
