<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlatformRequest;
use App\Models\Platform;
use Illuminate\Http\Request;

class PlatformController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->ajax()) {
            return view('admin.platforms.index');
        }

        $platforms = Platform::query();

        return Platform::dataTable($platforms);
    }

    public function store(PlatformRequest $request)
    {
        $data = $request->validated();

        $platform = Platform::create($data);
        $platform->addAttachment($data['icon'], 'icon');

        return $this->jsonSuccess('Platform created successfully!');
    }

    public function update(PlatformRequest $request, Platform $platform)
    {
        $data = $request->validated();
        $platform->update($data);

        if ($data['icon']) {
            $platform->addAttachment($data['icon'], 'icon');
        }

        if ($request->ajax()) {
            return $this->jsonSuccess('Platform updated successfully!');
        }

        return redirect()->back();
    }

    public function read(Request $request, Platform $Platform)
    {
        $Platform->update([
            'is_read' => true,
        ]);

        return redirect()->back();
    }

    public function destroy(Request $request, Platform $platform)
    {
        $platform->delete();

        return $this->jsonSuccess('Platform updated successfully');
    }
}
