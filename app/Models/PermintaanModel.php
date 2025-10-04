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
}
