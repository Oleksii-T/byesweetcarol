@extends('layouts.admin.app')

@section('title', 'Feedback Preview')

@section('content_header')
    <x-admin.title
        :text="'Feedback Preview #' . $feedback->id"
    />
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>User</label>
                        @if ($feedback->user)
                            <a href="{{route('admin.users.edit', $feedback->user)}}" class="form-control">{{$feedback->user->name}}</a>
                        @else
                            <input type="text" class="form-control" disabled>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" class="form-control" value="{{$feedback->email}}" disabled>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" value="{{$feedback->name}}" disabled>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" class="form-control" value="{{$feedback->subject}}" disabled>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Text</label>
                        <textarea rows="3" class="form-control" disabled>{{$feedback->text}}</textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Status</label>
                        <input type="text" class="form-control" value="{{$feedback->status->readable()}}" disabled>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Created At</label>
                        <input type="text" class="form-control" value="{{$feedback->created_at->adminFormat()}}" disabled>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>IP</label>
                        <input type="text" class="form-control" value="{{$feedback->created_ip}}" disabled>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Feedbacks from same IP</label>
                        <input type="text" class="form-control" value="{{$feedback->fromSameIp()}}" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @foreach (\App\Enums\FeedbackStatus::all() as $key => $value)
        <form action="{{route('admin.feedbacks.update', $feedback)}}" method="post" style="display: inline-block">
            @csrf
            @method('PUT')
            @if ($feedback->status->value != $key)
                <button type="submit" name="status" value="{{$key}}" class="btn btn-warning min-w-100">Mark as {{$value}}</button>
            @endif
        </form>
    @endforeach
    <a href="{{route('admin.feedbacks.index')}}" class="btn btn-outline-secondary text-dark min-w-100">Cancel</a>
@endsection
