<?php namespace App\Models;

use CodeIgniter\Model;

class PermintaanModel extends Model
{
    protected $table = 'permintaan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pemohon_id', 'tgl_masak', 'menu_makan', 'jumlah_porsi', 'status', 'alasan_tolak', 'created_at']; 

    // Validasi untuk Permintaan
    protected $validationRules = [
        'tgl_masak'    => 'required|valid_date',
        'menu_makan'   => 'required|min_length[5]',
        'jumlah_porsi' => 'required|is_natural_no_zero',
    ];
    
    /**
     * Mengambil data permintaan dengan JOIN ke tabel user.
     * @param int|null $id ID Permintaan (opsional)
     * @return \CodeIgniter\Database\Query|array|object
     */ 
    // Read dengan Join (Kriteria Mahir: Read dengan JOIN)
    public function getPermintaanWithPemohon($id = null)
    {
        $builder = $this->db->table($this->table)
                    ->select('permintaan.*, user.name as nama_pemohon')
                    ->join('user', 'user.id = permintaan.pemohon_id')
                    ->orderBy('permintaan.created_at', 'DESC');
        
        if ($id !== null) {
            // Jika ID ada, kembalikan array (satu baris)
            return $builder->where('permintaan.id', $id)->get()->getRowArray();
        }
        
        // Jika ID tidak ada, kembalikan Query Builder object
        return $builder; 
    }
}

class PermintaanDetailModel extends Model
{
    protected $table = 'permintaan_detail';
    protected $primaryKey = 'id';
    protected $allowedFields = ['permintaan_id', 'bahan_id', 'jumlah_diminta'];
}