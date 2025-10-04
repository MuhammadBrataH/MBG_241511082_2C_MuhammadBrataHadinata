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
        <div class="col-12">
            <div class="card border-0 rounded-4 shadow-lg">
                <div class="card-header bg-white border-0 py-3 rounded-top-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary fw-bold"><i class="fas fa-list me-2"></i> Daftar Bahan Baku</h5>
                    <a href="<?= base_url('gudang/bahan-baku/add') ?>" class="btn btn-primary rounded-pill btn-sm">
                        <i class="fas fa-plus me-1"></i> Tambah Baru
                    </a>
                </div>
                
                <div class="card-body p-4">

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                            <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                            <i class="fas fa-times-circle me-2"></i> <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="bahanBakuTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Bahan</th>
                                    <th>Kategori</th>
                                    <th>Stok</th>
                                    <th>Tgl Masuk</th>
                                    <th>Tgl Kadaluarsa</th>
                                    <th class="text-center">STATUS</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($bahan_baku)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Belum ada data bahan baku. Silakan tambahkan.</td>
                                </tr>
                                <?php else: ?>
                                    <?php $no = 1; ?>
                                    <?php foreach ($bahan_baku as $bahan): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= esc($bahan['nama']) ?></td>
                                        <td><?= esc($bahan['kategori']) ?></td>
                                        <td><?= esc($bahan['jumlah']) . ' ' . esc($bahan['satuan']) ?></td>
                                        <td><?= date('d M Y', strtotime($bahan['tanggal_masuk'])) ?></td>
                                        <td>
                                            <span class="<?= (strtotime($bahan['tanggal_kadaluarsa']) < time() || $bahan['status'] == 'kadaluarsa') ? 'text-danger fw-bold' : '' ?>">
                                                <?= date('d M Y', strtotime($bahan['tanggal_kadaluarsa'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                                $status = strtolower($bahan['status']);
                                                $badge_class = '';
                                                $text_status = '';

                                                switch ($status) {
                                                    case 'tersedia': $badge_class = 'bg-success'; $text_status = 'Tersedia'; break;
                                                    case 'segera_kadaluarsa': $badge_class = 'bg-warning text-dark'; $text_status = 'Segera Kadaluarsa'; break;
                                                    case 'kadaluarsa': $badge_class = 'bg-danger'; $text_status = 'Kadaluarsa'; break;
                                                    case 'habis': $badge_class = 'bg-secondary'; $text_status = 'Habis'; break;
                                                    default: $badge_class = 'bg-info'; $text_status = 'Tidak Diketahui';
                                                }
                                            ?>
                                            <span class="badge <?= $badge_class ?> fw-bold p-2"><?= $text_status ?></span>
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <!-- Tombol Update Stok (Aktif dan merujuk ke route GET edit-stok) -->
                                            <a href="<?= base_url('gudang/bahan-baku/edit-stok/' . $bahan['id']) ?>" 
                                                class="btn btn-sm btn-info text-white me-1" 
                                                title="Update Stok"><i class="fas fa-edit"></i> Stok</a>
                                            
                                            <!-- Tombol Hapus (Placeholder nonaktif) -->
                                            <button 
                                                class="btn btn-sm btn-secondary" 
                                                disabled
                                                title="Hapus (Nonaktif)"><i class="fas fa-trash-alt"></i></button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const currentUrl = window.location.href;
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            if (link.href === currentUrl) {
                link.classList.add('active');
            }
        });
    });
</script>
<?= $this->endSection() ?>
