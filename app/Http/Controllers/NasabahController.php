<?php

namespace App\Http\Controllers;

use App\Http\Requests\NasabahRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NasabahController extends Controller
{
   public function index()
{
    // Ambil data nasabah dengan paginasi
    $nasabah = DB::table('_anggota')->paginate(5);

    // Iterasi melalui setiap nasabah untuk memeriksa saldo mereka
    foreach ($nasabah as $nasabahItem) {
        if ($nasabahItem->saldo == 0) {
            // Update status_anggota menjadi non-aktif (0) jika saldo 0
            DB::table('_anggota')
                ->where('id', $nasabahItem->id)
                ->update(['status_anggota' => 0]);
        }
    }

    return view('backend.nasabah.index', compact('nasabah'));
}

    public function create()
    {
        return view('backend.nasabah.create');
    }


    public function store(NasabahRequest $request)
    {
        DB::beginTransaction();
        $imageName = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/backend/img'), $imageName);
        }

        try {
            // Create a new user
            $user = User::create([
                'name' => $request->nama,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'image' => $imageName, // Make sure the image storage is configured properly
                'created_at' => \Carbon\Carbon::now(),
            ]);

            // Determine status based on saldo
            $saldo = 0;
            $statusAnggota = $saldo == 0 ? 0 : 1; // Set to non-aktif if saldo is 0

            // Create a new nasabah related to the user
            DB::table('_anggota')->insert([
                'name' => $request->nama,
                'telphone' => $request->telphone,
                'nip' => $request->nip,
                'agama' => $request->agama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tgl_lahir' => $request->tgl_lahir,
                'pekerjaan' => $request->pekerjaan,
                'alamat' => $request->alamat,
                'image' => $imageName,
                'status_anggota' => $statusAnggota,
                'saldo' => $saldo,
                'tgl_gabung' => $request->tgl_gabung,
                'user_id' => $user->id,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('nasabah')->with('message', 'Data Nasabah Berhasil Disimpan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('nasabah')->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withErrors($e->getMessage());
        }
    }

    public function edit($id)
    {
        // Menemukan nasabah berdasarkan id
        $nasabah = DB::table('_anggota')->where('id', $id)->first();

        if (!$nasabah) {
            return redirect()->route('nasabah')->with('error', 'Nasabah tidak ditemukan');
        }

        // Menemukan pengguna berdasarkan user_id dari nasabah
        $user = DB::table('users')->where('id', $nasabah->user_id)->first();

        return view('backend.nasabah.edit', compact('nasabah', 'user'));
    }


    public function update(Request $request, $id)
    {
        // Validate request
        $this->validate($request, [
            'nama' => 'required',
            'email' => 'required|email',
            // Add other validations as needed
        ]);

        DB::beginTransaction();

        try {
            // Update user
            User::where('id', $id)->update([
                'name' => $request->nama,
                'email' => $request->email,
                // Update other fields as needed
            ]);

            // Update nasabah
            DB::table('_anggota')->where('user_id', $id)->update([
                'name' => $request->nama,
                'telphone' => $request->telphone,
                'nip' => $request->nip,
                'agama' => $request->agama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tgl_lahir' => $request->tgl_lahir,
                'pekerjaan' => $request->pekerjaan,
                'alamat' => $request->alamat,
                // Update other fields as needed
            ]);

            // Handle image update
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('assets/backend/img'), $imageName);

                // Update image field
                DB::table('_anggota')->where('user_id', $id)->update([
                    'image' => $imageName,
                ]);
            }

            DB::commit();

            return redirect()->route('nasabah')->with('message', 'Data Nasabah Berhasil Diupdate');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('nasabah')->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }
    public function show($id)
    {
        try {
            $anggota = DB::table('_anggota')
            ->select(
                'id',
                'user_id',
                'nip',
                'name',
                'telphone',
                'agama',
                'jenis_kelamin',
                'tgl_lahir',
                'pekerjaan',
                'alamat',
                'image',
                'status_anggota',
                'saldo',
                'tgl_gabung',
                'created_by',
                'updated_by',
                'created_at',
                'updated_at'
            )
                ->where('id', $id)
                ->first();

            if (!$anggota) {
                return redirect()->route('nasabah')->with('error', 'Anggota tidak ditemukan.');
            }

            return view('backend.nasabah.show', compact('anggota'));
        } catch (\Exception $e) {
            return redirect()->route('nasabah')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            // Delete the user and related nasabah data
            DB::table('users')->where('id', $id)->delete();
            DB::table('_anggota')->where('user_id', $id)->delete();

            return redirect()->route('nasabah')->with('message', 'Data Nasabah Berhasil Dihapus');
        } catch (\Exception $e) {
            return redirect()->route('nasabah')->with('error', 'Gagal menghapus data: ' . $e->getMessage())->withErrors($e->getMessage());
        }
    }
}
