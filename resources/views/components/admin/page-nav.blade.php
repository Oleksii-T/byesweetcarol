<div class="float-left pl-3">
    <a href="{{$active == 'general' ? '#' : route('admin.pages.edit', $page)}}" class="btn {{$active == 'general' ? 'btn-default' : 'btn-primary'}}">General</a>
</div>
@if ($page->isStatic())
    @if ($page->pageBlocks()->exists())
        <div class="float-left pl-3">
            <a href="{{$active == 'template' ? '#' : route('admin.pages.template', $page)}}" class="btn {{$active == 'template' ? 'btn-default' : 'btn-primary'}}">Template</a>
        </div>
    @endif
    <div class="float-left pl-3">
        <a href="{{$active == 'blocks' ? '#' : route('admin.pages.blocks', $page)}}" class="btn {{$active == 'blocks' ? 'btn-default' : 'btn-primary'}}">Blocks</a>
    </div>
@endif
