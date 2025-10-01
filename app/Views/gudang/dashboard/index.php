<!-- Pastikan view ini dimuat setelah layout/header Bootstrap Anda -->

<div class="container-fluid py-4">
    <h2 class="mb-4">Dashboard Petugas Gudang</h2>
    <h3 class="mb-4">Selamat Datang, <?= session()->get('name') ?> (Petugas Gudang)</h3>

    <!-- Kartu Ringkasan Status Stok & Permintaan (Menggunakan data dari Controller) -->
    <div class="row">
        
        <!-- Total Bahan Baku -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Jenis Bahan Baku
                            </div>
                            <!-- Jika $total_bahan sudah dikirim dari Controller -->
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_bahan ?? 'N/A' ?></div> 
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cubes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permintaan Menunggu Persetujuan -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Permintaan Menunggu Proses
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $permintaan_menunggu ?? 'N/A' ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bahan Segera Kadaluarsa -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Segera Kadaluarsa (H-3)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($segera_kadaluarsa ?? []) ?> Jenis</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Tabel Daftar Bahan Kadaluarsa -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">Daftar Bahan Baku Kadaluarsa (Siap Dihapus)</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($kadaluarsa)): ?>
                        <div class="alert alert-info mb-0">Tidak ada bahan baku yang berstatus Kadaluarsa saat ini.</div>
                    <?php else: ?>
                        <p>Terdapat **<?= count($kadaluarsa) ?>** jenis bahan baku yang sudah kadaluarsa dan **dapat segera dihapus** dari sistem. Silakan kelola di menu **Kelola Bahan Baku**.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>


    <!-- Tombol Aksi Cepat (Link yang diperbaiki) -->
    <div class="row mt-3">
        <div class="col-12">
            <!-- LINK PERBAIKAN: Mengarah ke Controller BahanBaku::index -->
            <a href="<?= base_url('gudang/bahan') ?>" class="btn btn-success btn-lg shadow-sm">
                <i class="fas fa-cubes mr-2"></i> Kelola Bahan Baku
            </a>
            <!-- LINK PERBAIKAN: Mengarah ke Controller Permintaan::index -->
            <a href="<?= base_url('gudang/permintaan') ?>" class="btn btn-warning btn-lg shadow-sm ml-3">
                <i class="fas fa-clock mr-2"></i> Proses Permintaan Dapur
            </a>
            <!-- Tambahkan link Logout agar bisa kembali ke login -->
            <a href="<?= base_url('logout') ?>" class="btn btn-danger btn-lg shadow-sm ml-3">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </div>
    </div>
</div>
<!-- Catatan: Anda mungkin perlu menambahkan library FontAwesome (fas) dan Bootstrap ke layout utama Anda agar icon dan styling tampil sempurna. -->
