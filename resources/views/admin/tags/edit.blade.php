@extends('layouts.admin.app')

@section('title', 'Edit Tag')

@section('content_header')
    <x-admin.title
        text="Edit Tag"
    />
@stop

@section('content')
    <form action="{{ route('admin.tags.update', $tag) }}" method="POST" class="general-ajax-submit mb-4">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" value="{{$tag->name}}">
                            <span data-input="name" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Slug</label>
                            <input type="text" class="form-control" name="slug" value="{{$tag->slug}}">
                            <span data-input="slug" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Alternative names <small>(json, lowercase, used for tag guessing)</small></label>
                            <input type="text" class="form-control" name="alter_names" value="{{ $tag->alter_names ? json_encode($tag->alter_names) : '' }}">
                            <span data-input="alter_names" class="input-error"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success min-w-100">Save</button>
    </form>
    <form action="{{ route('admin.tags.transfer', $tag) }}" method="POST" class="general-ajax-submit mb-4">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-header">
                <h5 class="m-0">Transfer Posts</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>To</label>
                            <select name="tag_id" class="form-control">
                                <option value=""></option>
                                @foreach ($otherTags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                @endforeach
                            </select>
                            <span data-input="slug" class="input-error"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success min-w-100">Transfer</button>
        <a href="{{ route('admin.tags.index') }}" class="btn btn-outline-secondary text-dark min-w-100">Cancel</a>
    </form>

    <div class="card">
        <div class="card-header">
            <h5 class="m-0">Posts</h5>
        </div>
        <div class="card-body">
            <table id="posts-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="ids-column">ID</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Views <small>total/real/unique-real/bots</small></th>
                        <th>Status</th>
                        <th>Created_at</th>
                        <th class="actions-column-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{asset('/js/admin/posts.js')}}"></script>
@endpush
