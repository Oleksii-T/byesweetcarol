@extends('admin.layouts.app')

@section('title', 'Edit Feature')

@section('content_header')
    <x-admin.title
        text="Edit Feature"
        :bcRoute="['admin.subscription-features.create', $subscriptionFeature]"
    />
@stop

@section('content')
    <form action="{{route('admin.subscription-features.update', $subscriptionFeature)}}" method="POST" class="general-ajax-submit">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Text</label>
                            <input name="text" type="text" class="form-control" value="{{$subscriptionFeature->text}}">
                            <span data-input="text" class="input-error"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success min-w-100">Save</button>
        <a href="{{ route('admin.subscription-features.index') }}" class="btn btn-outline-secondary text-dark min-w-100">Cancel</a>
    </form>
@endsection
