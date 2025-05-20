@extends('backend.app')
@section('title', 'Data Pengguna')

@section('content')
<div class="container-fluid pt-4 px-4">
  <h3 class="mb-4">Data Pengguna</h3>
  <div class="row g-4">
    <div class="col-12">
      <div class="bg-light rounded shadow h-100 p-4">
        {{-- Success Alert --}}
        @if(Session::has('message'))
          <div id="successAlert" class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ Session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        <div class="mb-3 text-end">
          <a href="{{ route('createUser') }}" class="btn btn-primary rounded-pill">
            <i class="fas fa-user-plus"></i> Tambah Pengguna
          </a>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-primary">
              <tr>
                <th>#</th>
                <th>Foto</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Diupdate</th>
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
                  <td>
                    @if($user->image)
                      <img src="{{ asset('assets/backend/img/' . $user->image) }}" alt="foto" class="rounded-circle" width="36" height="36">
                    @else
                      <span class="text-muted"><i class="fas fa-user-circle fa-2x"></i></span>
                    @endif
                  </td>
                  <td>{{ $user->name }}</td>
                  <td>{{ $user->email }}</td>
                  <td>
                    <span class="badge bg-info text-dark">{{ $user->role_name }}</span>
                  </td>
                  <td>
                    {{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : '-' }}
                  </td>
                  <td>
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-outline-info btn-sm rounded-pill">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ url('delete-user/'.$user->id) }}" method="POST"
                          class="d-inline" onsubmit="return confirm('Yakin hapus user ini?')">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-outline-danger btn-sm rounded-pill">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @endforeach
              @if($users->isEmpty())
                <tr><td colspan="7" class="text-center text-muted">Belum ada data user.</td></tr>
              @endif
            </tbody>
          </table>
        </div>

        {{-- Pagination --}}
        <div class="float-end mt-2">
          {{ $users->links() }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  setTimeout(() => {
    document.querySelectorAll('.custom-alert').forEach(alertEl => {
      new bootstrap.Alert(alertEl).close();
    });
  }, 4000);
</script>
@endsection
