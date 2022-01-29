<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        // TODO refactor into UserDomain
        if ($request->has('all')) {
            $users = User::paginate(1)->withQueryString();
        } else {
            $users = User::has('roles')->paginate(1)->withQueryString();
        }

        $roles = Role::all();
        foreach ($users as $user) {
            $user->roles = $user->roles;
        }

        return Inertia::render('Role/Index', [
            'users' => $users,
            'roles' => $roles
        ]);
    }

    public function assign(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'numeric'],
            'role_id' => ['required', 'numeric'],
        ]);

        // TODO refactor into UserDomain

        $user = User::find($request->user_id);
        $role = Role::find($request->role_id);

        $user->roles()->detach();
        $user->roles()->attach($role->id);

        return Redirect::back();
    }
}
