@extends('admin.layouts.app')

@section('title', 'Subscription Plans')

@section('content_header')
    <x-admin.title
        text="Subscription Plans"
        :button="['+ Add Plan', route('admin.subscription-plans.create')]"
        bcRoute="admin.subscription-plans.index"
    />
@stop

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <table id="subscription-plans-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="ids-column">ID</th>
                                <th>Title</th>
                                <th>Interval</th>
                                <th>Price</th>
                                <th>Trial</th>
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
    <script src="{{asset('/js/admin/subscription-plans.js')}}"></script>
@endpush
