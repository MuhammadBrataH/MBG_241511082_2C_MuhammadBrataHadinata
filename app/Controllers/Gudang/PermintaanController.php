<?php namespace App\Controllers\Gudang;

use App\Controllers\BaseController;
use App\Models\PermintaanModel;
use App\Models\PermintaanDetailModel;
use App\Models\BahanBakuModel;
use CodeIgniter\I18n\Time;

class PermintaanController extends BaseController
{
    protected $permintaanModel;
    protected $permintaanDetailModel;
    protected $bahanBakuModel;
    protected $db;

    public function __construct()
    {
        $this->permintaanModel = new PermintaanModel();
        $this->permintaanDetailModel = new PermintaanDetailModel();
        $this->bahanBakuModel = new BahanBakuModel();
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);
    }

    /**
     * Menampilkan daftar permintaan dengan status 'menunggu' untuk diproses oleh Gudang.
     */
    public function list()
    {
        // Mendapatkan semua permintaan dengan status 'menunggu'
        $permintaanMenunggu = $this->permintaanModel
                                   ->select('permintaan.*, user.name as pemohon_name')
                                   ->join('user', 'user.id = permintaan.pemohon_id')
                                   ->where('permintaan.status', 'menunggu')
                                   ->findAll();

        $data = [
            'title' => 'Daftar Permintaan Menunggu Persetujuan',
            'list_permintaan' => $permintaanMenunggu
        ];

        return view('gudang/permintaan/list', $data);
    }

    /**
     * Menampilkan detail satu permintaan beserta bahan baku yang diminta.
     */
    public function detail(int $id)
    {
        // Mendapatkan data permintaan utama
        $permintaan = $this->permintaanModel
                           ->select('permintaan.*, user.name as pemohon_name, user.email as pemohon_email')
                           ->join('user', 'user.id = permintaan.pemohon_id')
                           ->find($id);

        if (!$permintaan) {
            session()->setFlashdata('error', 'Permintaan tidak ditemukan.');
            return redirect()->to('/gudang/permintaan/list');
        }

        // Mendapatkan detail bahan baku yang diminta
        $detailBahan = $this->permintaanDetailModel
                            ->select('permintaan_detail.*, bahan_baku.nama, bahan_baku.satuan, bahan_baku.jumlah as stok_saat_ini')
                            ->join('bahan_baku', 'bahan_baku.id = permintaan_detail.bahan_id')
                            ->where('permintaan_id', $id)
                            ->findAll();

        $data = [
            'title' => 'Detail Permintaan #'.$id,
            'permintaan' => $permintaan,
            'detail_bahan' => $detailBahan
        ];

        return view('gudang/permintaan/detail', $data);
    }

    /**
     * Memproses persetujuan (ACC) atau penolakan (Tolak) permintaan.
     * [KRITIS] Logika ACC melibatkan Transaksi Database dan Pengurangan Stok.
     */
    public function proses(int $permintaanId)
    {
        $action = $this->request->getPost('action'); // 'acc' atau 'tolak'
        $alasanPenolakan = $this->request->getPost('alasan_penolakan');
        $statusBaru = ($action === 'acc') ? 'disetujui' : 'ditolak';

        // 1. Logika TOLAK (Sederhana)
        if ($statusBaru === 'ditolak') {
            $this->permintaanModel->update($permintaanId, [
                'status' => 'ditolak',
                'alasan_penolakan' => $alasanPenolakan ?? 'Tidak ada alasan.'
            ]);
            session()->setFlashdata('success', 'Permintaan **DITOLAK** berhasil dicatat.');
            return redirect()->to('/gudang/permintaan/list');
        }

        // 2. Logika ACC (Kompleks: Transaksi & Pengurangan Stok)
        
        $this->db->transBegin();

        try {
            // A. Dapatkan detail bahan yang diminta
            $detailPermintaan = $this->permintaanDetailModel
                                     ->where('permintaan_id', $permintaanId)
                                     ->findAll();

            if (empty($detailPermintaan)) {
                 throw new \Exception("Detail bahan untuk permintaan ini kosong.");
            }

            // B. Proses Pengurangan Stok untuk setiap item
            foreach ($detailPermintaan as $detail) {
                $bahanId = $detail['bahan_id'];
                $jumlahDiminta = $detail['jumlah_diminta'];

                // Panggil method reduceStok() di BahanBakuModel
                // reduceStok akan memvalidasi stok > 0 dan update status otomatis
                $stokBerhasilDikurangi = $this->bahanBakuModel->reduceStok($bahanId, $jumlahDiminta);
                
                if ($stokBerhasilDikurangi === false) {
                    // Jika reduceStok mengembalikan false (stok tidak cukup), batalkan transaksi
                    $bahanGagal = $this->bahanBakuModel->find($bahanId);
                    throw new \Exception("Stok bahan **".$bahanGagal['nama']."** tidak mencukupi untuk permintaan ini.");
                }
            }

            // C. Update Status Permintaan menjadi 'disetujui'
            $this->permintaanModel->update($permintaanId, ['status' => 'disetujui']);
            
            // D. Commit Transaksi
            $this->db->transCommit();
            session()->setFlashdata('success', 'Permintaan **DISETUJUI**! Stok bahan baku telah dikurangi secara otomatis.');
            
        } catch (\Exception $e) {
            // Rollback Transaksi jika ada kegagalan (stok tidak cukup atau DB error)
            $this->db->transRollback();
            session()->setFlashdata('error', 'Gagal memproses persetujuan: ' . $e->getMessage());
        }

        return redirect()->to('/gudang/permintaan/list');
    }
}
