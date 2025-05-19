<!-- create simpanan -->
<div class="modal fade" id="buatSimpanan" tabindex="-1" aria-labelledby="buatSimpananLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="buatSimpananLabel">Buat Simpanan</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="formSimpanan"
            method="POST"
            action="{{ route('simpanan.store') }}"
            enctype="multipart/form-data">
        @csrf

        <div class="modal-body">
          {{-- Kode Transaksi --}}
          <div class="form-floating mb-3">
            <input
              type="text"
              class="form-control"
              id="kode_transaksi"
              name="kode_transaksi"
              value="{{ $kodeTransaksi }}"
              readonly
            >
            <label for="kode_transaksi">Kode Transaksi</label>
          </div>

          {{-- Tanggal Simpanan --}}
          <div class="form-floating mb-3">
            <input
              type="date"
              class="form-control @error('tanggal_simpanan') is-invalid @enderror"
              id="tanggal_simpanan"
              name="tanggal_simpanan"
              value="{{ old('tanggal_simpanan') }}"
              required
            >
            <label for="tanggal_simpanan">Tanggal Simpanan</label>
            @error('tanggal_simpanan')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Anggota --}}
          <div class="form-floating mb-3">
            <select
              id="anggota_id"
              name="anggota_id"
              class="form-select @error('anggota_id') is-invalid @enderror"
              required
            >
              <option value="" disabled {{ old('anggota_id') ? '' : 'selected' }}>Pilih Anggota</option>
              @foreach($anggotaList as $nasabah)
                <option
                  value="{{ $nasabah->id }}"
                  {{ old('anggota_id') == $nasabah->id ? 'selected' : '' }}
                >{{ $nasabah->name }}</option>
              @endforeach
            </select>
            <label for="anggota_id">Nama Anggota</label>
            @error('anggota_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Jenis Simpanan --}}
          <div class="form-floating mb-3">
            <select
              id="jenis_simpanan_id"
              name="jenis_simpanan_id"
              class="form-select @error('jenis_simpanan_id') is-invalid @enderror"
              required
            >
              <option value="" disabled {{ old('jenis_simpanan_id') ? '' : 'selected' }}>Pilih Jenis Simpanan</option>
              @foreach($jenisList as $jenis)
                <option
                  value="{{ $jenis->id }}"
                  data-nominal="{{ $jenis->id == 1 ? 250000 : ($jenis->id == 2 ? 20000 : 0) }}"
                  {{ old('jenis_simpanan_id') == $jenis->id ? 'selected' : '' }}
                >{{ $jenis->nama }}</option>
              @endforeach
            </select>
            <label for="jenis_simpanan_id">Jenis Simpanan</label>
            @error('jenis_simpanan_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Jumlah Simpanan --}}
          <div class="form-floating mb-3">
            <input
              type="number"
              class="form-control @error('jumlah_simpanan') is-invalid @enderror"
              id="jumlah_simpanan"
              name="jumlah_simpanan"
              value="{{ old('jumlah_simpanan') }}"
              required
            >
            <label for="jumlah_simpanan">Jumlah Simpanan</label>
            @error('jumlah_simpanan')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Bukti Pembayaran --}}
          <div class="mb-3">
            <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran</label>
            <input
              type="file"
              class="form-control @error('bukti_pembayaran') is-invalid @enderror"
              id="bukti_pembayaran"
              name="bukti_pembayaran"
              accept="image/*,application/pdf"
              required
            >
            @error('bukti_pembayaran')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            <img
              id="image-preview"
              class="img-fluid mt-2"
              style="display: none; max-width: 50%;"
            >
            <div id="crop-container"
                 class="mt-2"
                 style="display: none; overflow: hidden; max-height: 70vh;">
              <img id="crop-image" class="img-fluid">
            </div>
            <button type="button"
                    id="crop-button"
                    class="btn btn-secondary mt-2"
                    style="display: none;">
              Crop Image
            </button>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button"
                  class="btn btn-outline-secondary"
                  data-bs-dismiss="modal">
            Close
          </button>
          <button type="submit" class="btn btn-success">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const jenisSelect = document.getElementById('jenis_simpanan_id');
    const jumlahInput = document.getElementById('jumlah_simpanan');

    function syncNominal() {
      const opt = jenisSelect.options[jenisSelect.selectedIndex];
      const defaultNominal = opt?.dataset.nominal || '0';
      jumlahInput.value = defaultNominal;
      jumlahInput.readOnly = defaultNominal !== '0';
    }

    syncNominal();
    jenisSelect.addEventListener('change', syncNominal);

    // Image cropper (jika dipakai)
    let cropper;
    const fileInput    = document.getElementById('bukti_pembayaran');
    const cropContainer= document.getElementById('crop-container');
    const cropImage    = document.getElementById('crop-image');
    const cropButton   = document.getElementById('crop-button');
    const preview      = document.getElementById('image-preview');

    fileInput.addEventListener('change', e => {
      const file = e.target.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = () => {
        cropImage.src = reader.result;
        cropContainer.style.display = 'block';
        if (cropper) cropper.destroy();
        cropper = new Cropper(cropImage, { aspectRatio: 1, viewMode: 1 });
        cropButton.style.display = 'inline-block';
      };
      reader.readAsDataURL(file);
    });

    cropButton.addEventListener('click', () => {
      const canvas = cropper.getCroppedCanvas();
      preview.src = canvas.toDataURL();
      preview.style.display = 'block';
      cropContainer.style.display = 'none';
      cropButton.style.display = 'none';
    });
  });
</script>
