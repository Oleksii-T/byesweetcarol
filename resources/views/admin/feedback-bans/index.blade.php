@extends('layouts.admin.app')

@section('title', 'Feedback Bans')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <div class="float-left">
                    <h1 class="m-0">Feedback Bans</h1>
                </div>
                <div class="float-left pl-3">
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#create-ban">
                        + Add Ban
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
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-2">
                            <select class="table-filter form-control" name="type">
                                <option value="">Select status</option>
                                @foreach (\App\Models\FeedbackBan::TYPES as $type)
                                    <option value="{{$type}}">{{readable($type)}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="feedback-bans-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="ids-column">Id</th>
                                <th>Type</th>
                                <th>Value</th>
                                <th>Tries</th>
                                <th>Action</th>
                                <th>Status</th>
                                <th>Create At</th>
                                <th class="actions-column-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="create-ban">
        <div class="modal-dialog">
            <form action="{{route('admin.feedback-bans.store')}}" method="post" class="modal-content general-ajax-submit">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Create feedback ban</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Type</label>
                                <select class="table-filter form-control" name="type">
                                    <option value="">Select status</option>
                                    @foreach (\App\Models\FeedbackBan::TYPES as $type)
                                        <option value="{{$type}}">{{readable($type)}}</option>
                                    @endforeach
                                </select>
                                <span data-input="type" class="input-error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Value</label>
                                <input type="text" class="form-control" name="value">
                                <span data-input="value" class="input-error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>
                                    Action
                                    <span class="help-tooltip-icon" data-tippy-content="abort - throw '429 Too Many Requests' error when user tries to submit feedback. spam - allow submit feedback, but mark feedback as spam automatically">
                                        @svg('images/icons/info.svg')
                                    </span>
                                </label>
                                <select class="table-filter form-control" name="action">
                                    <option value="">Select action</option>
                                    @foreach (\App\Models\FeedbackBan::ACTIONS as $action)
                                        <option value="{{$action}}">{{readable($action)}}</option>
                                    @endforeach
                                </select>
                                <span data-input="action" class="input-error"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{asset('/js/admin/feedback-bans.js')}}"></script>
@endpush
