<?php

namespace App\Models;

use App\Traits\GetAllSlugs;
use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\DataTables;

class Tag extends Model
{
    use GetAllSlugs;

    protected $fillable = [
        'name',
        'alter_names',
        'slug',
        'updated_at', // fix for mass assignment
    ];

    protected $casts = [
        'alter_names' => 'array',
    ];

    // overload laravel`s method for route key generation
    public function getRouteKey()
    {
        return $this->slug;
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    public function postTags()
    {
        return $this->hasMany(PostTag::class);
    }

    public static function dataTable($query)
    {
        $query->withCount('posts');

        return DataTables::of($query)
            ->addColumn('name', function ($model) {
                return $model->name;
            })
            ->editColumn('created_at', function ($model) {
                return $model->created_at->format(env('ADMIN_DATETIME_FORMAT'));
            })
            ->addColumn('posts_count', function ($model) {
                return $model->posts_count;
            })
            ->addColumn('action', function ($model) {
                return view('components.admin.actions', [
                    'model' => $model,
                    'name' => 'tags',
                ])->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
