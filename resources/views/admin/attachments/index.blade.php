@extends('layouts.admin.app')

@section('title', 'Attachments')

@section('content_header')
    <x-admin.title
        text="Attachments"
    />
@stop

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-2">
                            <select class="table-filter form-control" name="type">
                                <option value="">Type filter</option>
                                    <option value="image" @selected(request()->role == 'image')>Image</option>
                                    <option value="document" @selected(request()->type == 'document')>Document</option>
                                    <option value="video" @selected(request()->type == 'video')>Video</option>
                                    <option value="file" @selected(request()->type == 'file')>File</option>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <select class="table-filter form-control" name="has_entity">
                                <option value="">Has Entity filter</option>
                                <option value="1" @selected(request()->has_entity == 1)>Yes</option>
                                <option value="2" @selected(request()->has_entity == 2)>No</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="attachments-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="ids-column">ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th>Created_at</th>
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
@endsection

@push('scripts')
    <script src="{{asset('/js/admin/attachments.js')}}"></script>
@endpush
