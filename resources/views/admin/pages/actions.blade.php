<div class="table-actions d-flex align-items-center">
    <a href="{{route("admin.pages.edit", $model)}}" class="btn btn-primary btn-sm mr-1">Edit</a>
    <button data-link="{{route("admin.pages.destroy", $model)}}" type="button" class="delete-resource btn btn-danger btn-sm mr-1">Delete</button>
    @if ($model->status != \App\Enums\PageStatus::ENTITY)
        <a href="{{url($model->link)}}" target="_blank" class="btn btn-default btn-sm">View</a>
    @endif
</div>
