@extends('layouts.admin.app')

@section('title', 'Authors')

@section('content_header')
    <x-admin.title
        text="Authors"
        :button="['+ Add Author', route('admin.authors.create')]"
    />
@stop

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <table id="authors-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="ids-column">ID</th>
                                <th>Avatar</th>
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
    <script src="{{asset('/js/admin/authors.js')}}"></script>
@endpush
