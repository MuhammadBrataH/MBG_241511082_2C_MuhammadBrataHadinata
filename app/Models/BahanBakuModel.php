<?php namespace App\Models;

use CodeIgniter\Model;

class BahanBakuModel extends Model
{
    protected $table = 'bahan_baku';
    protected $primaryKey = 'id';
    // Tambahkan 'status' ke allowedFields agar bisa di-update manual jika perlu (meskipun otomatis oleh callback)
    protected $allowedFields = ['nama', 'kategori', 'jumlah', 'satuan', 'tanggal_masuk', 'tanggal_kadaluarsa', 'status', 'created_at'];
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = null; 

    // Pastikan callback dipanggil saat insert/update untuk update status otomatis
    protected $beforeInsert = ['setStatus'];
    protected $beforeUpdate = ['setStatus'];

    /**
     * Logika untuk menentukan status otomatis berdasarkan tgl kadaluarsa dan stok
     * Dipanggil sebelum insert/update oleh CI4 Callback
     */
    protected function setStatus(array $data)
    {
        // Jika data update tidak menyertakan 'jumlah', ambil dari data lama
        $jumlah = $data['data']['jumlah'] ?? $this->find($data['id'])['jumlah'] ?? 0;
        $tgl_kadaluarsa = $data['data']['tanggal_kadaluarsa'] ?? $this->find($data['id'])['tanggal_kadaluarsa'] ?? null;
        $hari_ini = date('Y-m-d');

        if ($jumlah < 0) {
            // Seharusnya sudah divalidasi di Controller, tapi sebagai guard:
            $data['data']['status'] = 'habis'; 
        } elseif ($jumlah == 0) {
            $data['data']['status'] = 'habis'; // Habis: jika jumlah = 0
        } elseif ($tgl_kadaluarsa && strtotime($hari_ini) > strtotime($tgl_kadaluarsa)) {
            $data['data']['status'] = 'kadaluarsa'; // Kadaluarsa: jika hari_ini > tanggal_kadaluarsa
        } elseif ($tgl_kadaluarsa) {
            $diff = strtotime($tgl_kadaluarsa) - strtotime($hari_ini);
            $days_diff = round($diff / (60 * 60 * 24));

            if ($jumlah > 0 && $days_diff >= 0 && $days_diff <= 3) {
                $data['data']['status'] = 'segera_kadaluarsa'; // Segera Kadaluarsa: jika tgl_kadaluarsa <= hari_ini + 3 hari dan stok > 0
            } else {
                $data['data']['status'] = 'tersedia'; // Tersedia
            }
        } else {
             $data['data']['status'] = 'tersedia';
        }
        
        return $data;
    }

    /**
     * Mengambil semua bahan baku dengan status yang sudah dihitung dinamis.
     * Digunakan untuk menampilkan tabel di Gudang.
     */
    public function getAllBahanBaku()
    {
        $bahan_baku_list = $this->findAll();
        
        // Loop untuk update status dinamis
        foreach ($bahan_baku_list as &$bahan) {
            $data_temp = ['data' => $bahan];
            $updated_data = $this->setStatus($data_temp);
            $bahan['status'] = $updated_data['data']['status'];
        }
        return $bahan_baku_list;
    }
    
    /**
     * Mengambil daftar bahan baku yang tersedia untuk Petugas Dapur.
     * Syarat: stok > 0 dan status BUKAN 'kadaluarsa' atau 'habis'.
     */
    public function getAvailableBahanBaku()
    {
        // Logika ini akan mengambil data, dan status akan dihitung dinamis di callback
        $bahan = $this->where('jumlah >', 0)->findAll();
        
        $result = [];
        foreach ($bahan as $item) {
            $data_temp = ['data' => $item];
            $updated_data = $this->setStatus($data_temp);
            $item['status'] = $updated_data['data']['status'];

            // Filter di sini: hanya yang BUKAN 'kadaluarsa' dan BUKAN 'habis'
            if ($item['status'] !== 'kadaluarsa' && $item['status'] !== 'habis') {
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * [KRITIS] Mengurangi stok bahan baku dan memperbarui status secara otomatis.
     * Dipanggil di dalam Transaksi Permintaan.
     * * @param int $bahanId ID Bahan Baku
     * @param int $jumlahKurang Jumlah yang akan dikurangi
     * @return bool True jika berhasil update, False jika stok tidak mencukupi (stok < 0)
     */
    public function reduceStok(int $bahanId, int $jumlahKurang): bool
    {
        // 1. Ambil data bahan baku saat ini
        $bahan = $this->find($bahanId);

        if (!$bahan) {
            return false; // Bahan tidak ditemukan
        }

        // 2. Hitung stok baru
        $stokBaru = $bahan['jumlah'] - $jumlahKurang;

        // 3. Validasi stok tidak boleh negatif
        if ($stokBaru < 0) {
            // Karena ini dipanggil di dalam transaksi, kembalikan false agar transaksi roll back
            return false; 
        }

        // 4. Update data bahan baku
        $dataUpdate = [
            'id' => $bahanId,
            'jumlah' => $stokBaru,
            // Callback beforeUpdate akan memanggil setStatus() secara otomatis
        ];

        // Memanggil save() yang akan memicu callback setStatus() untuk menentukan status baru
        if ($this->save($dataUpdate) === false) {
             // Gagal menyimpan (meskipun validasi stok sudah dilakukan, ini untuk menangani kegagalan DB)
            return false;
        }

        // Jika berhasil, kembalikan true
        return true;
    }
}
