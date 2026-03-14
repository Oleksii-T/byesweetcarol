<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TagStoreRequest;
use App\Http\Requests\Admin\TagTransferRequest;
use App\Http\Requests\Admin\TagUpdateRequest;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TagController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->ajax()) {
            return view('admin.tags.index');
        }

        $tags = Tag::query();

        return Tag::dataTable($tags);
    }

    public function create()
    {
        return view('admin.tags.create');
    }

    public function store(TagStoreRequest $request)
    {
        $input = $request->validated();
        // if (! ($input['order'] ?? null)) {
        //     $input['order'] = Tag::max('order') + 1;
        // }
        $tag = Tag::create($input);
        // $tag->saveTranslations($input);
        Tag::getAllSlugs(true);
        $this->resetCache();

        return $this->jsonSuccess('Tag created successfully', [
            'redirect' => route('admin.tags.index'),
        ]);
    }

    public function edit(Request $request, Tag $tag)
    {
        if (! $request->ajax()) {
            $otherTags = Tag::where('id', '!=', $tag->id)->latest()->get();

            return view('admin.tags.edit', compact('tag', 'otherTags'));
        }

        return Post::dataTable(Post::whereIn('id', $tag->postTags->pluck('post_id')));
    }

    public function transferPosts(TagTransferRequest $request, Tag $tag)
    {
        $data = $request->validated();

        foreach ($tag->posts as $post) {
            $post->tags()->syncWithoutDetaching([$data['tag_id']]);
            $post->tags()->detach($tag->id);
        }
        $this->resetCache();

        return $this->jsonSuccess('Tag created successfully', [
            'redirect' => route('admin.tags.index'),
        ]);
    }

    public function update(TagUpdateRequest $request, Tag $tag)
    {
        $input = $request->validated();

        if ($input['alter_names'] ?? null) {
            $input['alter_names'] = json_decode($input['alter_names'], true);
        }

        $tag->update($input);
        Tag::getAllSlugs(true);
        $this->resetCache();

        return $this->jsonSuccess('Tag updated successfully');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        $this->resetCache();

        return $this->jsonSuccess('Tag deleted successfully');
    }

    private function resetCache(): void
    {
        Cache::forget('top_tags');
    }
}
