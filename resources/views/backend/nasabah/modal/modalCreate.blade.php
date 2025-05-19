<!-- The Modal -->
<div class="modal fade" id="buatAnggota" tabindex="-1" aria-labelledby="buatAnggotaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="buatAnggotaLabel">Tambah Nasabah</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('nasabah.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="row">
            {{-- Nama & NIP --}}
            <div class="col-md-6">
              <div class="form-floating mb-3">
                <input type="text" name="name" id="name"
                       value="{{ old('name') }}"
                       class="form-control @error('name') is-invalid @enderror">
                <label for="name">Nama</label>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="form-floating mb-3">
                <input type="text" name="nip" id="nip"
                       value="{{ old('nip') }}"
                       class="form-control @error('nip') is-invalid @enderror">
                <label for="nip">NIP</label>
                @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              {{-- Telepon & Tanggal Lahir --}}
              <div class="form-floating mb-3">
                <input type="tel" name="telphone" id="telphone"
                       value="{{ old('telphone') }}"
                       class="form-control @error('telphone') is-invalid @enderror">
                <label for="telphone">Telepon</label>
                @error('telphone') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="form-floating mb-3">
                <input type="date" name="tgl_lahir" id="tgl_lahir"
                       value="{{ old('tgl_lahir') }}"
                       class="form-control @error('tgl_lahir') is-invalid @enderror">
                <label for="tgl_lahir">Tanggal Lahir</label>
                @error('tgl_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            {{-- Alamat & Pekerjaan --}}
            <div class="col-md-6">
              <div class="form-floating mb-3">
                <textarea name="alamat" id="alamat" rows="3"
                          class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat') }}</textarea>
                <label for="alamat">Alamat</label>
                @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="form-floating mb-3">
                <input type="text" name="pekerjaan" id="pekerjaan"
                       value="{{ old('pekerjaan') }}"
                       class="form-control @error('pekerjaan') is-invalid @enderror">
                <label for="pekerjaan">Pekerjaan</label>
                @error('pekerjaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              {{-- Agama & Jenis Kelamin --}}
              <div class="mb-3">
                <label for="agama" class="form-label">Agama</label>
                <select name="agama" id="agama"
                        class="form-select @error('agama') is-invalid @enderror">
                  <option value="" disabled selected>Pilih Agama</option>
                  <option value="Islam"     {{ old('agama')=='Islam'? 'selected':'' }}>Islam</option>
                  <option value="Kristen"   {{ old('agama')=='Kristen'? 'selected':'' }}>Kristen</option>
                  <option value="Katolik"   {{ old('agama')=='Katolik'? 'selected':'' }}>Katolik</option>
                  <option value="Hindu"     {{ old('agama')=='Hindu'? 'selected':'' }}>Hindu</option>
                  <option value="Buddha"    {{ old('agama')=='Buddha'? 'selected':'' }}>Buddha</option>
                  <option value="Konghucu"  {{ old('agama')=='Konghucu'?'selected':'' }}>Konghucu</option>
                </select>
                @error('agama') <div class="text-danger">{{ $message }}</div> @enderror
              </div>
              <div class="mb-3">
                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                <select name="jenis_kelamin" id="jenis_kelamin"
                        class="form-select @error('jenis_kelamin') is-invalid @enderror">
                  <option value="" disabled selected>Pilih Jenis Kelamin</option>
                  <option value="L" {{ old('jenis_kelamin')=='L'? 'selected':'' }}>Laki-Laki</option>
                  <option value="P" {{ old('jenis_kelamin')=='P'? 'selected':'' }}>Perempuan</option>
                </select>
                @error('jenis_kelamin') <div class="text-danger">{{ $message }}</div> @enderror
              </div>
            </div>

            {{-- Foto --}}
            <div class="col-12">
              <label for="image" class="form-label">Foto (opsional)</label>
              <input type="file" name="image" id="image"
                     class="form-control @error('image') is-invalid @enderror"
                     accept="image/*">
              @error('image') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button"
                  class="btn btn-secondary"
                  data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
    let cropper;
    document.getElementById('image').addEventListener('change', function(event) {
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