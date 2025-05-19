@extends('backend.app')
@section('title', 'Roles and Permissions')

@section('content')
<div class="container-fluid pt-4 px-4">
    <h6 class="mb-4">Roles and Permissions</h6>
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <div class="table-responsive">

                    @if(Session::has('message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <h5><i class="icon fas fa-check"></i> Sukses!</h5>
                        {{ Session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="mb-3">
                        <a href="{{ route('roles.create') }}"
                           class="btn btn-outline-primary rounded-pill">
                            {{ __('Create Role') }}
                        </a>
                    </div>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $rowNumber = ($roles->currentPage() - 1) * $roles->perPage() + 1;
                            @endphp

                            @foreach($roles as $role)
                            <tr>
                                <td>{{ $rowNumber++ }}</td>
                                <td>{{ $role->name }}</td>
                                <td class="d-flex gap-1">
                                    <a href="{{ route('roles.edit', $role->id) }}"
                                       class="btn btn-outline-warning btn-sm">
                                        {{ __('Edit') }}
                                    </a>

                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus role ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="float-right">
                        {{ $roles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
