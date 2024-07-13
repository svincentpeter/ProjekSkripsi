@extends('backend.app')
@section('title', 'roles and Permission')
@section('content')


<div class="container-fluid pt-4 px-4">
    <h6 class="mb-4">Roles And Permissions</h6>
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <div class="table-responsive">
                    @if(Session::has('message'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5>
                            <i class="icon fas fa-check"></i> Sukses!
                        </h5>
                        {{ Session('message') }}
                    </div>
                    @endif

                    <table class="table">
                        <thead>
                            <div class=" mb-3">
                                <a href="{{ URL('create-roles') }}" class="btn btn-outline-primary rounded-pill m-2">{{ __('Create Roles') }}</a>

                            </div>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Calculate the row number --}}
                            @php
                            $rowNumber = ($roles->currentPage() - 1) * $roles->perPage() + 1;
                            @endphp

                            @foreach ($roles as $role)
                            <tr>
                                <td scope="row">{{ $rowNumber++ }}</td>
                                <td>{{ $role->name }}</td>
                                <td>

                                    <a href="{{ URL('edit-role') }}/{{ $role->id }}" class="btn btn-outline-warning ">{{ __('Edit') }}</a>
                                    <a href="{{ URL('delete-role') }}/{{ $role->id }}" class="btn btn-outline-danger ">{{ __('Delete') }}</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Pagination Links -->
                    <div class="float-right">
                        {{ $roles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection