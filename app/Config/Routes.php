<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Rute Default
$routes->get('/', 'Auth::login');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::processLogin');
$routes->get('/logout', 'Auth::logout');

// Rute untuk Gudang (Admin) - Diterapkan Filter Auth dan Gudang
$routes->group('gudang', ['filter' => ['auth', 'gudang'], 'namespace' => 'App\Controllers\Gudang'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    
    // Bahan Baku (CRUD dengan Rules)
    $routes->get('bahan', 'BahanBaku::index');
    $routes->get('bahan/create', 'BahanBaku::create');
    $routes->post('bahan/store', 'BahanBaku::store');
    $routes->post('bahan/update/(:num)', 'BahanBaku::updateStock/$1'); // Update Stok
    $routes->get('bahan/delete/(:num)', 'BahanBaku::delete/$1'); // Delete bahan kadaluarsa
    
    // Permintaan (Proses Persetujuan)
    $routes->get('permintaan', 'Permintaan::index'); // Daftar permintaan 'menunggu'
    $routes->get('permintaan/detail/(:num)', 'Permintaan::detail/$1');
    $routes->post('permintaan/process/(:num)', 'Permintaan::process/$1'); // Approve/Reject
});

// Rute untuk Dapur (Client) - Diterapkan Filter Auth dan Dapur
$routes->group('dapur', ['filter' => ['auth', 'dapur'], 'namespace' => 'App\Controllers\Dapur'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    
    // Permintaan Bahan (Create & Read Status)
    $routes->get('permintaan/new', 'Permintaan::newPermintaan'); // Form permintaan
    $routes->post('permintaan/store', 'Permintaan::store');
    $routes->get('permintaan/status', 'Permintaan::status'); // Lihat status permintaan
    $routes->get('permintaan/detail/(:num)', 'Permintaan::detail/$1');
});