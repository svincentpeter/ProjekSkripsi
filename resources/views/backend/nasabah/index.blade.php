@extends('backend.app')
@section('title', 'Nasabah')
@section('content')
<div class="container-fluid pt-4 px-4">
    <h2 class="mb-4">Data Nasabah</h2>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Alert Success -->
    @if(Session::has('message'))
    <div id="successAlert" class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
        <h5 class="alert-heading"><i class="icon fas fa-check-circle"></i> Sukses!</h5>
        {{ Session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Alert Error -->
    @if(Session::has('error'))
    <div id="errorAlert" class="alert alert-danger alert-dismissible fade show custom-alert" role="alert">
        <h5 class="alert-heading"><i class="icon fas fa-times-circle"></i> Error!</h5>
        {{ Session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <div class="table-responsive">


                    <table class="table">
                        <thead>
                            <div class=" mb-3">

                                <!-- <a href="{{route('createNasabah')}}" class="btn btn-outline-primary rounded-pill m-2"><i class="fas fa-user-plus"> </i> Tambah </a> -->
                                <!-- Button to Open the Modal -->
                                @can('nasabah-create')
                                <button type="button" class="btn btn-outline-primary rounded-pill m-3" data-bs-toggle="modal" data-bs-target="#buatAnggota">
                                    <i class="fas fa-user-plus"></i> Tambah
                                </button>
                                @endcan
                                @include('backend.nasabah.modal.modalCreate')
                            </div>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">name</th>
                                <th scope="col">Jenis Kelamin</th>
                                <th scope="col">Pekerjaan</th>
                                <th scope="col">Saldo</th>
                                <th scope="col">Status</th>
                                <th scope="col">Update_at</th>
                                @can('nasabah-detail')
                                <th scope="col">Aksi</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Calculate the row number --}}
                            @php
                            $rowNumber = ($nasabah->currentPage() - 1) * $nasabah->perPage() + 1;
                            @endphp

                            @foreach($nasabah as $anggota)
                            <tr>
                                <td scope="row">{{ $rowNumber++ }}</td>
                                <td>{{$anggota->name}}</td>
                                <td>{{$anggota->jenis_kelamin}}</td>
                                <td>{{$anggota->pekerjaan}}</td>
                                <td>Rp {{ number_format($anggota->saldo, 2, ',', '.') }}</td>
                                <td>
                                    @if ($anggota-> status_anggota == 0)
                                    <span class="text-danger">non-Aktif</span>
                                    @elseif ($anggota-> status_anggota == 1)
                                    <span class="text-success">Aktif</span>
                                    @endif
                                </td>

                                <td>{{$anggota->updated_at ?? \Carbon\Carbon::now() }}</td>
                                <td>
                                    @can('nasabah-detail')
                                    <a href="{{ route('nasabah.show', $anggota->id) }}" class="btn btn-outline-info" title="Show">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('nasabah-edit')
                                    <a href="{{ route('nasabah.edit', $anggota->id) }}" class="btn btn-outline-warning" title="edit"> <i class="fas fa-edit"></i></a>
                                    @endcan
                                    @can('nasabah-delete')
                                    <form action="{{ route('nasabah.destroy', $anggota->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Pagination Links -->
                    <div class="float-right">
                        {{ $nasabah->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script untuk menutup alert secara otomatis -->
<script>
    // Menutup alert secara otomatis setelah 5 detik
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
            new bootstrap.Alert(alert).close();
        });
    }, 5000); // 5000 milidetik = 5 detik

    // Membuat animasi alert muncul di depan tabel
    $(document).ready(function() {
        $(".custom-alert").each(function(index) {
            $(this).delay(300 * index).fadeIn("slow");
        });
    });
</script>
@endsection