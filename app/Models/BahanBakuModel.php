<?php namespace App\Models;

use CodeIgniter\Model;

class BahanBakuModel extends Model
{
    protected $table = 'bahan_baku';
    protected $primaryKey = 'id';
    // Hanya tambahkan field yang diizinkan untuk diisi/diubah
    protected $allowedFields = ['nama', 'kategori', 'jumlah', 'satuan', 'tanggal_masuk', 'tanggal_kadaluarsa', 'status', 'created_at'];
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = null; // Tidak ada updated_at

    protected $beforeInsert = ['setStatus'];
    protected $beforeUpdate = ['setStatus'];

    /**
     * Logika untuk menentukan status otomatis
     * Status: tersedia, segera_kadaluarsa, kadaluarsa, habis
     */
    protected function setStatus(array $data)
    {
        $jumlah = $data['data']['jumlah'] ?? 0;
        $tgl_kadaluarsa = $data['data']['tanggal_kadaluarsa'] ?? null;
        $hari_ini = date('Y-m-d');

        if ($jumlah == 0) {
            $data['data']['status'] = 'habis';
        } elseif ($tgl_kadaluarsa && strtotime($hari_ini) > strtotime($tgl_kadaluarsa)) {
            $data['data']['status'] = 'kadaluarsa';
        } elseif ($tgl_kadaluarsa) {
            $diff = strtotime($tgl_kadaluarsa) - strtotime($hari_ini);
            $days_diff = round($diff / (60 * 60 * 24));

            if ($jumlah > 0 && $days_diff >= 0 && $days_diff <= 3) {
                $data['data']['status'] = 'segera_kadaluarsa';
            } else {
                $data['data']['status'] = 'tersedia';
            }
        } else {
             $data['data']['status'] = 'tersedia';
        }
        
        return $data;
    }
    
    // Fungsi untuk mendapatkan semua data bahan baku dengan status dinamis
    public function getAllBahanBaku()
    {
        $bahan_baku_list = $this->findAll();
        
        // Loop untuk update status dinamis saat dibaca
        foreach ($bahan_baku_list as &$bahan) {
            $data_temp = ['data' => $bahan];
            $updated_data = $this->setStatus($data_temp);
            $bahan['status'] = $updated_data['data']['status'];
        }
        return $bahan_baku_list;
    }
}
