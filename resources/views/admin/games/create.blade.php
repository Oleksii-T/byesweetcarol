@extends('layouts.admin.app')

@section('title', 'Create Game')

@section('content_header')
    <x-admin.title
        text="Create Game"
    />
@stop

@section('content')
    <form action="{{ route('admin.games.store') }}" method="POST" class="general-ajax-submit">
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
                        <div class="form-group">
                            <label>RCClubGame Rating</label>
                            <input type="number" class="form-control" name="rating">
                            <span data-input="rating" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Metacritic Rating</label>
                            <input type="number" class="form-control" name="metacritic">
                            <span data-input="metacritic" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Users score</label>
                            <input type="number" class="form-control" name="users_score" step="0.1">
                            <span data-input="users_score" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Release Date</label>
                            <input type="text" class="form-control daterangepicker-single" name="release_date">
                            <span data-input="release_date" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Developer</label>
                            <input type="text" class="form-control" name="developer">
                            <span data-input="developer" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Publisher</label>
                            <input type="text" class="form-control" name="publisher">
                            <span data-input="publisher" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Platforms</label>
                            <select class="form-control select2" name="platforms[]" multiple>
                                @foreach (\App\Models\Platform::all() as $platform)
                                    <option value="{{$platform->id}}">{{$platform->name}}</option>
                                @endforeach
                            </select>
                            <span data-input="platforms" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Ganres</label>
                            <input type="text" class="form-control" name="ganres">
                            <span data-input="ganres" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                @foreach (\App\Enums\GameStatus::all() as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                            <span data-input="status" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control"></textarea>
                            <span data-input="description" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Summary</label>
                            <textarea name="summary" class="form-control summernote"></textarea>
                            <span data-input="summary" class="input-error"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Official Site</label>
                            <input type="text" class="form-control" name="official_site">
                            <span data-input="official_site" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Steam</label>
                            <input type="text" class="form-control" name="steam">
                            <span data-input="steam" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>PlayStation store</label>
                            <input type="text" class="form-control" name="playstation_store">
                            <span data-input="playstation_store" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Xbox store</label>
                            <input type="text" class="form-control" name="xbox_store">
                            <span data-input="xbox_store" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Nintendo store</label>
                            <input type="text" class="form-control" name="nintendo_store">
                            <span data-input="nintendo_store" class="input-error"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>ESRB</label>
                            <input type="text" class="form-control" name="esbr">
                            <span data-input="esbr" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <x-admin.rich-image-input name="esbr_image" />
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>MAIN STORY</label>
                            <input type="number" class="form-control" name="hours[main]" step="0.5">
                            <span data-input="hours.main" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>MAIN + SIDES</label>
                            <input type="number" class="form-control" name="hours[main_sides]" step="0.5">
                            <span data-input="hours.main_sides" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>COMPLETIONIST</label>
                            <input type="number" class="form-control" name="hours[completionist]" step="0.5">
                            <span data-input="hours.completionist" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>ALL STYLES</label>
                            <input type="number" class="form-control" name="hours[all]" step="0.5">
                            <span data-input="hours.all" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <x-admin.rich-image-input name="thumbnail" />
                    </div>
                    <div class="col-md-12">
                        <x-admin.rich-image-input name="screenshots" multiple="1" />
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success min-w-100">Save</button>
        <a href="{{ route('admin.games.index') }}" class="btn btn-outline-secondary text-dark min-w-100">Cancel</a>
    </form>
@endsection
