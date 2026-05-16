@extends('layouts.admin.app')

@section('title', 'Edit Game')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <div class="float-left">
                    <h1 class="m-0">Edit Game #{{$game->id}}</h1>
                </div>
                <div class="pl-3">
                    <a href="{{route('games.show', $game)}}" class="btn" style="color: gray" target="_blank">Preview</a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <form action="{{ route('admin.games.update', $game) }}" method="POST" class="general-ajax-submit">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" value="{{$game->name}}">
                            <span data-input="name" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Slug</label>
                            <input type="text" class="form-control" name="slug" value="{{$game->slug}}">
                            <span data-input="slug" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Meta Title</label>
                            <input type="text" class="form-control" name="meta_title" value="{{$game->meta_title}}">
                            <span data-input="meta_title" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Meta Description</label>
                            <input type="text" class="form-control" name="meta_description" value="{{$game->meta_description}}">
                            <span data-input="meta_description" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>RCClubGame Rating</label>
                            <input type="number" class="form-control" name="rating" value="{{$game->rating}}">
                            <span data-input="rating" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Metacritic Rating</label>
                            <input type="number" class="form-control" name="metacritic" value="{{$game->metacritic}}">
                            <span data-input="metacritic" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Users score</label>
                            <input type="number" class="form-control" name="users_score" step="0.1" value="{{$game->users_score}}">
                            <span data-input="users_score" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Release Date</label>
                            <div class="input-group">
                                <input type="text" class="form-control daterangepicker-single" name="release_date" value="{{$game->release_date?->format('Y-m-d')}}">
                                <div class="input-group-append daterangepicker-single-clear" style="cursor: pointer">
                                    <span class="input-group-text"><i class="fas fa-times"></i></span>
                                </div>
                            </div>
                            <span data-input="release_date" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Developer</label>
                            <input type="text" class="form-control" name="developer" value="{{$game->developer}}">
                            <span data-input="developer" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Publisher</label>
                            <input type="text" class="form-control" name="publisher" value="{{$game->publisher}}">
                            <span data-input="publisher" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Platforms</label>
                            <select class="form-control select2" name="platforms[]" multiple>
                                @foreach (\App\Models\Platform::all() as $platform)
                                    <option value="{{$platform->id}}" @selected($game->platforms->contains('id', $platform->id))>{{$platform->name}}</option>
                                @endforeach
                            </select>
                            <span data-input="platforms" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Ganres</label>
                            <input type="text" class="form-control" name="ganres" value="{{$game->ganres}}">
                            <span data-input="ganres" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                @foreach (\App\Enums\GameStatus::all() as $key => $value)
                                    <option value="{{$key}}" @selected($game->status->value == $key)>{{$value}}</option>
                                @endforeach
                            </select>
                            <span data-input="status" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control">{{$game->description}}</textarea>
                            <span data-input="description" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Summary</label>
                            <textarea name="summary" class="form-control summernote">{!!$game->summary!!}</textarea>
                            <span data-input="summary" class="input-error"></span>
                        </div>
                    </div>
                </div>
                <div class="row">                     
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Official Site</label>
                            <input type="text" class="form-control" name="official_site" value="{{$game->official_site}}">
                            <span data-input="official_site" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Steam</label>
                            <input type="text" class="form-control" name="steam" value="{{$game->steam}}">
                            <span data-input="steam" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>PlayStation store</label>
                            <input type="text" class="form-control" name="playstation_store" value="{{$game->playstation_store}}">
                            <span data-input="playstation_store" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Xbox store</label>
                            <input type="text" class="form-control" name="xbox_store" value="{{$game->xbox_store}}">
                            <span data-input="xbox_store" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Nintendo store</label>
                            <input type="text" class="form-control" name="nintendo_store" value="{{$game->nintendo_store}}">
                            <span data-input="nintendo_store" class="input-error"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>ESRB</label>
                            <input type="text" class="form-control" name="esbr" value="{{$game->esbr}}">
                            <span data-input="esbr" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <x-admin.rich-image-input name="esbr_image" :file="$game->esbr_image()" />
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>MAIN STORY</label>
                            <input type="number" class="form-control" name="hours[main]" step="0.5" value="{{$game->hours['main']??''}}">
                            <span data-input="hours.main" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>MAIN + SIDES</label>
                            <input type="number" class="form-control" name="hours[main_sides]" step="0.5" value="{{$game->hours['main_sides']??''}}">
                            <span data-input="hours.main_sides" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>COMPLETIONIST</label>
                            <input type="number" class="form-control" name="hours[completionist]" step="0.5" value="{{$game->hours['completionist']??''}}">
                            <span data-input="hours.completionist" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>ALL STYLES</label>
                            <input type="number" class="form-control" name="hours[all]" step="0.5" value="{{$game->hours['all']??''}}">
                            <span data-input="hours.all" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <x-admin.rich-image-input name="thumbnail" :file="$game->thumbnail()" />
                    </div>
                    <div class="col-md-12">
                        <x-admin.rich-image-input name="screenshots" :files="$game->screenshots" multiple="1" />
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success min-w-100">Save</button>
        <button type="button" data-url="{{route('admin.games.scrape', $game)}}" class="scrape-game-btn btn btn-warning text-dark min-w-100">Scrape</button>
        <a href="{{ route('admin.games.index') }}" class="btn btn-outline-secondary text-dark min-w-100">Cancel</a>
    </form>
@endsection

@push('scripts')
    <script src="{{asset('/js/admin/games.js')}}"></script>
@endpush
