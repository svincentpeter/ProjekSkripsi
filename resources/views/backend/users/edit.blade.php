@extends('backend.app')
@section('title', 'Edit Pengguna')

@section('content')
<div class="container-fluid pt-4 px-4">
  <div class="row justify-content-center">
    <div class="col-md-7">
      <div class="bg-light rounded shadow p-4">
        <h4 class="mb-4">Edit Pengguna</h4>

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

        <form method="POST" action="{{ route('users.update', $edituser->id) }}" enctype="multipart/form-data">
          @csrf
          @method('PUT')

          <div class="form-floating mb-3">
            <input type="text" name="name" id="name"
              class="form-control @error('name') is-invalid @enderror"
              value="{{ old('name', $edituser->name) }}" placeholder="Nama Lengkap">
            <label for="name">Nama Lengkap</label>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="form-floating mb-3">
            <input type="email" name="email" id="email"
              class="form-control @error('email') is-invalid @enderror"
              value="{{ old('email', $edituser->email) }}" placeholder="Email">
            <label for="email">Email</label>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Role User <span class="text-danger">*</span></label>
            <select name="roles[]" class="form-select @error('roles') is-invalid @enderror" multiple>
              @foreach($roles as $roleId => $roleName)
                <option value="{{ $roleId }}" {{ in_array($roleId, old('roles', $userRole)) ? 'selected' : '' }}>
                  {{ $roleName }}
                </option>
              @endforeach
            </select>
            <div class="form-text">Bisa pilih lebih dari satu (Ctrl+klik)</div>
            @error('roles')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>

          <div class="form-floating mb-3">
            <input type="password" name="password" id="password"
              class="form-control @error('password') is-invalid @enderror"
              placeholder="Password (kosongkan jika tidak ingin diubah)">
            <label for="password">Password (opsional)</label>
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
            <label for="image" class="form-label">Foto Pengguna</label>
            <input type="file" name="image" id="image"
              class="form-control @error('image') is-invalid @enderror"
              accept="image/*">
            @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

            {{-- Preview gambar lama --}}
            @if(!empty($edituser->image))
              <div class="my-2">
                <img id="image-preview"
                     src="{{ asset('assets/backend/img/' . $edituser->image) }}"
                     alt="{{ $edituser->name }}"
                     class="img-thumbnail"
                     style="max-width: 120px;">
              </div>
            @endif
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
  const preview = document.getElementById('image-preview');
  if (!file) return;
  const reader = new FileReader();
  reader.onload = () => {
    preview.src = reader.result;
    preview.style.display = 'block';
  };
  reader.readAsDataURL(file);
});
</script>
@endsection
