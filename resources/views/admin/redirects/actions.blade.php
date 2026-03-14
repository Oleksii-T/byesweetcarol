<div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Actions
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <button data-link="{{route("admin.redirects.destroy", $model)}}" type="button" class="delete-resource dropdown-item">Delete</button>
        <button class="dropdown-item" data-toggle="modal" data-target="#edit-ban-{{$model->id}}">Edit</button>
    </div>
</div>

<div class="modal fade" id="edit-ban-{{$model->id}}">
    <div class="modal-dialog">
        <form action="{{route('admin.redirects.update', $model)}}" method="post" class="modal-content general-ajax-submit">
            @csrf
            @method('PUT')
            <input type="hidden" name="is_active" value="0">
            <div class="modal-header">
                <h4 class="modal-title">Edit redirect ban</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>From</label>
                            <input type="text" class="form-control" name="from" value="{{$model->from}}">
                            <span data-input="from" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>To</label>
                            <input type="text" class="form-control" name="to" value="{{$model->to}}">
                            <span data-input="to" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Code</label>
                            <input type="text" class="form-control" name="code" value="{{$model->code}}">
                            <span data-input="code" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Is Active</label>
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="is_active" name="is_active" value="1" @checked($model->is_active)>
                                <label for="is_active" class="custom-control-label">Yes</label>
                            </div>
                            <span data-input="is_active" class="input-error"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</div>
