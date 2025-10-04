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
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-secondary border-0 rounded-4 shadow-sm" role="alert">
                <h4 class="alert-heading"><i class="fas fa-concierge-bell me-2"></i> Siap Memasak?</h4>
                <p class="mb-0">Anda masuk sebagai Petugas Dapur. Ajukan permintaan bahan baku Anda H-1 sebelum tanggal masak.</p>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Card 1: Permintaan Disetujui -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 rounded-4 shadow-sm h-100 bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-thumbs-up fa-3x me-3 opacity-75"></i>
                        <div>
                            <p class="text-uppercase mb-0 fw-bold opacity-75">Permintaan Disetujui</p>
                            <!-- Placeholder: Ganti dengan data aktual dari controller -->
                            <h2 class="display-5 fw-bold mb-0">12</h2> 
                        </div>
                    </div>
                    <a href="<?= base_url('dapur/permintaan?status=disetujui') ?>" class="stretched-link text-white text-decoration-none mt-2 d-block opacity-75">Lihat</a>
                </div>
            </div>
        </div>

        <!-- Card 2: Permintaan Menunggu -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 rounded-4 shadow-sm h-100 bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-history fa-3x me-3 opacity-75"></i>
                        <div>
                            <p class="text-uppercase mb-0 fw-bold opacity-75">Permintaan Menunggu</p>
                            <!-- Placeholder: Ganti dengan data aktual dari controller -->
                            <h2 class="display-5 fw-bold mb-0">3</h2> 
                        </div>
                    </div>
                    <a href="<?= base_url('dapur/permintaan?status=menunggu') ?>" class="stretched-link text-dark text-decoration-none mt-2 d-block opacity-75">Lihat</a>
                </div>
            </div>
        </div>

        <!-- Card 3: Permintaan Ditolak -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 rounded-4 shadow-sm h-100 bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-times-circle fa-3x me-3 opacity-75"></i>
                        <div>
                            <p class="text-uppercase mb-0 fw-bold opacity-75">Permintaan Ditolak</p>
                            <!-- Placeholder: Ganti dengan data aktual dari controller -->
                            <h2 class="display-5 fw-bold mb-0">1</h2> 
                        </div>
                    </div>
                    <a href="<?= base_url('dapur/permintaan?status=ditolak') ?>" class="stretched-link text-white text-decoration-none mt-2 d-block opacity-75">Lihat</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Section: Permintaan Terakhir -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-header bg-white border-0 fw-bold">Status Permintaan Terakhir</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tanggal Masak</th>
                                <th>Menu</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Placeholder Baris Data -->
                            <tr>
                                <td>10</td>
                                <td>2025-10-04</td>
                                <td>Sup ayam + susu fortifikasi</td>
                                <td><span class="badge bg-warning">Menunggu</span></td>
                                <td><a href="#" class="btn btn-sm btn-info text-white">Detail</a></td>
                            </tr>
                            <tr>
                                <td>9</td>
                                <td>2025-10-04</td>
                                <td>Nasi + tempe goreng + sayur bening</td>
                                <td><span class="badge bg-success">Disetujui</span></td>
                                <td><a href="#" class="btn btn-sm btn-info text-white">Detail</a></td>
                            </tr>
                            <!-- Akhir Placeholder -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
