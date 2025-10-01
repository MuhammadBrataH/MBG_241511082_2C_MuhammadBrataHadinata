<?php

namespace App\Controllers;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function index()
    {
        return view('login');
    }

    public function login()
{
    $userModel = new UserModel();
    $username  = $this->request->getPost('username');
    $password  = $this->request->getPost('password');

    $user = $userModel->getUserByUsername($username);

    if ($user && password_verify($password, $user['password'])) {
        // set session
        session()->set([
            'user_id'   => $user['id'],
            'nama'      => $user['nama'],
            'username'  => $user['username'],
            'role'      => $user['role'],
            'logged_in' => true,
        ]);

        // redirect berdasarkan role
        if ($user['role'] == 'admin') {
            return redirect()->to('/admin');
        } else {
            return redirect()->to('/student');
        }
    } else {
        session()->setFlashdata('error', 'Username atau Password salah!');
        return redirect()->to('/auth');
    }
}


    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth');
    }
}
