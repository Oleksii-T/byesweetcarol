@extends('admin.layouts.app')

@section('title', 'Subscription Features')

@section('content_header')
    <x-admin.title
        text="Subscription Features"
        :button="['+ Add Feature', route('admin.subscription-features.create')]"
        bcRoute="admin.subscription-features.index"
    />
@stop

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <table id="subscription-features-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="ids-column">ID</th>
                                <th>Text</th>
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
    <script src="{{asset('/js/admin/subscription-features.js')}}"></script>
@endpush
