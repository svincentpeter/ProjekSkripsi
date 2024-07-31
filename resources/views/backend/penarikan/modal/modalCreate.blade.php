 <div class="modal fade" id="buatPenarikan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h1 class="modal-title fs-5" id="exampleModalLabel">Buat Pinjaman</h1>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>

             <div class="modal-body">
                 <div class="card text-center">
                     <div class="card-header">
                         Informasi
                     </div>
                     <div class="card-body">
                         Jumlah saldo anda saat ini adalah <span id="saldoText">0</span>.
                     </div>
                 </div><br>
                 <form method="POST" action="{{ route('penarikan.store') }}" enctype="multipart/form-data">
                     @csrf
                     <div class="form-floating mb-3">
                         <select class="form-select @error('id_anggota') is-invalid @enderror" id="id_anggota" name="id_anggota" required>
                             <option value="">Pilih Anggota</option>
                             @foreach($anggota as $member)
                             <option value="{{ $member->id }}" data-saldo="{{ $member->saldo }}" {{ old('id_anggota') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                             @endforeach
                         </select>
                         <label for="id_anggota">Anggota</label>
                         @error('id_anggota')
                         <span class="invalid-feedback" role="alert">
                             <strong>{{ $message }}</strong>
                         </span>
                         @enderror
                     </div>
                     <div class="form-floating mb-3">
                         <input type="date" class="form-control @error('tanggal_penarikan') is-invalid @enderror" id="tanggal_penarikan" name="tanggal_penarikan" value="{{ old('tanggal_penarikan') }}" required>
                         <label for="tanggal_pinjam">Tanggal Penarikan</label>
                         @error('tanggal_penarikan')
                         <span class="invalid-feedback" role="alert">
                             <strong>{{ $message }}</strong>
                         </span>
                         @enderror
                     </div>

                     <div class="form-floating mb-3">
                         <input type="number" class="form-control @error('jumlah_penarikan') is-invalid @enderror" id="jumlah_penarikan" name="jumlah_penarikan" value="{{ old('jumlah_penarikan') }}" required>
                         <label for="jumlah_penarikan">Jumlah Penarikan</label>
                         @error('jumlah_penarikan')
                         <span class="invalid-feedback" role="alert">
                             <strong>{{ $message }}</strong>
                         </span>
                         @enderror
                     </div>
                     <div class="form-floating mb-3">
                         <textarea class="form-control @error('keterangan') is-invalid @enderror" placeholder="beri keterangan" name="keterangan" value="{{ old('keterangan') }}" id="keterangan" style="height: 150px;"></textarea>
                         <label for="keterangan">Keterangan</label>
                         @error('keterangan')
                         <span class="invalid-feedback" role="alert">
                             <strong>{{ $message }}</strong>
                         </span>
                         @enderror
                     </div>

                     <div class="modal-footer">
                         <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                         <button type="submit" class="btn btn-success">Simpan</button>
                     </div>
                 </form>
             </div>
         </div>
     </div>
 </div>