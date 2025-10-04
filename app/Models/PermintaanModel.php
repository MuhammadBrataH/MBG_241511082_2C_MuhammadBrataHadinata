<?php namespace App\Models;

use CodeIgniter\Model;

class PermintaanModel extends Model
{
    protected $table = 'permintaan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pemohon_id', 'tgl_masak', 'menu_makan', 'jumlah_porsi', 'status', 'created_at'];
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = null;

    /**
     * Mengambil daftar permintaan berdasarkan status (untuk Admin Gudang)
     */
    public function getPermintaanByStatus(string $status)
    {
        return $this->select('permintaan.*, user.name as pemohon_name')
                    ->join('user', 'user.id = permintaan.pemohon_id')
                    ->where('permintaan.status', $status)
                    ->orderBy('permintaan.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Mengambil daftar permintaan yang diajukan oleh pemohon tertentu (untuk Client Dapur)
     */
    public function getPermintaanByPemohon(int $pemohonId)
    {
        return $this->select('permintaan.*')
                    ->where('permintaan.pemohon_id', $pemohonId)
                    ->orderBy('permintaan.created_at', 'DESC')
                    ->findAll();
    }
    
    /**
     * Mengambil detail bahan baku untuk permintaan spesifik
     * Note: Karena model ini didefinisikan untuk tabel 'permintaan', kita menggunakan Query Builder
     * secara eksplisit untuk join multi-tabel.
     */
    public function getDetailPermintaan(int $permintaanId)
    {
        $builder = $this->db->table('permintaan_detail');
        return $builder->select('permintaan_detail.*, bahan_baku.nama as nama_bahan, bahan_baku.satuan, bahan_baku.jumlah as stok_tersedia')
                       ->join('bahan_baku', 'bahan_baku.id = permintaan_detail.bahan_id')
                       ->where('permintaan_detail.permintaan_id', $permintaanId)
                       ->get()
                       ->getResultArray();
    }

    /**
     * Custom Validation: Memastikan tanggal masak adalah hari ini atau setelahnya (H >= H)
     * Dipindahkan ke Model agar dapat ditemukan oleh Validator CI4.
     */
    public function validateTanggalMasak(string $str, string $fields, array $data): bool
    {
        $tglMasak = strtotime($str);
        $tglHariIni = strtotime(date('Y-m-d')); // Mengambil tanggal hari ini (tanpa waktu)

        if ($tglMasak < $tglHariIni) {
            // Kita tidak perlu mengatur error di sini karena Controller akan menangani pesan error
            return false;
        }
        return true;
    }
}
