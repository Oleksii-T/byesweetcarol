<div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Actions
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <button data-link="{{route("admin.feedback-bans.destroy", $model)}}" type="button" class="delete-resource dropdown-item">Delete</button>
        <button class="dropdown-item" data-toggle="modal" data-target="#edit-ban-{{$model->id}}">Edit</button>
    </div>
</div>

<div class="modal fade" id="edit-ban-{{$model->id}}">
    <div class="modal-dialog">
        <form action="{{route('admin.feedback-bans.update', $model)}}" method="post" class="modal-content general-ajax-submit">
            @csrf
            @method('PUT')
            <input type="hidden" name="is_active" value="0">
            <div class="modal-header">
                <h4 class="modal-title">Edit feedback ban</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Type</label>
                            <select class="table-filter form-control" name="type">
                                <option value="">Select status</option>
                                @foreach (\App\Models\FeedbackBan::TYPES as $type)
                                    <option value="{{$type}}" @selected($model->type == $type)>{{readable($type)}}</option>
                                @endforeach
                            </select>
                            <span data-input="type" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Value</label>
                            <input type="text" class="form-control" name="value" value="{{$model->id}}">
                            <span data-input="value" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Action</label>
                            <select class="table-filter form-control" name="action">
                                <option value="">Select action</option>
                                @foreach (\App\Models\FeedbackBan::ACTIONS as $action)
                                    <option value="{{$action}}" @selected($model->action == $action)>{{readable($action)}}</option>
                                @endforeach
                            </select>
                            <span data-input="action" class="input-error"></span>
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
