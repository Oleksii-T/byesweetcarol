@extends('admin.layouts.app')

@section('title', 'Edit Plan')

@section('content_header')
    <x-admin.title
        text="Edit Plan"
        :bcRoute="['admin.subscription-plans.edit', $subscriptionPlan]"
    />
@stop

@section('content')
    <form action="{{route('admin.subscription-plans.update', $subscriptionPlan)}}" method="POST" class="general-ajax-submit">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Title</label>
                            <input name="title" type="text" class="form-control" value="{{$subscriptionPlan->title}}">
                            <span data-input="title" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Price ({{\App\Models\Setting::get('currency_sign')}})</label>
                            <input name="price" type="text" class="form-control" value="{{$subscriptionPlan->price}}" readonly>
                            <span data-input="price" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Trial days</label>
                            <input name="trial" type="number" class="form-control" value="{{$subscriptionPlan->trial}}">
                            <span data-input="trial" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Interval</label>
                            <select class="form-control" name="interval" readonly>
                                @foreach (\App\Models\SubscriptionPlan::INTERVALS as $interval)
                                    <option value="{{$interval}}" @selected($subscriptionPlan->interval == $interval)>{{ucfirst($interval)}}</option>
                                @endforeach
                            </select>
                            <span data-input="role" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" type="text" class="form-control">{{$subscriptionPlan->description}}</textarea>
                            <span data-input="description" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Features</label>
                            <select class="form-control select2" name="features[]" multiple>
                                @foreach (\App\Models\SubscriptionFeature::all() as $f)
                                    <option value="{{$f->id}}" @selected($subscriptionPlan->features->pluck('id')->contains($f->id))>{{$f->text}}</option>
                                @endforeach
                            </select>
                            <span data-input="role" class="input-error"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success min-w-100">Save</button>
        <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-outline-secondary text-dark min-w-100">Cancel</a>
    </form>
@endsection
