<?php namespace App\Controllers\Gudang;

use App\Controllers\BaseController;
use App\Models\BahanBakuModel;

class BahanBakuController extends BaseController
{
    protected $bahanBakuModel;

    public function __construct()
    {
        $this->bahanBakuModel = new BahanBakuModel();
        helper(['form']);
    }

    // Tampilkan daftar bahan baku (Read)
    public function index()
    {
        $dataBahan = $this->bahanBakuModel->getAllBahanBaku();

        $data = [
            'title'     => 'Data Bahan Baku',
            'bahan_baku' => $dataBahan,
        ];
        return view('gudang/bahan_baku/index', $data); 
    }


    // Tampilkan form tambah bahan baku (GET)
    public function create()
    {
        $data = ['title' => 'Tambah Bahan Baku Baru'];
        return view('gudang/bahan_baku/tambah', $data);
    }

    // Simpan data bahan baku baru (POST)
    public function store()
    {
        // 1. Validasi Input CI4
        $rules = [
            'nama'               => 'required|max_length[120]|is_unique[bahan_baku.nama]',
            'kategori'           => 'required|max_length[60]',
            'jumlah'             => 'required|integer|greater_than_equal_to[0]', // Validasi Stok >= 0
            'satuan'             => 'required|max_length[20]',
            'tanggal_masuk'      => 'required|valid_date',
            'tanggal_kadaluarsa' => 'required|valid_date|after_or_equal[tanggal_masuk]', // Tgl Kadaluarsa harus >= Tgl Masuk
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Ambil Data
        $data = [
            'nama'               => $this->request->getPost('nama'),
            'kategori'           => $this->request->getPost('kategori'),
            'jumlah'             => $this->request->getPost('jumlah'),
            'satuan'             => $this->request->getPost('satuan'),
            'tanggal_masuk'      => $this->request->getPost('tanggal_masuk'),
            'tanggal_kadaluarsa' => $this->request->getPost('tanggal_kadaluarsa'),
            // 'status' dihitung otomatis oleh Model (setStatus)
        ];

        // 3. Simpan ke Database
        $this->bahanBakuModel->insert($data);
        
        // 4. Redirect & Feedback
        session()->setFlashdata('success', 'Bahan baku **' . $data['nama'] . '** berhasil ditambahkan dengan status **Tersedia**.');

        // Redirect ke halaman index (Lihat Data Bahan Baku)
        return redirect()->to('/gudang/bahan-baku');
    }
    
}
