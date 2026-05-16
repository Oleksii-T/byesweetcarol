<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Yajra\DataTables\DataTables;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin()
    {
        return (bool) $this->roles()->where('name', 'admin')->count();
    }

    public function isWriter($strict = false)
    {
        $roles = ['writer'];

        if (! $strict) {
            $roles[] = 'admin';
        }

        return (bool) $this->roles()->whereIn('name', $roles)->count();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public static function simpleGet($name, $email)
    {
        return self::query()
            // ->where('name', $name)
            ->where('email', $email)
            ->first();
    }

    public static function dataTable($query)
    {
        return DataTables::of($query)
            ->addColumn('name', function ($model) {
                return $model->name;
            })
            ->editColumn('created_at', function ($model) {
                return $model->created_at->format(env('ADMIN_DATETIME_FORMAT'));
            })
            ->addColumn('action', function ($model) {
                return view('components.admin.actions', [
                    'model' => $model,
                    'name' => 'users',
                ])->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
