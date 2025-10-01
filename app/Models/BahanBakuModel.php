<?php namespace App\Models;

use CodeIgniter\Model;
use DateTime; // Pastikan ini di-import untuk fungsi DateTime

class BahanBakuModel extends Model
{
    protected $table = 'bahan_baku';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama', 'kategori', 'jumlah', 'satuan', 'tanggal_masuk', 'tanggal_kadaluarsa', 'status', 'created_at'];

    // Aturan validasi (Kriteria Mahir)
    protected $validationRules = [
        'nama'               => 'required|min_length[3]',
        'kategori'           => 'required',
        'jumlah'             => 'required|is_natural',
        'satuan'             => 'required',
        'tanggal_masuk'      => 'required|valid_date',
        // Pastikan tanggal kadaluarsa lebih besar dari tanggal masuk
        'tanggal_kadaluarsa' => 'required|valid_date|greater_than[tanggal_masuk]' 
    ];

    /**
     * Reusable Method: Menghitung Status Bahan Baku (Clean Code)
     */
    public function determineStatus($jumlah, $tanggal_kadaluarsa)
    {
        $today = new DateTime();
        $expiryDate = new DateTime($tanggal_kadaluarsa);
        $interval = $today->diff($expiryDate);

        if ((int)$jumlah <= 0) {
            return 'habis'; // Habis
        }
        
        if ($today > $expiryDate) {
            return 'kadaluarsa'; // Kadaluarsa
        }
        
        // Cek jika tersisa 3 hari atau kurang
        if ($interval->days <= 3) {
            return 'segera_kadaluarsa'; // Segera Kadaluarsa H-3
        }
        
        return 'tersedia'; // Tersedia
    }

    /**
     * Method untuk mengambil data dengan status otomatis terbaru
     */
    public function getBahanBakuWithStatus()
    {
        // Gunakan findAll() untuk mengambil semua data
        $bahan = $this->findAll();
        
        // Menggunakan '&' untuk referensi agar array $bahan diubah langsung
        foreach ($bahan as &$item) { 
            $newStatus = $this->determineStatus($item['jumlah'], $item['tanggal_kadaluarsa']);
            
            // Update di DB untuk Konsistensi Data jika status berubah
            if ($item['status'] !== $newStatus) {
                 $this->update($item['id'], ['status' => $newStatus]);
                 $item['status'] = $newStatus;
            }
        }
        // Jangan lupa hilangkan '&' jika tidak diperlukan setelah loop, 
        // tapi di sini aman karena ini adalah akhir method.
        return $bahan;
    }
}