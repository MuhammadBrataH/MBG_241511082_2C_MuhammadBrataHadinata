<?php

namespace App\Controllers;

use App\Models\UserModel;

class Login extends BaseController
{
    public function index()
    {
        return view('login'); // tampilkan form login
    }

    public function auth()
{
    $session = session();
    $userModel = new UserModel();

    $username = $this->request->getPost('username');
    $password = $this->request->getPost('password');

    $user = $userModel->where('username', $username)->first();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $session->set([
                'id'       => $user['id'],
                'username' => $user['username'],
                'role'     => $user['role'],
                'logged_in' => true
            ]);

            // 🔹 redirect berdasarkan role
            if ($user['role'] === 'admin') {
                return redirect()->to('/admin');
            } else {
                return redirect()->to('/student');
            }
        } else {
            return redirect()->back()->with('error', 'Password salah');
        }
    } else {
        return redirect()->back()->with('error', 'Username tidak ditemukan');
    }
}


    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
