<?php namespace App\Controllers\Gudang;

use App\Controllers\BaseController;
use App\Models\BahanBakuModel;
use App\Models\PermintaanModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $bahanModel = new BahanBakuModel();
        $permintaanModel = new PermintaanModel();
        
        // Data untuk ditampilkan di dashboard Gudang
        $data['title'] = 'Dashboard Petugas Gudang';
        $data['total_bahan'] = $bahanModel->countAllResults();
        $data['permintaan_menunggu'] = $permintaanModel->where('status', 'menunggu')->countAllResults();
        
        // Ambil data bahan kadaluarsa/segera
        $semuaBahan = $bahanModel->getBahanBakuWithStatus();
        $data['kadaluarsa'] = array_filter($semuaBahan, function($item) {
            return $item['status'] == 'kadaluarsa';
        });
        $data['segera_kadaluarsa'] = array_filter($semuaBahan, function($item) {
            return $item['status'] == 'segera_kadaluarsa';
        });

        return view('gudang/dashboard/index', $data);
    }
}
    