<?php namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class DapurFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Jika role bukan 'dapur', tolak akses
        if (session()->get('role') !== 'dapur') {
            session()->setFlashdata('error', 'Akses ditolak. Anda bukan Petugas Dapur.');
            return redirect()->to(base_url('gudang/dashboard')); 
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}   