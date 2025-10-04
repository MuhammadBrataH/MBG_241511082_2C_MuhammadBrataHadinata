<?php namespace App\Controllers\Gudang;

use App\Controllers\BaseController;
use App\Models\BahanBakuModel;

class BahanBakuController extends BaseController
{
    protected $bahanBakuModel;
    protected $db; // Tambahkan properti untuk database connection

    public function __construct()
    {
        $this->bahanBakuModel = new BahanBakuModel();
        $this->db = \Config\Database::connect(); // Inisialisasi DB connection
        helper(['form', 'url']);
    }

    // Tampilkan daftar bahan baku (Read)
    public function index()
    {
        // Menggunakan fungsi di Model untuk mendapatkan data dengan status dinamis
        $dataBahan = $this->bahanBakuModel->getAllBahanBaku();

        $data = [
            'title'     => 'Data Bahan Baku',
            'bahan_baku' => $dataBahan,
        ];
        
        return view('gudang/bahan_baku/index', $data);
    }

    // Tampilkan form tambah bahan baku
    public function create()
    {
        $data = ['title' => 'Tambah Bahan Baku'];
        return view('gudang/bahan_baku/tambah', $data);
    }

    // Simpan data bahan baku baru
    public function store()
    {
        $rules = [
            'nama'               => 'required|max_length[120]',
            'kategori'           => 'required|max_length[60]',
            'jumlah'             => 'required|integer|greater_than_equal_to[0]',
            'satuan'             => 'required|max_length[20]',
            'tanggal_masuk'      => 'required|valid_date',
            'tanggal_kadaluarsa' => 'required|valid_date|after_or_equal[tanggal_masuk]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama'               => $this->request->getPost('nama'),
            'kategori'           => $this->request->getPost('kategori'),
            'jumlah'             => $this->request->getPost('jumlah'),
            'satuan'             => $this->request->getPost('satuan'),
            'tanggal_masuk'      => $this->request->getPost('tanggal_masuk'),
            'tanggal_kadaluarsa' => $this->request->getPost('tanggal_kadaluarsa'),
            // 'status' akan diisi otomatis oleh beforeInsert di Model (setStatus)
        ];

        $this->bahanBakuModel->insert($data);
        session()->setFlashdata('success', 'Bahan baku **' . $data['nama'] . '** berhasil ditambahkan.');

        return redirect()->to('/gudang/bahan-baku');
    }
    
    // Tampilkan form edit stok bahan baku
    public function editStok($id = null)
    {
        $bahan = $this->bahanBakuModel->find($id);

        if (!$bahan) {
            session()->setFlashdata('error', 'Data bahan baku tidak ditemukan.');
            return redirect()->to('/gudang/bahan-baku');
        }

        $data = [
            'title' => 'Update Stok Bahan Baku',
            'bahan' => $bahan
        ];
        return view('gudang/bahan_baku/edit_stok', $data);
    }

    // Proses update stok bahan baku
    public function updateStok()
    {
        $id = $this->request->getPost('id');
        $bahan = $this->bahanBakuModel->find($id);

        if (!$bahan) {
            session()->setFlashdata('error', 'Data bahan baku tidak ditemukan.');
            return redirect()->to('/gudang/bahan-baku');
        }

        $rules = [
            'jumlah' => 'required|integer|greater_than_equal_to[0]', 
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            return redirect()->back()->withInput();
        }

        $newJumlah = $this->request->getPost('jumlah');

        // Data yang akan diupdate, status akan dihitung ulang di Model (setStatus)
        $dataUpdate = [
            'id' => $id,
            'jumlah' => $newJumlah,
            'tanggal_kadaluarsa' => $bahan['tanggal_kadaluarsa'] // Penting untuk perhitungan status
        ];

        $this->bahanBakuModel->save($dataUpdate); // Menggunakan save() untuk update

        // Ambil status terbaru setelah save
        $updatedBahan = $this->bahanBakuModel->find($id);
        
        session()->setFlashdata('success', 'Stok **' . $bahan['nama'] . '** berhasil diperbarui menjadi ' . $newJumlah . ' ' . $bahan['satuan'] . '. Status terbaru: ' . strtoupper($updatedBahan['status']) . '.');
        
        return redirect()->to('/gudang/bahan-baku');
    }

    // Proses hapus bahan baku
    public function delete($id = null)
    {
        // 1. Ambil data bahan baku
        $bahan = $this->bahanBakuModel->find($id);

        if (!$bahan) {
            session()->setFlashdata('error', 'Data bahan baku tidak ditemukan.');
            return redirect()->back();
        }
        
        // 2. Hitung ulang status bahan baku secara dinamis
        // Panggil setStatus pada data yang ditemukan untuk mendapatkan status terkini
        $data_temp = ['data' => $bahan];
        $updated_data = $this->bahanBakuModel->setStatus($data_temp);
        $current_status = $updated_data['data']['status'];

        // 3. Aturan Bisnis: Sistem hanya mengizinkan penghapusan bahan baku yang berstatus 'kadaluarsa'
        if ($current_status !== 'kadaluarsa') {
            session()->setFlashdata('error', 'Penolakan! Bahan baku **' . $bahan['nama'] . '** tidak dapat dihapus karena statusnya adalah **' . strtoupper($current_status) . '**. Hanya bahan baku dengan status **Kadaluarsa** yang diizinkan untuk dihapus.');
            return redirect()->to('/gudang/bahan-baku');
        }

        // 4. Proses Hapus
        try {
            $this->bahanBakuModel->delete($id);
            session()->setFlashdata('success', 'Bahan baku **' . $bahan['nama'] . '** (Status: Kadaluarsa) berhasil dihapus dari sistem.');
        } catch (\Exception $e) {
            // Handle error jika ada relasi FK, meskipun tidak seharusnya terjadi pada tabel bahan_baku
            session()->setFlashdata('error', 'Gagal menghapus bahan baku. Mungkin terkait dengan data permintaan yang masih menggunakan bahan ini.');
        }

        return redirect()->to('/gudang/bahan-baku');
    }
}
