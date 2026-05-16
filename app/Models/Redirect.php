<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\DataTables;

class Redirect extends Model
{
    protected $guarded = [];

    protected $casts = [
        'last_at' => 'datetime',
    ];

    public static function dataTable($query)
    {
        return DataTables::of($query)
            ->editColumn('last_at', function ($model) {
                return $model->last_at?->adminFormat();
            })
            ->editColumn('is_active', function ($model) {
                return $model->is_active ? 'Active' : 'Inactive';
            })
            ->addColumn('actions', function ($model) {
                return view('admin.redirects.actions', compact('model'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public static function getAll($reload = false)
    {
        $cKey = 'redirects';

        if ($reload) {
            cache()->forget($cKey);
        }

        $redirect = self::where('is_active', true)->get();

        return cache()->remember($cKey, 60 * 60 * 24, fn () => $redirect);
    }
}
