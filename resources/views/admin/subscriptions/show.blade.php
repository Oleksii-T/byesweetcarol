@extends('admin.layouts.app')

@section('title', 'Subscription')

@section('content_header')
    <x-admin.title
        text="Subscription"
        :bcRoute="['admin.subscriptions.show', $subscription]"
    />
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>User</label>
                        <a class="form-control" href="{{route('admin.users.edit', $subscription->user_id)}}">
                            {{$subscription->user->name}}
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Plan</label>
                        <a class="form-control" href="{{route('admin.subscription-plans.edit', $subscription->plan)}}">
                            {{$subscription->plan->title}}
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Status</label>
                        <p class="form-control">
                            {{ucfirst($subscription->status)}}
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Created at</label>
                        <p class="form-control">
                            {{$subscription->created_at->format(env('ADMIN_DATETIME_FORMAT'))}}
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Stripe Id</label>
                        <p class="form-control">
                            {{$subscription->stripe_id}}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="m-0">Billing Cycles</h5>
                </div>
                <div class="card-body">
                    <table id="subscription-cycles-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="ids-column">ID</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary text-dark min-w-100">Cancel</a>
@endsection

@push('scripts')
    <script src="{{asset('/js/admin/subscriptions.js')}}"></script>
@endpush
