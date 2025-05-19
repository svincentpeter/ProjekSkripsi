<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Edit User') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        {{-- Validation Errors --}}
        @if($errors->any())
        <div id="modalEditErrorAlert" class="alert alert-danger alert-dismissible fade show" role="alert">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <form method="POST" action="{{ route('updateUser', $user->id) }}" enctype="multipart/form-data">
          @csrf
          @method('PUT')

          <div class="form-floating mb-3">
            <input
              type="text"
              class="form-control @error('name') is-invalid @enderror"
              id="edit-name"
              name="name"
              value="{{ old('name', $user->name) }}"
              placeholder="Name">
            <label for="edit-name">Name</label>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-floating mb-3">
            <input
              type="email"
              class="form-control @error('email') is-invalid @enderror"
              id="edit-email"
              name="email"
              value="{{ old('email', $user->email) }}"
              placeholder="Email">
            <label for="edit-email">Email</label>
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-floating mb-3">
            <input
              type="password"
              class="form-control @error('password') is-invalid @enderror"
              id="edit-password"
              name="password"
              placeholder="Password (leave blank to keep current)">
            <label for="edit-password">Password</label>
            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-floating mb-3">
            <input
              type="password"
              class="form-control @error('password_confirmation') is-invalid @enderror"
              id="edit-password_confirmation"
              name="password_confirmation"
              placeholder="Confirm Password">
            <label for="edit-password_confirmation">Confirm Password</label>
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
                <option
                  value="{{ $role->name }}"
                  {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'selected' : '' }}>
                  {{ $role->name }}
                </option>
              @endforeach
            </select>
            @error('roles')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="edit-image" class="form-label">Foto Pengguna</label>
            <input
              type="file"
              class="form-control @error('image') is-invalid @enderror"
              id="edit-image"
              name="image"
              accept="image/*">
            @error('image')
            <div class="text-danger">{{ $message }}</div>
            @enderror

            {{-- Preview --}}
            @if($user->image)
            <p class="mt-2 mb-1">Current photo:</p>
            <img
              id="current-preview-image"
              src="{{ asset('assets/backend/img/' . $user->image) }}"
              alt="Current Photo"
              style="max-width:100%; margin-bottom:10px;">
            @endif

            <p class="mb-1">New preview:</p>
            <img
              id="edit-preview-image"
              src="#"
              alt="New Preview"
              style="display:none; max-width:100%; margin-top:10px;">
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-outline-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@section('scripts')
<script>
// Auto-close the validation alert inside modal after 5s
setTimeout(() => {
  const alert = document.getElementById('modalEditErrorAlert');
  if (alert) new bootstrap.Alert(alert).close();
}, 5000);

// Image preview
document.getElementById('edit-image').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = () => {
    const img = document.getElementById('edit-preview-image');
    img.src = reader.result;
    img.style.display = 'block';
  };
  reader.readAsDataURL(file);
});
</script>
@endsection
