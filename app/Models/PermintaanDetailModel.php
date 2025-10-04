<?php namespace App\Models;

use CodeIgniter\Model;

class PermintaanDetailModel extends Model
{
    protected $table = 'permintaan_detail';
    protected $primaryKey = 'id';
    
    // Field yang diizinkan untuk diisi (digunakan saat insert/update)
    protected $allowedFields = ['permintaan_id', 'bahan_id', 'jumlah_diminta'];
    
    // Karena tabel ini hanya menyimpan data relasional, kita tidak memerlukan timestamps
    protected $useTimestamps = false; 
    
    /**
     * Fungsi untuk mengambil detail bahan baku berdasarkan ID Permintaan.
     * Digunakan untuk menampilkan rincian permintaan di Gudang dan Dapur.
     * @param int $permintaanId ID Permintaan
     * @return array
     */
    public function getDetailBahanByPermintaanId(int $permintaanId)
    {
        return $this->select('permintaan_detail.*, bahan_baku.nama, bahan_baku.satuan, bahan_baku.kategori, bahan_baku.jumlah as stok_saat_ini')
                    ->join('bahan_baku', 'bahan_baku.id = permintaan_detail.bahan_id')
                    ->where('permintaan_detail.permintaan_id', $permintaanId)
                    ->findAll();
    }
}
