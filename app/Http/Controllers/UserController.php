<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Requests\UsersUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
        }

        $users = User::with('roles')->paginate(10);

foreach ($users as $user) {
    $user->role_name = $user->roles->pluck('name')->implode(', ');
}


        return view('backend.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::pluck('name', 'id'); // ['id' => 'name']
        return view('backend.users.create', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        $imageName = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/backend/img'), $imageName);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'image' => $imageName,
        ]);

        // Assign role dengan ID, bukan nama
        $roleIds = $request->input('roles');
        $roleNames = Role::whereIn('id', $roleIds)->pluck('name')->toArray();
        $user->assignRole($roleNames);

        return redirect()->route('user')->with('message', 'User berhasil disimpan!');
    }

    public function edit($id)
    {
        $edituser = User::findOrFail($id);
        $roles = Role::pluck('name', 'id');
        $userRole = $edituser->roles->pluck('id')->all();
        return view('backend.users.edit', compact('edituser', 'roles', 'userRole'));
    }

    public function update(UsersUpdateRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->only(['name', 'email']);
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($user->image && file_exists(public_path('assets/backend/img/' . $user->image))) {
                unlink(public_path('assets/backend/img/' . $user->image));
            }
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/backend/img'), $imageName);
            $data['image'] = $imageName;
        }

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        if ($request->has('roles')) {
            $roleIds = $request->input('roles');
            $roleNames = Role::whereIn('id', $roleIds)->pluck('name')->toArray();
            $user->syncRoles($roleNames);
        }

        return redirect()->route('user')->with('message', 'User berhasil diperbarui!');
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete(); // Soft delete
        return redirect()->route('user')->with('message', 'User berhasil dihapus!');
    }
    
}
