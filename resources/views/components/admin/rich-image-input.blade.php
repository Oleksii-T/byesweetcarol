<div class="form-group">
    <label>{{readable($name)}}</label>
    @if (!isset($multiple) || !$multiple)
        <div class="rii-wrapper" data-uuid="{{Str::uuid()}}">
            <input type="file" name="{{$name}}[file]" class="rii-filefile d-none">
            <input type="hidden" name="{{$name}}[id_old]" value="{{@$file->id}}">
            <input type="hidden" name="{{$name}}[id]" value="{{@$file->id}}" class="rii-fileid">
            <input type="hidden" name="{{$name}}[alt]" class="rii-filealt d-none">
            <input type="hidden" name="{{$name}}[title]" class="rii-filetitle d-none">
            <div class="rii-content">
                <div class="rii-box">
                    <img class="rii-filepreview {{isset($file) ? '' : 'd-none'}}" src="{{@$file->url}}" alt="">
                    <span class="rii-filename">
                        @if (isset($file))
                            {{$file->name}}
                        @else
                            Drag files here, or click to upload
                        @endif
                    </span>
                </div>
                <div>
                    @if (!isset($withoutbrowser))
                        <button type="button" class="rii-action-btn rii-action-browse" data-toggle="modal" data-target="#select-image">
                            <i class="fas fa-fw fa-folder-open"></i>
                        </button>
                    @endif
                    <button type="button" class="rii-action-btn rii-action-editnew d-none" data-toggle="modal" data-target="#edit-image-meta">
                        <i class="fas fa-fw fa-edit"></i>
                    </button>
                    @if (isset($file))
                        <a href="{{route('admin.attachments.edit', $file)}}" target="_blank" class="rii-action-btn">
                            <i class="fas fa-fw fa-edit"></i>
                        </a>
                        <a href="{{$file->url}}" target="_blank" class="rii-action-btn">
                            <i class="fas fa-fw fa-expand"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @else
        <button class="btn btn-success rii-action-add">+</button>
        <div class="row rii-multiple-wrapper">
            @foreach ((!isset($files) || $files->isEmpty()) ? [null] : $files as $file)
                <div class="col-6 rii-wrapper" data-uuid="{{Str::uuid()}}">
                    <input type="file" name="{{$name}}[file][]" class="rii-filefile d-none">
                    <input type="hidden" name="{{$name}}[id_old][]" value="{{@$file->id}}">
                    <input type="hidden" name="{{$name}}[id][]" value="{{@$file->id}}" class="rii-fileid">
                    <input type="hidden" name="{{$name}}[alt][]" class="rii-filealt d-none">
                    <input type="hidden" name="{{$name}}[title][]" class="rii-filetitle d-none">
                    <div class="rii-content">
                        <div class="rii-box">
                            <img class="rii-filepreview {{isset($file) ? '' : 'd-none'}}" src="{{@$file->url}}" alt="">
                            <span class="rii-filename">
                                @if (isset($file))
                                    {{$file->name}}
                                @else
                                    Drag files here, or click to upload
                                @endif
                            </span>
                        </div>
                        <div>
                            <button type="button" class="rii-action-btn rii-action-browse" data-toggle="modal" data-target="#select-image">
                                <i class="fas fa-fw fa-folder-open"></i>
                            </button>
                            <button type="button" class="rii-action-btn rii-action-editnew d-none" data-toggle="modal" data-target="#edit-image-meta">
                                <i class="fas fa-fw fa-edit"></i>
                            </button>
                            @if (isset($file))
                                <a href="{{route('admin.attachments.edit', $file)}}" target="_blank" class="rii-action-btn">
                                    <i class="fas fa-fw fa-edit"></i>
                                </a>
                                <a href="{{$file->url}}" target="_blank" class="rii-action-btn">
                                    <i class="fas fa-fw fa-expand"></i>
                                </a>
                            @endif
                            <button class="rii-action-btn rii-action-remove">
                                <i class="fas fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    <span data-input="{{$name}}" class="input-error"></span>
</div>