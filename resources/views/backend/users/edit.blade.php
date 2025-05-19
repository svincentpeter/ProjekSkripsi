@extends('backend.app')
@section('title', 'Edit User')

@section('content')
<div class="container-fluid pt-4 px-4">
    {{-- Global Validation Errors --}}
    @if($errors->any())
      <div id="pageErrorAlert" class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <h2 class="mb-4">Edit Pengguna</h2>
    <div class="bg-light rounded h-100 p-4">
        <form action="{{ route('users.update', $edituser->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('put')

            <div class="form-floating mb-3">
              <input
                type="text"
                name="name"
                value="{{ old('name', $edituser->name) }}"
                class="form-control @error('name') is-invalid @enderror"
                id="name"
                placeholder="Name">
              <label for="name">Name</label>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-floating mb-3">
              <input
                type="email"
                name="email"
                value="{{ old('email', $edituser->email) }}"
                class="form-control @error('email') is-invalid @enderror"
                id="email"
                placeholder="Email">
              <label for="email">Email</label>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Role User <span class="text-danger">*</span></label>
              <select
                name="roles[]"
                class="form-select @error('roles') is-invalid @enderror"
                multiple>
                @foreach($roles as $roleId => $roleName)
                  <option value="{{ $roleId }}"
                    {{ in_array($roleId, old('roles', $userRole)) ? 'selected' : '' }}>
                    {{ $roleName }}
                  </option>
                @endforeach
              </select>
              @error('roles')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-floating mb-3">
              <input
                type="password"
                name="password"
                class="form-control @error('password') is-invalid @enderror"
                id="password"
                placeholder="Password">
              <label for="password">Password</label>
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-floating mb-3">
              <input
                type="password"
                name="confirm-password"
                class="form-control @error('confirm-password') is-invalid @enderror"
                id="confirm-password"
                placeholder="Confirm Password">
              <label for="confirm-password">Confirm Password</label>
              @error('confirm-password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-floating mb-3">
              <input
                type="file"
                class="form-control @error('image') is-invalid @enderror"
                id="image"
                name="image"
                accept="image/*">
              <label for="image">Gambar</label>
              @error('image')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            {{-- Existing image preview --}}
            <div class="mb-3 text-center">
              @if(!empty($edituser->image))
                <img
                  id="image-preview"
                  src="{{ asset('assets/backend/img/' . $edituser->image) }}"
                  alt="{{ $edituser->name }}"
                  class="img-thumbnail"
                  style="max-width: 150px;">
              @else
                <img
                  id="image-preview"
                  src="#"
                  alt="No Image"
                  class="img-thumbnail"
                  style="display:none; max-width: 150px;">
              @endif
            </div>

            <a href="{{ route('user') }}" class="btn btn-info">Kembali</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Auto-close the page-level error alert after 5 seconds
setTimeout(() => {
  const alert = document.getElementById('pageErrorAlert');
  if (alert) new bootstrap.Alert(alert).close();
}, 5000);

// Live image preview
document.getElementById('image').addEventListener('change', function(e) {
  const file = e.target.files[0];
  const preview = document.getElementById('image-preview');
  if (!file) {
    preview.style.display = 'none';
    return;
  }
  const reader = new FileReader();
  reader.onload = () => {
    preview.src = reader.result;
    preview.style.display = 'block';
  };
  reader.readAsDataURL(file);
});
</script>
@endsection
