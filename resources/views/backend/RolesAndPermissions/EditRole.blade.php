@extends('backend.app')

@section('title', 'Edit Role')

@section('head')
<style>
    .table {
        width: 50%;
        border-collapse: collapse;
    }
    .table th,
    .table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    .table th {
        background-color: #f2f2f2;
    }
    .table tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    /* Style for checkboxes */
    .styled-checkbox {
        position: relative;
        cursor: pointer;
        display: inline-block;
    }
    .styled-checkbox input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }
    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 20px;
        width: 20px;
        background-color: #eee;
        border: 1px solid #ccc;
    }
    .styled-checkbox input:checked + .checkmark:after {
        content: "";
        position: absolute;
        display: block;
        left: 6px;
        top: 2px;
        width: 6px;
        height: 12px;
        border: solid #333;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h1>Edit Role</h1>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('roles.update', $role->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Nama Role</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $role->name) }}"
                    class="form-control @error('name') is-invalid @enderror"
                    required
                >
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h3>Permissions</h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Pilih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $permission)
                            <tr>
                                <td>{{ $permission->name }}</td>
                                <td class="styled-checkbox">
                                    <input
                                        type="checkbox"
                                        id="permission_{{ $permission->id }}"
                                        name="permissions[]"
                                        value="{{ $permission->name }}"
                                        {{ $role->permissions->contains('id', $permission->id) ? 'checked' : '' }}
                                    >
                                    <label for="permission_{{ $permission->id }}" class="checkmark"></label>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="col-md-6">
                    <h3>Users</h3>
                    <div class="mb-3">
                        <label for="users" class="form-label">Pilih Users</label>
                        <select
                            id="users"
                            name="users[]"
                            class="form-control"
                            multiple
                        >
                            @foreach($users as $user)
                            <option
                                value="{{ $user->id }}"
                                {{ $role->users->contains('id', $user->id) ? 'selected' : '' }}
                            >{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        </form>
    </div>
</div>
@endsection
