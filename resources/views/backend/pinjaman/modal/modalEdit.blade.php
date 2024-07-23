<!-- Edit Pinjaman Modal -->
<div class="modal fade" id="editPinjaman" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Pinjaman</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card text-center">
                    <div class="card-header">Informasi</div>
                    <div class="card-body">
                        Jumlah maksimal pinjaman baru adalah Rp {{ number_format($maxPinjamanBaru, 0, ',', '.') }}.
                    </div>
                </div>
                <br>
                <form method="POST" action="" class="form-edit" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="edit_tanggal_pinjam">Tanggal Pinjam</label>
                        <input type="text" class="form-control @error('tanggal_pinjam') is-invalid @enderror" id="edit_tanggal_pinjam" name="tanggal_pinjam" readonly>
                        @error('tanggal_pinjam')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="edit_jml_cicilan">Lama/bulan</label>
                        <input type="number" class="form-control @error('jml_cicilan') is-invalid @enderror edit_jml_cicilan" id="edit_jml_cicilan" name="jml_cicilan" required>
                        @error('jml_cicilan')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="edit_jatuh_tempo">Jatuh Tempo</label>
                        <input type="text" class="form-control @error('jatuh_tempo') is-invalid @enderror" id="edit_jatuh_tempo" name="jatuh_tempo" readonly>
                        @error('jatuh_tempo')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="edit_jml_pinjam">Jumlah Pinjam</label>
                        <input type="number" class="form-control @error('jml_pinjam') is-invalid @enderror" id="edit_jml_pinjam" name="jml_pinjam" required>
                        @error('jml_pinjam')
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editPinjamanModal = document.getElementById('editPinjaman');

        editPinjamanModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var tanggal_pinjam = button.getAttribute('data-tanggal_pinjam');
            var jml_pinjam = button.getAttribute('data-jml_pinjam');
            var jml_cicilan = button.getAttribute('data-jml_cicilan');
            var jatuh_tempo = button.getAttribute('data-jatuh_tempo');
            var formEdit = editPinjamanModal.querySelector('.form-edit');

            formEdit.setAttribute('action', `/pinjaman/${id}`);
            formEdit.querySelector('#edit_tanggal_pinjam').value = tanggal_pinjam;
            formEdit.querySelector('#edit_jml_pinjam').value = jml_pinjam;
            formEdit.querySelector('#edit_jml_cicilan').value = jml_cicilan;
            formEdit.querySelector('#edit_jatuh_tempo').value = jatuh_tempo;

            // Calculate new jatuh tempo based on jml_cicilan
            formEdit.querySelector('#edit_jml_cicilan').addEventListener('input', function() {
                var tanggalPinjamValue = new Date(tanggal_pinjam);
                var lamaCicilan = parseInt(this.value, 10);

                if (!isNaN(lamaCicilan) && tanggalPinjamValue) {
                    var newJatuhTempo = new Date(tanggalPinjamValue);
                    newJatuhTempo.setMonth(tanggalPinjamValue.getMonth() + lamaCicilan);
                    var formattedJatuhTempo = newJatuhTempo.toISOString().split('T')[0];
                    formEdit.querySelector('#edit_jatuh_tempo').value = formattedJatuhTempo;
                } else {
                    formEdit.querySelector('#edit_jatuh_tempo').value = '';
                }
            });
        });
    });
</script>