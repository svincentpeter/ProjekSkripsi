<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Requests\UsersUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        $users->map(function ($user) {
            $user->role_name = $user->roles->pluck('name')->implode(', ');
            return $user;
        });
        return view('backend.users.index', compact('users'));
    }

    public function create()
    {
        $roles = DB::table('roles')->get();
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

        // Simpan data ke database
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'image' => $imageName,
        ];

        $user = User::create($userData);
        $user->assignRole($request->input('roles'));

        return redirect()->route('user')->with('message', 'User berhasil disimpan!');
    }

    public function edit($id)
    {
        // Mengambil data user yang akan diedit berdasarkan ID menggunakan model User
        $edituser = User::find($id);

        // Mengambil semua roles yang tersedia
        $roles = Role::pluck('name', 'id'); // Menggunakan id sebagai value

        // Mengambil roles yang dimiliki oleh pengguna yang akan diedit
        $userRole = $edituser->roles->pluck('id')->all();
        // dd($edituser);
        // Arahkan ke halaman edit dengan data pengguna, roles, dan userRole
        return view('backend.users.edit', compact('edituser', 'roles', 'userRole'));
    }
    public function update(UsersUpdateRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('image')) {
            $oldImageName = $user->image;

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/backend/img'), $imageName);

            if ($oldImageName !== null) {
                $oldImagePath = public_path('assets/backend/img') . '/' . $oldImageName;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $data['image'] = $imageName;
        }

        if (!empty($request->password)) {
            $data['password'] = bcrypt($request->password);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        } else {
            $user->roles()->detach();
        }

        return redirect()->route('user')->with('message', 'User berhasil diperbarui!');
    }


    // public function destroy($id)
    // {
    //     DB::table('users')->where('id', $id)->delete();
    //     return redirect()->route('users.delete')->with('message', 'Users Berhasil Dihapus!');
    // }
    public function delete($id)
    {
        User::where('id', $id)->delete();
        return redirect('/users')->with('message', 'Users Berhasil Dihapus!');
    }

   
}
