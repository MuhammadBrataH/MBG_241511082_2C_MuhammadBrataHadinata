<?php namespace App\Controllers\Gudang;
use App\Controllers\BaseController;
use App\Models\BahanBakuModel;

class BahanBaku extends BaseController
{
    // Pastikan BaseController di-use atau di-extend dengan benar
    protected $bahanModel;

    public function __construct() { 
        // Menginisialisasi Model
        $this->bahanModel = new BahanBakuModel(); 
    }

    public function index() { 
        $data['bahan'] = $this->bahanModel->getBahanBakuWithStatus(); 
        return view('gudang/bahan_baku/index', $data); 
    }
    public function create() { 
        return view('gudang/bahan_baku/create'); 
    }

    // Tambah Bahan Baku (Create dengan validasi/rule)
    public function store()
    {
        // Cek validasi dari Model
        if (!$this->validate($this->bahanModel->validationRules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        $data = $this->request->getPost();
        
        // Tentukan status awal secara otomatis
        $data['status'] = $this->bahanModel->determineStatus($data['jumlah'], $data['tanggal_kadaluarsa']); 
        $data['created_at'] = date('Y-m-d H:i:s');
        
        $this->bahanModel->save($data);

        session()->setFlashdata('success', 'Data bahan baku berhasil ditambahkan.');
        return redirect()->to(base_url('gudang/bahan'));
    }
    
    // Update Stok Bahan Baku
    public function updateStock($id)
    {
        $newStock = (int)$this->request->getPost('jumlah');
        $bahan = $this->bahanModel->find($id);

        // Tolak jika nilai stok < 0
        if ($newStock < 0) { 
            session()->setFlashdata('error', 'Stok tidak boleh kurang dari 0.');
            return redirect()->back();
        }
        
        $data = [
            'jumlah' => $newStock,
            // Update status otomatis
            'status' => $this->bahanModel->determineStatus($newStock, $bahan['tanggal_kadaluarsa']) 
        ];

        $this->bahanModel->update($id, $data);
        session()->setFlashdata('success', 'Stok bahan baku berhasil diperbarui.');
        return redirect()->to(base_url('gudang/bahan'));
    }
    
    // Hapus Bahan Baku
    public function delete($id)
    {
        $bahan = $this->bahanModel->find($id);
        
        // Hanya izinkan penghapusan bahan baku yang berstatus kadaluarsa
        if ($bahan['status'] !== 'kadaluarsa') {
            session()->setFlashdata('error', 'Penghapusan gagal. Bahan baku harus berstatus "kadaluarsa".');
            return redirect()->to(base_url('gudang/bahan'));
        }
        
        $this->bahanModel->delete($id);
        session()->setFlashdata('success', 'Data bahan baku kadaluarsa berhasil dihapus.');
        return redirect()->to(base_url('gudang/bahan'));
    }
}