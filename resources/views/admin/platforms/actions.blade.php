<div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Actions
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <button data-link="{{route("admin.platforms.destroy", $model)}}" type="button" class="delete-resource dropdown-item">Delete</button>
        <button class="dropdown-item" data-toggle="modal" data-target="#edit-platform-{{$model->id}}">Edit</button>
    </div>
</div>

<div class="modal fade" id="edit-platform-{{$model->id}}">
    <div class="modal-dialog">
        <form action="{{route('admin.platforms.update', $model)}}" method="post" class="modal-content general-ajax-submit">
            @csrf
            @method('PUT')
            <input type="hidden" name="is_active" value="0">
            <div class="modal-header">
                <h4 class="modal-title">Edit platform</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" value="{{$model->name}}">
                            <span data-input="name" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Slug</label>
                            <input type="text" class="form-control" name="slug" value="{{$model->slug}}">
                            <span data-input="slug" class="input-error"></span>
                        </div>
                    </div>
                    <div class="col-12">
                        <x-admin.rich-image-input name="icon" :file="$model->icon()" />
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
