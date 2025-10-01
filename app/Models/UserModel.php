<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'mahasiswa'; // 🔹 ganti sesuai nama tabelmu
    protected $primaryKey = 'id';

    protected $allowedFields = ['username', 'password', 'role', 'nim', 'nama', 'umur'];

    public function getUserByUsername($username)
    {
        return $this->where('username', $username)->first();
    }
}
