<?php

namespace App\Http\Controllers\Admin;

use App\Actions\GenerateSitemap;
use App\Actions\SaveContentBlocks;
use App\Enums\BlockItemType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AuthorRequest;
use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->ajax()) {
            return view('admin.authors.index');
        }

        $authors = Author::query();

        return Author::dataTable($authors);
    }

    public function create()
    {
        return view('admin.authors.create');
    }

    public function store(AuthorRequest $request)
    {
        $input = $request->validated();
        $input['description'] = sanitizeHtml($input['description']);
        $input['description_small'] = sanitizeHtml($input['description_small']);

        $author = Author::create($input);
        $author->addAttachment($input['meta_thumbnail'], 'meta_thumbnail');
        $author->addAttachment($input['avatar'], 'avatar');

        Author::getAllSlugs(true);
        GenerateSitemap::run();

        return $this->jsonSuccess('Author created successfully', [
            'redirect' => route('admin.authors.index'),
        ]);
    }

    public function edit(Author $author)
    {
        return view('admin.authors.edit', compact('author'));
    }

    public function update(AuthorRequest $request, Author $author)
    {
        $input = $request->validated();
        $input['description'] = sanitizeHtml($input['description']);
        $input['description_small'] = sanitizeHtml($input['description_small']);

        $author->update($input);
        $author->addAttachment($input['avatar'] ?? false, 'avatar');
        $author->addAttachment($input['meta_thumbnail'] ?? false, 'meta_thumbnail');

        Author::getAllSlugs(true);
        GenerateSitemap::run();

        return $this->jsonSuccess('Author updated successfully');
    }

    public function socials(Author $author)
    {
        return view('admin.authors.socials', compact('author'));
    }

    public function updateSocials(Request $request, Author $author)
    {
        $data = $request->validate([
            'facebook' => ['nullable', 'string', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'youtube' => ['nullable', 'string', 'max:255'],
            'twitter' => ['nullable', 'string', 'max:255'],
            'steam' => ['nullable', 'string', 'max:255'],
            'linkedin' => ['nullable', 'string', 'max:255'],
        ]);

        $author->update($data);

        return $this->jsonSuccess('Socials updated successfully');
    }

    public function blocks(Author $author)
    {
        $blocks = $author->blocks()->with('items')->get();
        $blocksA = $blocks->toArray();
        $appData = json_encode([
            'itemTypes' => BlockItemType::all(),
            'model' => [
                'id' => $author->id,
                'blocks' => $blocksA,
            ],
            'submitUrl' => route('admin.authors.update-blocks', $author),
        ]);

        return view('admin.authors.blocks', compact('appData', 'author'));
    }

    public function updateBlocks(Request $request, Author $author)
    {
        $data = $request->validate([
            'blocks' => ['required', 'array', new \App\Rules\ValidBlocksRule],
        ]);

        \DB::transaction(fn () => SaveContentBlocks::run($author, $data));

        return $this->jsonSuccess('Author content saved successfully', $author);
    }

    public function destroy(Author $author)
    {
        $author->delete();

        return $this->jsonSuccess('Author deleted successfully');
    }
}
