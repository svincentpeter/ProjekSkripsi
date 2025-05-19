<div class="modal fade" id="buatPenarikan" tabindex="-1" aria-labelledby="buatPenarikanLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content shadow-lg rounded-4 border-0">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold" id="buatPenarikanLabel"><i class="fas fa-receipt me-2"></i>Tambah Penarikan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="card text-center mb-3 border-0 bg-light rounded-3">
          <div class="card-header bg-primary text-white rounded-3">Informasi</div>
          <div class="card-body">
            Jumlah saldo anggota saat ini: <br>
            <strong class="fs-5 text-success">Rp <span id="saldoText">0.00</span></strong>
          </div>
        </div>
        <form method="POST" action="{{ route('penarikan.store') }}" id="formCreatePenarikan">
          @csrf

          {{-- Pilih Anggota --}}
          <div class="form-floating mb-3">
            <select name="anggota_id" id="anggota_id"
                    class="form-select @error('anggota_id') is-invalid @enderror" required>
              <option value="">Pilih Anggota</option>
              @foreach($anggota as $member)
              <option value="{{ $member->id }}"
                      data-saldo="{{ $member->saldo }}"
                      {{ old('anggota_id') == $member->id ? 'selected' : '' }}>
                {{ $member->name }}
              </option>
              @endforeach
            </select>
            <label for="anggota_id"><i class="fas fa-user me-2"></i>Anggota</label>
            @error('anggota_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Tanggal Penarikan --}}
          <div class="form-floating mb-3">
            <input type="date" name="tanggal_penarikan" id="tanggal_penarikan"
                   value="{{ old('tanggal_penarikan') ?? now()->toDateString() }}"
                   class="form-control @error('tanggal_penarikan') is-invalid @enderror" required>
            <label for="tanggal_penarikan"><i class="fas fa-calendar me-2"></i>Tanggal Penarikan</label>
            @error('tanggal_penarikan')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Jumlah Penarikan --}}
          <div class="form-floating mb-3">
            <input type="number" min="1" name="jumlah_penarikan" id="jumlah_penarikan" step="0.01"
                   value="{{ old('jumlah_penarikan') }}"
                   class="form-control @error('jumlah_penarikan') is-invalid @enderror" required autocomplete="off">
            <label for="jumlah_penarikan"><i class="fas fa-money-bill-wave me-2"></i>Jumlah Penarikan</label>
            <div id="jumlahError" class="text-danger small mt-1"></div>
            @error('jumlah_penarikan')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Keterangan --}}
          <div class="form-floating mb-3">
            <textarea name="keterangan" id="keterangan" rows="4"
                      class="form-control @error('keterangan') is-invalid @enderror"
                      placeholder="Keterangan">{{ old('keterangan') }}</textarea>
            <label for="keterangan"><i class="fas fa-info-circle me-2"></i>Keterangan</label>
            @error('keterangan')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-success rounded-pill px-4" id="btnSubmitPenarikan">
              <i class="fas fa-save me-2"></i>Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Script untuk update saldoText dan validasi jumlah --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const anggotaSelect = document.getElementById('anggota_id');
  const saldoText = document.getElementById('saldoText');
  const jumlahInput = document.getElementById('jumlah_penarikan');
  const errorBox = document.getElementById('jumlahError');
  const btnSubmit = document.getElementById('btnSubmitPenarikan');
  let saldo = 0;

  anggotaSelect.addEventListener('change', function() {
    const selected = this.selectedOptions[0];
    saldo = parseFloat(selected.dataset.saldo || 0);
    saldoText.textContent = saldo.toLocaleString('id-ID', {minimumFractionDigits:2, maximumFractionDigits:2});
    cekJumlah();
  });

  jumlahInput.addEventListener('input', cekJumlah);

  function cekJumlah() {
    const nilai = parseFloat(jumlahInput.value || 0);
    if (nilai > saldo) {
      errorBox.textContent = 'Jumlah penarikan tidak boleh melebihi saldo anggota!';
      btnSubmit.disabled = true;
    } else if (nilai < 1) {
      errorBox.textContent = 'Jumlah penarikan minimal Rp 1';
      btnSubmit.disabled = true;
    } else {
      errorBox.textContent = '';
      btnSubmit.disabled = false;
    }
  }

  // Trigger saat modal dibuka (biar saldo langsung update)
  document.getElementById('buatPenarikan').addEventListener('show.bs.modal', function () {
    if (anggotaSelect.value) anggotaSelect.dispatchEvent(new Event('change'));
  });
});
</script>
