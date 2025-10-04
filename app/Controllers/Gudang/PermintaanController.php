<?php namespace App\Controllers\Gudang;

use App\Controllers\BaseController;
use App\Models\BahanBakuModel;
use App\Models\PermintaanModel;
use App\Models\PermintaanDetailModel;

class PermintaanController extends BaseController
{
    protected $permintaanModel;
    protected $permintaanDetailModel;
    protected $bahanBakuModel;
    protected $db; // Deklarasi properti $db

    public function __construct()
    {
        $this->permintaanModel = new PermintaanModel();
        $this->permintaanDetailModel = new PermintaanDetailModel();
        $this->bahanBakuModel = new BahanBakuModel();
        helper(['form', 'url', 'number']);
        $this->db = \Config\Database::connect(); // Inisialisasi koneksi database
    }

    /**
     * PERBAIKAN ROUTING: Mengarahkan URL /gudang/permintaan ke list()
     * Method ini ditambahkan hanya untuk memenuhi routing default CodeIgniter
     * dan mengarahkan ke method yang sebenarnya menampilkan daftar.
     */
    public function index()
    {
        // Panggil method list() di Controller yang sama
        return $this->list();
    }

    /**
     * Menampilkan daftar permintaan dengan status 'menunggu' untuk diproses Admin Gudang.
     */
    public function list()
    {
        // Mengambil permintaan dengan status 'menunggu' dan data pemohon
        $listPermintaan = $this->permintaanModel->getPermintaanByStatus('menunggu');

        $data = [
            'title' => 'Daftar Permintaan Menunggu Persetujuan',
            'list_permintaan' => $listPermintaan,
        ];

        return view('gudang/permintaan/list', $data);
    }

    /**
     * Menampilkan detail permintaan spesifik.
     */
    public function detail($id = null)
    {
        if (!$id) {
            session()->setFlashdata('error', 'ID Permintaan tidak valid.');
            return redirect()->to('/gudang/permintaan/list');
        }

        // Mengambil data utama permintaan
        $permintaan = $this->permintaanModel->getPermintaanById($id);

        if (!$permintaan) {
            session()->setFlashdata('error', 'Data permintaan tidak ditemukan.');
            return redirect()->to('/gudang/permintaan/list');
        }

        // Mengambil detail bahan baku yang diminta (JOIN permintaan_detail, bahan_baku)
        $details = $this->permintaanDetailModel->getDetailWithBahanInfo($id);

        $permintaan['details'] = $details;
        
        $data = [
            'title' => 'Detail Permintaan #'. $id,
            'permintaan' => $permintaan,
        ];

        return view('gudang/permintaan/detail', $data);
    }

    /**
     * Memproses persetujuan (ACC) atau penolakan (Tolak) permintaan.
     */
    public function proses($permintaanId)
    {
        $action = $this->request->getPost('action');

        if (!in_array($action, ['acc', 'tolak'])) {
            session()->setFlashdata('error', 'Aksi tidak valid.');
            return redirect()->back();
        }

        // 1. Validasi permintaan
        $permintaan = $this->permintaanModel->find($permintaanId);
        if (!$permintaan || $permintaan['status'] !== 'menunggu') {
            session()->setFlashdata('error', 'Permintaan tidak ditemukan atau sudah diproses.');
            return redirect()->to('/gudang/permintaan/list');
        }

        if ($action === 'tolak') {
            // Logika Tolak: Hanya update status
            $alasan = $this->request->getPost('alasan_penolakan') ?? 'Tidak ada alasan spesifik.';
            $this->permintaanModel->update($permintaanId, ['status' => 'ditolak', 'alasan_penolakan' => $alasan]);
            session()->setFlashdata('success', 'Permintaan #'. $permintaanId .' berhasil DITOLAK.');
            return redirect()->to('/gudang/permintaan/list');
        } 
        
        // Logika ACC: Transaksi dan Pengurangan Stok
        $details = $this->permintaanDetailModel->getDetailWithBahanInfo($permintaanId);
        
        $this->db->transBegin();

        try {
            // 2. Loop dan kurangi stok untuk setiap bahan
            foreach ($details as $detail) {
                $bahanId = $detail['bahan_id'];
                $jumlahDiminta = $detail['jumlah_diminta'];
                
                // Panggil method reduceStok di BahanBakuModel
                $stokBerhasilKurang = $this->bahanBakuModel->reduceStok($bahanId, $jumlahDiminta);

                if (!$stokBerhasilKurang) {
                    // Jika pengurangan stok gagal (stok kurang), batalkan transaksi
                    $this->db->transRollback();
                    $namaBahan = $detail['nama'];
                    session()->setFlashdata('error', "Stok **{$namaBahan}** tidak mencukupi. Permintaan dibatalkan (Rollback).");
                    return redirect()->to('/gudang/permintaan/list');
                }
            }

            // 3. Update status permintaan menjadi 'disetujui'
            $this->permintaanModel->update($permintaanId, ['status' => 'disetujui']);
            
            // 4. Commit transaksi
            $this->db->transCommit();
            session()->setFlashdata('success', 'Permintaan #'. $permintaanId .' berhasil DISETUJUI dan stok bahan baku telah dikurangi.');

        } catch (\Exception $e) {
            // Tangani error umum
            $this->db->transRollback();
            session()->setFlashdata('error', 'Terjadi kesalahan sistem saat memproses ACC: ' . $e->getMessage());
        }

        return redirect()->to('/gudang/permintaan/list');
    }
}
