@extends('layouts.admin.app')

@section('title', 'Comments')

@section('content_header')
    <x-admin.title
        text="Edit Comments"
    />
@stop

@section('content')
<form action="{{ route('admin.comments.update', $comment) }}" method="POST" class="general-ajax-submit" style="padding-bottom:1.5rem">
    @csrf
    @method('PUT')
    <div class="card card-info card-outline">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label>User</label>
                        <select name="user_id" class="form-control">
                            @foreach (\App\Models\User::all() as $user)
                                <option value="{{$user->id}}" @selected($comment->user_id == $user->ud)>{{$user->name}}</option>
                            @endforeach
                        </select>
                        <span data-input="user_id" class="input-error"></span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            @foreach (\App\Enums\CommentStatus::all() as $key => $value)
                                <option value="{{$key}}" @selected($key == $comment->status->value)>{{$value}}</option>
                            @endforeach
                        </select>
                        <span data-input="status" class="input-error"></span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label>Text</label>
                        <textarea name="text" class="form-control">{{$comment->text}}</textarea>
                        <span data-input="text" class="input-error"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-success min-w-100">Save</button>
    <a href="{{ route('admin.comments.index') }}" class="btn btn-outline-secondary text-dark min-w-100">Cancel</a>
</form>
@endsection

@push('scripts')
    <script src="{{asset('/js/admin/comments.js')}}"></script>
@endpush
