<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AttachmentRequest;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->ajax()) {
            return view('admin.attachments.index');
        }

        $attachments = Attachment::query();

        if ($request->type !== null) {
            $attachments->where('type', $request->type);
        }

        if ($request->has_entity == 1) {
            $attachments->whereHas('attachmentables');
        }

        if ($request->has_entity == 2) {
            $attachments->whereDoesntHave('attachmentables');
        }

        return Attachment::dataTable($attachments);
    }

    public function edit(Attachment $attachment)
    {
        $resources = $attachment->attachmentables()->latest()->get();

        return view('admin.attachments.edit', compact('attachment', 'resources'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'image', 'max:5000'],
        ]);

        $disk = 'aimages';
        $type = 'image';
        $file = $request->file;
        $og_name = Attachment::makeUniqueName($file->getClientOriginalName(), $disk);

        $path = $file->storeAs('', $og_name, $disk);
        $alt = readable(strstr($og_name, '.', true));
        $title = $alt;

        // create new attachment
        $a = Attachment::create([
            'name' => $path,
            'original_name' => $file->getClientOriginalName(),
            'type' => $type,
            'alt' => $alt,
            'title' => $title,
            'size' => $file->getSize(),
        ]);

        return $this->jsonSuccess('', [
            'url' => $a->url,
            'title' => $a->title,
            'alt' => $a->alt,
        ]);
    }

    public function update(AttachmentRequest $request, Attachment $attachment)
    {
        $input = $request->validated();

        $attachment->update([
            'original_name' => $input['original_name'],
            'alt' => $input['alt'],
            'title' => $input['title'],
        ]);

        if ($input['new_file']['file'] ?? null) {
            $attachment->getStorage()->delete($attachment->name);
            $disk = 'aimages';
            $file = $input['new_file']['file'];
            $og_name = Attachment::makeUniqueName($file->getClientOriginalName(), $disk);
            $path = $file->storeAs('', $og_name, $disk);
            $attachment->update([
                'name' => $path,
                'size' => $file->getSize(),
            ]);
        } elseif ($input['name'] != $attachment->name) {
            $storage = $attachment->getStorage();
            $storage->move($attachment->name, $input['name']);
            $attachment->update([
                'name' => $input['name'],
            ]);
        }

        return $this->jsonSuccess('Attachment updated successfully');
    }

    public function download(Request $request, Attachment $attachment)
    {
        $disk = Attachment::disk($attachment->type);

        return Storage::disk($disk)->download($attachment->name);
    }

    public function images(Request $request)
    {
        $search = $request->search;
        $sort = $request->sort;
        $images = Attachment::query()
            ->where('type', 'image')
            ->when($search, fn ($q) => $q->where('name', 'like', "%$search%")->orWhere('original_name', 'like', "%$search%")
            )
            ->when(true, function ($q) use ($sort) {
                if (! $sort) {
                    $q->latest();
                } elseif ($sort == 1) {
                    $q->orderBy('id');
                } elseif ($sort == 2) {
                    $q->orderByDesc('size');
                } elseif ($sort == 3) {
                    $q->orderBy('size');
                } elseif ($sort == 4) {
                    $q->orderBy('name');
                }
            })
            ->paginate(12);

        return $this->jsonSuccess('', [
            'html' => view('components.admin.images', compact('images'))->render(),
        ]);
    }

    public function destroy(Request $request, Attachment $attachment)
    {
        $attachment->delete();

        return $this->jsonSuccess('Blog updated successfully');
    }
}
