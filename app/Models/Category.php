<?php

namespace App\Models;

use App\Traits\GetAllSlugs;
use App\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\DataTables;

class Category extends Model
{
    use GetAllSlugs;
    use HasAttachments;

    const ATTACHMENTS = [
        'meta_thumbnail',
    ];

    protected $fillable = [
        'in_menu',
        'name',
        'description',
        'order',
        'slug',
        'meta_description',
        'meta_title',
    ];

    // overload laravel`s method for route key generation
    public function getRouteKey()
    {
        return $this->slug;
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function meta_thumbnail()
    {
        return $this->morphToMany(Attachment::class, 'attachmentable')->where('attachmentables.group', 'meta_thumbnail')->first();
    }

    public function paginationLink($page, $includeQueryParams = [])
    {
        $tagSlug = request()->route('tagSlug');

        $routeData = ['category' => $this];

        if ($tagSlug) {
            $routeData['tagSlug'] = $tagSlug;
        }

        if ($page != 1) {
            $routeData['page'] = "page-$page";
        }

        foreach ($includeQueryParams as $param) {
            if (request()->$param) {
                $routeData[$param] = request()->$param;
            }
        }

        return route($tagSlug ? 'categories.show.tag' : 'categories.show', $routeData);
    }

    public static function forHeader()
    {
        return self::where('in_menu', true)->orderBy('order')->get();
    }

    public static function dataTable($query)
    {
        return DataTables::of($query)
            ->addColumn('name', function ($model) {
                return $model->name;
            })
            ->addColumn('in_menu', function ($model) {
                return $model->in_menu
                    ? '<span class="badge badge-success">yes</span>'
                    : '<span class="badge badge-warning">no</span>';
            })
            ->editColumn('created_at', function ($model) {
                return $model->created_at->format(env('ADMIN_DATETIME_FORMAT'));
            })
            ->addColumn('action', function ($model) {
                return view('components.admin.actions', [
                    'model' => $model,
                    'name' => 'categories',
                ])->render();
            })
            ->rawColumns(['in_menu', 'action'])
            ->make(true);
    }
}
