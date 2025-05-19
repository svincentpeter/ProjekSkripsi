<!-- Modal Buat Pinjaman -->
<div class="modal fade" id="buatPinjaman" tabindex="-1" aria-labelledby="buatPinjamanLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content shadow">
      <form method="POST" action="{{ route('pinjaman.store') }}">
        @csrf
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="buatPinjamanLabel">
            <i class="fas fa-plus-circle me-2"></i>Buat Pinjaman
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body bg-light">
          <div class="alert alert-info text-center mb-3 shadow-sm">
            <strong>Jumlah maksimal pinjaman baru:</strong> <br>
            <span class="fw-bold text-primary">Rp {{ number_format($maxPinjamanBaru,0,',','.') }}</span>
          </div>
          {{-- Tanggal Pinjam --}}
          <div class="form-floating mb-3">
            <input type="date" name="tanggal_pinjam" id="tanggal_pinjam"
                   value="{{ old('tanggal_pinjam') }}"
                   class="form-control @error('tanggal_pinjam') is-invalid @enderror" required>
            <label for="tanggal_pinjam">Tanggal Pinjam</label>
            @error('tanggal_pinjam')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          {{-- Lama Cicilan --}}
          <div class="form-floating mb-3">
            <input type="number" name="tenor" id="tenor" min="1"
                   value="{{ old('tenor') }}"
                   class="form-control @error('tenor') is-invalid @enderror" required>
            <label for="tenor">Lama Cicilan (bulan)</label>
            @error('tenor')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          {{-- Jatuh Tempo (auto) --}}
          <div class="form-floating mb-3">
            <input type="text" name="jatuh_tempo" id="jatuh_tempo" readonly
                   class="form-control" value="{{ old('jatuh_tempo') }}">
            <label for="jatuh_tempo">Jatuh Tempo</label>
          </div>
          {{-- Bunga --}}
          <div class="form-floating mb-3">
            <input type="number" name="bunga" id="bunga" min="0" step="0.01"
                   value="{{ old('bunga') }}"
                   class="form-control @error('bunga') is-invalid @enderror" required>
            <label for="bunga">Bunga Pinjam (%)</label>
            @error('bunga')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          {{-- Jumlah Pinjam --}}
          <div class="form-floating mb-3">
            <input type="number" name="jumlah_pinjam" id="jumlah_pinjam" min="1" step="0.01"
                   value="{{ old('jumlah_pinjam') }}"
                   class="form-control @error('jumlah_pinjam') is-invalid @enderror" required>
            <label for="jumlah_pinjam">Jumlah Pinjam</label>
            @error('jumlah_pinjam')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          {{-- Pilih Anggota --}}
          <div class="form-floating mb-3">
            <select name="anggota_id" id="anggota_id"
                    class="form-select @error('anggota_id') is-invalid @enderror" required>
              <option value="">Pilih Anggota</option>
              @foreach($anggota as $m)
              <option value="{{ $m->id }}" {{ old('anggota_id')==$m->id?'selected':'' }}>
                {{ $m->name }}
              </option>
              @endforeach
            </select>
            <label for="anggota_id">Anggota</label>
            @error('anggota_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button class="btn btn-secondary rounded-pill" type="button" data-bs-dismiss="modal">
            <i class="fas fa-times"></i> Batal
          </button>
          <button class="btn btn-success rounded-pill" type="submit">
            <i class="fas fa-save"></i> Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
{{-- Hitung Jatuh Tempo Otomatis --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  const tanggalEl = document.getElementById('tanggal_pinjam');
  const tenorEl = document.getElementById('tenor');
  const jatuhEl = document.getElementById('jatuh_tempo');

  function updateJatuh() {
    const t = tanggalEl.value;
    const c = parseInt(tenorEl.value) || 0;
    if (!t || c < 1) return jatuhEl.value = '';
    const d = new Date(t);
    d.setMonth(d.getMonth() + c);
    jatuhEl.value = d.toISOString().slice(0, 10);
  }

  tanggalEl.addEventListener('change', updateJatuh);
  tenorEl.addEventListener('input', updateJatuh);
});
</script>
