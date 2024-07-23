 <!-- create pinjaman -->
 <div class="modal fade" id="buatSimpanan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h1 class="modal-title fs-5" id="exampleModalLabel">Buat Simpanan</h1>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>

             <div class="modal-body">
                 <form method="POST" action="{{ route('simpanan.store') }}" enctype="multipart/form-data" id="formSimpanan">
                     @csrf
                     <div class="form-floating mb-3">
                         <input type="text" class="form-control" id="kodeTransaksiSimpanan" name="kodeTransaksiSimpanan" value="{{ $kodeTransaksiSimpanan }}" readonly>
                         <label for="kodeTransaksiSimpanan">Kode Transaksi</label>
                     </div>
                     <div class="form-floating mb-3">
                         <input type="date" class="form-control" value="{{ old('tanggal_simpanan') }}" id="tanggal_simpanan" name="tanggal_simpanan">
                         <label for="tanggal_simpanan">Tanggal Simpanan</label>
                     </div>
                     <div class="form-floating mb-3">
                         <select id="id_anggota" name="id_anggota" class="form-select">
                             <option value="" selected disabled>Pilih Anggota</option>
                             @foreach($namaNasabah as $nasabah)
                             <option value="{{ $nasabah->id }}" {{ old('id_anggota') == $nasabah->id ? 'selected' : '' }}>{{ $nasabah->name }}</option>
                             @endforeach
                         </select>
                         <label for="id_anggota">Nama Anggota</label>
                     </div>
                     <div class="form-floating mb-3">
                         <select id="id_jenis_simpanan" name="id_jenis_simpanan" class="form-select">
                             <option value="" selected disabled>Pilih Jenis Simpanan</option>
                             @foreach($jenisSimpanan as $jenis)
                             <option value="{{ $jenis->id }}" data-nominal="{{ $jenis->id == 1 ? 250000 : ($jenis->id == 2 ? 20000 : 0) }}" {{ old('id_jenis_simpanan') == $jenis->id ? 'selected' : '' }}>{{ $jenis->nama }}</option>
                             @endforeach
                         </select>
                         <label for="id_jenis_simpanan">Jenis Simpanan</label>

                     </div>
                     <div class="form-floating mb-3">
                         <input type="number" class="form-control" value="{{ old('jml_simpanan') }}" id="jml_simpanan" name="jml_simpanan">
                         <label for="jml_simpanan">Jumlah Simpanan</label>
                     </div>

                     <div class="mb-3">
                         <label for="image" class="form-label">Bukti Pembayaran</label>
                         <input class="form-control form-control-sm" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*" type="file">
                         @error('image')
                         <div class="text-danger">{{ $message }}</div>
                         @enderror
                         <img id="image-preview" src="#" alt="Image Preview" style="display: none; max-width: 50%; height: auto; margin-top: 10px;">
                         <div id="crop-container" style="width: 100%; max-height: 70vh; overflow: hidden; display: none;">
                             <img id="crop-image" src="#" alt="Crop Image" style="max-width: 100%; height: auto;">
                         </div>
                         <button type="button" class="btn btn-secondary mt-2" id="crop-button" style="display: none;">Crop Image</button>
                     </div>

                     <div class="modal-footer">
                         <button type="button" class="btn btn-outline-secondary  " data-bs-dismiss="modal">Close</button>
                         <button type="submit" class="btn btn-success  ">Simpan</button>
                     </div>
                 </form>
             </div>
         </div>
     </div>
 </div>
 <script>
     document.addEventListener('DOMContentLoaded', function() {
         var jenisSimpanan = document.getElementById('id_jenis_simpanan');
         var jmlSimpananInput = document.getElementById('jml_simpanan');

         // Cek nilai awal saat halaman dimuat
         var selectedOption = jenisSimpanan.options[jenisSimpanan.selectedIndex];
         if (selectedOption) {
             var nominalDefault = selectedOption.getAttribute('data-nominal');
             jmlSimpananInput.value = nominalDefault;
             jmlSimpananInput.readOnly = (nominalDefault !== '0'); // Set read-only jika ada nominal default
         }

         // Event untuk mengatur nilai saat pilihan jenis simpanan berubah
         jenisSimpanan.addEventListener('change', function() {
             var selectedOption = jenisSimpanan.options[jenisSimpanan.selectedIndex];
             var nominalDefault = selectedOption.getAttribute('data-nominal');
             jmlSimpananInput.value = nominalDefault;
             jmlSimpananInput.readOnly = (nominalDefault !== '0'); // Set read-only jika ada nominal default
         });
     });
 </script>
 <script>
     let cropper;
     document.getElementById('bukti_pembayaran').addEventListener('change', function(event) {
         var reader = new FileReader();
         reader.onload = function() {
             var output = document.getElementById('crop-image');
             output.src = reader.result;
             document.getElementById('crop-container').style.display = 'block';

             // Initialize cropper
             if (cropper) {
                 cropper.destroy();
             }
             cropper = new Cropper(output, {
                 aspectRatio: 1,
                 viewMode: 1,
                 scalable: true,
                 zoomable: true,
             });
             document.getElementById('crop-button').style.display = 'inline-block';
         };
         reader.readAsDataURL(event.target.files[0]);
     });

     document.getElementById('crop-button').addEventListener('click', function() {
         var canvas = cropper.getCroppedCanvas();
         var output = document.getElementById('image-preview');
         output.src = canvas.toDataURL();
         output.style.display = 'block';

         document.getElementById('crop-container').style.display = 'none';
         document.getElementById('crop-button').style.display = 'none';
     });
 </script>