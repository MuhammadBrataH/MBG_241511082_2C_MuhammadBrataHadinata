<?php namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function login()
    {
        helper(['form']);
        if (session()->get('logged_in')) {
            if (session()->get('role') === 'gudang') {
                return redirect()->to(base_url('gudang/dashboard'));
            } else {
                return redirect()->to(base_url('dapur/dashboard'));
            }
        }
        return view('auth/login');
    }

    public function processLogin()
    {
        $model = new UserModel();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $model->where('email', $email)->first();

        if ($user) {
            
            // PERBAIKAN KRITIS: Ganti password_verify dengan pembandingan MD5
            // Hash password inputan dari user menggunakan MD5
            $input_hash = md5($password);
            
            if ($input_hash === $user['password']) { // Perbandingan langsung hash MD5 dari input dan DB
                $ses_data = [
                    'user_id'   => $user['id'],
                    'name'      => $user['name'],
                    'email'     => $user['email'],
                    'role'      => $user['role'],
                    'logged_in' => TRUE
                ];
                session()->set($ses_data);
                
                session()->setFlashdata('success', 'Selamat datang, ' . $user['name'] . '!');

                // Arahkan berdasarkan role
                if ($user['role'] === 'gudang') {
                    return redirect()->to(base_url('gudang/dashboard'));
                } else { // 'dapur'
                    return redirect()->to(base_url('dapur/dashboard'));
                }
            } else {
                session()->setFlashdata('error', 'Email atau Password Salah.');
                return redirect()->to(base_url('login'))->withInput();
            }
        } else {
            session()->setFlashdata('error', 'Email atau Password Salah.');
            return redirect()->to(base_url('login'))->withInput();
        }
    }
    
    public function logout()
    {
        session()->destroy();
        session()->setFlashdata('success', 'Anda berhasil logout.');
        return redirect()->to(base_url('login'));
    }
}
