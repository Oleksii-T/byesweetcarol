@extends('layouts.admin.app')

@section('title', 'Edit post assets')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <div class="float-left">
                    <h1 class="m-0">Edit Post #{{$post->id}}</h1>
                </div>
                <x-admin.post-nav active="assets" :post="$post" />
            </div>
        </div>
    </div>
@stop

@section('content')
    <form action="{{route('admin.posts.update-assets', $post)}}" method="POST" class="general-ajax-submit">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group show-uploaded-file-name">
                            <label>CSS</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="css" name="css">
                                <label class="custom-file-label" for="css">{{$post->css?->original_name ?? 'Choose File'}}</label>
                            </div>
                            <span data-input="css" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group show-uploaded-file-name">
                            <label>JS</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="js" name="js">
                                <label class="custom-file-label" for="js">{{$post->js?->original_name ?? 'Choose File'}}</label>
                            </div>
                            <span data-input="js" class="input-error"></span>
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
