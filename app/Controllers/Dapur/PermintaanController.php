<?php namespace App\Controllers\Dapur;

use App\Controllers\BaseController;
use App\Models\PermintaanModel;
use App\Models\PermintaanDetailModel;
use App\Models\BahanBakuModel; // Diperlukan untuk mengambil data bahan baku
use CodeIgniter\I18n\Time;

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

    // Menampilkan form untuk membuat permintaan baru
    public function baru()
    {
        // Ambil daftar bahan baku yang tersedia (stok > 0 dan status bukan 'kadaluarsa' atau 'habis')
        $bahan_baku = $this->bahanBakuModel->getAvailableBahanBaku();

        $data = [
            'title' => 'Buat Permintaan Baru',
            'bahan_baku' => $bahan_baku,
            'errors' => session('errors') // Ambil error validasi
        ];
        
        return view('dapur/permintaan/form', $data);
    }

    /**
     * Memproses permintaan POST dan menjalankan Transaksi Database
     */
    public function simpan()
    {
        $validationRules = [
            'tgl_masak' => 'required|valid_date|after_or_equal[today]',
            'menu_makan' => 'required|max_length[255]',
            'jumlah_porsi' => 'required|integer|greater_than[0]',
            // Validasi array detail bahan baku
            'bahan_id' => 'required|min_length[1]',
            'jumlah_diminta' => 'required|min_length[1]',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $bahanIds = $this->request->getPost('bahan_id');
        $jumlahDiminta = $this->request->getPost('jumlah_diminta');
        $detailPermintaan = [];

        // Gabungkan dan validasi detail array lebih lanjut
        foreach ($bahanIds as $index => $bahanId) {
            $jumlah = (int)$jumlahDiminta[$index];

            if ($bahanId && $jumlah > 0) {
                $detailPermintaan[] = [
                    'bahan_id' => $bahanId,
                    'jumlah_diminta' => $jumlah,
                ];
            }
        }
        
        // Cek minimal 1 item valid diminta
        if (empty($detailPermintaan)) {
            session()->setFlashdata('error', 'Permintaan bahan baku minimal harus terdiri dari satu item yang valid.');
            return redirect()->back()->withInput();
        }

        // Mulai Transaksi Database
        $this->db->transBegin();

        try {
            // 1. Simpan Data Utama ke tabel `permintaan`
            $dataPermintaan = [
                'pemohon_id' => session()->get('id'), // Ambil ID user dari sesi
                'tgl_masak' => $this->request->getPost('tgl_masak'),
                'menu_makan' => $this->request->getPost('menu_makan'),
                'jumlah_porsi' => $this->request->getPost('jumlah_porsi'),
                'status' => 'menunggu', // Status awal: menunggu
                'created_at' => Time::now(),
            ];

            $permintaanId = $this->permintaanModel->insert($dataPermintaan);

            if (!$permintaanId) {
                throw new \Exception('Gagal menyimpan data permintaan utama.');
            }

            // 2. Simpan Data Detail ke tabel `permintaan_detail`
            $detailBatch = [];
            foreach ($detailPermintaan as $detail) {
                $detailBatch[] = [
                    'permintaan_id' => $permintaanId,
                    'bahan_id' => $detail['bahan_id'],
                    'jumlah_diminta' => $detail['jumlah_diminta'],
                ];
            }

            if (!$this->detailModel->insertBatch($detailBatch)) {
                throw new \Exception('Gagal menyimpan detail bahan baku.');
            }

            // Commit Transaksi
            $this->db->transCommit();

            session()->setFlashdata('success', 'Permintaan bahan baku berhasil diajukan dengan status **MENUNGGU**. Silakan tunggu persetujuan dari Petugas Gudang.');
            return redirect()->to('/dapur/permintaan/status');

        } catch (\Exception $e) {
            // Rollback Transaksi jika terjadi Exception
            $this->db->transRollback();

            // Log error
            log_message('error', 'Gagal Transaksi Permintaan: ' . $e->getMessage());
            
            session()->setFlashdata('error', 'Gagal mengajukan permintaan. Terjadi kesalahan pada sistem database. ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    // Menampilkan daftar status permintaan user yang sedang login
    public function status()
    {
        $pemohonId = session()->get('id');
        
        // Ambil semua permintaan yang diajukan oleh user ini
        $permintaanList = $this->permintaanModel->getPermintaanByPemohon($pemohonId);

        // Ambil detail bahan baku untuk setiap permintaan
        foreach ($permintaanList as &$permintaan) {
            $permintaan['details'] = $this->permintaanModel->getDetailPermintaan($permintaan['id']);
        }

        $data = [
            'title' => 'Status Permintaan Bahan',
            'permintaanList' => $permintaanList,
        ];
        
        return view('dapur/permintaan/status', $data);
    }

    // Menampilkan detail spesifik satu permintaan (jika menggunakan halaman terpisah)
    public function detail($id)
    {
        $permintaan = $this->permintaanModel
                           ->where('id', $id)
                           ->where('pemohon_id', session()->get('id'))
                           ->first();

        if (!$permintaan) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Permintaan tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $permintaan['details'] = $this->permintaanModel->getDetailPermintaan($id);

        $data = [
            'title' => 'Detail Permintaan',
            'permintaan' => $permintaan,
        ];

        return view('dapur/permintaan/detail', $data);
    }
}
