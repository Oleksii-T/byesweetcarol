@extends('layouts.admin.app')

@section('title', 'Edit post')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <div class="float-left">
                    <h1 class="m-0">Edit Post #{{$post->id}}</h1>
                </div>
                <x-admin.post-nav active="general" :post="$post" />
            </div>
        </div>
    </div>
@stop

@section('content')
    <form action="{{route('admin.posts.update', $post)}}" method="POST" class="general-ajax-submit">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-header">
                <h5 class="m-0">General info</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" value="{{$post->title}}">
                            <span data-input="title" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Slug</label>
                            <input type="text" class="form-control" name="slug" value="{{$post->slug}}">
                            <span data-input="slug" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Title For Links</label>
                            <input type="text" class="form-control" name="links_title" value="{{$post->getRawOriginal('links_title')}}">
                            <span data-input="links_title" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>-</label>
                            <div class="form-check">
                                <input type="hidden" name="human_written" value="0">
                                <input class="form-check-input" type="checkbox" name="human_written" value="1" id="human_written" @checked($post->human_written)>
                                <label class="form-check-label" for="human_written">Human written</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Meta Title</label>
                            <input type="text" class="form-control" name="meta_title" value="{{$post->meta_title}}">
                            <span data-input="meta_title" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Meta Description</label>
                            <input type="text" class="form-control" name="meta_description" value="{{$post->meta_description}}">
                            <span data-input="meta_description" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status">
                                @foreach (\App\Enums\PostStatus::all() as $key => $value)
                                    <option value="{{$key}}" @selected($post->status->value == $key)>{{$value}}</option>
                                @endforeach
                            </select>
                            <span data-input="status" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Category</label>
                            <select class="form-control" name="category_id">
                                @foreach (\App\Models\Category::latest()->get() as $model)
                                    <option value="{{$model->id}}" @selected($post->category_id==$model->id)>{{$model->name}}</option>
                                @endforeach
                            </select>
                            <span data-input="category" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Game</label>
                            <select class="form-control select2" name="game_id">
                                <option value="">Select Game</option>
                                @foreach (\App\Models\Game::latest()->get() as $model)
                                    <option value="{{$model->id}}" @selected($post->game_id==$model->id)>{{$model->name}}</option>
                                @endforeach
                            </select>
                            <span data-input="game_id" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Author</label>
                            <select class="form-control" name="author_id">
                                @foreach (\App\Models\Author::all() as $model)
                                    <option value="{{$model->id}}" @selected($post->author_id==$model->id)>{{$model->name}}</option>
                                @endforeach
                            </select>
                            <span data-input="author_id" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tags</label>
                            <select class="form-control select2" name="tags[]" multiple>
                                @foreach (\App\Models\Tag::latest()->get() as $model)
                                    <option value="{{$model->id}}" @selected($post->tags->contains('id', $model->id))>{{$model->name}}</option>
                                @endforeach
                            </select>
                            <span data-input="tags" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Table of content style</label>
                            <select class="form-control" name="tc_style">
                                @foreach (\App\Enums\PostTCStyle::all() as $key => $value)
                                    <option value="{{$key}}" @selected($post->tc_style->value == $key)>{{$value}}</option>
                                @endforeach
                            </select>
                            <span data-input="tc_style" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <x-admin.rich-image-input name="thumbnail" :file="$post->thumbnail()" />
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Parent</label>
                            <select class="form-control select2" name="parent_id">
                                <option value="">-</option>
                                @foreach ($parents as $parent)
                                    <option value="{{$parent->id}}" @selected($post->parent_id == $parent->id)>{{$parent->title}}</option>
                                @endforeach
                            </select>
                            <span data-input="parent_id" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>Sub Title</label>
                            <textarea name="sub_title" class="form-control custom-summernote">{{$post->sub_title}}</textarea>
                            <span data-input="sub_title" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>Intro</label>
                            <textarea name="intro" class="form-control custom-summernote">{{$post->intro}}</textarea>
                            <span data-input="intro" class="input-error"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success min-w-100">Save</button>
        <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary text-dark min-w-100">Cancel</a>
        @if ($post->info && $post->info->source)
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="m-0">Other info</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Source</label>
                                <a class="form-control" target="_blank" href="{{$post->info->source}}">{{$post->info->source}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </form>
@endsection

@push('scripts')
    <script src="{{asset('/js/admin/posts.js')}}"></script>
@endpush
