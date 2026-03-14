<div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Actions
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a href="{{route("admin.feedbacks.show", $model)}}" class="dropdown-item">View</a>
        <button data-link="{{route("admin.feedbacks.destroy", $model)}}" type="button" class="delete-resource dropdown-item">Delete</button>
        <div class="dropdown-divider"></div>
        @foreach (\App\Enums\FeedbackStatus::all() as $key => $value)
            <button data-url="{{route('admin.feedbacks.update', $model)}}" type="button" data-key="status" data-value="{{$key}}" class="update-resource dropdown-item {{$model->status->value == $key ? 'disabled' : ''}}">Mark as {{$value}}</button>
        @endforeach
        <div class="dropdown-divider"></div>
        <form action="{{route('admin.feedbacks.spam-by-ip', $model)}}" method="post" class="general-ajax-submit ask" data-asktext="All feedbacks from this IP will be marked as Spam and IP itself will be banned">
            @csrf
            <button type="submit" class="dropdown-item">Mark as Spam by IP</button>
        </form>
    </div>
  </div>