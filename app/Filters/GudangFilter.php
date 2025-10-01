<?php namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class GudangFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Jika role bukan 'gudang', tolak akses
        if (session()->get('role') !== 'gudang') {
            session()->setFlashdata('error', 'Akses ditolak. Anda bukan Petugas Gudang.');
            return redirect()->to(base_url('dapur/dashboard')); 
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}   