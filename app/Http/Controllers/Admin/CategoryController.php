<?php

namespace App\Http\Controllers\Admin;

use App\Actions\GenerateSitemap;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->ajax()) {
            return view('admin.categories.index');
        }

        $categories = Category::query();

        return Category::dataTable($categories);
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(CategoryRequest $request)
    {
        $input = $request->validated();
        if (! ($input['order'] ?? null)) {
            $input['order'] = Category::max('order') + 1;
        }
        $input['description'] = sanitizeHtml($input['description']);

        $category = Category::create($input);
        $category->addAttachment($input['meta_thumbnail'], 'meta_thumbnail');

        Category::getAllSlugs(true);
        GenerateSitemap::run();

        return $this->jsonSuccess('Category created successfully', [
            'redirect' => route('admin.categories.index'),
        ]);
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $input = $request->validated();
        $input['in_menu'] = $input['in_menu'] ?? false;
        $input['description'] = sanitizeHtml($input['description']);

        $category->update($input);
        $category->addAttachment($input['meta_thumbnail'] ?? null, 'meta_thumbnail');

        Category::getAllSlugs(true);
        GenerateSitemap::run();

        return $this->jsonSuccess('Category updated successfully');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        Category::getAllSlugs(true);
        GenerateSitemap::run();

        return $this->jsonSuccess('Category deleted successfully');
    }
}
