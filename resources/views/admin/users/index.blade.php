@extends('layouts.admin.app')

@section('title', 'Users')

@section('content_header')
    <x-admin.title
        text="Users"
        :button="['+ Add User', route('admin.users.create')]"
    />
@stop

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-2">
                            <select class="table-filter form-control" name="role">
                                <option value="">Role filter</option>
                                @foreach (\App\Models\Role::all() as $model)
                                    <option value="{{$model->id}}" @selected(request()->role == $model->id)>{{$model->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="users-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="ids-column">ID</th>
                                <th>Email</th>
                                <th>Name</th>
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
    <script src="{{asset('/js/admin/users.js')}}"></script>
@endpush
