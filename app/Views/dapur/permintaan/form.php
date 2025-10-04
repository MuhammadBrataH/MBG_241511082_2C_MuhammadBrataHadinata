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
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-warning text-dark p-3 rounded-top">
            <h5 class="mb-0"><i class="fas fa-kitchen-set me-2"></i> Buat Permintaan Bahan Baru</h5>
            <small class="text-muted">Ajukan permintaan bahan baku untuk menu yang akan dimasak (disarankan H-1).</small>
        </div>
        <div class="card-body p-4">

            <!-- Notifikasi dan Errors -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-1"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('dapur/permintaan/simpan') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="tgl_masak" class="form-label fw-bold">Tanggal Rencana Memasak <span class="text-danger">*</span></label>
                        <input type="date" class="form-control <?= (session('errors.tgl_masak') ? 'is-invalid' : '') ?>" id="tgl_masak" name="tgl_masak" value="<?= old('tgl_masak') ?>" min="<?= date('Y-m-d') ?>">
                        <?php if (session('errors.tgl_masak')): ?>
                            <div class="invalid-feedback"><?= session('errors.tgl_masak') ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-5">
                        <label for="menu_makan" class="form-label fw-bold">Menu yang Akan Dibuat <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= (session('errors.menu_makan') ? 'is-invalid' : '') ?>" id="menu_makan" name="menu_makan" value="<?= old('menu_makan') ?>" placeholder="Contoh: Nasi Ayam Goreng dan Tumis Sayur">
                        <?php if (session('errors.menu_makan')): ?>
                            <div class="invalid-feedback"><?= session('errors.menu_makan') ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-3">
                        <label for="jumlah_porsi" class="form-label fw-bold">Jumlah Porsi <span class="text-danger">*</span></label>
                        <input type="number" class="form-control <?= (session('errors.jumlah_porsi') ? 'is-invalid' : '') ?>" id="jumlah_porsi" name="jumlah_porsi" value="<?= old('jumlah_porsi') ?>" min="1" placeholder="cth: 150">
                        <?php if (session('errors.jumlah_porsi')): ?>
                            <div class="invalid-feedback"><?= session('errors.jumlah_porsi') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <h6 class="mt-4 mb-3 fw-bold text-primary border-bottom pb-2">Daftar Bahan Baku yang Diminta</h6>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-hover" id="bahanList">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%">#</th>
                                <th>Nama Bahan</th>
                                <th width="15%">Stok Tersedia</th>
                                <th width="20%">Status</th>
                                <th width="15%">Jumlah Diminta</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($bahan_tersedia)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">Semua bahan sedang habis atau kadaluarsa. Tidak dapat membuat permintaan.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($bahan_tersedia as $bahan): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <?= esc($bahan['nama']) ?> (<?= esc($bahan['satuan']) ?>)
                                            <!-- Hidden field untuk ID bahan -->
                                            <input type="hidden" name="bahan_id[]" value="<?= $bahan['id'] ?>">
                                        </td>
                                        <td class="text-center"><?= $bahan['jumlah'] ?></td>
                                        <td>
                                            <span class="badge rounded-pill bg-<?= ($bahan['status'] == 'tersedia' ? 'success' : 'warning text-dark') ?>">
                                                <?= ucfirst($bahan['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   name="jumlah_diminta[]" 
                                                   class="form-control form-control-sm text-center" 
                                                   min="0" 
                                                   max="<?= $bahan['jumlah'] ?>"
                                                   placeholder="0" 
                                                   value="<?= old("jumlah_diminta.$bahan[id]") ?>">
                                            <!-- Peringatan: Form array old() di CI4 butuh penanganan khusus. Untuk sederhana, kita abaikan old() pada array input -->
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (session('errors.bahan_id')): ?>
                    <div class="alert alert-danger mt-2">
                        <?= session('errors.bahan_id') ?>
                    </div>
                <?php endif; ?>

                <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                    <button type="submit" 
                            class="btn btn-primary rounded-pill px-5 shadow-sm"
                            onclick="return confirm('Apakah Anda yakin ingin mengajukan permintaan bahan baku ini?')">
                        <i class="fas fa-paper-plane me-2"></i> Ajukan Permintaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>