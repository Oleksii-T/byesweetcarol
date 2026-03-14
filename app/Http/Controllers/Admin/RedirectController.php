<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Redirect;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->ajax()) {
            return view('admin.redirects.index');
        }

        $redirects = Redirect::when($request->status !== null, function ($q) {
            $q->where('status', request()->status);
        });

        return Redirect::dataTable($redirects);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'from' => ['required'],
            'to' => ['required'],
            'code' => ['required'],
        ]);
        $data['is_active'] = true;

        Redirect::create($data);
        Redirect::getAll(true);

        return $this->jsonSuccess('Redirect created successfully!', [
            'reload' => true,
        ]);
    }

    public function update(Request $request, Redirect $redirect)
    {
        $data = $request->validate([
            'from' => ['required'],
            'to' => ['required'],
            'code' => ['required'],
            'is_active' => ['required'],
        ]);

        $redirect->update($data);
        Redirect::getAll(true);

        return $this->jsonSuccess('Redirect updated successfully!', [
            'reload' => true,
        ]);
    }

    public function destroy(Request $request, Redirect $redirect)
    {
        $redirect->delete();
        Redirect::getAll(true);

        return $this->jsonSuccess('Redirect updated successfully');
    }
}
