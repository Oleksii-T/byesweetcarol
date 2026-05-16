<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\DataTables;

class Feedback extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'name',
        'subject',
        'status',
        'text',
        'ip',
    ];

    protected $casts = [
        'status' => \App\Enums\FeedbackStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fromSameIp($asCount = true)
    {
        $ip = $this->ip;

        if ($asCount) {
            return self::where('ip', $ip)->count();
        }

        return self::where('ip', $ip)->get();
    }

    public static function dataTable($query)
    {
        return DataTables::of($query)
            ->addColumn('user', function ($model) {
                $user = $model->user;
                if (! $user) {
                    return "$model->name | $model->email";
                }

                return '<a href="'.route('admin.users.edit', $user).'">'.$user->name.'</a>';
            })
            ->editColumn('text', function ($model) {
                return strlen($model->text) > 250
                    ? (substr($model->text, 0, 250).'...')
                    : $model->text;
            })
            ->addColumn('ip', function ($model) {
                $ip = $model->ip;
                $count = $model->fromSameIp();

                return "$ip ($count)";
            })
            ->editColumn('created_at', function ($model) {
                return $model->created_at->adminFormat();
            })
            ->addColumn('action', function ($model) {
                return view('admin.feedbacks.actions', compact('model'))->render();
            })
            ->rawColumns(['user', 'action'])
            ->make(true);
    }
}
