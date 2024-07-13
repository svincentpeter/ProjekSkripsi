@extends('backend.app')
@section('title', 'Users')
@section('content')
<div class="container-fluid pt-4 px-4">
    <h6 class="mb-4">DATA PENGGUNA</h6>
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                @if(Session::has('message'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5>
                        <i class="icon fas fa-check"></i> Sukses!
                    </h5>
                    {{ Session('message') }}
                </div>
                @endif


                <div class="mb-3">
                    <a href="{{route('createUser')}}" class="btn btn-outline-primary rounded-pill m-2">Tambah Pengguna</a>
                    <!-- <a href="" class="btn btn-outline-primary rounded-pill m-2" data-bs-toggle="modal" data-bs-target="#createUserModal">Tambah Pengguna</a> -->
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">name</th>
                            <th scope="col">roles</th>
                            <th scope="col">email</th>
                            <th scope="col">Update_at</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Calculate the row number --}}
                        @php
                        $rowNumber = ($users->currentPage() - 1) * $users->perPage() + 1;
                        @endphp

                        @foreach($users as $user)
                        <tr>
                            <td scope="row">{{ $rowNumber++ }}</td>
                            <td>{{$user->name}}</td>
                            <td>
                                <small class="text-success">{{ $user->role_name }}</small>
                            </td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->updated_at ?? \Carbon\Carbon::now() }}</td>
                            <td>
                                <!-- <a href="{{ route('users.edit', $user->id) }}" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#editUserModal{{$user->id}}">Edit</a> -->
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-outline-info">Edit</a>

                                <a href="{{ URL('delete-user') }}/{{ $user->id }}" class="btn btn-outline-danger " onclick="return confirm('Are you sure?')">{{ __('Delete') }}</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- Pagination Links -->
                <div class="float-right">
                    {{ $users->links() }}
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection