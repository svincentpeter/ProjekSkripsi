@extends('backend.app')

@section('title', 'Edit Role')

@section('head')
<style>
/* ========== Radio-style Checkbox for Assign User ========== */
.radio-checkbox {
    display: inline-block;
    position: relative;
    padding-left: 30px;
    margin-bottom: 8px;
    margin-right: 18px;
    cursor: pointer;
    font-size: 1em;
    user-select: none;
    min-width: 130px;
}
.radio-checkbox input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0; width: 0;
}
.radio-mark {
    position: absolute;
    left: 0;
    top: 2px;
    height: 20px;
    width: 20px;
    background: #f1f1f1;
    border-radius: 50%;
    border: 2px solid #bbb;
    transition: border-color 0.2s;
}
.radio-checkbox input[type="checkbox"]:checked ~ .radio-mark {
    background-color: #3490dc;
    border-color: #3490dc;
}
.radio-mark:after {
    content: "";
    position: absolute;
    display: none;
}
.radio-checkbox input[type="checkbox"]:checked ~ .radio-mark:after {
    display: block;
}
.radio-checkbox .radio-mark:after {
    left: 6px;
    top: 6px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #fff;
    position: absolute;
}
.radio-checkbox input[type="checkbox"]:checked ~ .radio-mark:after {
    background: #fff;
    box-shadow: 0 0 0 4px #3490dc inset;
}
/* ========== Responsive wrapping ========== */
.user-checkbox-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.2rem 1.1rem;
    align-items: flex-start;
    max-height: 210px;
    overflow-y: auto;
    border: 1px solid #e3e6f0;
    border-radius: 6px;
    padding: 10px 12px;
    background: #fcfcfc;
}
.user-search-box {
    width: 100%;
    margin-bottom: 10px;
}
</style>

@endsection

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-edit me-2"></i> Edit Role</h4>
            <a href="{{ route('roles.index') }}" class="btn btn-light btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('roles.update', $role->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Role</label>
                    <input type="text" id="name" name="name"
                        value="{{ old('name', $role->name) }}"
                        class="form-control @error('name') is-invalid @enderror"
                        required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h5>Daftar Hak Akses (Permissions)</h5>
                        <table class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th style="width:55%">Akses / Fitur</th>
                                    <th style="width:15%">Pilih</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissions as $permission)
                                <tr>
                                    <td>
                                        <span class="perm-label">
                                            {{ config('permission_labels.'.$permission->name) ?? ucfirst(str_replace('-', ' ', $permission->name)) }}
                                        </span>
                                        <span class="perm-desc">
                                            ({{ $permission->name }})
                                        </span>
                                    </td>
                                    <td class="styled-checkbox">
                                        <input
                                            type="checkbox"
                                            id="permission_{{ $permission->id }}"
                                            name="permissions[]"
                                            value="{{ $permission->name }}"
                                            {{ $role->permissions->contains('id', $permission->id) ? 'checked' : '' }}>
                                        <label for="permission_{{ $permission->id }}" class="checkmark"></label>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <small class="text-muted">Centang hak akses yang boleh dipakai role ini.</small>
                    </div>

                    <!-- Col Assign User (kanan) -->
    <div class="col-md-6">
        <h5>Assign User <small class="text-muted">(Optional)</small></h5>
        <div class="mb-2 user-search-box">
            <input type="text" class="form-control" id="user-search" placeholder="Cari user...">
        </div>
        <div class="user-checkbox-list">
            @foreach($users as $user)
                <label class="radio-checkbox">
                    <input
                        type="checkbox"
                        name="users[]"
                        value="{{ $user->id }}"
                        @if(is_array(old('users')) && in_array($user->id, old('users')))
                            checked
                        @elseif(isset($role) && $role->users->contains('id', $user->id))
                            checked
                        @endif
                    >
                    <span class="radio-mark"></span>
                    {{ $user->name }}
                </label>
            @endforeach
        </div>
        <small class="text-muted">*Bisa pilih lebih dari satu user, boleh dikosongkan</small>
    </div>
                </div>
                <button type="submit" class="btn btn-success mt-2"><i class="fas fa-save"></i> Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('user-search');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let userLabels = document.querySelectorAll('.user-checkbox-list label');
            userLabels.forEach(function(label) {
                let text = label.textContent.toLowerCase();
                label.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
});
</script>

@endsection
