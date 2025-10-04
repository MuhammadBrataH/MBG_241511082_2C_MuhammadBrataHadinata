<?php namespace App\Controllers\Dapur;

use App\Controllers\BaseController;
use App\Models\BahanBakuModel;
use App\Models\PermintaanModel;
use App\Models\PermintaanDetailModel;

class PermintaanController extends BaseController
{
    protected $bahanBakuModel;
    protected $permintaanModel;
    protected $permintaanDetailModel;
    protected $db;

    public function __construct()
    {
        $this->bahanBakuModel = new BahanBakuModel();
        $this->permintaanModel = new PermintaanModel();
        $this->permintaanDetailModel = new PermintaanDetailModel();
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);
    }

    // [FITUR BUAT PERMINTAAN] - Tampilkan Form
    public function baru()
    {
        // Syarat: stok > 0 dan status bukan 'kadaluarsa'
        $bahanTersedia = $this->bahanBakuModel->where('jumlah >', 0)
                                              ->where('status !=', 'kadaluarsa')
                                              ->findAll();
        
        $data = [
            'title' => 'Buat Permintaan Bahan',
            'bahan_tersedia' => $bahanTersedia,
            'validation' => \Config\Services::validation()
        ];
        return view('dapur/permintaan/form', $data);
    }

    // [FITUR BUAT PERMINTAAN] - Proses Simpan Transaksi
    public function simpan()
    {
        $rules = [
            'tgl_masak'    => 'required|valid_date',
            'menu_makan'   => 'required|max_length[255]',
            'jumlah_porsi' => 'required|integer|greater_than[0]',
            'bahan_id'     => 'required' // Memastikan ada minimal satu bahan diminta
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Ambil data dari form
        $tgl_masak = $this->request->getPost('tgl_masak');
        $menu_makan = $this->request->getPost('menu_makan');
        $jumlah_porsi = $this->request->getPost('jumlah_porsi');
        $bahan_ids = $this->request->getPost('bahan_id');
        $jumlah_diminta = $this->request->getPost('jumlah_diminta');
        $pemohonId = session()->get('id'); // ID user Dapur yang sedang login

        // Mulai Transaksi Database
        $this->db->transBegin();

        try {
            // 1. Simpan ke tabel `permintaan` (Data Utama)
            $dataPermintaan = [
                'pemohon_id'   => $pemohonId,
                'tgl_masak'    => $tgl_masak,
                'menu_makan'   => $menu_makan,
                'jumlah_porsi' => $jumlah_porsi,
                'status'       => 'menunggu',
            ];
            $permintaanId = $this->permintaanModel->insert($dataPermintaan);

            if (!$permintaanId) {
                throw new \Exception('Gagal menyimpan data Permintaan utama.');
            }

            // 2. Simpan ke tabel `permintaan_detail` (Data Rincian)
            $detailData = [];
            foreach ($bahan_ids as $key => $bahan_id) {
                // Pastikan jumlah permintaan > 0
                if (isset($jumlah_diminta[$key]) && $jumlah_diminta[$key] > 0) {
                    $detailData[] = [
                        'permintaan_id' => $permintaanId,
                        'bahan_id'      => $bahan_id,
                        'jumlah_diminta' => $jumlah_diminta[$key],
                    ];
                }
            }

            if (empty($detailData)) {
                 throw new \Exception('Tidak ada bahan baku dengan jumlah diminta yang valid.');
            }

            $success = $this->permintaanDetailModel->insertBatch($detailData);

            if (!$success) {
                throw new \Exception('Gagal menyimpan data Permintaan Detail.');
            }

            // Jika semua sukses, commit transaksi
            $this->db->transCommit();
            session()->setFlashdata('success', 'Permintaan bahan baku berhasil diajukan dengan status **Menunggu Persetujuan**.');
            return redirect()->to('/dapur/permintaan/status');

        } catch (\Exception $e) {
            // Jika ada error, rollback transaksi
            $this->db->transRollback();
            log_message('error', 'Error saat simpan permintaan: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal menyimpan permintaan. Terjadi kesalahan pada sistem: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    // [FITUR LIHAT STATUS PERMINTAAN]
    public function status()
    {
        $pemohonId = session()->get('id');
        
        // Ambil semua permintaan yang diajukan oleh user ini
        $permintaanList = $this->permintaanModel->getPermintaanByPemohon($pemohonId);

        // Ambil detail bahan baku untuk setiap permintaan
        $dataPermintaan = [];
        foreach ($permintaanList as $permintaan) {
            $permintaan['detail_bahan'] = $this->permintaanModel->getDetailPermintaan($permintaan['id']);
            $dataPermintaan[] = $permintaan;
        }

        $data = [
            'title' => 'Status Permintaan Bahan',
            'permintaan_list' => $dataPermintaan,
        ];
        
        return view('dapur/permintaan/status', $data);
    }
}
