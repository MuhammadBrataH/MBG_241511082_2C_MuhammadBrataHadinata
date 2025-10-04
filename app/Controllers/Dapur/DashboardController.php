<?php namespace App\Controllers\Dapur;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $data = ['title' => 'Dashboard Dapur'];
        return view('dapur/dashboard', $data); // Tampilan utama Dapur
    }
}