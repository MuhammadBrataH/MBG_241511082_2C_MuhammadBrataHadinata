<?php namespace App\Controllers\Dapur;

use App\Controllers\BaseController;
use App\Models\PermintaanModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $permintaanModel = new PermintaanModel();
        
        // Data untuk ditampilkan di dashboard Dapur
        $data['title'] = 'Dashboard Petugas Dapur';
        $user_id = session()->get('user_id');
        
        $data['total_permintaan_saya'] = $permintaanModel->where('pemohon_id', $user_id)->countAllResults();
        $data['status_menunggu'] = $permintaanModel->where('pemohon_id', $user_id)->where('status', 'menunggu')->countAllResults();
        $data['status_disetujui'] = $permintaanModel->where('pemohon_id', $user_id)->where('status', 'disetujui')->countAllResults();
        
        return view('dapur/dashboard/index', $data);
    }
}
