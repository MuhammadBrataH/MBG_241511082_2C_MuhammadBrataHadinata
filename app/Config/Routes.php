<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth\AuthController::index');
$routes->get('login', 'Auth\AuthController::index');
$routes->post('login', 'Auth\AuthController::login');
$routes->get('logout', 'Auth\AuthController::logout');

// Rute untuk Petugas Gudang (Admin) - Role: gudang
$routes->group('gudang', ['filter' => 'auth:gudang'], function($routes){
    $routes->get('dashboard', 'Gudang\DashboardController::index'); 
    
    // Bahan Baku (CRUD)
    $routes->get('bahan-baku', 'Gudang\BahanBakuController::index');           // Lihat Data
    $routes->get('bahan-baku/add', 'Gudang\BahanBakuController::create');      // Tampilan Tambah
    $routes->post('bahan-baku/save', 'Gudang\BahanBakuController::store');     // Proses Tambah
    $routes->get('bahan-baku/edit-stok/(:num)', 'Gudang\BahanBakuController::editStok/$1'); // Tampilan Edit Stok
    $routes->post('bahan-baku/update-stok', 'Gudang\BahanBakuController::updateStok'); // Proses Update Stok
    
    // PERBAIKAN HAPUS: Mengubah method dari DELETE/POST menjadi GET
    $routes->get('bahan-baku/hapus/(:num)', 'Gudang\BahanBakuController::hapus/$1'); // Hapus
    
    // Permintaan (Proses Persetujuan)
    $routes->get('permintaan', 'Gudang\PermintaanController::index');        // Alias untuk list
    $routes->get('permintaan/list', 'Gudang\PermintaanController::list');    // Lihat Daftar Permintaan
    $routes->get('permintaan/detail/(:num)', 'Gudang\PermintaanController::detail/$1'); // Lihat Detail Permintaan
    $routes->post('permintaan/proses/(:num)', 'Gudang\PermintaanController::proses/$1'); // Proses ACC/Tolak
});

// Rute untuk Petugas Dapur (Client) - Role: dapur
$routes->group('dapur', ['filter' => 'auth:dapur'], function($routes){
    $routes->get('dashboard', 'Dapur\DashboardController::index'); 
    
    // Permintaan Bahan
    $routes->get('permintaan', 'Dapur\PermintaanController::status');          // Alias untuk status
    $routes->get('permintaan/status', 'Dapur\PermintaanController::status');  // Lihat Status Permintaan
    $routes->get('permintaan/baru', 'Dapur\PermintaanController::baru');       // Tampilan Form Buat Permintaan
    $routes->get('permintaan/create', 'Dapur\PermintaanController::baru');     // Alias untuk baru
    $routes->post('permintaan/simpan', 'Dapur\PermintaanController::simpan'); // Proses Simpan Permintaan
});
