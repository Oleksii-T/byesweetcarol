<?php

namespace App\Models;

use App\Enums\CommentStatus;
use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\DataTables;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'post_id',
        'comment_id',
        'status',
        'text',
    ];

    protected $casts = [
        'status' => CommentStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function parent()
    {
        $this->belongsTo(Comment::class);
    }

    public function replies()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(CommentLike::class);
    }

    public function scopeVisible($query)
    {
        return $query->where(fn ($q) => $q->where('status', CommentStatus::APPROVED)->orWhere('user_id', auth()->id())
        );
    }

    public static function getCommentTree($comment)
    {
        $comment->liked = $comment->likes()->where('ip', request()->ip())->exists();
        $comment->replies = $comment->replies()
            ->withCount('likes')
            ->visible()
            ->latest()
            ->get()
            ->each(function ($reply) {
                self::getCommentTree($reply);
            });

        return $comment;
    }

    public static function dataTable($query)
    {
        return DataTables::of($query)
            ->addColumn('user', function ($model) {
                $u = $model->user;
                $url = route('admin.users.edit', $u);

                return "<a href='$url'>$u->name</a>";
            })
            ->addColumn('post', function ($model) {
                $p = $model->post;
                $url = route('admin.posts.edit', $p);

                return "<a href='$url'>$p->title</a>";
            })
            ->editColumn('created_at', function ($model) {
                return $model->created_at->format(env('ADMIN_DATETIME_FORMAT'));
            })
            ->addColumn('action', function ($model) {
                return view('admin.comments.actions', compact('model'))->render();
            })
            ->rawColumns(['user', 'post', 'action'])
            ->make(true);
    }
}
