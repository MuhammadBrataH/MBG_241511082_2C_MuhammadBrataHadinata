<?php namespace App\Controllers\Dapur;
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
        $this->permintaanModel = new PermintaanModel();
        $this->detailModel = new PermintaanDetailModel();
        $this->bahanModel = new BahanBakuModel();
        // Inisialisasi koneksi DB secara eksplisit
        $this->db = \Config\Database::connect(); 
    }

    // Form Buat Permintaan Bahan
    public function newPermintaan()
    {
        $semuaBahan = $this->bahanModel->getBahanBakuWithStatus();
        
        $data['bahan_tersedia'] = array_filter($semuaBahan, function($item) {
            // Syarat: stok > 0 DAN status BUKAN kadaluarsa
            return $item['jumlah'] > 0 && $item['status'] !== 'kadaluarsa';
        });
        
        return view('dapur/permintaan/new', $data);
    }
    
    // Simpan Permintaan Bahan (Challenge: Simpan ke 2 tabel)
    public function store()
    {
        // Validasi form utama
        if (!$this->validate($this->permintaanModel->validationRules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $bahan_diminta = $this->request->getPost('bahan');
        
        if (empty($bahan_diminta)) {
            session()->setFlashdata('error', 'Mohon pilih minimal satu bahan baku yang diminta.');
            return redirect()->back()->withInput();
        }

        $this->db->transBegin();
        
        // Simpan ke tabel Permintaan (Status Awal 'menunggu')
        $permintaanData = [
            'pemohon_id' => session()->get('user_id'),
            'tgl_masak' => $this->request->getPost('tgl_masak'),
            'menu_makan' => $this->request->getPost('menu_makan'),
            'jumlah_porsi' => $this->request->getPost('jumlah_porsi'),
            'status' => 'menunggu',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->permintaanModel->insert($permintaanData);
        $permintaan_id = $this->permintaanModel->insertID();

        // Simpan ke tabel Permintaan Detail
        $valid_detail = false;
        foreach ($bahan_diminta as $bahan) {
            // Memastikan data valid dan jumlah diminta > 0
            if (isset($bahan['bahan_id']) && $bahan['bahan_id'] && (int)$bahan['jumlah_diminta'] > 0) {
                $this->detailModel->insert([
                    'permintaan_id' => $permintaan_id,
                    'bahan_id' => $bahan['bahan_id'],
                    'jumlah_diminta' => (int)$bahan['jumlah_diminta']
                ]);
                $valid_detail = true;
            }
        }
        
        if (!$valid_detail) {
            $this->db->transRollback();
            session()->setFlashdata('error', 'Permintaan gagal. Pastikan Anda memasukkan jumlah permintaan untuk setidaknya satu bahan.');
            return redirect()->to(base_url('dapur/permintaan/new'));
        }

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            session()->setFlashdata('error', 'Permintaan gagal disimpan karena error database.');
        } else {
            $this->db->transCommit();
            session()->setFlashdata('success', 'Permintaan berhasil diajukan dan menunggu persetujuan Gudang.');
        }

        return redirect()->to(base_url('dapur/permintaan/status'));
    }
    
    // Lihat Status Permintaan (Challenge)
    public function status()
    {
        // 1. Ambil Query Builder Object dari Model
        $builder = $this->permintaanModel->getPermintaanWithPemohon();
        
        // 2. Lanjutkan rantai query (where) dan eksekusi (findAll)
        $data['permintaan'] = $builder
                                    ->where('pemohon_id', session()->get('user_id'))
                                    ->findAll();
                                    
        return view('dapur/permintaan/status', $data);
    }
    
    // Lihat Detail Permintaan
    public function detail($id)
    {
        $data['permintaan'] = $this->permintaanModel->getPermintaanWithPemohon($id);
        
        // Cek kepemilikan (agar user Dapur A tidak bisa melihat permintaan Dapur B)
        if (empty($data['permintaan']) || $data['permintaan']['pemohon_id'] != session()->get('user_id')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data['detail_bahan'] = $this->detailModel->db->table('permintaan_detail d')
            ->select('d.jumlah_diminta, b.nama, b.satuan')
            ->join('bahan_baku b', 'b.id = d.bahan_id')
            ->where('d.permintaan_id', $id)
            ->findAll();
            
        return view('dapur/permintaan/detail', $data);
    }
}
