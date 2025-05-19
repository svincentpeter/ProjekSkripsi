@extends('backend.app')
@section('title', 'Users')

@section('content')
<div class="container-fluid pt-4 px-4">
  <h6 class="mb-4">DATA PENGGUNA</h6>
  <div class="row g-4">
    <div class="col-12">
      <div class="bg-light rounded h-100 p-4">
        {{-- Success Alert --}}
        @if(Session::has('message'))
          <div id="successAlert" class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
            <h5 class="alert-heading"><i class="icon fas fa-check-circle"></i> Sukses!</h5>
            {{ Session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <div class="mb-3">
          <a href="{{ route('createUser') }}" class="btn btn-outline-primary rounded-pill">
            <i class="fas fa-user-plus"></i> Tambah Pengguna
          </a>
        </div>

        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Roles</th>
                <th>Email</th>
                <th>Updated At</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @php
                $rowNumber = ($users->currentPage() - 1) * $users->perPage() + 1;
              @endphp

              @foreach($users as $user)
              <tr>
                <td>{{ $rowNumber++ }}</td>
                <td>{{ $user->name }}</td>
                <td><small class="text-success">{{ $user->role_name }}</small></td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->updated_at ?? now() }}</td>
                <td>
                  <a href="{{ route('users.edit', $user->id) }}" class="btn btn-outline-info btn-sm">
                    <i class="fas fa-edit"></i>
                  </a>
                  <form action="{{ url('delete-user/'.$user->id) }}" method="POST"
                        class="d-inline" onsubmit="return confirm('Are you sure?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </form>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{-- Pagination --}}
        <div class="float-right">
          {{ $users->links() }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  // Auto-close alerts after 5 seconds
  setTimeout(() => {
    document.querySelectorAll('.custom-alert').forEach(alertEl => {
      new bootstrap.Alert(alertEl).close();
    });
  }, 5000);
</script>
@endsection
