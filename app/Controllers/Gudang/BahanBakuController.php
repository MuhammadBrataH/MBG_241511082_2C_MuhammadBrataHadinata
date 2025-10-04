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
        $rules = [
            'nama'               => 'required|max_length[120]|is_unique[bahan_baku.nama]',
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
        ];

        $this->bahanBakuModel->insert($data);
        
        session()->setFlashdata('success', 'Bahan baku **' . $data['nama'] . '** berhasil ditambahkan.');

        return redirect()->to('/gudang/bahan-baku');
    }
    
    // [EDIT STOK - GET] Tampilkan form edit stok
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

    // [UPDATE STOK - POST] Proses update stok
    public function updateStok()
    {
        $id = $this->request->getPost('id');
        $bahan = $this->bahanBakuModel->find($id);

        if (!$bahan) {
            session()->setFlashdata('error', 'Data bahan baku tidak ditemukan.');
            return redirect()->to('/gudang/bahan-baku');
        }
        
        // 1. Validasi Stok (Harus >= 0) Sesuai Dokumen Soal
        $rules = [
            'jumlah' => 'required|integer|greater_than_equal_to[0]', 
        ];

        if (!$this->validate($rules)) {
            // Jika validasi gagal, kembali ke form edit dengan error
            session()->setFlashdata('errors', $this->validator->getErrors());
            // Redirect ke rute GET editStok agar data bahan baku terbawa lagi
            return redirect()->to('gudang/bahan-baku/edit-stok/' . $id)->withInput();
        }

        $newJumlah = $this->request->getPost('jumlah');

        // 2. Data yang akan diupdate
        $dataUpdate = [
            'id' => $id,
            'jumlah' => $newJumlah,
            // Model akan memanggil setStatus() untuk menentukan status baru
        ];

        // 3. Simpan Update (akan memanggil setStatus() di Model)
        $this->bahanBakuModel->save($dataUpdate); 
        
        session()->setFlashdata('success', 'Stok bahan baku **' . $bahan['nama'] . '** berhasil diperbarui menjadi ' . $newJumlah . ' ' . $bahan['satuan'] . '. Status otomatis diperbarui.');
        
        return redirect()->to('/gudang/bahan-baku');
    }
}
