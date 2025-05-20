@extends('backend.app')
@section('title', 'Tambah Pengguna')

@section('content')
<div class="container-fluid pt-4 px-4">
  <div class="row justify-content-center">
    <div class="col-md-7">
      <div class="bg-light rounded shadow p-4">
        <h4 class="mb-4">Tambah Pengguna Baru</h4>

        {{-- Error alert --}}
        @if($errors->any())
          <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        <form method="POST" action="{{ route('storeUser') }}" enctype="multipart/form-data" autocomplete="off">
          @csrf

          <div class="form-floating mb-3">
            <input type="text" name="name" id="name"
              class="form-control @error('name') is-invalid @enderror"
              value="{{ old('name') }}" placeholder="Nama Lengkap">
            <label for="name">Nama Lengkap</label>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="form-floating mb-3">
            <input type="email" name="email" id="email"
              class="form-control @error('email') is-invalid @enderror"
              value="{{ old('email') }}" placeholder="Email">
            <label for="email">Email</label>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="form-floating mb-3">
            <input type="password" name="password" id="password"
              class="form-control @error('password') is-invalid @enderror"
              placeholder="Password">
            <label for="password">Password</label>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="form-floating mb-3">
            <input type="password" name="password_confirmation" id="password_confirmation"
              class="form-control @error('password_confirmation') is-invalid @enderror"
              placeholder="Konfirmasi Password">
            <label for="password_confirmation">Konfirmasi Password</label>
            @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Role User <span class="text-danger">*</span></label>
            <select name="roles[]" class="form-select @error('roles') is-invalid @enderror" multiple>
              @foreach($roles as $role)
                <option value="{{ $role->name }}">{{ $role->name }}</option>
              @endforeach
            </select>
            <div class="form-text">Bisa pilih lebih dari satu (Ctrl+klik)</div>
            @error('roles')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label for="image" class="form-label">Foto Pengguna</label>
            <input type="file" name="image" id="image"
              class="form-control @error('image') is-invalid @enderror"
              accept="image/*">
            @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

            {{-- Preview gambar --}}
            <img id="preview-image" src="#" alt="Preview" style="display:none;max-width:120px;margin-top:10px;">
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4">Simpan</button>
            <a href="{{ route('user') }}" class="btn btn-outline-secondary px-4">Kembali</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('image').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = () => {
    const img = document.getElementById('preview-image');
    img.src = reader.result;
    img.style.display = 'block';
  };
  reader.readAsDataURL(file);
});
</script>
@endsection
