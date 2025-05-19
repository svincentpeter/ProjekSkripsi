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
        $roles = Role::orderBy('id', 'DESC')->paginate(5);
        return view('backend.RolesAndPermissions.index', compact('roles'));
    }

    public function createRole()
    {
        $permissions = Permission::all();
        $users       = User::select('name', 'id')->get();
        return view('backend.RolesAndPermissions.CreateRoles', compact('permissions', 'users'));
    }

    public function create(Request $request)
    {
        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permission);
        foreach ($request->users as $userId) {
            User::find($userId)->assignRole($role->name);
        }
        return redirect()->route('show-roles');
    }

    public function editRole($id)
    {
        $role        = Role::with('permissions', 'users')->findOrFail($id);
        $permissions = Permission::all();
        $users       = User::select('name', 'id')->get();
        return view('backend.RolesAndPermissions.EditRole', compact('role', 'permissions', 'users'));
    }

    public function updateRole(Request $request)
    {
        $role = Role::findOrFail($request->id);
        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permission);

        // Re-assign roles to users
        DB::table('model_has_roles')->where('role_id', $role->id)->delete();
        foreach ($request->users as $userId) {
            User::find($userId)->assignRole($role->name);
        }

        return redirect()->route('show-roles');
    }

    public function delete($id)
    {
        Role::destroy($id);
        return redirect()->route('show-roles');
    }
}
