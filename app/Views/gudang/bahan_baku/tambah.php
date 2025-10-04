<?= $this->extend('layout/main') ?>

<?= $this->section('sidebar') ?>
    <ul class="nav flex-column mb-auto">
        <li class="nav-item">
            <a href="<?= base_url('gudang/dashboard') ?>" class="nav-link">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('gudang/bahan-baku') ?>" class="nav-link">
                <i class="fas fa-boxes me-2"></i> Lihat Bahan Baku
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('gudang/bahan-baku/add') ?>" class="nav-link active">
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
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 rounded-4 shadow-lg">
                <div class="card-header bg-primary text-white border-0 py-3 rounded-top-4">
                    <h5 class="mb-0"><i class="fas fa-box-open me-2"></i> Form Input Bahan Baku Baru</h5>
                </div>
                <div class="card-body p-4">
                    
                    <?php 
                    // Ambil error dari session flashdata
                    $errors = session()->getFlashdata('errors');
                    // Ambil instance Validation untuk menampilkan error spesifik per field
                    $validation = service('validation');
                    ?>

                    <?php if ($errors): ?>
                        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i> Mohon Perbaiki Kesalahan Input:</h6>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Pastikan URL mengarah ke route POST yang benar -->
                    <form action="<?= base_url('gudang/bahan-baku/save') ?>" method="post" id="formTambahBahan">
                        <?= csrf_field() ?>

                        <!-- Row 1: Nama dan Kategori -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nama" class="form-label fw-semibold">Nama Bahan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= $errors && isset($errors['nama']) ? 'is-invalid' : '' ?>" id="nama" name="nama" value="<?= old('nama') ?>" placeholder="Contoh: Beras Medium" required>
                                <?php if ($errors && isset($errors['nama'])): ?>
                                    <div class="invalid-feedback"><?= $errors['nama'] ?></div>
                                <?php endif ?>
                            </div>
                            <div class="col-md-6">
                                <label for="kategori" class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= $errors && isset($errors['kategori']) ? 'is-invalid' : '' ?>" id="kategori" name="kategori" value="<?= old('kategori') ?>" placeholder="Contoh: Karbohidrat" required>
                                <?php if ($errors && isset($errors['kategori'])): ?>
                                    <div class="invalid-feedback"><?= $errors['kategori'] ?></div>
                                <?php endif ?>
                            </div>
                        </div>

                        <!-- Row 2: Jumlah dan Satuan -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="jumlah" class="form-label fw-semibold">Jumlah (Stok Awal) <span class="text-danger">*</span></label>
                                <!-- Menggunakan type="number" dan min="0" untuk memastikan validasi front-end -->
                                <input type="number" min="0" class="form-control <?= $errors && isset($errors['jumlah']) ? 'is-invalid' : '' ?>" id="jumlah" name="jumlah" value="<?= old('jumlah', 0) ?>" required>
                                <div class="form-text">Masukkan angka stok awal (>= 0).</div>
                                <?php if ($errors && isset($errors['jumlah'])): ?>
                                    <div class="invalid-feedback"><?= $errors['jumlah'] ?></div>
                                <?php endif ?>
                            </div>
                            <div class="col-md-6">
                                <label for="satuan" class="form-label fw-semibold">Satuan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= $errors && isset($errors['satuan']) ? 'is-invalid' : '' ?>" id="satuan" name="satuan" value="<?= old('satuan') ?>" placeholder="Contoh: kg, butir, ikat" required>
                                <?php if ($errors && isset($errors['satuan'])): ?>
                                    <div class="invalid-feedback"><?= $errors['satuan'] ?></div>
                                <?php endif ?>
                            </div>
                        </div>

                        <!-- Row 3: Tanggal Masuk dan Tanggal Kadaluarsa -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="tanggal_masuk" class="form-label fw-semibold">Tanggal Masuk <span class="text-danger">*</span></label>
                                <input type="date" class="form-control <?= $errors && isset($errors['tanggal_masuk']) ? 'is-invalid' : '' ?>" id="tanggal_masuk" name="tanggal_masuk" value="<?= old('tanggal_masuk', date('Y-m-d')) ?>" required>
                                <?php if ($errors && isset($errors['tanggal_masuk'])): ?>
                                    <div class="invalid-feedback"><?= $errors['tanggal_masuk'] ?></div>
                                <?php endif ?>
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_kadaluarsa" class="form-label fw-semibold">Tanggal Kadaluarsa <span class="text-danger">*</span></label>
                                <input type="date" class="form-control <?= $errors && isset($errors['tanggal_kadaluarsa']) ? 'is-invalid' : '' ?>" id="tanggal_kadaluarsa" name="tanggal_kadaluarsa" value="<?= old('tanggal_kadaluarsa') ?>" required>
                                <div class="form-text">Tanggal harus >= Tanggal Masuk.</div>
                                <?php if ($errors && isset($errors['tanggal_kadaluarsa'])): ?>
                                    <div class="invalid-feedback"><?= $errors['tanggal_kadaluarsa'] ?></div>
                                <?php endif ?>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="d-flex justify-content-end border-top pt-3">
                            <a href="<?= base_url('gudang/bahan-baku') ?>" class="btn btn-secondary me-2 rounded-pill"><i class="fas fa-arrow-left me-1"></i> Batal</a>
                            <button type="button" class="btn btn-primary rounded-pill" id="btnSimpan"><i class="fas fa-save me-1"></i> Simpan Bahan Baku</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Kustom (Menggantikan window.confirm) -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-warning text-dark rounded-top-4">
                    <h5 class="modal-title" id="confirmModalLabel"><i class="fas fa-question-circle me-2"></i> Konfirmasi Penyimpanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Apakah Anda yakin ingin menyimpan data bahan baku baru ini ke dalam sistem?</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Tidak, Batalkan</button>
                    <button type="button" class="btn btn-primary rounded-pill" id="confirmSaveBtn">Ya, Simpan</button>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.getElementById('btnSimpan').addEventListener('click', function(e) {
        // Tampilkan Modal Konfirmasi sebelum submit (Sesuai Aturan Umum)
        var confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        confirmModal.show();
    });

    document.getElementById('confirmSaveBtn').addEventListener('click', function() {
        // Jika Ya, submit form
        document.getElementById('formTambahBahan').submit();
    });
</script>
<?= $this->endSection() ?>
