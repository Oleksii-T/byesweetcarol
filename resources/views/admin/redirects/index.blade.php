@extends('layouts.admin.app')

@section('title', 'Redirects')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <div class="float-left">
                    <h1 class="m-0">Redirects</h1>
                </div>
                <div class="float-left pl-3">
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#create-redirect">
                        + Add Redirect
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
                    <table id="redirects-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="ids-column">Id</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Hits</th>
                                <th>Code</th>
                                <th>Last At</th>
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

    <div class="modal fade" id="create-redirect">
        <div class="modal-dialog">
            <form action="{{route('admin.redirects.store')}}" method="post" class="modal-content general-ajax-submit">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Create redirect</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>From</label>
                                <input type="text" class="form-control" name="from">
                                <span data-input="from" class="input-error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>To</label>
                                <input type="text" class="form-control" name="to">
                                <span data-input="to" class="input-error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Code</label>
                                <input type="text" class="form-control" name="code" value="301">
                                <span data-input="code" class="input-error"></span>
                            </div>
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
    <script src="{{asset('/js/admin/redirects.js')}}"></script>
@endpush
