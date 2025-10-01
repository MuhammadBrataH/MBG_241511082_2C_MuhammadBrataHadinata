<?php
namespace App\Models;
use CodeIgniter\Model;

class MahasiswaModel extends Model
{
    protected $table = 'mahasiswa';
    
    public function getMahasiswa()
    {
        $db = \Config\Database::connect();
        $query = $db->query('SELECT * FROM mahasiswa');
        return $query->getResultArray();
    }
}
?>