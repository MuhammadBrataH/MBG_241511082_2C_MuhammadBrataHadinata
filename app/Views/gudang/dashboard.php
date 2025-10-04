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
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info border-0 rounded-4 shadow-sm" role="alert">
                <h4 class="alert-heading"><i class="fas fa-check-circle me-2"></i> Selamat Datang!</h4>
                <p class="mb-0">Anda masuk sebagai Petugas Gudang. Silakan pantau stok dan proses permintaan dari dapur.</p>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Card 1: Stok Total -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 rounded-4 shadow-sm h-100 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-warehouse fa-3x me-3 opacity-75"></i>
                        <div>
                            <p class="text-uppercase mb-0 fw-bold opacity-75">Total Stok Bahan</p>
                            <!-- Placeholder: Ganti dengan data aktual dari controller -->
                            <h2 class="display-5 fw-bold mb-0">9999</h2> 
                        </div>
                    </div>
                    <a href="<?= base_url('gudang/bahan-baku') ?>" class="stretched-link text-white text-decoration-none mt-2 d-block opacity-75">Lihat Detail Stok</a>
                </div>
            </div>
        </div>

        <!-- Card 2: Segera Kadaluarsa (H-3) -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 rounded-4 shadow-sm h-100 bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-3x me-3 opacity-75"></i>
                        <div>
                            <p class="text-uppercase mb-0 fw-bold opacity-75">Segera Kadaluarsa (H-3)</p>
                            <!-- Placeholder: Ganti dengan data aktual dari controller -->
                            <h2 class="display-5 fw-bold mb-0">15</h2> 
                        </div>
                    </div>
                    <a href="<?= base_url('gudang/bahan-baku?status=segera_kadaluarsa') ?>" class="stretched-link text-dark text-decoration-none mt-2 d-block opacity-75">Periksa Sekarang</a>
                </div>
            </div>
        </div>

        <!-- Card 3: Permintaan Menunggu -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 rounded-4 shadow-sm h-100 bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-hourglass-half fa-3x me-3 opacity-75"></i>
                        <div>
                            <p class="text-uppercase mb-0 fw-bold opacity-75">Permintaan Menunggu</p>
                            <!-- Placeholder: Ganti dengan data aktual dari controller -->
                            <h2 class="display-5 fw-bold mb-0">5</h2> 
                        </div>
                    </div>
                    <a href="<?= base_url('gudang/permintaan?status=menunggu') ?>" class="stretched-link text-white text-decoration-none mt-2 d-block opacity-75">Proses Permintaan</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Section: Log Aktivitas Terbaru (Opsional) -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-header bg-white border-0 fw-bold">Aktivitas Terbaru</div>
                <div class="card-body">
                    <p class="text-muted">Di sini akan tampil log perubahan stok dan persetujuan permintaan.</p>
                    <!-- Placeholder Tabel Aktivitas -->
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
