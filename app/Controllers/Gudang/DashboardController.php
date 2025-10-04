<?php namespace App\Controllers\Gudang;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $data = ['title' => 'Dashboard Gudang'];
        return view('gudang/dashboard', $data); // Tampilan utama Gudang
    }
}