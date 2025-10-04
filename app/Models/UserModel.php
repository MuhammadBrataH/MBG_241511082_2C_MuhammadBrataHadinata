<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'email', 'password', 'role', 'created_at'];
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = null; // Tidak ada updated_at

    /**
     * Fungsi untuk hashing password sebelum disimpan
     */
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (! isset($data['data']['password'])) {
            return $data;
        }

        // Gunakan password_hash() untuk keamanan yang lebih baik
        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        
        return $data;
    }

    /**
     * Fungsi untuk mencari user berdasarkan email
     */
    public function getUserByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }
}