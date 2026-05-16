@extends('layouts.admin.app')

@section('title', 'Create Page')

@section('content_header')
    <x-admin.title
        text="Create Page"
        bcRoute="admin.pages.create"
    />
@stop

@section('content')
    <form action="{{ route('admin.pages.store') }}" method="POST" class="general-ajax-submit">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Title</label>
                            <input name="title" type="text" class="form-control">
                            <span data-input="title" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status">
                                @foreach (\App\Enums\PageStatus::getEditables() as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                            <span data-input="status" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>URL</label>
                            <input name="link" type="text" class="form-control">
                            <span data-input="link" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Meta title</label>
                            <input name="meta_title" type="text" class="form-control">
                            <span data-input="meta_title" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Meta description</label>
                            <input name="meta_description" type="text" class="form-control">
                            <span data-input="meta_description" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <textarea name="content" class="form-control summernote"></textarea>
                            <span data-input="content" class="input-error"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success min-w-100">Save</button>
        <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary text-dark min-w-100">Cancel</a>
    </form>
@endsection
