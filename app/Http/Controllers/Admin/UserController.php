<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->ajax()) {
            return view('admin.users.index');
        }

        $users = User::query();

        if ($request->role !== null) {
            $users->whereHas('roles', function ($q) use ($request) {
                $q->where('roles.id', $request->role);
            });
        }

        return User::dataTable($users);
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(UserRequest $request)
    {
        $input = $request->validated();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $user->roles()->attach($input['roles']);

        return $this->jsonSuccess('User created successfully', [
            'redirect' => route('admin.users.index'),
        ]);
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user)
    {
        $input = $request->validated();
        if ($input['password']) {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }
        $user->update($input);
        $user->roles()->sync($input['roles']);

        return $this->jsonSuccess('User updated successfully');
    }

    public function destroy(User $user)
    {
        if ($user->id == auth()->id()) {
            return $this->jsonError('Can not delete current user', [], 200);
        }

        $user->delete();

        return $this->jsonSuccess('User deleted successfully');
    }
}
