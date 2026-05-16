@extends('layouts.admin.app')

@section('title', 'Edit post Content')

@push('styles')

@endpush

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <div class="float-left">
                    <h1 class="m-0">Edit Post #{{$post->id}}</h1>
                </div>
                <x-admin.post-nav active="faqs" :post="$post" />
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="m-0" style="display: inline-block;padding-right: 10px">
                Post FAQs
            </h5>
            <button class="btn btn-success" data-toggle="modal" data-target="#add-faq">+ Add FAQ</button>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="ids-column">Order</th>
                        <th>Question</th>
                        <th>Answer</th>
                        <th class="actions-column-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($post->faqs as $faq)
                        <tr>
                            <td>{{$faq->order}}</td>
                            <td>{{$faq->question}}</td>
                            <td>{!!$faq->answer!!}</td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-primary mr-2" data-toggle="modal" data-target="#edit-faq-{{$faq->id}}">
                                        Edit
                                    </button>
                                    <button class="btn btn-danger delete-resource" data-link="{{route('admin.posts.destroy-faq', [$post, $faq])}}">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade show" id="add-faq">
        <div class="modal-dialog">
            <form action="{{route('admin.posts.store-faq', $post)}}" method="post" class="modal-content general-ajax-submit">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Create FAQ</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Question</label>
                                        <input type="text" class="form-control" name="question">
                                        <span data-input="question" class="input-error"></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Answer</label>
                                        <textarea name="answer" class="form-control summernote"></textarea>
                                        <span data-input="answer" class="input-error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-light" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-outline-light">Create FAQ</button>
                </div>
            </form>
        </div>
    </div>

    @foreach ($post->faqs as $faq)
        <div class="modal fade show" id="edit-faq-{{$faq->id}}">
            <div class="modal-dialog">
                <form action="{{route('admin.posts.update-faq', [$post, $faq])}}" method="post" class="modal-content general-ajax-submit">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Edit FAQ</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Question</label>
                                            <input type="text" class="form-control" name="question" value="{{$faq->question}}">
                                            <span data-input="question" class="input-error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Order</label>
                                            <input type="text" class="form-control" name="order" value="{{$faq->order}}">
                                            <span data-input="order" class="input-error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Answer</label>
                                            <textarea name="answer" class="form-control summernote">{{$faq->answer}}</textarea>
                                            <span data-input="answer" class="input-error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-outline-light" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-outline-light">Update FAQ</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $(document).on('click', '.delete-resource', function (e) {
                e.preventDefault();
                deleteResource(null, $(this).data('link'));
            });
        });
    </script>
@endpush
