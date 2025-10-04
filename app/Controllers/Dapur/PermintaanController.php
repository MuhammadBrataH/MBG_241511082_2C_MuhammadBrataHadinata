<?php namespace App\Controllers\Dapur;

use App\Controllers\BaseController;
use App\Models\PermintaanModel;
use App\Models\PermintaanDetailModel;
use App\Models\BahanBakuModel;

class PermintaanController extends BaseController
{
    protected $permintaanModel;
    protected $detailModel;
    protected $bahanBakuModel;
    protected $db;

    public function __construct()
    {
        $this->permintaanModel = new PermintaanModel();
        $this->detailModel = new PermintaanDetailModel();
        $this->bahanBakuModel = new BahanBakuModel();
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);
    }

    // Menampilkan form buat permintaan baru
    public function baru()
    {
        $bahan_tersedia = $this->bahanBakuModel->getAvailableBahanBaku();
        
        $data = [
            'title' => 'Buat Permintaan Bahan Baru',
            'bahan_baku' => $bahan_tersedia,
        ];
        
        return view('dapur/permintaan/form', $data);
    }

    // Proses penyimpanan permintaan ke tabel permintaan dan permintaan_detail (Transaksi)
    public function simpan()
    {
        $session = session();
        
        // Aturan validasi
        $rules = [
            // Perbaikan: Menggunakan nama method validasi yang ada di PermintaanModel
            'tgl_masak'    => 'required|valid_date|validateTanggalMasak', 
            'menu_makan'   => 'required|max_length[255]',
            'jumlah_porsi' => 'required|integer|greater_than[0]',
            'bahan_id'     => 'required|min_length[1]', // Memastikan minimal ada satu bahan diminta
        ];
        
        // Pesan error kustom untuk validasi Model
        $messages = [
            'tgl_masak' => [
                'validateTanggalMasak' => 'Tanggal masak harus hari ini atau setelahnya.'
            ]
        ];

        // Memvalidasi menggunakan model
        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $pemohonId = $session->get('id');
        $bahanIds = $this->request->getPost('bahan_id');
        $jumlahDiminta = $this->request->getPost('jumlah_diminta');
        
        // Memulai Transaksi Database
        $this->db->transBegin();

        try {
            // 1. Simpan data utama ke tabel 'permintaan'
            $dataPermintaan = [
                'pemohon_id'   => $pemohonId,
                'tgl_masak'    => $this->request->getPost('tgl_masak'),
                'menu_makan'   => $this->request->getPost('menu_makan'),
                'jumlah_porsi' => $this->request->getPost('jumlah_porsi'),
                'status'       => 'menunggu', // Status awal: menunggu
                'created_at'   => date('Y-m-d H:i:s'),
            ];

            $this->permintaanModel->insert($dataPermintaan);
            $permintaanId = $this->permintaanModel->insertID();

            // 2. Simpan detail bahan baku ke tabel 'permintaan_detail'
            $detailBahan = [];
            
            if (is_array($bahanIds) && is_array($jumlahDiminta)) {
                $uniqueBahanIds = [];
                
                foreach ($bahanIds as $index => $bahanId) {
                    $jumlah = (int) $jumlahDiminta[$index];

                    // Validasi internal: Pastikan jumlah > 0 dan bahan ID unik
                    if ($bahanId && $jumlah > 0 && !in_array($bahanId, $uniqueBahanIds)) {
                        $detailBahan[] = [
                            'permintaan_id'  => $permintaanId,
                            'bahan_id'       => $bahanId,
                            'jumlah_diminta' => $jumlah,
                        ];
                        $uniqueBahanIds[] = $bahanId;
                    }
                }
            }

            if (empty($detailBahan)) {
                throw new \Exception('Daftar bahan baku yang diminta tidak valid atau kosong.');
            }
            
            // Simpan semua detail sekaligus
            $this->detailModel->insertBatch($detailBahan);

            // Commit transaksi jika semua berhasil
            $this->db->transCommit();
            session()->setFlashdata('success', 'Permintaan bahan baku berhasil diajukan! Status: **MENUNGGU** persetujuan Gudang.');
            return redirect()->to('/dapur/permintaan/status');

        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $this->db->transRollback();
            log_message('error', 'Error saat menyimpan permintaan: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal mengajukan permintaan. Terjadi kesalahan pada sistem. ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
    
    // Menampilkan daftar permintaan yang diajukan oleh user yang sedang login
    public function status()
    {
        $pemohonId = session()->get('id');
        $dataPermintaan = $this->permintaanModel->getPermintaanByPemohon($pemohonId);

        $data = [
            'title'      => 'Status Permintaan Bahan',
            'permintaan' => $dataPermintaan,
        ];

        return view('dapur/permintaan/status', $data);
    }
    
    // Menampilkan detail spesifik permintaan
    public function detail($id)
    {
        $permintaan = $this->permintaanModel->find($id);
        
        if (!$permintaan || $permintaan['pemohon_id'] !== session()->get('id')) {
             session()->setFlashdata('error', 'Permintaan tidak ditemukan atau Anda tidak memiliki akses.');
             return redirect()->to('/dapur/permintaan/status');
        }
        
        $detail = $this->permintaanModel->getDetailPermintaan($id);

        $data = [
            'title'      => 'Detail Permintaan #'.$id,
            'permintaan' => $permintaan,
            'detail'     => $detail,
        ];
        
        return view('dapur/permintaan/detail', $data);
    }
}
