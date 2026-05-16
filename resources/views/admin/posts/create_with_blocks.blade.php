@extends('layouts.admin.app')

@section('title', 'Create Post')

@section('content_header')
    <x-admin.title
        text="Create Post"
    />
@stop

@section('content')
    <form action="{{ route('admin.posts.store') }}" method="POST" class="general-ajax-submit@">
        @csrf
        <div class="card">
            <div class="card-header">
                <h5 class="m-0">General info</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title">
                            <span data-input="slug" class="input-error"></span>
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
                        <div class="form-group show-uploaded-file-name show-uploaded-file-preview">
                            <label>Thumbnail</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="thumbnail" name="thumbnail">
                                <label class="custom-file-label" for="thumbnail">Choose file</label>
                            </div>
                            <img src="" alt="" class="custom-file-preview">
                            <span data-input="thumbnail" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Is Active</label>
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="is_active" name="is_active" value="1">
                                <label for="is_active" class="custom-control-label">Yes</label>
                            </div>
                            <span data-input="is_active" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Category</label>
                            <select class="form-control" name="category_id">
                                @foreach (\App\Models\Category::all() as $model)
                                    <option value="{{$model->id}}">{{$model->name}}</option>
                                @endforeach
                            </select>
                            <span data-input="category" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Game</label>
                            <select class="form-control" name="game_id">
                                @foreach (\App\Models\Game::all() as $model)
                                    <option value="{{$model->id}}">{{$model->name}}</option>
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
                                    <option value="{{$model->id}}">{{$model->name}}</option>
                                @endforeach
                            </select>
                            <span data-input="author_id" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tags</label>
                            <select class="form-control select2" name="tags[]" multiple>
                                @foreach (\App\Models\Tag::all() as $model)
                                    <option value="{{$model->id}}">{{$model->name}}</option>
                                @endforeach
                            </select>
                            <span data-input="tags" class="input-error"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5 class="m-0">Custom css\js</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group show-uploaded-file-name">
                            <label>CSS</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="css" name="css">
                                <label class="custom-file-label" for="css">Choose file</label>
                            </div>
                            <span data-input="css" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group show-uploaded-file-name">
                            <label>JS</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="js" name="js">
                                <label class="custom-file-label" for="js">Choose file</label>
                            </div>
                            <span data-input="js" class="input-error"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="item-inputs">
            <div class="title">
                <input type="text" class="form-control" name="value">
            </div>
            <div class="text">
                <textarea name="value" class="form-controll"></textarea>
            </div>
            <div class="image">
                <div class="show-uploaded-file-name show-uploaded-file-preview">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input">
                        <label class="custom-file-label">Choose file</label>
                    </div>
                    <img src="" alt="" class="custom-file-preview">
                </div>
            </div>
            <div class="video">
                <div class="show-uploaded-file-name">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input">
                        <label class="custom-file-label">Choose file</label>
                    </div>
                </div>
            </div>
            <div class="slider">
                todo
            </div>
        </div>
        <div class="card">
            <div class="card-header row">
                <h5 class="m-0 col">Content</h5>
                <div class="col">
                    <button type="button" class="btn btn-success d-block float-right add-block">Add Block</button>
                </div>
            </div>
            <div class="card-body">
                <div class="row post-block-wrapper">
                    <div class="card card-outline card-dark w-100 post-block d-none clone">
                        <div class="card-header row">
                            <div class="col">
                                <h5 class="m-0 d-inline">id=</h5>
                                "<input type="text" class="form-control d-inline w-auto" name="blocks[0][id]">"
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-warning d-block float-right remove-block">Remove</button>
                                <button type="button" class="btn btn-success d-block mr-2 float-right add-item">Add Item</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row block-item-wrapper">
                                <div class="col-md-12 block-item d-none clone">
                                    <div class="form-group">
                                        <div class="mb-2">
                                            <select name="blocks[0][items][0][type]" class="form-control w-auto d-inline item-type-select">
                                                @foreach ($itemTypes as $item)
                                                    <option value="{{$item}}">{{readable($item)}}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-warning remove-item float-right">Remove</button>
                                        </div>
                                        <div class="item-input">
                                            <input type="text" name="blocks[0][items][0][value]" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
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
