<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// 1. Rute Dasar (Root URL): Arahkan ke login
$routes->get('/', function() {
    return redirect()->to(base_url('login'));
});

// 2. Rute Autentikasi (TIDAK ADA FILTER!)
$routes->get('login', 'Auth\AuthController::index');
$routes->post('login', 'Auth\AuthController::login');
$routes->get('logout', 'Auth\AuthController::logout');

// 3. Rute yang Dilindungi (Filter Applied)
$routes->group('gudang', ['filter' => 'auth:gudang'], function($routes){
    $routes->get('dashboard', 'Gudang\DashboardController::index');
    // Bahan Baku (CRUD)
    $routes->get('bahan-baku', 'Gudang\BahanBakuController::index');           
    $routes->get('bahan-baku/add', 'Gudang\BahanBakuController::create');      // [GET Form Tambah]
    $routes->post('bahan-baku/save', 'Gudang\BahanBakuController::store');     // [POST Proses Simpan]
    
    // Update Stok
    $routes->get('bahan-baku/edit-stok/(:num)', 'Gudang\BahanBakuController::editStok/$1'); // Tampilkan form edit stok
    $routes->post('bahan-baku/update-stok', 'Gudang\BahanBakuController::updateStok'); // Proses update stok

});

$routes->group('dapur', ['filter' => 'auth:dapur'], function($routes){
    $routes->get('dashboard', 'Dapur\DashboardController::index'); 
});
