<div class="table-actions d-flex align-items-center">
    <a href="{{route("admin.comments.edit", $model)}}" class="btn btn-primary btn-sm mr-1">Edit</a>
    <button data-link="{{route("admin.comments.destroy", $model)}}" type="button" class="delete-resource btn btn-danger btn-sm mr-1">Delete</button>
    @if ($model->status == \App\Enums\CommentStatus::PENDING)
        <form action="{{route('admin.comments.update-status', $model)}}" method="post" class="comment-update-status-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="{{\App\Enums\CommentStatus::APPROVED->value}}">
            <button type="submit" class="btn btn-success btn-sm mr-1">Approve</button>
        </form>
        <form action="{{route('admin.comments.update-status', $model)}}" method="post" class="comment-update-status-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="{{\App\Enums\CommentStatus::REJECTED->value}}">
            <button type="submit" class="btn btn-warning btn-sm mr-1">Reject</button>
        </form>
    @endif
</div>
