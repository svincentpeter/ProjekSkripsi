@extends('backend.app')
@section('title', 'Tambah User')
@section('content')
<div class="container-fluid pt-4 px-4">
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <h2 class="mb-4">Tambah Pengguna</h2>
    <div class="bg-light rounded h-100 p-4">
        <form method="POST" action="{{ route('storeUser') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="name" value="{{ old('name') }}" name="name" placeholder="">
                <label for="nama">Name</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" value="{{ old('email') }}" id="email" name="email" placeholder="">
                <label for="email">Email</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" name="password" placeholder="">
                <label for="password">Password</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="">
                <label for="password_confirmation">Password Confirmation</label>
            </div>
            <div class="form-group mb-4">
                <label class="mb-2">Role User <strong style="color: red;">*</strong></label>
                <select class="form-select" multiple="" aria-label="multiple select example" name="roles[]">
                    @foreach ($roles as $role)
                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Masukkan Foto</label>
                <input class="form-control" id="image" name="image" accept="image/*" type="file">
                @error('image')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ route('user') }}" class="btn btn-info">Kembali</a>
        </form>
    </div>
</div>
@endsection