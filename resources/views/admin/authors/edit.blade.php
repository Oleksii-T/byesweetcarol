@extends('layouts.admin.app')

@section('title', 'Edit Author')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <div class="float-left">
                    <h1 class="m-0">Edit Author</h1>
                </div>
                <x-admin.author-nav active="general" :author="$author" />
            </div>
        </div>
    </div>
@stop

@section('content')
    <form action="{{ route('admin.authors.update', $author) }}" method="POST" class="general-ajax-submit">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" value="{{$author->name}}">
                            <span data-input="slug" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" class="form-control" name="email" value="{{$author->email}}">
                            <span data-input="email" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Meta Title</label>
                            <input type="text" class="form-control" name="meta_title" value="{{$author->meta_title}}">
                            <span data-input="meta_title" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Meta Description</label>
                            <input type="text" class="form-control" name="meta_description" value="{{$author->meta_description}}">
                            <span data-input="meta_description" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-admin.rich-image-input name="avatar" :file="$author->avatar()" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" value="{{$author->title}}">
                            <span data-input="title" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <x-admin.rich-image-input name="meta_thumbnail" :file="$author->meta_thumbnail()" />
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Slug</label>
                            <input type="text" class="form-control" name="slug" value="{{$author->slug}}">
                            <span data-input="slug" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control summernote" name="description">{!!$author->description!!}</textarea>
                            <span data-input="description" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Description for Posts page</label>
                            <textarea class="form-control summernote" name="description_small">{!!$author->description_small!!}</textarea>
                            <span data-input="description_small" class="input-error"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success min-w-100">Save</button>
        <a href="{{ route('admin.authors.index') }}" class="btn btn-outline-secondary text-dark min-w-100">Cancel</a>
    </form>
@endsection
