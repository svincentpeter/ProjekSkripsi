@extends('backend.app')
@section('title', 'Tambah User')

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

    <h2 class="mb-4">Tambah Pengguna</h2>
    <div class="bg-light rounded h-100 p-4">
      <form method="POST" action="{{ route('storeUser') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-floating mb-3">
          <input
            type="text"
            class="form-control @error('name') is-invalid @enderror"
            id="name"
            name="name"
            value="{{ old('name') }}"
            placeholder="Name">
          <label for="name">Name</label>
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-floating mb-3">
          <input
            type="email"
            class="form-control @error('email') is-invalid @enderror"
            id="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="Email">
          <label for="email">Email</label>
          @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-floating mb-3">
          <input
            type="password"
            class="form-control @error('password') is-invalid @enderror"
            id="password"
            name="password"
            placeholder="Password">
          <label for="password">Password</label>
          @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-floating mb-3">
          <input
            type="password"
            class="form-control @error('password_confirmation') is-invalid @enderror"
            id="password_confirmation"
            name="password_confirmation"
            placeholder="Confirm Password">
          <label for="password_confirmation">Confirm Password</label>
          @error('password_confirmation')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Role User <span class="text-danger">*</span></label>
          <select
            name="roles[]"
            class="form-select @error('roles') is-invalid @enderror"
            multiple>
            @foreach($roles as $role)
              <option value="{{ $role->name }}">{{ $role->name }}</option>
            @endforeach
          </select>
          @error('roles')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label for="image" class="form-label">Foto Pengguna</label>
          <input
            type="file"
            class="form-control @error('image') is-invalid @enderror"
            id="image"
            name="image"
            accept="image/*">
          @error('image')
            <div class="text-danger">{{ $message }}</div>
          @enderror

          {{-- Preview --}}
          <img
            id="preview-image"
            src="#"
            alt="Preview"
            style="display:none; max-width:100%; margin-top:10px;">
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
        <a href="{{ route('user') }}" class="btn btn-info">Kembali</a>
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

// Image preview logic
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
