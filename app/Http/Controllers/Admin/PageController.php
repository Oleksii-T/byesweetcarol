<?php

namespace App\Http\Controllers\Admin;

use App\Actions\GenerateSitemap;
use App\Actions\SaveContentBlocks;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageRequest;
use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->ajax()) {
            return view('admin.pages.index');
        }

        return Page::dataTable(Page::query());
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(PageRequest $request)
    {
        $input = $request->validated();
        Page::create($input);

        Page::getAllSlugs(true);
        GenerateSitemap::run();

        return $this->jsonSuccess('Page created successfully', [
            'redirect' => route('admin.pages.index'),
        ]);
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(PageRequest $request, Page $page)
    {
        $input = $request->validated();
        $page->update($input);

        Page::getAllSlugs(true);
        GenerateSitemap::run();

        return $this->jsonSuccess('Page updated successfully');
    }

    public function template(Page $page)
    {
        return view('admin.pages.template', compact('page'));
    }

    public function updateTemplate(Request $request, Page $page)
    {
        $disk = Storage::disk('pages');
        foreach ($request->blocks as $id => $block) {
            foreach ($block as $item_name => $item) {
                if ($item['type'] == 'dynamic') {
                    $tmp_item = ['blocks' => [], 'type' => 'dynamic'];
                    foreach ($item['blocks'] as $name => $tmp_block) {
                        foreach ($tmp_block['type'] as $number => $type) {
                            if ($type == 'video') {
                                $tmp_item['blocks'][$number][$name] = [
                                    'value' => $tmp_block['value'][$number],
                                    'src' => $tmp_block['src'][$number],
                                    'type' => $type,
                                ];
                            } elseif ($type == 'image') {
                                $tmp_item['blocks'][$number][$name] = [
                                    'value' => isset($tmp_block['value'][$number])
                                        ? $disk->putFile('', $tmp_block['value'][$number])
                                        : $tmp_block['value_old'][$number],
                                    'type' => $type,
                                ];
                            } else {
                                $tmp_item['blocks'][$number][$name] = [
                                    'value' => $tmp_block['value'][$number],
                                    'type' => $type,
                                ];
                            }
                        }
                    }
                    $block[$item_name] = $tmp_item;
                } elseif ($item['type'] == 'image') {
                    $block[$item_name]['value'] = isset($item['value'])
                        ? $disk->putFile('', $item['value'])
                        : $item['value_old'];
                } elseif ($item['type'] == 'editor') {
                    $block[$item_name]['value'] = sanitizeHtml($block[$item_name]['value']);
                }
            }

            PageBlock::findOrFail($id)->update([
                'data' => $block,
            ]);
        }

        return $this->jsonSuccess('Page updated successfully', [
            'redirect' => route('admin.pages.edit', $page),
        ]);
    }

    public function blocks(Page $page)
    {
        $blocks = $page->blocks()->with('items')->get();
        $blocksA = $blocks->toArray();
        $appData = json_encode([
            'itemTypes' => \App\Enums\BlockItemType::all(),
            'model' => [
                'id' => $page->id,
                'blocks' => $blocksA,
            ],
            'submitUrl' => route('admin.pages.update-blocks', $page),
        ]);

        return view('admin.pages.blocks', compact('appData', 'page'));
    }

    public function updateBlocks(Request $request, Page $page)
    {
        $data = $request->validate([
            'blocks' => ['required', 'array', new \App\Rules\ValidBlocksRule],
        ]);

        \DB::transaction(fn () => SaveContentBlocks::run($page, $data));

        return $this->jsonSuccess('Page content saved successfully', $page);
    }

    public function destroy(Page $page)
    {
        if ($page->notDynamic()) {
            return $this->jsonError('Can not delete static page', [], 200);
        }

        $page->delete();

        return $this->jsonSuccess('Page deleted successfully');
    }
}
