<div class="container-fluid">
    <h1>Data Bahan Baku (Gudang)</h1>
    <a href="<?= base_url('gudang/bahan/create') ?>" class="btn btn-success mb-3">Tambah Bahan Baru</a>
    
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Masuk/Kadaluarsa</th>
                <th>Stok (Jml/Satuan)</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bahan as $item): ?>
            <tr>
                <td><?= $item['id'] ?></td>
                <td><?= $item['nama'] ?></td>
                <td><?= $item['kategori'] ?></td>
                <td><?= $item['tanggal_masuk'] ?> / <?= $item['tanggal_kadaluarsa'] ?></td>
                <td><?= $item['jumlah'] ?> <?= $item['satuan'] ?></td>
                <td>
                    <span class="badge badge-<?= ($item['status'] == 'kadaluarsa' || $item['status'] == 'habis') ? 'danger' : (($item['status'] == 'segera_kadaluarsa') ? 'warning' : 'success') ?>">
                        <?= ucfirst(str_replace('_', ' ', $item['status'])) ?>
                    </span>
                </td>
                <td>
                    <form action="<?= base_url('gudang/bahan/update/' . $item['id']) ?>" method="post" class="d-inline">
                        <?= csrf_field() ?>
                        <input type="number" name="jumlah" value="<?= $item['jumlah'] ?>" min="0" style="width: 80px;" class="form-control-sm d-inline" required>
                        <button type="submit" class="btn btn-sm btn-info" onclick="return confirm('Apakah Anda yakin ingin mengubah stok?')">Update</button>
                    </form>
                    
                    <?php if ($item['status'] == 'kadaluarsa'): ?>
                        <a href="<?= base_url('gudang/bahan/delete/' . $item['id']) ?>" class="btn btn-sm btn-danger ml-1" onclick="return confirm('KONFIRMASI HAPUS: Apakah Anda yakin menghapus bahan KADALUARSA ini?')">Hapus</a>
                    <?php else: ?>
                        <button class="btn btn-sm btn-secondary ml-1" disabled title="Hanya bahan kadaluarsa yang bisa dihapus">Hapus</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>