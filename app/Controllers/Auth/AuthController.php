<?php namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    // Menampilkan form login
    public function index()
{
    // Jika sudah login, redirect ke dashboard
    if (session()->get('isLoggedIn')) {
        $target = session()->get('role') == 'gudang' ? 'gudang/dashboard' : 'dapur/dashboard';
        return redirect()->to(base_url($target));
    }
    return view('auth/login');
}

    // Proses login
    public function login()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[3]', // Min length 3 sesuai data instance
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->getUserByEmail($email);

        
        $isPasswordCorrect = false;
        if ($user) {
            // Menggunakan data instance MD5:
            if (md5($password) === $user['password']) {
                $isPasswordCorrect = true;
            }
        }

        if (!$user || !$isPasswordCorrect) {
            session()->setFlashdata('error', 'Email atau Password salah.');
            return redirect()->back()->withInput();
        }

        // Set Session
        $ses_data = [
            'id'        => $user['id'],
            'name'      => $user['name'],
            'email'     => $user['email'],
            'role'      => $user['role'],
            'isLoggedIn'=> TRUE
        ];
        session()->set($ses_data);

        // Redirect sesuai role
        if ($user['role'] === 'gudang') {
            return redirect()->to('/gudang/dashboard');
        } elseif ($user['role'] === 'dapur') {
            return redirect()->to('/dapur/dashboard');
        }
    }

    // Proses logout
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}