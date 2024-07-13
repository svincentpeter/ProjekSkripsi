@extends('backend.app')

@section('title', 'Edit Nasabah')

@section('content')
<div class="container-fluid pt-4 px-4">
    <h6 class="mb-4">Edit Nasabah</h6>

    @if(Session::has('message'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h5>
            <i class="icon fas fa-check"></i> Sukses!
        </h5>
        {{ Session('message')}}
    </div>
    @endif

    @if(Session::has('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h5>
            <i class="icon fas fa-times"></i> Error!
        </h5>
        {{ Session('error')}}
    </div>
    @endif

    <div class="bg-light rounded h-100 p-4">
        <form method="POST" action="{{ route('nasabah.update', $nasabah->user_id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="nama" name="nama" value="{{ $nasabah->name }}">
                        <label for="nama">Nama</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}">
                        <label for="email">Email</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                        <label for="password">Password</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="tel" class="form-control" id="telphone" name="telphone" value="{{ $nasabah->telphone }}">
                        <label for="telphone">Telphone</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="nip" name="nip" value="{{ $nasabah->nip }}">
                        <label for="nip">NIP</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select id="agama" name="agama" class="form-select">
                            <option value="Islam" {{ $nasabah->agama == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ $nasabah->agama == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katholik" {{ $nasabah->agama == 'Katholik' ? 'selected' : '' }}>Katholik</option>
                            <option value="Hindu" {{ $nasabah->agama == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ $nasabah->agama == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Kong Hu Cu" {{ $nasabah->agama == 'Kong Hu Cu' ? 'selected' : '' }}>Kong Hu Cu</option>
                        </select>
                        <label for="agama">Agama</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <select id="jenis_kelamin" name="jenis_kelamin" class="form-select">
                            <option value="Laki-laki" {{ $nasabah->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ $nasabah->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir" value="{{ $nasabah->tgl_lahir }}">
                        <label for="tgl_lahir">Tanggal Lahir</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="pekerjaan" name="pekerjaan" value="{{ $nasabah->pekerjaan }}">
                        <label for="pekerjaan">Pekerjaan</label>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="alamat" name="alamat" style="height: 100px;">{{ $nasabah->alamat }}</textarea>
                        <label for="alamat">Alamat</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="file" class="form-control" id="image" name="image" onchange="previewImage(this)">
                        <label for="image">Gambar</label>
                        <div id="imagePreview" class="mt-2">
                            @if ($nasabah->image)
                            <img id="preview" src="{{ asset('assets/backend/img/' . $nasabah->image) }}" alt="Gambar" style="max-width: 200px;">
                            <input type="hidden" name="old_image" value="{{ $nasabah->image }}">
                            @else
                            <img id="preview" src="#" alt="Preview" style="display: none; max-width: 200px;">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-outline-primary">Update</button>
        </form>
    </div>
</div>
<script>
    function previewImage(input) {
        var preview = document.querySelector('#preview');
        var file = input.files[0];
        var reader = new FileReader();

        reader.onloadend = function() {
            preview.src = reader.result;
            preview.style.display = 'block';
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = '';
            preview.style.display = 'none';
        }
    }
</script>
@endsection