@extends('backend.app')
@section('title', 'Edit User')
@section('content')
<div class="container-fluid pt-4 px-4">
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <h2 class="mb-4">Edit Pengguna</h2>
    <div class="bg-light rounded h-100 p-4">
        <form action="{{ route('users.update', $edituser->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('put')
            <div class="modal-body">
                <div class="form-floating mb-3">
                    <input type="text" name="name" value="{{ $edituser->name }}" placeholder="{{ __('Name') }}" class="form-control">
                    <label for="nama">Name</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="email" value="{{ $edituser->email }}" placeholder="{{ __('Email') }}" class="form-control">
                    <label for="email">Email</label>
                </div>
                <div class="form-group mb-4">
                    <label class="mb-2">Role User <strong style="color: red;">*</strong></label>
                    <select class="form-select" multiple="" aria-label="multiple select example" name="roles[]">
                        @foreach($roles as $roleId => $roleName)
                        <option value="{{ $roleId }}" {{ in_array($roleId, $userRole) ? 'selected' : '' }}>
                            {{ $roleName }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" name="password" placeholder="{{ __('Password') }}" class="form-control">
                    <label for="password">Password</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" name="confirm-password" placeholder="{{ __('Confirm Password') }}" class="form-control">
                    <label for="confirm-password">Confirm Password</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <label for="image">Gambar</label>
                </div>

                <div class="form-group">
                    @if(!empty($edituser->image))
                    <img id="image-preview" src="{{ asset('assets/backend/img/' . $edituser->image) }}" alt="{{ $edituser->name }}" class="img-thumbnail" style="width: 150px;">
                    @else
                    <div class="text-center py-4">No Image</div>
                    @endif
                </div>
            </div>

            <a href="{{route('user')}}" class="btn btn-info">kembali</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
<script>
    var inputImage = document.getElementById('image');
    var imagePreview = document.getElementById('image-preview');

    inputImage.addEventListener('change', function() {
        if (inputImage.files && inputImage.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            }

            reader.readAsDataURL(inputImage.files[0]);
        } else {
            imagePreview.src = '#';
            imagePreview.style.display = 'none';
        }
    });
</script>
@endsection