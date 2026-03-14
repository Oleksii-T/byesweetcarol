@extends('layouts.admin.app')

@section('title', 'Edit Attachment')

@section('content_header')
    <x-admin.title
        text="Edit Attachment #{{$attachment->id}}"
    />
@stop

@section('content')
    <form action="{{ route('admin.attachments.update', $attachment) }}" method="POST" class="general-ajax-submit">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Original Name</label>
                            <input name="original_name" type="text" class="form-control" value="{{$attachment->original_name}}">
                            <span data-input="original_name" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>File Name <small>(some files saved with altered name in order to keep name uniqueness)</small></label>
                            <input name="name" type="text" class="form-control" value="{{$attachment->name}}">
                            <span data-input="name" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Type</label>
                            <input type="text" class="form-control" value="{{$attachment->type}}" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Size</label>
                            <input type="text" class="form-control" value="{{$attachment->getSize()}}" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Alt</label>
                            <input type="text" class="form-control" name="alt" value="{{$attachment->alt}}">
                            <span data-input="alt" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" value="{{$attachment->title}}">
                            <span data-input="title" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Source</label>
                            <input type="text" class="form-control" name="title" value="{{$attachment->source}}">
                            <span data-input="title" class="input-error"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>URL</label>
                            <a href="{{$attachment->url}}" target="_blank" class="form-control">{{$attachment->url}}</a>
                            @if ($attachment->type == 'image')
                                <img style="max-width:100%;max-height:300px" src="{{$attachment->url}}" alt="">
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <x-admin.rich-image-input name="new_file" withoutbrowser />
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <table id="attachments-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="ids-column">ID</th>
                            <th>Entity</th>
                            <th>Group</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($resources as $r)
                            <tr>
                                <td>{{$r->attachmentable_id}}</td>
                                <td>
                                    @if ($r->attachmentable && $r->attachmentable_type == \App\Models\Author::class)
                                        <a href="{{route('admin.authors.edit', $r->attachmentable)}}">{{$r->attachmentable->name}}</a>
                                    @elseif ($r->attachmentable && $r->attachmentable_type == \App\Models\Post::class)
                                        <a href="{{route('admin.posts.edit', $r->attachmentable)}}">{{$r->attachmentable->title}}</a>
                                    @elseif ($r->attachmentable && $r->attachmentable_type == \App\Models\Category::class)
                                        <a href="{{route('admin.categories.edit', $r->attachmentable)}}">{{$r->attachmentable->name}}</a>
                                    @elseif ($r->attachmentable && $r->attachmentable_type == \App\Models\Game::class)
                                        <a href="{{route('admin.games.edit', $r->attachmentable)}}">{{$r->attachmentable->name}}</a>
                                    @elseif ($r->attachmentable && $r->attachmentable_type == \App\Models\BlockItem::class)
                                        Block Item for {{$r->attachmentable->block->blockable_type}} #{{$r->attachmentable->block->blockable_id}} content
                                    @else
                                        {{$r->attachmentable_type}}
                                    @endif
                                </td>
                                <td>{{$r->group}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <button type="submit" class="btn btn-success min-w-100">Save</button>
        <a href="{{ route('admin.attachments.index') }}" class="btn btn-outline-secondary text-dark min-w-100">Cancel</a>
    </form>
@endsection
