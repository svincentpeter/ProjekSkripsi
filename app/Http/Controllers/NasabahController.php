<?php

namespace App\Http\Controllers;

use App\Http\Requests\NasabahRequest;
use App\Models\Anggota;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NasabahController extends Controller
{
    // Tampilkan semua nasabah (paginated)
    public function index()
    {
        // Menggunakan Eloquent supaya Carbon otomatis
        $nasabah = Anggota::with('user')->orderBy('updated_at', 'desc')->paginate(5);
        return view('backend.nasabah.index', compact('nasabah'));
    }

    // Tampilkan form create
    public function create()
    {
        return view('backend.nasabah.create');
    }

    // Simpan data baru
    public function store(NasabahRequest $request)
    {
        DB::beginTransaction();
        try {
            // Handle gambar
            $imageName = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('assets/backend/img'), $imageName);
            }

            // Buat user baru
            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'image'     => $imageName,
            ]);

            // Buat anggota (nasabah)
            Anggota::create([
                'user_id'        => $user->id,
                'name'           => $request->name,
                'nip'            => $request->nip,
                'telphone'       => $request->telphone,
                'agama'          => $request->agama,
                'jenis_kelamin'  => $request->jenis_kelamin,
                'tgl_lahir'      => $request->tgl_lahir,
                'pekerjaan'      => $request->pekerjaan,
                'alamat'         => $request->alamat,
                'image'          => $imageName,
                'status_anggota' => 0, // default non-aktif, bisa ganti ke 1 jika mau langsung aktif
                'saldo'          => 0,
                'created_by'     => Auth::id(),
                'updated_by'     => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('nasabah.index')->with('message', 'Data Nasabah berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal menyimpan data: '.$e->getMessage());
        }
    }

    // Tampilkan detail nasabah
    public function show($id)
    {
        $anggota = Anggota::with('user')->findOrFail($id);
        return view('backend.nasabah.show', compact('anggota'));
    }

    // Tampilkan form edit
    public function edit($id)
    {
        $nasabah = Anggota::with('user')->findOrFail($id);
        return view('backend.nasabah.edit', [
            'nasabah' => $nasabah,
            'user'    => $nasabah->user,
        ]);
    }

    // Proses update data nasabah
    public function update(NasabahRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $anggota = Anggota::findOrFail($id);
            $user = $anggota->user;

            // Update data User
            $user->name  = $request->name;
            $user->email = $request->email;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            // Handle update foto user jika ada
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('assets/backend/img'), $imageName);
                $user->image = $imageName;
                $anggota->image = $imageName;
            }
            $user->save();

            // Update data Anggota
            $anggota->update([
    'name'           => $request->name,
    'nip'            => $request->nip,
    'telphone'       => $request->telphone,
    'agama'          => $request->agama,
    'jenis_kelamin'  => $request->jenis_kelamin,
    'tgl_lahir'      => $request->tgl_lahir,
    'pekerjaan'      => $request->pekerjaan,
    'alamat'         => $request->alamat,
    'status_anggota' => $request->status_anggota, // tambahkan ini
    'updated_by'     => Auth::id(),
]);


            DB::commit();
            return redirect()->route('nasabah.index')->with('message', 'Data Nasabah berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal mengupdate data: '.$e->getMessage());
        }
    }

    // Hapus nasabah
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $anggota = Anggota::findOrFail($id);
            // Hapus user sekalian
            $user = $anggota->user;
            $anggota->delete();
            if ($user) {
                $user->delete();
            }
            DB::commit();
            return redirect()->route('nasabah.index')->with('message', 'Data Nasabah berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menghapus data: '.$e->getMessage());
        }
    }
}
