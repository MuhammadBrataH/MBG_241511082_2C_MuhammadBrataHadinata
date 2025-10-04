<?php
/**
 * View: app/Views/gudang/permintaan/detail.php
 * Menampilkan detail spesifik dari satu permintaan bahan baku untuk Petugas Gudang (Admin).
 * Termasuk tombol Aksi ACC/Tolak.
 */
?>

<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="text-gray-800 mb-4">Detail Permintaan Bahan Baku #<?= esc($permintaan['id']) ?></h2>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-clipboard-list me-2"></i> Informasi Utama Permintaan
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <p class="mb-1 text-muted small">ID Pemohon:</p>
                    <p class="lead mb-0"><strong><?= esc($permintaan['pemohon_name']) ?></strong> (ID: <?= esc($permintaan['pemohon_id']) ?>)</p>
                </div>
                <div class="col-md-6 mb-3">
                    <p class="mb-1 text-muted small">Status Saat Ini:</p>
                    <?php 
                        $badge_class = [
                            'menunggu' => 'warning', 
                            'disetujui' => 'success', 
                            'ditolak' => 'danger'
                        ][$permintaan['status']] ?? 'secondary';
                    ?>
                    <h4 class="mb-0"><span class="badge bg-<?= $badge_class ?> p-2"><?= strtoupper(esc($permintaan['status'])) ?></span></h4>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-4">
                    <p class="mb-1 text-muted small">Tanggal Masak Rencana:</p>
                    <p class="fs-5 mb-0"><strong><?= date('d F Y', strtotime($permintaan['tgl_masak'])) ?></strong></p>
                </div>
                <div class="col-md-4">
                    <p class="mb-1 text-muted small">Jumlah Porsi:</p>
                    <p class="fs-5 mb-0"><strong><?= esc($permintaan['jumlah_porsi']) ?></strong></p>
                </div>
                <div class="col-md-4">
                    <p class="mb-1 text-muted small">Menu Makanan:</p>
                    <p class="fs-5 mb-0"><em>"<?= esc($permintaan['menu_makan']) ?>"</em></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-secondary text-white">
            <i class="fas fa-cubes me-2"></i> Daftar Bahan Baku yang Diminta
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Bahan Baku</th>
                            <th>Kategori</th>
                            <th class="text-end">Jumlah Diminta</th>
                            <th>Stok Saat Ini (Gudang)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($permintaan['details'] as $detail): ?>
                            <?php 
                                $is_critical = $detail['jumlah'] < $detail['jumlah_diminta'];
                            ?>
                            <tr class="<?= $is_critical ? 'table-danger' : '' ?>">
                                <td><?= $no++ ?></td>
                                <td><?= esc($detail['nama']) ?></td>
                                <td><?= esc($detail['kategori']) ?></td>
                                <td class="text-end">
                                    <strong><?= esc($detail['jumlah_diminta']) ?> <?= esc($detail['satuan']) ?></strong>
                                </td>
                                <td>
                                    <?= esc($detail['jumlah']) ?> <?= esc($detail['satuan']) ?>
                                    <?php if ($is_critical): ?>
                                        <span class="badge bg-danger ms-2">STOK KURANG!</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($permintaan['details'])): ?>
                            <tr><td colspan="5" class="text-center text-muted">Tidak ada detail bahan baku yang terdaftar.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (isset($permintaan['alasan_penolakan']) && !empty($permintaan['alasan_penolakan'])): ?>
                <div class="alert alert-danger mt-3">
                    <p class="mb-0"><strong>Alasan Penolakan:</strong> <?= esc($permintaan['alasan_penolakan']) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tombol Aksi (Hanya ditampilkan jika status MASIH 'menunggu') -->
    <div class="d-flex justify-content-between mb-5">
        <a href="<?= base_url('gudang/permintaan/list') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Permintaan</a>
        
        <?php if ($permintaan['status'] === 'menunggu'): ?>
            <div>
                <!-- Tombol ACC (Setuju) - Memanggil Modal Konfirmasi -->
                <button 
                    class="btn btn-success me-2" 
                    data-bs-toggle="modal" 
                    data-bs-target="#confirmAccModal" 
                    data-id="<?= $permintaan['id'] ?>" 
                    data-menu="<?= esc($permintaan['menu_makan']) ?>" 
                    title="Setujui dan Kurangi Stok">
                    <i class="fas fa-check-double me-2"></i> Setujui (ACC)
                </button>

                <!-- Tombol Tolak - Memanggil Modal Alasan -->
                <button 
                    class="btn btn-danger" 
                    data-bs-toggle="modal" 
                    data-bs-target="#tolakModal" 
                    data-id="<?= $permintaan['id'] ?>"
                    data-menu="<?= esc($permintaan['menu_makan']) ?>"
                    title="Tolak Permintaan">
                    <i class="fas fa-times me-2"></i> Tolak
                </button>
            </div>
        <?php endif; ?>
    </div>

</div>


<!-- MODAL ACC (Untuk aksi di halaman Detail) -->
<div class="modal fade" id="confirmAccModal" tabindex="-1" aria-labelledby="confirmAccModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="confirmAccModalLabel"><i class="fas fa-exclamation-triangle me-2"></i> Konfirmasi Persetujuan (ACC)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAccDetail" method="POST" action="<?= base_url('gudang/permintaan/proses/' . $permintaan['id']) ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Apakah Anda **YAKIN** ingin menyetujui permintaan <strong id="menuAccDetail"><?= esc($permintaan['menu_makan']) ?></strong>?</p>
                    <p class="text-danger small">
                        **PERHATIAN:** Menyetujui permintaan ini akan secara **PERMANEN** mengurangi stok bahan baku di Gudang. Pastikan stok cukup!
                    </p>
                    <?php if (array_reduce($permintaan['details'], fn($carry, $item) => $carry || ($item['jumlah'] < $item['jumlah_diminta']), false)): ?>
                        <div class="alert alert-warning small">
                            Beberapa bahan memiliki stok gudang yang **kurang** dari jumlah yang diminta. Persetujuan dapat dibatalkan (Rollback) jika stok tidak mencukupi saat proses transaksi.
                        </div>
                    <?php endif; ?>
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

<!-- MODAL TOLAK (Untuk aksi di halaman Detail) -->
<div class="modal fade" id="tolakModal" tabindex="-1" aria-labelledby="tolakModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="tolakModalLabel"><i class="fas fa-times-circle me-2"></i> Konfirmasi Penolakan Permintaan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTolakDetail" method="POST" action="<?= base_url('gudang/permintaan/proses/' . $permintaan['id']) ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Anda akan menolak permintaan <strong id="menuTolakDetail"><?= esc($permintaan['menu_makan']) ?></strong>. Berikan alasan penolakan (Opsional):</p>
                    <div class="mb-3">
                        <label for="alasan_penolakan" class="form-label">Alasan Penolakan</label>
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

<?= $this->endSection() ?>
