<div class="row mb-3">
    <div class="col-9">
        <input type="text" name="search" class="form-control rii-is-search" placeholder="Search..." value="{{request()->search}}">
    </div>
    <div class="col-3">
        <select name="sort" class="form-control rii-is-sort">
            <option value="" @selected(!request()->sort)>Latest</option>
            <option value="1" @selected(request()->sort == 1)>Oldest</option>
            <option value="2" @selected(request()->sort == 2)>Largest</option>
            <option value="3" @selected(request()->sort == 3)>Smallest</option>
            <option value="4" @selected(request()->sort == 4)>Alphabetic</option>
        </select>
    </div>
</div>
<div class="row rii-is-images">
    @foreach ($images as $image)
        <div class="col-3 mb-3 rii-is-image">
            <div class="rii-is-img" data-image="{{json_encode($image)}}">
                <img src="{{$image->url}}" alt="{{$image->alt}}" title="{{$image->title}}">
            </div>
            <div class="rii-is-legend">
                <span>{{$image->name}}</span>
                <span>({{$image->getSize()}})</span>
                <a href="{{route('admin.attachments.edit', $image)}}" target="_blank">&#9881;</a>
                <a href="{{$image->url}}" target="_blank">&#x1F50D;</a>
            </div>
        </div>
    @endforeach
</div>
<div class="rii-is-pagination">
    {{$images->links()}}
</div>