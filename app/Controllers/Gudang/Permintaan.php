<?php namespace App\Controllers\Gudang;
use App\Controllers\BaseController;
// Pastikan semua Model di-import di sini
use App\Models\PermintaanModel; 
use App\Models\PermintaanDetailModel;
use App\Models\BahanBakuModel;

class Permintaan extends BaseController
{
    /**
     * @var \CodeIgniter\Database\BaseConnection
     */
    protected $db;
    protected $permintaanModel;
    protected $detailModel;
    protected $bahanModel;

    public function __construct() {
        // Inisialisasi koneksi DB secara eksplisit
        $this->db = \Config\Database::connect();
        
        $this->permintaanModel = new PermintaanModel();
        $this->detailModel = new PermintaanDetailModel();
        $this->bahanModel = new BahanBakuModel();
    }

    // Daftar permintaan 'menunggu'
    public function index()
    {
        // Ambil Query Builder Object dari Model (PERBAIKAN: Pemecahan Rantai Query)
        $builder = $this->permintaanModel->getPermintaanWithPemohon();
        
        $data['permintaan'] = $builder
                                    ->where('status', 'menunggu')
                                    ->findAll(); 
        return view('gudang/permintaan/index', $data);
    }
    
    public function detail($id)
    {
        // PERBAIKAN: Gunakan DocBlock inline untuk memberi tahu VS Code bahwa hasilnya adalah array
        /** @var array $permintaan */ 
        $data['permintaan'] = $this->permintaanModel->getPermintaanWithPemohon($id);
        $data['detail_bahan'] = $this->detailModel->db->table('permintaan_detail d')
            ->select('d.jumlah_diminta, b.nama, b.satuan, b.jumlah as stok_saat_ini')
            ->join('bahan_baku b', 'b.id = d.bahan_id')
            ->where('d.permintaan_id', $id)
            ->findAll();
        return view('gudang/permintaan/detail', $data);
    }

    // Proses Persetujuan (Challenge: Disetujui/Tolak dan Pengurangan Stok Otomatis)
    public function process($permintaan_id)
    {
        $action = $this->request->getPost('action'); 
        
        if ($action === 'approve') {
            $this->db->transBegin();
            $details = $this->detailModel->where('permintaan_id', $permintaan_id)->findAll();
            $stok_kurang = [];

            // 1. Cek ketersediaan stok
            foreach ($details as $detail) {
                $bahan = $this->bahanModel->find($detail['bahan_id']);
                if ($bahan && $bahan['jumlah'] < $detail['jumlah_diminta']) {
                    $stok_kurang[] = $bahan['nama'];
                }
            }
            
            if (!empty($stok_kurang)) {
                $this->db->transRollback();
                session()->setFlashdata('error', 'Gagal menyetujui. Stok bahan: ' . implode(', ', $stok_kurang) . ' tidak mencukupi.');
                return redirect()->to(base_url('gudang/permintaan'));
            }

            // 2. Kurangi stok (Pengurangan stok otomatis)
            foreach ($details as $detail) {
                $bahan = $this->bahanModel->find($detail['bahan_id']);
                $newStock = $bahan['jumlah'] - $detail['jumlah_diminta'];
                $newStatus = $this->bahanModel->determineStatus($newStock, $bahan['tanggal_kadaluarsa']);
                
                $this->bahanModel->update($bahan['id'], [
                    'jumlah' => $newStock,
                    'status' => $newStatus // Status 'habis' otomatis jika stok akhir = 0
                ]);
            }

            // 3. Update status permintaan
            $this->permintaanModel->update($permintaan_id, ['status' => 'disetujui']);
            
            if ($this->db->transStatus() === false) {
                 $this->db->transRollback();
                 session()->setFlashdata('error', 'Gagal memproses persetujuan database error.');
            } else {
                 $this->db->transCommit();
                 session()->setFlashdata('success', 'Permintaan disetujui. Stok bahan baku telah dikurangi.');
            }

        } elseif ($action === 'reject') {
            $alasan = $this->request->getPost('alasan_tolak');
            $this->permintaanModel->update($permintaan_id, [
                'status' => 'ditolak',
                'alasan_tolak' => $alasan 
            ]); // Tolak dengan alasan
            session()->setFlashdata('success', 'Permintaan berhasil ditolak.');
        }
        
        return redirect()->to(base_url('gudang/permintaan'));
    }
}
