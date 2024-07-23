<!-- Modal Edit Penarikan -->
<div class="modal fade" id="editPenarikan" tabindex="-1" aria-labelledby="editPenarikanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editPenarikanLabel">Edit Penarikan</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" class="form-edit-penarikan">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Saldo Anggota:</label>
                        <div class="card text-center">
                            <div class="card-header">
                                Informasi
                            </div>
                            <div class="card-body">
                                Jumlah saldo anda saat ini adalah <span id="saldoText">0</span>.
                            </div>
                        </div><br>
                    </div>
                    <div class="form-group">
                        <label for="edit_jumlah_penarikan">Jumlah Penarikan:</label>
                        <input type="number" class="form-control" id="edit_jumlah_penarikan" name="jumlah_penarikan" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_keterangan">Keterangan:</label>
                        <input type="text" class="form-control" id="edit_keterangan" name="keterangan">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editPenarikanModal = document.getElementById('editPenarikan');

        editPenarikanModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var jumlahPenarikan = button.getAttribute('data-jumlah_penarikan');
            var saldo = button.getAttribute('data-saldo');
            var formEdit = editPenarikanModal.querySelector('.form-edit-penarikan');

            formEdit.setAttribute('action', `/penarikan/${id}`);
            formEdit.querySelector('#edit_jumlah_penarikan').value = jumlahPenarikan;
            formEdit.querySelector('#edit_keterangan').value = button.getAttribute('data-keterangan');
            editPenarikanModal.querySelector('#saldoText').textContent = saldo;

            // Preview image
            formEdit.querySelector('#edit_bukti_pembayaran').addEventListener('change', function(event) {
                var reader = new FileReader();
                reader.onload = function() {
                    var output = document.getElementById('edit_image_preview');
                    output.src = reader.result;
                    output.style.display = 'block';
                };
                reader.readAsDataURL(event.target.files[0]);
            });
        });
    });
</script>