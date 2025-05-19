<!-- Edit Pinjaman Modal -->
<div class="modal fade" id="editPinjaman" tabindex="-1" aria-labelledby="editPinjamanLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content shadow">
      <form method="POST" action="" class="form-edit-pinjaman">
        @csrf
        @method('PUT')
        <div class="modal-header bg-warning">
          <h5 class="modal-title" id="editPinjamanLabel">
            <i class="fas fa-edit me-2"></i>Edit Pinjaman
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body bg-light">
          <div class="alert alert-info text-center mb-3 shadow-sm">
            Batas maksimal pinjaman baru:
            <span class="fw-bold text-primary">Rp {{ number_format($maxPinjamanBaru,0,',','.') }}</span>
          </div>
          {{-- Tanggal Pinjam (readonly) --}}
          <div class="form-floating mb-3">
            <input type="date" class="form-control" id="edit_tanggal_pinjam" name="tanggal_pinjam" readonly>
            <label for="edit_tanggal_pinjam">Tanggal Pinjam</label>
          </div>
          {{-- Tenor (bulan) --}}
          <div class="form-floating mb-3">
            <input type="number" class="form-control @error('tenor') is-invalid @enderror"
                   id="edit_tenor" name="tenor" min="1" required>
            <label for="edit_tenor">Tenor (bulan)</label>
            @error('tenor')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          {{-- Jatuh Tempo (auto) --}}
          <div class="form-floating mb-3">
            <input type="date" class="form-control @error('jatuh_tempo') is-invalid @enderror"
                   id="edit_jatuh_tempo" name="jatuh_tempo" readonly>
            <label for="edit_jatuh_tempo">Jatuh Tempo</label>
            @error('jatuh_tempo')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          {{-- Jumlah Pinjam --}}
          <div class="form-floating mb-3">
            <input type="number" class="form-control @error('jumlah_pinjam') is-invalid @enderror"
                   id="edit_jumlah_pinjam" name="jumlah_pinjam" min="0.01" step="0.01" required>
            <label for="edit_jumlah_pinjam">Jumlah Pinjam</label>
            @error('jumlah_pinjam')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          {{-- Bunga (%) --}}
          <div class="form-floating mb-3">
            <input type="number" class="form-control @error('bunga') is-invalid @enderror"
                   id="edit_bunga" name="bunga" min="0" step="0.01" required>
            <label for="edit_bunga">Bunga (%)</label>
            @error('bunga')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          {{-- Anggota --}}
          <div class="form-floating mb-3">
            <select class="form-select @error('anggota_id') is-invalid @enderror"
                    id="edit_anggota_id" name="anggota_id" required>
              <option value="">-- Pilih Anggota --</option>
              @foreach($anggota as $m)
                <option value="{{ $m->id }}">{{ $m->name }}</option>
              @endforeach
            </select>
            <label for="edit_anggota_id">Anggota</label>
            @error('anggota_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">
            <i class="fas fa-times"></i> Batal
          </button>
          <button type="submit" class="btn btn-success rounded-pill">
            <i class="fas fa-save"></i> Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('editPinjaman');
  modal.addEventListener('show.bs.modal', event => {
    const btn = event.relatedTarget;
    const id = btn.getAttribute('data-id');
    const tanggal = btn.getAttribute('data-tanggal_pinjam');
    const jumlah = btn.getAttribute('data-jumlah_pinjam');
    const tenor = btn.getAttribute('data-tenor');
    const jatuhTempo = btn.getAttribute('data-jatuh_tempo');
    const bunga = btn.getAttribute('data-bunga');
    const anggotaId = btn.getAttribute('data-anggota_id');
    const form = modal.querySelector('form.form-edit-pinjaman');

    form.action = `/pinjaman/${id}`;
    form.querySelector('#edit_tanggal_pinjam').value = tanggal;
    form.querySelector('#edit_jumlah_pinjam').value = jumlah;
    form.querySelector('#edit_tenor').value = tenor;
    form.querySelector('#edit_jatuh_tempo').value = jatuhTempo;
    form.querySelector('#edit_bunga').value = bunga;
    form.querySelector('#edit_anggota_id').value = anggotaId;

    // recalc jatuh tempo if tenor changes
    form.querySelector('#edit_tenor').addEventListener('input', e => {
      const t0 = new Date(tanggal);
      const t1 = parseInt(e.target.value, 10);
      if (!isNaN(t1)) {
        t0.setMonth(t0.getMonth() + t1);
        form.querySelector('#edit_jatuh_tempo').value = t0.toISOString().slice(0,10);
      }
    });
  });
});
</script>
