<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(Request $request)
    {
        $settings = $request->s;

        foreach ($request->s as $key => $value) {
            Setting::set($key, $value);
        }

        return $this->jsonSuccess('Settings udpated successfully');
    }
}
