@extends('layouts.admin.app')

@section('title', 'Create Author')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <div class="float-left">
                    <h1 class="m-0">Edit Author</h1>
                </div>
                <x-admin.author-nav active="socials" :author="$author" />
            </div>
        </div>
    </div>
@stop

@section('content')
    <form action="{{ route('admin.authors.update-socials', $author) }}" method="POST" class="general-ajax-submit">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Facebook</label>
                            <input type="text" class="form-control" name="facebook" value="{{$author->facebook}}">
                            <span data-input="facebook" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Instagram</label>
                            <input type="text" class="form-control" name="instagram" value="{{$author->instagram}}">
                            <span data-input="instagram" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Youtube</label>
                            <input type="text" class="form-control" name="youtube" value="{{$author->youtube}}">
                            <span data-input="youtube" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Twitter</label>
                            <input type="text" class="form-control" name="twitter" value="{{$author->twitter}}">
                            <span data-input="twitter" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Steam</label>
                            <input type="text" class="form-control" name="steam" value="{{$author->steam}}">
                            <span data-input="steam" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>LinkedIn</label>
                            <input type="text" class="form-control" name="linkedin" value="{{$author->linkedin}}">
                            <span data-input="linkedin" class="input-error"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success min-w-100">Save</button>
        <a href="{{ route('admin.authors.index') }}" class="btn btn-outline-secondary text-dark min-w-100">Cancel</a>
    </form>
@endsection
