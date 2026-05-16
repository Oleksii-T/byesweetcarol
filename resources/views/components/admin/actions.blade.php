{{-- Display general actions for admin panel data tables. Default actions is 'show' and 'destroy' --}}
<div class="table-actions d-flex align-items-center">
    @if (!isset($actions) || in_array('edit', $actions??[]))
        <a href="{{route("admin.$name.edit", $model)}}" class="btn btn-primary btn-sm mr-1">Edit</a>
    @endif
    @if (in_array('show', $actions??[]))
        <a href="{{route("admin.$name.show", $model)}}" class="btn btn-primary btn-sm mr-1">View</a>
    @endif
    @if (!isset($actions) || in_array('destroy', $actions??[]))
        <button data-link="{{route("admin.$name.destroy", $model)}}" type="button" class="delete-resource btn btn-danger btn-sm">Delete</button>
    @endif
</div>
