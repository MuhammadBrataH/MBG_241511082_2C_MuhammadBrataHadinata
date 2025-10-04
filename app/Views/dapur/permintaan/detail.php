<?php 
    // Menggunakan layout utama
    
    // Tentukan warna badge berdasarkan status
    $badge_class = '';
    switch ($permintaan['status']) {
        case 'disetujui':
            $badge_class = 'bg-success';
            break;
        case 'ditolak':
            $badge_class = 'bg-danger';
            break;
        case 'menunggu':
        default:
            $badge_class = 'bg-warning text-dark';
            break;
    }
?>

<?= $this->extend('layout/main') ?>

<?= $this->section('sidebar') ?>
    <ul class="nav flex-column mb-auto">
        <li class="nav-item">
            <a href="<?= base_url('dapur/dashboard') ?>" class="nav-link active">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('dapur/permintaan/create') ?>" class="nav-link">
                <i class="fas fa-utensils me-2"></i> Buat Permintaan Baru
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('dapur/permintaan') ?>" class="nav-link">
                <i class="fas fa-clock me-2"></i> Lihat Status Permintaan
            </a>
        </li>
    </ul>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-dark text-white rounded-top-4 p-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-search me-2"></i> Detail Permintaan #<?= esc($permintaan['id']) ?></h5>
                    <a href="<?= base_url('dapur/permintaan/status') ?>" class="btn btn-sm btn-info text-white">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Status
                    </a>
                </div>
                
                <div class="card-body p-4">
                    <h6 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-info-circle me-2"></i> Informasi Utama Permintaan</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Tanggal Masak</label>
                            <p class="h5"><?= date('d M Y', strtotime($permintaan['tgl_masak'])) ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Status Permintaan</label>
                            <p class="h5">
                                <span class="badge <?= $badge_class ?> p-2"><?= strtoupper($permintaan['status']) ?></span>
                            </p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label text-muted">Menu Makanan</label>
                            <p class="h5"><?= esc($permintaan['menu_makan']) ?></p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label text-muted">Jumlah Porsi</label>
                            <p class="h5"><?= esc($permintaan['jumlah_porsi']) ?> Porsi</p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label text-muted">Diajukan Oleh</label>
                            <p class="h5"><?= esc($permintaan['pemohon_name']) ?> (ID: <?= esc($permintaan['pemohon_id']) ?>)</p>
                        </div>
                    </div>

                    <h6 class="text-primary border-bottom pb-2 mt-4 mb-3"><i class="fas fa-list-ul me-2"></i> Daftar Bahan yang Diminta</h6>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Bahan</th>
                                    <th>Kategori</th>
                                    <th>Jumlah Diminta</th>
                                    <th>Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($permintaan['detail_bahan'])): ?>
                                    <?php $i = 1; ?>
                                    <?php foreach ($permintaan['detail_bahan'] as $detail): ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= esc($detail['nama']) ?></td>
                                            <td><?= esc($detail['kategori']) ?></td>
                                            <td><?= esc($detail['jumlah_diminta']) ?></td>
                                            <td><?= esc($detail['satuan']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Tidak ada rincian bahan baku yang diminta.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer text-center p-3">
                    <a href="<?= base_url('dapur/permintaan/status') ?>" class="btn btn-secondary">
                        <i class="fas fa-list-ul me-1"></i> Lihat Daftar Permintaan Lain
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    echo $this->endSection(); 
?>
