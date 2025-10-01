<div class="container">
    <h1>Buat Permintaan Bahan Baku</h1>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form action="<?= base_url('dapur/permintaan/store') ?>" method="post">
        <?= csrf_field() ?>
        
        <div class="form-group">
            <label>Tanggal Masak:</label>
            <input type="date" name="tgl_masak" class="form-control" required value="<?= old('tgl_masak') ?>">
        </div>
        <div class="form-group">
            <label>Menu yang akan dibuat:</label>
            <input type="text" name="menu_makan" class="form-control" required value="<?= old('menu_makan') ?>">
        </div>
        <div class="form-group">
            <label>Jumlah Porsi yang dibuat:</label>
            <input type="number" name="jumlah_porsi" class="form-control" min="1" required value="<?= old('jumlah_porsi') ?>">
        </div>

        <hr>
        [cite_start]<h5>Daftar Bahan Baku yang Diminta (Stok Tersedia) [cite: 163]</h5>
        
        <div id="bahan-list">
            <div class="form-row mb-2 bahan-row">
                <div class="col-md-6">
                    <select name="bahan[0][bahan_id]" class="form-control bahan-select" required>
                        <option value="">Pilih Bahan</option>
                        <?php foreach ($bahan_tersedia as $bahan): ?>
                            <option value="<?= $bahan['id'] ?>" data-stok="<?= $bahan['jumlah'] ?>">
                                <?= $bahan['nama'] ?> (Stok: <?= $bahan['jumlah'] ?> <?= $bahan['satuan'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="number" name="bahan[0][jumlah_diminta]" class="form-control jumlah-input" placeholder="Jumlah Diminta" min="1" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-bahan">Hapus</button>
                </div>
            </div>
        </div>
        
        <button type="button" id="add-bahan" class="btn btn-secondary mt-2">Tambah Bahan</button>
        
        <hr>
        <button type="submit" class="btn btn-primary" onclick="return confirm('Apakah Anda yakin ingin mengajukan permintaan ini?')">Ajukan Permintaan</button>
    </form>
</div>

<script>
    let counter = 1;
    // Fungsi untuk membuat baris baru
    function createNewBahanRow() {
        const list = document.getElementById('bahan-list');
        const template = list.querySelector('.bahan-row');
        if (!template) return;

        const newRow = template.cloneNode(true);
        
        // Atur ulang name attribute
        newRow.querySelectorAll('select, input').forEach(element => {
            element.name = element.name.replace(/\[\d+\]/, '[' + counter + ']');
            element.value = (element.type === 'number') ? '' : '';
        });
        
        // Event listener Hapus
        newRow.querySelector('.remove-bahan').addEventListener('click', function() {
            newRow.remove();
        });

        list.appendChild(newRow);
        counter++;
    }

    // Tambah Baris
    document.getElementById('add-bahan').addEventListener('click', createNewBahanRow);

    // Hapus Baris Awal
    document.querySelector('.remove-bahan').addEventListener('click', function(e) {
        if (document.querySelectorAll('.bahan-row').length > 1) {
            e.target.closest('.bahan-row').remove();
        } else {
            alert('Minimal harus ada satu bahan yang diminta.');
        }
    });

</script>