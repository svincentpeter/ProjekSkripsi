<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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
        $users = User::select('name', 'id')->get();
        return view('backend.RolesAndPermissions.CreateRoles', compact('permissions', 'users'));
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:100|unique:roles,name',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'string|exists:permissions,name',
            'users'       => 'nullable|array',
            'users.*'     => 'exists:users,id',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        if ($request->filled('users')) {
            foreach ($request->users as $userId) {
                User::find($userId)?->assignRole($role->name);
            }
        }

        return redirect()->route('roles.index')->with('message', 'Role berhasil dibuat!');
    }

    public function editRole($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $users = User::select('name', 'id')->get();

        // Get assigned users for this role
        $assignedUserIds = DB::table('model_has_roles')
            ->where('role_id', $role->id)
            ->where('model_type', User::class)
            ->pluck('model_id')->toArray();

        return view('backend.RolesAndPermissions.EditRole', compact('role', 'permissions', 'users', 'assignedUserIds'));
    }

    public function updateRole(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:100|unique:roles,name,' . $role->id,
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'string|exists:permissions,name',
            'users'       => 'nullable|array',
            'users.*'     => 'exists:users,id',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        // Remove role from all users first
        foreach (User::role($role->name)->get() as $user) {
            $user->removeRole($role->name);
        }
        // Re-assign role to selected users
        if ($request->filled('users')) {
            foreach ($request->users as $userId) {
                User::find($userId)?->assignRole($role->name);
            }
        }

        return redirect()->route('roles.index')->with('message', 'Role berhasil diupdate!');
    }

    public function delete($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return redirect()->route('roles.index')->with('message', 'Role berhasil dihapus!');
    }
}
