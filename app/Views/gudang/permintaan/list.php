<?php
/**
 * View: app/Views/gudang/permintaan/list.php
 * Halaman untuk menampilkan daftar permintaan dengan status 'menunggu'
 * bagi Petugas Gudang (Admin) untuk diproses (ACC/Tolak).
 */
?>

<?= $this->extend('layout/main') ?>

<?= $this->extend('layout/main') ?>

<?= $this->section('sidebar') ?>
    <ul class="nav flex-column mb-auto">
        <li class="nav-item">
            <a href="<?= base_url('gudang/dashboard') ?>" class="nav-link active">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('gudang/bahan-baku') ?>" class="nav-link">
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

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="text-gray-800 mb-4">Proses Persetujuan Permintaan (Menunggu)</h2>
            <p class="text-muted">Kelola permintaan bahan baku dari dapur. **Setuju (ACC)** akan mengurangi stok secara otomatis.</p>
        </div>
    </div>

    <!-- Notifikasi Sukses/Error -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-times-circle me-2"></i>Gagal memproses permintaan: <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-tasks me-2"></i> Permintaan dengan Status "Menunggu"
        </div>
        <div class="card-body">
            <?php if (empty($list_permintaan)): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-box-open me-2"></i> Tidak ada permintaan bahan baku yang saat ini berstatus **Menunggu**.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="dataTable" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th>ID</th>
                                <th>Pemohon</th>
                                <th>Tgl Masak</th>
                                <th>Menu Makan</th>
                                <th>Porsi</th>
                                <th>Diajukan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($list_permintaan as $permintaan): ?>
                                <tr>
                                    <td><?= $permintaan['id'] ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?= esc($permintaan['pemohon_name']) ?></span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($permintaan['tgl_masak'])) ?></td>
                                    <td><?= esc($permintaan['menu_makan']) ?></td>
                                    <td><?= esc($permintaan['jumlah_porsi']) ?></td>
                                    <td><?= date('d M Y H:i', strtotime($permintaan['created_at'])) ?></td>
                                    <td class="text-center">
                                        <!-- Tombol Detail -->
                                        <a href="<?= base_url('gudang/permintaan/detail/' . $permintaan['id']) ?>" class="btn btn-sm btn-info text-white me-1" title="Lihat Detail Permintaan">
                                            <i class="fas fa-info-circle"></i> Detail
                                        </a>
                                        
                                        <!-- Tombol ACC (Setuju) -->
                                        <button 
                                            class="btn btn-sm btn-success me-1 btn-acc" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#confirmAccModal" 
                                            data-id="<?= $permintaan['id'] ?>" 
                                            data-menu="<?= esc($permintaan['menu_makan']) ?>" 
                                            title="Setujui dan Kurangi Stok">
                                            <i class="fas fa-check"></i> ACC
                                        </button>

                                        <!-- Tombol Tolak -->
                                        <button 
                                            class="btn btn-sm btn-danger btn-tolak" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#tolakModal" 
                                            data-id="<?= $permintaan['id'] ?>"
                                            data-menu="<?= esc($permintaan['menu_makan']) ?>"
                                            title="Tolak Permintaan">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi ACC -->
<div class="modal fade" id="confirmAccModal" tabindex="-1" aria-labelledby="confirmAccModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="confirmAccModalLabel"><i class="fas fa-exclamation-triangle me-2"></i> Konfirmasi Persetujuan (ACC)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAcc" method="POST" action="">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Apakah Anda **YAKIN** ingin menyetujui permintaan <strong id="menuAcc"></strong>?</p>
                    <p class="text-danger small">
                        **PERHATIAN:** Menyetujui permintaan ini akan secara **PERMANEN** mengurangi stok bahan baku di Gudang. Pastikan stok cukup! Jika stok tidak mencukupi, transaksi akan dibatalkan (Rollback).
                    </p>
                    <input type="hidden" name="action" value="acc">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-check-double me-1"></i> Ya, Setujui dan Kurangi Stok</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Input Alasan Tolak -->
<div class="modal fade" id="tolakModal" tabindex="-1" aria-labelledby="tolakModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="tolakModalLabel"><i class="fas fa-times-circle me-2"></i> Konfirmasi Penolakan Permintaan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTolak" method="POST" action="">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Anda akan menolak permintaan <strong id="menuTolak"></strong>. Anda dapat memberikan alasan penolakan (Opsional).</p>
                    <div class="mb-3">
                        <label for="alasan_penolakan" class="form-label">Alasan Penolakan (Opsional)</label>
                        <textarea class="form-control" id="alasan_penolakan" name="alasan_penolakan" rows="3"></textarea>
                    </div>
                    <input type="hidden" name="action" value="tolak">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-times me-1"></i> Tolak Permintaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Script untuk mengisi ID Permintaan dan Menu ke dalam Modal ACC dan Tolak
    document.addEventListener('DOMContentLoaded', function() {
        const urlBase = '<?= base_url('gudang/permintaan/proses/') ?>';
        
        // Modal ACC
        document.querySelectorAll('.btn-acc').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const menu = this.getAttribute('data-menu');
                const form = document.getElementById('formAcc');
                
                document.getElementById('menuAcc').textContent = menu;
                form.action = urlBase + id;
            });
        });

        // Modal Tolak
        document.querySelectorAll('.btn-tolak').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const menu = this.getAttribute('data-menu');
                const form = document.getElementById('formTolak');
                
                document.getElementById('menuTolak').textContent = menu;
                form.action = urlBase + id;
            });
        });
    });
</script>

<?= $this->endSection() ?>
