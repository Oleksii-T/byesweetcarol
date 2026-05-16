@extends('layouts.admin.app')

@section('title', 'Edit Category')

@section('content_header')
    <x-admin.title
        text="Edit Category"
    />
@stop

@section('content')
    <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="general-ajax-submit">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" value="{{$category->name}}">
                            <span data-input="name" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Slug</label>
                            <input type="text" class="form-control" name="slug" value="{{$category->slug}}">
                            <span data-input="slug" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control summernote">{{$category->description}}</textarea>
                            <span data-input="description" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Order</label>
                            <input type="number" class="form-control" name="order" value="{{$category->order}}">
                            <span data-input="order" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Meta Title</label>
                            <input type="text" class="form-control" name="meta_title" value="{{$category->meta_title}}">
                            <span data-input="meta_title" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Meta Description</label>
                            <input type="text" class="form-control" name="meta_description" value="{{$category->meta_description}}">
                            <span data-input="meta_description" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <x-admin.rich-image-input name="meta_thumbnail" :file="$category->meta_thumbnail()" />
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>In Top Menu</label>
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="in_menu" value="1" name="in_menu" @checked($category->in_menu)>
                                <label for="in_menu" class="custom-control-label">Yes</label>
                            </div>
                            <span data-input="in_menu" class="input-error"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success min-w-100">Save</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary text-dark min-w-100">Cancel</a>
    </form>
@endsection
