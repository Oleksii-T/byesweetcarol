@extends('admin.layouts.app')

@section('title', 'Subscriptions')

@section('content_header')
    <x-admin.title
        text="Subscriptionss"
        bcRoute="admin.subscriptions.index"
    />
@stop

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <table id="subscriptions-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="ids-column">ID</th>
                                <th>User Name</th>
                                <th>Subscription Plan</th>
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
    <script src="{{asset('/js/admin/subscriptions.js')}}"></script>
@endpush
