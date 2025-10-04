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
        <div class="card-header bg-primary text-white p-3 rounded-top">
            <h5 class="mb-0 text-white"><i class="fas fa-boxes me-2"></i> <?= $title ?></h5>
        </div>
        <div class="card-body p-4">
            
            <!-- Notifikasi Sukses/Gagal -->
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

            <!-- Tombol Tambah Bahan Baku -->
            <a href="<?= base_url('gudang/bahan-baku/add') ?>" class="btn btn-success mb-3 shadow-sm rounded-pill px-4">
                <i class="fas fa-plus-circle me-1"></i> Tambah Bahan Baku
            </a>

            <div class="table-responsive">
                <table class="table table-hover table-striped" id="bahanBakuTable">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nama Bahan</th>
                            <th scope="col">Kategori</th>
                            <th scope="col" class="text-center">Stok</th>
                            <th scope="col">Satuan</th>
                            <th scope="col">Tgl. Masuk</th>
                            <th scope="col">Tgl. Kadaluarsa</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php if (empty($bahan_baku)): ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Tidak ada data bahan baku. Silakan tambahkan data baru.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($bahan_baku as $bahan): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($bahan['nama']) ?></td>
                                <td><?= esc($bahan['kategori']) ?></td>
                                <td class="text-center">
                                    <span class="fw-bold"><?= $bahan['jumlah'] ?></span>
                                </td>
                                <td><?= esc($bahan['satuan']) ?></td>
                                <td><?= date('d-m-Y', strtotime($bahan['tanggal_masuk'])) ?></td>
                                <td><?= date('d-m-Y', strtotime($bahan['tanggal_kadaluarsa'])) ?></td>
                                <td class="text-center">
                                    <?php
                                        $status = $bahan['status'];
                                        $badge_class = '';
                                        $status_text = '';

                                        switch ($status) {
                                            case 'tersedia':
                                                $badge_class = 'bg-success';
                                                $status_text = 'Tersedia';
                                                break;
                                            case 'segera_kadaluarsa':
                                                $badge_class = 'bg-warning text-dark';
                                                $status_text = 'Segera Kadaluarsa (H-'. floor((strtotime($bahan['tanggal_kadaluarsa']) - time()) / (60 * 60 * 24)) . ')';
                                                break;
                                            case 'kadaluarsa':
                                                $badge_class = 'bg-danger';
                                                $status_text = 'Kadaluarsa';
                                                break;
                                            case 'habis':
                                                $badge_class = 'bg-secondary';
                                                $status_text = 'Habis';
                                                break;
                                            default:
                                                $badge_class = 'bg-info';
                                                $status_text = 'N/A';
                                        }
                                    ?>
                                    <span class="badge <?= $badge_class ?>"><?= $status_text ?></span>
                                </td>
                                <td class="text-center">
                                    <!-- Tombol Update Stok -->
                                    <a href="<?= base_url('gudang/bahan-baku/edit-stok/' . $bahan['id']); ?>" 
                                       class="btn btn-sm btn-info text-white me-1 rounded-circle" 
                                       title="Update Stok">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    
                                    <!-- Tombol Hapus - AKTIF HANYA JIKA STATUS 'kadaluarsa' -->
                                    <?php if ($bahan['status'] === 'kadaluarsa'): ?>
                                        <form action="<?= base_url('gudang/bahan-baku/hapus/' . $bahan['id']); ?>" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus bahan baku <?= esc($bahan['nama']) ?>? Aksi ini permanen.')">
                                            <!-- CI4 method spoofing -->
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger rounded-circle" 
                                                    title="Hapus Bahan Kadaluarsa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-secondary rounded-circle" 
                                                title="Hanya bahan kadaluarsa yang bisa dihapus"
                                                disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
