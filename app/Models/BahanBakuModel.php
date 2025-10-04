<?php namespace App\Models;

use CodeIgniter\Model;

class BahanBakuModel extends Model
{
    protected $table = 'bahan_baku';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama', 'kategori', 'jumlah', 'satuan', 'tanggal_masuk', 'tanggal_kadaluarsa', 'status', 'created_at'];
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = null;

    protected $beforeInsert = ['setStatus'];
    protected $beforeUpdate = ['setStatus'];

    /**
     * Logika untuk menentukan status otomatis bahan baku.
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
    
    /**
     * Mengambil semua data bahan baku, menghitung status dinamis.
     * Digunakan oleh Gudang (Admin) untuk melihat seluruh stok.
     */
    public function getAllBahanBaku()
    {
        $bahan_baku_list = $this->findAll();
        
        // Loop untuk update status dinamis berdasarkan tanggal hari ini
        foreach ($bahan_baku_list as &$bahan) {
            $data_temp = ['data' => $bahan];
            $updated_data = $this->setStatus($data_temp);
            $bahan['status'] = $updated_data['data']['status'];
        }
        return $bahan_baku_list;
    }

    /**
     * Mengambil daftar bahan baku yang tersedia untuk diajukan oleh Dapur.
     * Syarat: stok > 0 DAN status BUKAN 'kadaluarsa' atau 'habis'.
     * @return array
     */
    public function getAvailableBahanBaku()
    {
        // Ambil semua bahan baku dengan stok > 0
        $bahan_baku_list = $this->where('jumlah >', 0)->findAll();
        $available_list = [];

        // Hitung status dinamis dan filter
        foreach ($bahan_baku_list as $bahan) {
            $data_temp = ['data' => $bahan];
            $updated_data = $this->setStatus($data_temp);
            $current_status = $updated_data['data']['status'];

            // Filter: Hanya yang statusnya 'tersedia' atau 'segera_kadaluarsa' yang boleh diajukan.
            if ($current_status !== 'kadaluarsa' && $current_status !== 'habis') {
                $bahan['status'] = $current_status;
                $available_list[] = $bahan;
            }
        }
        
        return $available_list;
    }
}
