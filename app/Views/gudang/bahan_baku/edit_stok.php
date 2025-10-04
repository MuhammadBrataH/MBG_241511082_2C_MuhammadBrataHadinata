<?= $this->extend('layout/main') ?>

<?= $this->section('sidebar') ?>
    <ul class="nav flex-column mb-auto">
        <li class="nav-item">
            <a href="<?= base_url('gudang/dashboard') ?>" class="nav-link">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('gudang/bahan-baku') ?>" class="nav-link active">
                <i class="fas fa-boxes me-2"></i> Lihat Bahan Baku
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('gudang/bahan-baku/add') ?>" class="nav-link">
                <i class="fas fa-plus-circle me-2"></i> Tambah Bahan Baru
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('gudang/permintaan') ?>" class="nav-link">
                <i class="fas fa-clipboard-list me-2"></i> Proses Permintaan
            </a>
        </li>
    </ul>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <div class="card border-0 rounded-4 shadow-lg">
                <div class="card-header bg-info text-white border-0 py-3 rounded-top-4">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i> Update Stok Bahan Baku</h5>
                </div>
                <div class="card-body p-4">
                    
                    <?php 
                    // Ambil error dari session flashdata
                    $errors = session()->getFlashdata('errors');
                    ?>

                    <?php if ($errors): ?>
                        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i> Validasi Gagal:</h6>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <h6 class="mb-3 text-secondary">Bahan: **<?= esc($bahan['nama']) ?>** (Stok Saat Ini: **<?= esc($bahan['jumlah']) . ' ' . esc($bahan['satuan']) ?>**)</h6>

                    <!-- Form mengarah ke route POST updateStok -->
                    <form action="<?= base_url('gudang/bahan-baku/update-stok') ?>" method="post" id="formUpdateStok">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= esc($bahan['id']) ?>">

                        <div class="mb-4">
                            <label for="jumlah" class="form-label fw-semibold">Stok Baru (<?= esc($bahan['satuan']) ?>) <span class="text-danger">*</span></label>
                            <!-- Pastikan type="number" dan min="0" untuk validasi visual -->
                            <input type="number" min="0" class="form-control" id="jumlah" name="jumlah" 
                                value="<?= old('jumlah', esc($bahan['jumlah'])) ?>" 
                                placeholder="Masukkan jumlah stok terbaru" required>
                            <div class="form-text">Stok tidak boleh kurang dari 0. Jika 0, status akan otomatis menjadi **Habis**.</div>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="d-flex justify-content-end border-top pt-3">
                            <a href="<?= base_url('gudang/bahan-baku') ?>" class="btn btn-secondary me-2 rounded-pill"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                            <button type="button" class="btn btn-info rounded-pill text-white" id="btnUpdate"><i class="fas fa-check me-1"></i> Update Stok</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Kustom -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-warning text-dark rounded-top-4">
                    <h5 class="modal-title" id="confirmModalLabel"><i class="fas fa-question-circle me-2"></i> Konfirmasi Update Stok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Apakah Anda yakin ingin memperbarui jumlah stok untuk bahan **<?= esc($bahan['nama']) ?>**?</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Tidak, Batalkan</button>
                    <button type="button" class="btn btn-info rounded-pill text-white" id="confirmUpdateBtn">Ya, Update</button>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.getElementById('btnUpdate').addEventListener('click', function(e) {
        // Tampilkan Modal Konfirmasi sebelum submit (Sesuai Aturan Umum)
        var confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        confirmModal.show();
    });

    document.getElementById('confirmUpdateBtn').addEventListener('click', function() {
        // Jika Ya, submit form
        document.getElementById('formUpdateStok').submit();
    });
</script>
<?= $this->endSection() ?>
