<!-- Modal Edit Penarikan -->
<div class="modal fade" id="editPenarikan" tabindex="-1" aria-labelledby="editPenarikanLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content shadow-lg rounded-4 border-0">
      <form method="POST" class="form-edit-penarikan">
        @csrf
        @method('PUT')
        <div class="modal-header border-0">
          <h5 class="modal-title fw-bold" id="editPenarikanLabel"><i class="fas fa-edit me-2"></i>Edit Penarikan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

          <div class="card text-center mb-3 border-0 bg-light rounded-3">
            <div class="card-header bg-primary text-white rounded-3">Informasi</div>
            <div class="card-body">
              Jumlah saldo anggota saat ini: <br>
              <strong class="fs-5 text-success">Rp <span id="editSaldoText">0.00</span></strong>
            </div>
          </div>

          {{-- Jumlah Penarikan --}}
          <div class="form-floating mb-3">
            <input type="number" min="1" step="0.01"
                   class="form-control"
                   id="edit_jumlah_penarikan"
                   name="jumlah_penarikan" required autocomplete="off">
            <label for="edit_jumlah_penarikan"><i class="fas fa-money-bill-wave me-2"></i>Jumlah Penarikan</label>
            <div id="editJumlahError" class="text-danger small mt-1"></div>
          </div>

          {{-- Keterangan --}}
          <div class="form-floating mb-3">
            <textarea class="form-control"
                      id="edit_keterangan"
                      name="keterangan"
                      placeholder="Keterangan"
                      style="height: 100px;"></textarea>
            <label for="edit_keterangan"><i class="fas fa-info-circle me-2"></i>Keterangan</label>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-success rounded-pill px-4" id="btnSubmitEditPenarikan">
            <i class="fas fa-save me-2"></i>Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('editPenarikan');
  let saldo = 0;

  modal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    const id         = button.getAttribute('data-id');
    const jumlah     = button.getAttribute('data-jumlah');
    const keterangan = button.getAttribute('data-keterangan') || '';
    saldo            = parseFloat(button.getAttribute('data-saldo') || 0);

    // Set form action
    const form = modal.querySelector('.form-edit-penarikan');
    form.action = `/penarikan/${id}`;

    // Isi field
    form.querySelector('#edit_jumlah_penarikan').value = jumlah;
    form.querySelector('#edit_keterangan').value = keterangan;
    modal.querySelector('#editSaldoText').textContent = saldo.toLocaleString('id-ID', {minimumFractionDigits:2, maximumFractionDigits:2});

    // Reset error
    document.getElementById('editJumlahError').textContent = '';
    document.getElementById('btnSubmitEditPenarikan').disabled = false;

    // Validasi awal
    cekJumlah();
  });

  // Validasi input jumlah penarikan
  const inputJumlah = document.getElementById('edit_jumlah_penarikan');
  inputJumlah.addEventListener('input', cekJumlah);

  function cekJumlah() {
    const nilai = parseFloat(inputJumlah.value || 0);
    const errorBox = document.getElementById('editJumlahError');
    const btn = document.getElementById('btnSubmitEditPenarikan');
    if (nilai > saldo) {
      errorBox.textContent = 'Jumlah penarikan tidak boleh melebihi saldo anggota!';
      btn.disabled = true;
    } else if (nilai < 1) {
      errorBox.textContent = 'Jumlah penarikan minimal Rp 1';
      btn.disabled = true;
    } else {
      errorBox.textContent = '';
      btn.disabled = false;
    }
  }
});
</script>
