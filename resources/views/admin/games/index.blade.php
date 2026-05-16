@extends('layouts.admin.app')

@section('title', 'Games')

@section('content_header')
    <x-admin.title
        text="Games"
        :button="['+ Add Game', route('admin.games.create')]"
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
                                <option value="">Status filter</option>
                                @foreach (\App\Enums\GameStatus::all() as $key => $name)
                                    <option value="{{$key}}" @selected(request()->status == $key)>{{$name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="games-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="ids-column">ID</th>
                                <th>Name</th>
                                <th>Status</th>
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
    <script src="{{asset('/js/admin/games.js')}}"></script>
@endpush
