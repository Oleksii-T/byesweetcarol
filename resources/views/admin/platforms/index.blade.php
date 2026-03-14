@extends('layouts.admin.app')

@section('title', 'Feedbacks')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <div class="float-left">
                    <h1 class="m-0">Platforms</h1>
                </div>
                <div class="float-left pl-3">
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#create-platform">
                        + Add Platform
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <table id="platforms-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="ids-column">Id</th>
                                <th>Name</th>
                                <th>Icon</th>
                                <th>Games</th>
                                <th>Created At</th>
                                <th class="actions-column-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="create-platform">
        <div class="modal-dialog">
            <form action="{{route('admin.platforms.store')}}" method="post" class="modal-content general-ajax-submit">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Create platform</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
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
                        <div class="col-12">
                            <x-admin.rich-image-input name="icon" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{asset('/js/admin/platforms.js')}}"></script>
@endpush
