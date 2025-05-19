@extends('backend.app')
@section('title', 'Edit Nasabah')
@section('content')
<div class="container-fluid pt-4 px-4">
    <h2 class="mb-4">Edit Nasabah</h2>

    @if(Session::has('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ Session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(Session::has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ Session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="bg-light rounded h-100 p-4">
        <form method="POST" action="{{ route('nasabah.update', $nasabah->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                {{-- Kolom Kiri --}}
                <div class="col-md-6">
                    {{-- Nama --}}
                    <div class="form-floating mb-3">
                        <input type="text" name="name" id="name"
                            value="{{ old('name', $nasabah->name) }}"
                            class="form-control @error('name') is-invalid @enderror">
                        <label for="name">Nama</label>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    {{-- Email --}}
                    <div class="form-floating mb-3">
                        <input type="email" name="email" id="email"
                            value="{{ old('email', $user->email ?? '') }}"
                            class="form-control @error('email') is-invalid @enderror">
                        <label for="email">Email</label>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    {{-- Password (Opsional, hanya jika ingin diubah) --}}
                    <div class="form-floating mb-3">
                        <input type="password" name="password" id="password"
                            class="form-control @error('password') is-invalid @enderror">
                        <label for="password">Password (Kosongkan jika tidak ingin diubah)</label>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    {{-- Konfirmasi Password --}}
                    <div class="form-floating mb-3">
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="form-control">
                        <label for="password_confirmation">Konfirmasi Password</label>
                    </div>
                    {{-- NIP --}}
                    <div class="form-floating mb-3">
                        <input type="text" name="nip" id="nip"
                            value="{{ old('nip', $nasabah->nip) }}"
                            class="form-control @error('nip') is-invalid @enderror">
                        <label for="nip">NIP</label>
                        @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    {{-- Telepon --}}
                    <div class="form-floating mb-3">
                        <input type="tel" name="telphone" id="telphone"
                            value="{{ old('telphone', $nasabah->telphone) }}"
                            class="form-control @error('telphone') is-invalid @enderror">
                        <label for="telphone">Telepon</label>
                        @error('telphone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                {{-- Kolom Kanan --}}
                <div class="col-md-6">
                    {{-- Tanggal Lahir --}}
                    <div class="form-floating mb-3">
                        <input type="date" name="tgl_lahir" id="tgl_lahir"
                            value="{{ old('tgl_lahir', $nasabah->tgl_lahir) }}"
                            class="form-control @error('tgl_lahir') is-invalid @enderror">
                        <label for="tgl_lahir">Tanggal Lahir</label>
                        @error('tgl_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    {{-- Alamat --}}
                    <div class="form-floating mb-3">
                        <textarea name="alamat" id="alamat" rows="3"
                            class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $nasabah->alamat) }}</textarea>
                        <label for="alamat">Alamat</label>
                        @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    {{-- Pekerjaan --}}
                    <div class="form-floating mb-3">
                        <input type="text" name="pekerjaan" id="pekerjaan"
                            value="{{ old('pekerjaan', $nasabah->pekerjaan) }}"
                            class="form-control @error('pekerjaan') is-invalid @enderror">
                        <label for="pekerjaan">Pekerjaan</label>
                        @error('pekerjaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    {{-- Agama --}}
                    <div class="mb-3">
                        <label for="agama" class="form-label">Agama</label>
                        <select name="agama" id="agama"
                                class="form-select @error('agama') is-invalid @enderror">
                            <option value="" disabled>Pilih Agama</option>
                            @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $agama)
                                <option value="{{ $agama }}" {{ old('agama', $nasabah->agama)==$agama ? 'selected' : '' }}>
                                    {{ $agama }}
                                </option>
                            @endforeach
                        </select>
                        @error('agama') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    {{-- Jenis Kelamin --}}
                    <div class="mb-3">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="jenis_kelamin"
                                class="form-select @error('jenis_kelamin') is-invalid @enderror">
                            <option value="" disabled>Pilih Jenis Kelamin</option>
                            <option value="L" {{ old('jenis_kelamin', $nasabah->jenis_kelamin)=='L' ? 'selected' : '' }}>
                                Laki-Laki
                            </option>
                            <option value="P" {{ old('jenis_kelamin', $nasabah->jenis_kelamin)=='P' ? 'selected' : '' }}>
                                Perempuan
                            </option>
                        </select>
                        @error('jenis_kelamin') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    {{-- Foto --}}
                    <div class="mb-3">
                        <label for="image" class="form-label">Foto (opsional)</label>
                        <input type="file" name="image" id="image"
                            class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        @error('image') <div class="text-danger">{{ $message }}</div> @enderror
                        <div class="mt-2">
                            @if($nasabah->image)
                                <img id="preview" src="{{ asset('assets/backend/img/'.$nasabah->image) }}"
                                     style="max-width:200px;">
                            @else
                                <img id="preview" src="#" style="display:none; max-width:200px;">
                            @endif
                        </div>
                    </div>
                    {{-- Status Anggota --}}
                    <div class="mb-3">
                        <label for="status_anggota" class="form-label">Status Anggota</label>
                        <select name="status_anggota" id="status_anggota"
                                class="form-select @error('status_anggota') is-invalid @enderror">
                            <option value="1" {{ old('status_anggota', $nasabah->status_anggota) == 1 ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('status_anggota', $nasabah->status_anggota) == 0 ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                        @error('status_anggota') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Update</button>
        </form>
    </div>
</div>
@endsection
