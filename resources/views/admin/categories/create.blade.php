@extends('layouts.admin.app')

@section('title', 'Create Category')

@section('content_header')
    <x-admin.title
        text="Create Category"
    />
@stop

@section('content')
    <form action="{{ route('admin.categories.store') }}" method="POST" class="general-ajax-submit">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name">
                            <span data-input="name" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Slug</label>
                            <input type="text" class="form-control" name="slug">
                            <span data-input="slug" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control summernote"></textarea>
                            <span data-input="description" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Order</label>
                            <input type="number" class="form-control" name="order">
                            <span data-input="order" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Meta Title</label>
                            <input type="text" class="form-control" name="meta_title">
                            <span data-input="meta_title" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Meta Description</label>
                            <input type="text" class="form-control" name="meta_description">
                            <span data-input="meta_description" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <x-admin.rich-image-input name="meta_thumbnail" />
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>In Top Menu</label>
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" value="1" id="in_menu" name="in_menu">
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
