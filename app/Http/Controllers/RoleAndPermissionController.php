<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionController extends Controller
{
    public function show()
    {
        // $roles = Role::all();
        $roles = Role::orderBy('id', 'DESC')->paginate(5);
        return view('backend.RolesAndPermissions.index', compact('roles'));
    }

    public function createRole()
    {
        $permissions = Permission::all();
        $users = User::select('name', 'id')->get();
        return view('backend.RolesAndPermissions.CreateRoles', compact('permissions', 'users'));
    }
    public function create(Request $request)
    {
        $role = Role::create(['name' => $request->name]);

        foreach ($request->permission as $permission) {
            $role->givePermissionTo($permission);
        }

        foreach ($request->users as $user) {
            $user = User::find($user);
            $user->assignRole($role->name);
        }

        return redirect('show-roles');
    }

    public function editRole($id)
    {
        $role = Role::where('id', $id)
            ->with('permissions', 'users')
            ->first();
        $permissions = Permission::all();
        $users = User::select('name', 'id')->get();

        return view('backend.RolesAndPermissions.EditRole', compact('role', 'permissions', 'users'));
    }

    public function updateRole(Request $request)
    {
        $role = Role::where('id', $request->id)->first();
        $role->name = $request->name;
        $role->update();

        $role->syncPermissions($request->permission);

        DB::table('model_has_roles')->where('role_id', $request->id)->delete();

        foreach ($request->users as $user) {
            $user = User::find($user);
            $user->assignRole($role->name);
        }

        return redirect('show-roles');
    }

    public function delete($id)
    {
        Role::where('id', $id)->delete();
        return redirect('show-roles');
    }
}
