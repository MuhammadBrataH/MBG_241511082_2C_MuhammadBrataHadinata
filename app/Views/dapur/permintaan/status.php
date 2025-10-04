<?php 
    // Menggunakan layout utama
    echo $this->extend('layout/main');
    echo $this->section('content');
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-primary text-white rounded-top-4 p-4">
                    <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i> Status Permintaan Bahan Baku</h5>
                </div>
                <div class="card-body p-4">
                    <!-- Pesan Notifikasi -->
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

                    <?php if (empty($permintaan_list)): ?>
                        <div class="alert alert-info text-center" role="alert">
                            <i class="fas fa-info-circle me-2"></i> Anda belum mengajukan permintaan bahan baku.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Tgl Masak</th>
                                        <th>Menu Makanan</th>
                                        <th>Jml Porsi</th>
                                        <th>Status</th>
                                        <th>Diajukan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($permintaan_list as $p): 
                                        // Tentukan warna badge berdasarkan status
                                        $badge_class = '';
                                        switch ($p['status']) {
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
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d M Y', strtotime($p['tgl_masak'])) ?></td>
                                            <td><?= esc($p['menu_makan']) ?></td>
                                            <td><?= esc($p['jumlah_porsi']) ?></td>
                                            <td>
                                                <span class="badge <?= $badge_class ?> p-2"><?= strtoupper($p['status']) ?></span>
                                            </td>
                                            <td><?= date('d M Y H:i', strtotime($p['created_at'])) ?></td>
                                            <td>
                                                <!-- Tombol Detail (Memicu Modal) -->
                                                <button class="btn btn-sm btn-info text-white" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#detailModal"
                                                        data-id="<?= $p['id'] ?>"
                                                        data-tgl-masak="<?= date('d M Y', strtotime($p['tgl_masak'])) ?>"
                                                        data-menu="<?= esc($p['menu_makan']) ?>"
                                                        data-porsi="<?= esc($p['jumlah_porsi']) ?>"
                                                        data-status="<?= strtoupper($p['status']) ?>"
                                                        data-status-class="<?= $badge_class ?>"
                                                        data-detail='<?= json_encode($p['detail_bahan']) ?>'>
                                                    <i class="fas fa-eye"></i> Detail
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-muted p-3">
                    <small>Data Permintaan Anda per <?= date('d M Y H:i') ?></small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Permintaan -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="detailModalLabel"><i class="fas fa-info-circle me-2"></i> Rincian Permintaan #<span id="detail-id"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Tanggal Masak:</strong> <span id="detail-tgl-masak"></span>
            </div>
            <div class="col-md-6">
                <strong>Jumlah Porsi:</strong> <span id="detail-porsi"></span>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <strong>Menu Makanan:</strong> <span id="detail-menu"></span>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <strong>Status Permintaan:</strong> <span id="detail-status" class="badge p-2"></span>
            </div>
        </div>

        <h6 class="mt-4 border-bottom pb-2 text-primary"><i class="fas fa-list-ul me-1"></i> Daftar Bahan yang Diminta:</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr class="table-primary">
                        <th>Nama Bahan</th>
                        <th>Kategori</th>
                        <th>Jml Diminta</th>
                        <th>Satuan</th>
                    </tr>
                </thead>
                <tbody id="detail-bahan-list">
                    <!-- Detail bahan akan diisi oleh JavaScript -->
                </tbody>
            </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- JavaScript untuk Modal Detail -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const detailModal = document.getElementById('detailModal');
    detailModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        
        // Ambil data dari tombol
        const id = button.getAttribute('data-id');
        const tglMasak = button.getAttribute('data-tgl-masak');
        const menu = button.getAttribute('data-menu');
        const porsi = button.getAttribute('data-porsi');
        const statusText = button.getAttribute('data-status');
        const statusClass = button.getAttribute('data-status-class');
        const detailJson = button.getAttribute('data-detail');
        const details = JSON.parse(detailJson);

        // Update elemen di Modal
        document.getElementById('detail-id').textContent = id;
        document.getElementById('detail-tgl-masak').textContent = tglMasak;
        document.getElementById('detail-menu').textContent = menu;
        document.getElementById('detail-porsi').textContent = porsi;
        
        const statusElement = document.getElementById('detail-status');
        statusElement.textContent = statusText;
        statusElement.className = `badge p-2 ${statusClass}`; // Ganti kelas status

        // Isi detail bahan baku
        const bahanListBody = document.getElementById('detail-bahan-list');
        bahanListBody.innerHTML = ''; // Kosongkan daftar sebelumnya

        if (details && details.length > 0) {
            details.forEach(item => {
                const row = bahanListBody.insertRow();
                row.insertCell().textContent = item.nama;
                row.insertCell().textContent = item.kategori;
                row.insertCell().textContent = item.jumlah_diminta;
                row.insertCell().textContent = item.satuan;
            });
        } else {
            const row = bahanListBody.insertRow();
            const cell = row.insertCell(0);
            cell.setAttribute('colspan', '4');
            cell.textContent = 'Tidak ada rincian bahan.';
            cell.classList.add('text-center');
        }
    });
});
</script>

<?php 
    echo $this->endSection(); 
?>
