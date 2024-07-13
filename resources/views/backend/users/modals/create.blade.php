 <!-- edit pengguna -->

 <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">

                 <h4 class="modal-title">{{ __('Create User') }}</h4>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             @if ($errors->any())
             <div class="alert alert-danger">
                 <ul>
                     @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
                     @endforeach
                 </ul>
             </div>
             @endif
             <div class="modal-body">
                 <form method="POST" action="{{ route('storeUser') }}" enctype="multipart/form-data">
                     @csrf
                     <div class="form-floating mb-3">
                         <input type="text" class="form-control" id="name" value="{{ old('name') }}" name="name" placeholder="">
                         <label for="nama">Name</label>
                     </div>
                     <div class="form-floating mb-3">
                         <input type="text" class="form-control" value="{{ old('email') }}" id="email" name="email" placeholder="">
                         <label for="email">Email</label>
                     </div>
                     <div class="form-floating mb-3">
                         <input type="password" class="form-control" id="password" name="password" placeholder="">
                         <label for="password">Password</label>
                     </div>
                     <div class="form-floating mb-3">
                         <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="">
                         <label for="password_confirmation">Password Confirmation</label>
                     </div>
                     <div class="form-group mb-4">
                         <label class="mb-2">Role User <strong style="color: red;">*</strong></label>
                         <select class="form-select" multiple="" aria-label="multiple select example" name="roles[]">
                             @foreach ($roles as $role)
                             <option value="{{ $role->name }}">{{ $role->name }}</option>
                             @endforeach
                         </select>
                     </div>

                     <div class="mb-3">
                         <label for="image" class="form-label">Masukkan Foto</label>
                         <input class="form-control form-control-sm" id="image" name="image" accept="image/*" type="file">
                         @error('image')
                         <div class="text-danger">{{ $message }}</div>
                         @enderror
                     </div><br>
                     <div class="modal-footer">
                         <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                         <button type="submit" class="btn btn-outline-primary">Add User</button>

                     </div>
                 </form>
             </div>
         </div>
     </div>
 </div>