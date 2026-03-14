@extends('layouts.admin.app')

@section('title', 'Feedbacks')

@section('content_header')
    <x-admin.title
        text="Feedbacks"
        :button="['Bans', route('admin.feedback-bans.index')]"
    />
@stop

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-2">
                            <select class="table-filter form-control" name="status">
                                <option value="">Select status</option>
                                @foreach (\App\Enums\FeedbackStatus::all() as $key => $value)
                                    <option value="{{$key}}" @selected($key === 0)>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="feedbacks-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="ids-column">Id</th>
                                <th>User</th>
                                <th>Subject</th>
                                <th>Text</th>
                                <th>IP</th>
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
@endsection

@push('scripts')
    <script src="{{asset('/js/admin/feedbacks.js')}}"></script>
@endpush
