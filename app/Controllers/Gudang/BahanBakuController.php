<?php namespace App\Controllers\Gudang;

use App\Controllers\BaseController;
use App\Models\BahanBakuModel;

class BahanBakuController extends BaseController
{
    protected $bahanBakuModel;

    public function __construct()
    {
        $this->bahanBakuModel = new BahanBakuModel();
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

    /**
     * Simpan data bahan baku baru
     * Termasuk validasi custom callback untuk tanggal kadaluarsa
     */
    public function store()
    {
        $rules = [
            'nama'               => 'required|max_length[120]',
            'kategori'           => 'required|max_length[60]',
            'jumlah'             => 'required|integer|greater_than_equal_to[0]',
            'satuan'             => 'required|max_length[20]',
            'tanggal_masuk'      => 'required|valid_date',
            // Perbaikan: Mengganti 'after_or_equal' dengan custom callback
            'tanggal_kadaluarsa' => 'required|valid_date|callback_validateTanggalKadaluarsa[tanggal_masuk]',
        ];
        
        // Menambahkan pesan custom untuk callback
        $messages = [
            'tanggal_kadaluarsa' => [
                'callback_validateTanggalKadaluarsa' => 'Tanggal Kadaluarsa harus sama dengan atau setelah Tanggal Masuk.',
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama'               => $this->request->getPost('nama'),
            'kategori'           => $this->request->getPost('kategori'),
            'jumlah'             => $this->request->getPost('jumlah'),
            'satuan'             => $this->request->getPost('satuan'),
            'tanggal_masuk'      => $this->request->getPost('tanggal_masuk'),
            'tanggal_kadaluarsa' => $this->request->getPost('tanggal_kadaluarsa'),
            'created_at'         => date('Y-m-d H:i:s'), // Set manual karena useTimestamps=false
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
            'title' => 'Update Stok Bahan: ' . $bahan['nama'],
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
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $newJumlah = $this->request->getPost('jumlah');

        // Data yang akan diupdate. Model akan memanggil setStatus() otomatis melalui beforeUpdate.
        $dataUpdate = [
            'id' => $id,
            'jumlah' => $newJumlah,
            // Pastikan tgl kadaluarsa tetap ada agar perhitungan status di Model berjalan
            'tanggal_kadaluarsa' => $bahan['tanggal_kadaluarsa'] 
        ];

        $this->bahanBakuModel->save($dataUpdate); 
        session()->setFlashdata('success', 'Stok bahan baku **' . $bahan['nama'] . '** berhasil diperbarui menjadi ' . $newJumlah . ' ' . $bahan['satuan'] . '.');
        
        return redirect()->to('/gudang/bahan-baku');
    }

    /**
     * @param int $id ID Bahan Baku
     * Proses hapus bahan baku. Hanya diizinkan jika status 'kadaluarsa'.
     */
    public function hapus($id = null)
    {
        $bahan = $this->bahanBakuModel->find($id);

        if (!$bahan) {
            session()->setFlashdata('error', 'Data bahan baku tidak ditemukan.');
            return redirect()->to('/gudang/bahan-baku');
        }

        // Hitung ulang status bahan baku secara dinamis (penting sebelum hapus)
        // Kita panggil setStatus secara manual untuk mendapatkan status terkini
        $data_temp = ['data' => $bahan];
        $updated_data = $this->bahanBakuModel->setStatus($data_temp);
        $current_status = $updated_data['data']['status'];

        // Aturan Bisnis: Sistem hanya mengizinkan penghapusan bahan baku yang berstatus 'kadaluarsa'
        if ($current_status !== 'kadaluarsa') {
            session()->setFlashdata('error', 'Bahan baku **' . $bahan['nama'] . '** tidak dapat dihapus karena statusnya adalah **' . strtoupper($current_status) . '**. Hanya bahan baku dengan status **KADALUARSA** yang dapat dihapus.');
            return redirect()->to('/gudang/bahan-baku');
        }

        // Proses hapus data
        try {
            $this->bahanBakuModel->delete($id);
            session()->setFlashdata('success', 'Bahan baku **' . $bahan['nama'] . '** (Status: Kadaluarsa) berhasil dihapus dari sistem.');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
        
        return redirect()->to('/gudang/bahan-baku');
    }

    /**
     * Custom Validation Callback: Memastikan tanggal kadaluarsa >= tanggal masuk
     * @param string $tglKadaluarsa Nilai tanggal_kadaluarsa (dari $str)
     * @param string $tglMasukKey Nama field tanggal masuk (dari $fields)
     * @param array $data Semua data POST
     * @return bool
     */
    public function validateTanggalKadaluarsa(string $tglKadaluarsa, string $tglMasukKey, array $data): bool
    {
        if (!isset($data[$tglMasukKey])) {
            return false; // Field tanggal masuk tidak ada
        }
        
        $tglMasuk = $data[$tglMasukKey];

        // Konversi ke timestamp untuk perbandingan
        $tsKadaluarsa = strtotime($tglKadaluarsa);
        $tsMasuk = strtotime($tglMasuk);

        // Perbandingan: Tanggal Kadaluarsa harus >= Tanggal Masuk
        return $tsKadaluarsa >= $tsMasuk;
    }
}
