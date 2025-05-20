@extends('backend.app')

@section('title', 'Tambah Role')

@section('head')
<style>
    .table { width: 100%; border-collapse: collapse; }
    .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: middle; }
    .table th { background-color: #f2f2f2; }
    .table tr:nth-child(even) { background-color: #f8fafc; }
    .styled-checkbox { position: relative; cursor: pointer; display: inline-block; }
    .styled-checkbox input { position: absolute; opacity: 0; cursor: pointer; height: 0; width: 0; }
    .checkmark { position: absolute; top: 0; left: 0; height: 20px; width: 20px; background-color: #eee; border: 1px solid #ccc; }
    .styled-checkbox input:checked + .checkmark:after {
        content: "";
        position: absolute; display: block; left: 6px; top: 2px;
        width: 6px; height: 12px; border: solid #007bff; border-width: 0 2px 2px 0; transform: rotate(45deg);
    }
    /* Tooltip untuk label permission */
    .perm-label { font-weight: bold; font-size: 1em; }
    .perm-desc { color: #888; font-size: 0.96em; display: block; }

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
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-user-shield me-2"></i> Tambah Role Baru</h4>
            <a href="{{ route('roles.index') }}" class="btn btn-light btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>
            @endif

            @if (session('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            <form method="POST" action="{{ route('roles.store') }}">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Role <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror" required autofocus autocomplete="off">
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
                                        <input type="checkbox"
                                            id="permission_{{ $permission->id }}"
                                            name="permissions[]"
                                            value="{{ $permission->name }}"
                                            {{ is_array(old('permissions')) && in_array($permission->name, old('permissions')) ? 'checked' : '' }}>
                                        <label for="permission_{{ $permission->id }}" class="checkmark"></label>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <small class="text-muted">Centang hak akses yang boleh dipakai role ini.</small>
                    </div>

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
                                        {{ is_array(old('users')) && in_array($user->id, old('users')) ? 'checked' : '' }}
                                    >
                                    <span class="radio-mark"></span>
                                    {{ $user->name }}
                                </label>
                            @endforeach
                        </div>
                        <small class="text-muted">*Bisa pilih lebih dari satu user, boleh dikosongkan.</small>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-2"><i class="fas fa-save"></i> Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
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
