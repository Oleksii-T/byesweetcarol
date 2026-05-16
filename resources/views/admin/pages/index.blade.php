@extends('layouts.admin.app')

@section('title', 'Pages')

@section('content_header')
    <x-admin.title
        text="Pages"
        :button="['+ Add Page', route('admin.pages.create')]"
        bcRoute="admin.pages.index"
    />
@stop

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <table id="pages-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="ids-column">ID</th>
                                <th>title</th>
                                <th>URL</th>
                                <th>Status</th>
                                <th>Created at</th>
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
    <script src="{{asset('/js/admin/pages.js')}}"></script>
@endpush
