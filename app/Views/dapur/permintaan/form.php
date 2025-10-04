<?php 
    // Menggunakan layout utama
    $this->extend('layout/main');
    $this->section('content');
?>

<style>
    /* Custom styling untuk form permintaan */
    .card-bahan-detail {
        border-left: 5px solid #0d6efd; /* Warna biru Bootstrap */
    }
    .btn-action {
        width: 100%;
    }
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h3 class="mb-4 text-primary">
                <i class="fas fa-utensils me-2"></i> Ajukan Permintaan Bahan Baku
            </h3>
            
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body p-md-5">
                    
                    <form action="<?= base_url('dapur/permintaan/simpan') ?>" method="post" id="formPermintaan">
                        <?= csrf_field() ?>
                        
                        <!-- Bagian Informasi Utama Permintaan -->
                        <h5 class="text-secondary mb-3"><i class="fas fa-clipboard-list me-2"></i> Detail Pesanan</h5>
                        
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="tgl_masak" class="form-label fw-bold">Tanggal Rencana Masak <span class="text-danger">*</span></label>
                                <input type="date" class="form-control <?= session('errors.tgl_masak') ? 'is-invalid' : '' ?>" id="tgl_masak" name="tgl_masak" value="<?= old('tgl_masak') ?>" required>
                                <?php if (session('errors.tgl_masak')): ?><div class="invalid-feedback"><?= session('errors.tgl_masak') ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label for="menu_makan" class="form-label fw-bold">Menu yang akan Dibuat <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= session('errors.menu_makan') ? 'is-invalid' : '' ?>" id="menu_makan" name="menu_makan" value="<?= old('menu_makan') ?>" required placeholder="Contoh: Nasi Goreng Ayam">
                                <?php if (session('errors.menu_makan')): ?><div class="invalid-feedback"><?= session('errors.menu_makan') ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label for="jumlah_porsi" class="form-label fw-bold">Jumlah Porsi <span class="text-danger">*</span></label>
                                <input type="number" class="form-control <?= session('errors.jumlah_porsi') ? 'is-invalid' : '' ?>" id="jumlah_porsi" name="jumlah_porsi" value="<?= old('jumlah_porsi') ?>" min="1" required placeholder="Contoh: 150">
                                <?php if (session('errors.jumlah_porsi')): ?><div class="invalid-feedback"><?= session('errors.jumlah_porsi') ?></div><?php endif; ?>
                            </div>
                        </div>

                        <!-- Bagian Detail Bahan Baku -->
                        <hr class="my-4">
                        <h5 class="text-secondary mb-3 d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-cubes me-2"></i> Daftar Bahan Baku Diminta</span>
                            <button type="button" class="btn btn-sm btn-primary" id="tambahBaris">
                                <i class="fas fa-plus me-1"></i> Tambah Bahan
                            </button>
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th style="width: 5%;">No</th>
                                        <th style="width: 55%;">Nama Bahan Baku (Stok Saat Ini)</th>
                                        <th style="width: 30%;">Jumlah Diminta</th>
                                        <th style="width: 10%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="detailBahanBody">
                                    <!-- Baris bahan baku akan ditambahkan di sini oleh JavaScript -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Pesan Error Dinamis untuk Detail Bahan -->
                        <div id="detail-error-message" class="text-danger mb-3" style="display: none;">
                            Anda harus meminta minimal satu jenis bahan baku.
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-success btn-lg shadow-sm" onclick="return validateDetail()">
                                <i class="fas fa-paper-plane me-2"></i> Ajukan Permintaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Data bahan baku tersedia dari Controller PHP
    const bahanBakuData = <?= json_encode($bahan_baku) ?>;
    let rowCount = 0;

    // Fungsi untuk membuat baris input bahan baku baru
    function createBahanRow() {
        rowCount++;
        const newRow = document.createElement('tr');
        newRow.id = `row-${rowCount}`;
        newRow.innerHTML = `
            <td>${rowCount}</td>
            <td>
                <select name="bahan_id[]" class="form-select form-select-sm select-bahan" data-row-id="${rowCount}" required>
                    <option value="" selected disabled>-- Pilih Bahan Baku --</option>
                    <?php foreach ($bahan_baku as $bahan): ?>
                        <option value="<?= $bahan['id'] ?>" data-stok="<?= $bahan['jumlah'] ?>">
                            <?= $bahan['nama'] ?> (<?= $bahan['jumlah'] ?> <?= $bahan['satuan'] ?> | Status: <?= ucwords(str_replace('_', ' ', $bahan['status'])) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback stok-error" id="stok-error-${rowCount}"></div>
            </td>
            <td>
                <input type="number" name="jumlah_diminta[]" class="form-control form-control-sm input-jumlah" min="1" placeholder="Jumlah" required>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeBahanRow(${rowCount})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        document.getElementById('detailBahanBody').appendChild(newRow);
        updateRowNumbers();
    }

    // Fungsi untuk menghapus baris input
    function removeBahanRow(id) {
        document.getElementById(`row-${id}`).remove();
        updateRowNumbers();
        validateDetail(); // Re-validate detail count after removal
    }

    // Fungsi untuk memperbarui nomor urut baris
    function updateRowNumbers() {
        const rows = document.querySelectorAll('#detailBahanBody tr');
        rows.forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
        });
        rowCount = rows.length;
    }

    // Fungsi validasi sebelum submit
    function validateDetail() {
        const detailBody = document.getElementById('detailBahanBody');
        const detailError = document.getElementById('detail-error-message');
        const rows = detailBody.querySelectorAll('tr');

        if (rows.length === 0) {
            detailError.style.display = 'block';
            return false;
        } else {
            detailError.style.display = 'none';
        }
        
        // Cek duplikasi bahan dan stok
        const selectedBahanIds = {};
        let isValid = true;

        rows.forEach(row => {
            const select = row.querySelector('.select-bahan');
            const input = row.querySelector('.input-jumlah');
            const stokError = row.querySelector('.stok-error');
            
            const bahanId = select.value;
            const jumlahDiminta = parseInt(input.value);
            const maxStok = parseInt(select.options[select.selectedIndex].getAttribute('data-stok'));

            // 1. Cek duplikasi
            if (bahanId) {
                if (selectedBahanIds[bahanId]) {
                    select.classList.add('is-invalid');
                    stokError.textContent = 'Bahan ini sudah diminta di baris lain.';
                    isValid = false;
                } else {
                    selectedBahanIds[bahanId] = true;
                    select.classList.remove('is-invalid');
                }
            }

            // 2. Cek stok (Jumlah diminta tidak boleh melebihi stok yang ada)
            if (jumlahDiminta > maxStok) {
                input.classList.add('is-invalid');
                input.nextElementSibling.textContent = `Jumlah diminta melebihi stok tersedia (${maxStok} ${bahanBakuData.find(b => b.id == bahanId)?.satuan}).`;
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
                // input.nextElementSibling.textContent = ''; // Hapus pesan error jika valid
            }
        });

        return isValid;
    }
    
    // Event listener untuk tombol 'Tambah Bahan'
    document.getElementById('tambahBaris').addEventListener('click', createBahanRow);

    // Tambahkan baris pertama saat halaman dimuat
    window.onload = function() {
        createBahanRow();
        
        // Hapus class 'is-invalid' pada input number saat user mengetik
        document.getElementById('detailBahanBody').addEventListener('input', function(e) {
            if (e.target.classList.contains('input-jumlah')) {
                e.target.classList.remove('is-invalid');
            }
        });

        // Re-run validation on change for select/input
        document.getElementById('formPermintaan').addEventListener('change', function(e) {
            if (e.target.classList.contains('select-bahan') || e.target.classList.contains('input-jumlah')) {
                validateDetail();
            }
        });
    };
</script>

<?php $this->endSection(); ?>
