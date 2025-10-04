<?php namespace App\Models;

use CodeIgniter\Model;

class BahanBakuModel extends Model
{
    protected $table = 'bahan_baku';
    protected $primaryKey = 'id';
    // Hanya tambahkan field yang diizinkan untuk diisi
    protected $allowedFields = ['nama', 'kategori', 'jumlah', 'satuan', 'tanggal_masuk', 'tanggal_kadaluarsa', 'status', 'created_at'];
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = null; // Tidak ada updated_at

    protected $beforeInsert = ['setStatus'];
    protected $beforeUpdate = ['setStatus'];

    /**
     * Logika untuk menentukan status otomatis berdasarkan tanggal kadaluarsa dan jumlah.
     * Dipanggil sebelum insert dan update.
     */
    protected function setStatus(array $data)
    {
        // Jika data tidak mengandung 'jumlah' atau 'tanggal_kadaluarsa', lewati.
        if (!isset($data['data']['jumlah']) || !isset($data['data']['tanggal_kadaluarsa'])) {
            // Jika ini adalah insert, status default bisa diatur ke 'tersedia' jika stok > 0
            if (!isset($data['data']['status']) && ($data['data']['jumlah'] ?? 0) > 0) {
                 $data['data']['status'] = 'tersedia';
            }
            return $data;
        }

        $jumlah = (int)$data['data']['jumlah'];
        $tgl_kadaluarsa = $data['data']['tanggal_kadaluarsa'];
        $hari_ini = date('Y-m-d');

        if ($jumlah <= 0) {
            $data['data']['status'] = 'habis'; // 1. Habis: jika jumlah <= 0
        } elseif (strtotime($hari_ini) > strtotime($tgl_kadaluarsa)) {
            $data['data']['status'] = 'kadaluarsa'; // 2. Kadaluarsa: jika hari_ini > tanggal_kadaluarsa
        } else {
            // Hitung selisih hari antara tanggal kadaluarsa dan hari ini
            $tgl_kadaluarsa_obj = new \DateTime($tgl_kadaluarsa);
            $hari_ini_obj = new \DateTime($hari_ini);
            $diff = $hari_ini_obj->diff($tgl_kadaluarsa_obj);
            $days_diff = (int)$diff->days;

            // Pastikan tanggal_kadaluarsa belum terlampaui (sudah dicek di atas)
            if ($tgl_kadaluarsa_obj >= $hari_ini_obj && $days_diff <= 3) {
                $data['data']['status'] = 'segera_kadaluarsa'; // 3. Segera Kadaluarsa: jika sisa 0 sampai 3 hari
            } else {
                $data['data']['status'] = 'tersedia'; // 4. Tersedia
            }
        }
        
        return $data;
    }
    
    /**
     * Mengambil semua data bahan baku dan menghitung statusnya secara dinamis 
     * sebelum ditampilkan ke View (Read operation).
     */
    public function getAllBahanBaku()
    {
        // Menggunakan findAll() untuk mengambil semua data
        $bahan_baku_list = $this->findAll();
        
        // Loop untuk update status dinamis
        foreach ($bahan_baku_list as &$bahan) {
            // Simulasikan data yang masuk ke setStatus
            $data_temp = ['data' => $bahan];
            $updated_data = $this->setStatus($data_temp);
            
            // Perbarui status di array hasil (hanya untuk tampilan)
            $bahan['status'] = $updated_data['data']['status'];
            // Status yang disimpan di DB (jika berbeda) tidak diubah di sini. 
            // Untuk konsistensi DB, disarankan membuat CRON Job atau event untuk update status berkala.
            // Namun, untuk mempermudah, kita fokus pada perhitungan dinamis saat READ.
        }
        return $bahan_baku_list;
    }
}
