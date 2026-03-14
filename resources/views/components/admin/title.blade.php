<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <div class="float-left">
                <h1 class="m-0">{{$text}}</h1>
            </div>
            @if (isset($button))
                <div class="float-left pl-3">
                    <a href="{{$button[1]}}"
                    class="btn btn-primary">{{$button[0]}}
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
