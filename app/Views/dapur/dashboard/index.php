<!-- Pastikan view ini dimuat setelah layout/header Bootstrap Anda -->

<div class="container-fluid py-4">
    <h2 class="mb-4"><?= $title ?></h2>
    <h3 class="mb-4">Selamat Datang, <?= session()->get('name') ?> (Petugas Dapur)</h3>

    <!-- Kartu Ringkasan Status Permintaan -->
    <div class="row">
        
        <!-- Total Permintaan Saya -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Permintaan Diajukan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_permintaan_saya ?></div>
                        </div>
                        <div class="col-auto">
                            <!-- Ganti icon ini dengan icon FontAwesome jika Anda menggunakannya -->
                            <i class="fas fa-list-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Menunggu Persetujuan -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Status Menunggu
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $status_menunggu ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Disetujui -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Permintaan Disetujui
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $status_disetujui ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Tombol Aksi Cepat -->
    <div class="row mt-3">
        <div class="col-12">
            <a href="<?= base_url('dapur/permintaan/new') ?>" class="btn btn-primary btn-lg shadow-sm">
                <i class="fas fa-plus mr-2"></i> Buat Permintaan Bahan Baru
            </a>
            <a href="<?= base_url('dapur/permintaan/status') ?>" class="btn btn-info btn-lg shadow-sm ml-3">
                <i class="fas fa-list-alt mr-2"></i> Lihat Semua Status Permintaan
            </a>
        </div>
    </div>
</div>
