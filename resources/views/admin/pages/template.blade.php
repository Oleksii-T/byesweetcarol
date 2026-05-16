@extends('layouts.admin.app')

@section('title', 'Edit Page Blocks')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <div class="float-left">
                    <h1 class="m-0">Edit Page #{{$page->id}}</h1>
                </div>
                <x-admin.page-nav active="template" :page="$page" />
            </div>
        </div>
    </div>
@stop

@section('content')
    <form id="blocksForm" action="{{ route('admin.pages.update-template', $page) }}" method="post" class="general-ajax-submit" style="padding-bottom:1.5rem">
        @csrf
        @method('PUT')
        @foreach($page->pageBlocks as $block)
            <div class="card">
                <div class="card-header">
                    <h5 class="m-0">{{readable($block->name)}}</h5>
                </div>
                <div class="card-body">
                    @foreach($block->data as $name => $field)
                        <div class="form-group">
                            <label>{{ str_replace('_', ' ', ucfirst($name)) }}</label>
                            @switch($field['type'])
                                @case('text')
                                    <input type="text"
                                        name="blocks[{{ $block->id }}][{{ $name }}][value]"
                                        value="{{ $field['value'] ?? '' }}"
                                        class="form-control"
                                    >
                                @break

                                @case('textarea')
                                    <textarea
                                        name="blocks[{{ $block->id }}][{{ $name }}][value]"
                                        class="form-control" rows="2"
                                        cols="2"
                                    >{!! $field['value'] ?? '' !!}</textarea>
                                @break

                                @case('editor')
                                    <textarea
                                        name="blocks[{{ $block->id }}][{{ $name }}][value]"
                                        class="form-control summernote"
                                        rows="2"
                                        cols="2"
                                        id="editor{{ $name }}{{ $block->id }}"
                                    >{!! $field['value'] ?? '' !!}</textarea>
                                @break

                                @case('image')
                                    <div class="form-group show-uploaded-file-name show-uploaded-file-preview">
                                        <div class="custom-file">
                                            <input
                                                type="file"
                                                class="custom-file-input file-input-block"
                                                name="blocks[{{ $block->id }}][{{ $name }}][value]"
                                            >
                                            <input
                                                type="text"
                                                class="d-none"
                                                name="blocks[{{ $block->id }}][{{ $name }}][value_old]"
                                                value="{{$field['value']}}"
                                            >
                                            <label class="custom-file-label"
                                                for="pageInputFile">Choose file
                                            </label>
                                        </div>
                                        <img class="custom-file-preview {{$field['value'] ? '' : 'd-none'}}" style="max-height: 100px;" src="{{ $field['value'] ? \Storage::disk('pages')->url($field['value']) : '' }}"/>
                                    </div>
                                @break

                                @case('dynamic')
                                    <button type="button" class="{{--btn btn-success--}} button button-sm button-primary add-dynamic-block"
                                            data-dynamic="{{ $name }}"
                                            style="margin-bottom: 10px; vertical-align: initial; margin-left: 10px;"
                                    >
                                        <i class="fas fa-plus"></i>&nbsp;
                                        Add block
                                    </button>

                                    <div class="list-dynamic-blocks" data-dynamic="{{ $name }}">
                                        @foreach($field['blocks'] as $number => $dynamic_block)
                                            <div class="card" data-dynamic="{{ $name }}">
                                                <div class="card-body">
                                                    <button type="button"
                                                            class="btn btn-danger remove-dynamic-block btn btn-danger "
                                                            data-dynamic="{{ $name }}"
                                                            style="margin-bottom: 10px; vertical-align: initial; margin-left: 10px; float: right;"
                                                    >
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                    @foreach($dynamic_block as $dynamic_name => $dynamic_field)
                                                        <div class="form-group">
                                                            <label>{{ str_replace('_', ' ', ucfirst($dynamic_name)) }}</label>
                                                            @switch($dynamic_field['type'])
                                                                @case('text')
                                                                    <input type="text"
                                                                        name="blocks[{{ $block->id }}][{{ $name }}][blocks][{{ $dynamic_name }}][value][]"
                                                                        value="{{ $dynamic_field['value'] ?? '' }}"
                                                                        class="form-control"
                                                                    >
                                                                @break

                                                                @case('textarea')
                                                                    <textarea
                                                                        name="blocks[{{ $block->id }}][{{ $name }}][blocks][{{ $dynamic_name }}][value][]"
                                                                        class="form-control" rows="2"
                                                                        cols="2"
                                                                    >
                                                                        {!! $dynamic_field['value'] ?? '' !!}
                                                                    </textarea>
                                                                @break

                                                                @case('editor')
                                                                    <textarea
                                                                        name="blocks[{{ $block->id }}][{{ $name }}][blocks][{{ $dynamic_name }}][value][]"
                                                                        class="form-control editor-textarea" rows="2"
                                                                        cols="2"
                                                                        id="editor{{ $name }}{{ $block->id }}{{ $dynamic_name }}"
                                                                    >
                                                                        {!! $dynamic_field['value'] ?? '' !!}
                                                                    </textarea>
                                                                @break

                                                                @case('image')
                                                                    <div class="form-group show-uploaded-file-name show-uploaded-file-preview">
                                                                        <div class="custom-file">
                                                                            <input
                                                                                type="file"
                                                                                class="custom-file-input file-input-block"
                                                                                name="blocks[{{ $block->id }}][{{ $name }}][blocks][{{ $dynamic_name }}][value][]"
                                                                            >
                                                                            <input
                                                                                type="text"
                                                                                class="d-none"
                                                                                name="blocks[{{ $block->id }}][{{ $name }}][blocks][{{ $dynamic_name }}][value_old][]"
                                                                                value="{{$dynamic_field['value']}}"
                                                                            >
                                                                            <label
                                                                                class="custom-file-label"
                                                                                for="pageInputFile"
                                                                            >
                                                                                Choose file
                                                                            </label>
                                                                        </div>
                                                                        <img class="custom-file-preview {{$dynamic_field['value'] ? '' : 'd-none'}}" style="max-height: 100px;" src="{{ $dynamic_field['value'] ? \Storage::disk('pages')->url($dynamic_field['value']) : '' }}"/>
                                                                    </div>
                                                                @break

                                                                @case('video')
                                                                    <div class="form-group">
                                                                        <div class="custom-file mb-3">
                                                                            <input type="file"
                                                                                class="custom-file-input file-input-block">
                                                                            <label class="custom-file-label"
                                                                                for="pageInputFile">Choose file
                                                                            </label>
                                                                            <input type="hidden"
                                                                                class="file-value-input-block"
                                                                                name="blocks[{{ $block->id }}][{{ $name }}][blocks][{{ $dynamic_name }}][value][]"
                                                                                value="{{ $dynamic_field['value'] ?? '' }}"
                                                                            >
                                                                            <input type="hidden"
                                                                                class="file-link-input-block"
                                                                                name="blocks[{{ $block->id }}][{{ $name }}][blocks][{{ $dynamic_name }}][src][]"
                                                                                value="{{ $dynamic_field['src'] ?? '' }}"
                                                                            >
                                                                        </div>
                                                                        <div class="icon_thumbnails" style="{{ (!is_null($dynamic_field['value']) and strlen($dynamic_field['value'])) ? '' : 'display: none;' }}">
                                                                            <img src="{{ $dynamic_field['value'] ?? '' }}"/>
                                                                        </div>
                                                                    </div>
                                                                @break

                                                                @default
                                                            @endswitch
                                                            <input type="hidden"
                                                                name="blocks[{{ $block->id }}][{{ $name }}][blocks][{{ $dynamic_name }}][type][]"
                                                                value="{{ $dynamic_field['type'] }}"
                                                            >
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @break

                                @default
                            @endswitch
                            <input type="hidden" name="blocks[{{ $block->id }}][{{ $name }}][type]" value="{{ $field['type'] }}">
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        <button type="submit" class="btn btn-success min-w-100">Save</button>
        <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-outline-secondary text-dark min-w-100">Cancel</a>
    </form>
@endsection

@push('scripts')
    <script src="{{asset('/js/admin/pages.js')}}"></script>
@endpush
