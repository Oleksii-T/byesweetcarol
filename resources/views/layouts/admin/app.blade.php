@extends('adminlte::page')

@section('css')
    <link rel="stylesheet" href="/css/admin/custom.css">
    <link rel="stylesheet" href="/css/admin/rich-image-input.css?v={{time()}}">
    <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.css" type='text/css'>
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.css">
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/theme/monokai.css">
    @stack('styles')
@stop

@section('js')
    <div class="modal fade show" id="select-image" data-url="{{route('admin.attachments.images')}}">
        <div class="modal-dialog modal-xl">
            <div method="post" class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Select attachment</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    {{-- <button type="submit" class="btn btn-primary">Save changes</button> --}}
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade show" id="edit-image-meta">
        <div class="modal-dialog">
            <div method="post" class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit image meta</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Alt</label>
                                <input type="text" class="form-control" name="alt">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" class="form-control" name="title">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary rii-newimage-submit">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/mode/xml/xml.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/codemirror/2.36.0/formatting.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.15.1/beautify.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.15.1/beautify-css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.15.1/beautify-html.min.js"></script>
    <script type="text/javascript">
        window.Laravel = {!!$LaravelDataForJS!!};
    </script>
    <script src="/js/admin/custom.js?v={{time()}}"></script>
    <script src="/js/admin/rich-image-input.js?v={{time()}}"></script>
    @stack('scripts')
@stop
