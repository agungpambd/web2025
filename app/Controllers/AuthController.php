<?php

namespace App\Controllers;

use App\Models\AuthModel;

class AuthController extends BaseController
{
    protected $session;
    private $auth;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->auth = new AuthModel();
    }

    public function login()
    {
        if ($this->session->has('userSession')) {
            if ($this->session->get('role') == 0) {
                return redirect()->to('/admin');
            }
            if ($this->session->get('role') == 1) {
                return redirect()->to('/user');
            }
        } else {
            $data = [
                'title' => 'HRS | Login'
            ];
            return view('login', $data);
        }
    }

    public function loginCheck()
    {
        // ambil input dari form login
        $user = $this->request->getPost('username');
        $pass = $this->request->getPost('password');

        // ambil data email di database yang sama dengan inputan user
        $check = $this->auth->getUser($user);

        if ($check) { // cek apakah user ditemukan

            // verifikasi password menggunakan password_verify()
            if (!password_verify($pass, $check->password)) {
                session()->setFlashdata('login_fail', 'Password salah!');
                return redirect()->to('/');
            }

            // (opsional) jika hash perlu diupdate ke versi terbaru
            if (password_needs_rehash($check->password, PASSWORD_DEFAULT)) {
                $newHash = password_hash($pass, PASSWORD_DEFAULT);
                $this->auth->updatePassword($check->employee_id, $newHash);
            }

            // jika password benar dan role = 0 → admin
            if ($check->role == 0) {
                $this->session->set([
                    'userSession' => true,
                    'userData'    => $check
                ]);
                $this->session->regenerate();
                return redirect()->to('/admin');
            }

            // jika password benar dan role = 1 → user
            if ($check->role == 1) {
                $this->session->set([
                    'userSession' => true,
                    'userData'    => $check
                ]);
                $this->session->regenerate();
                return redirect()->to('/user');
            }
        } else {
            // jika username tidak ditemukan
            session()->setFlashdata('login_fail', 'Username tidak ditemukan!');
            return redirect()->to('/');
        }
    }

    public function logout()
    {
        //hapus session dan kembali ke halaman login
        $this->session->destroy();
        return redirect()->to('/');
    }

    public function generatePasswordHash()
    {
        return password_hash('user123', PASSWORD_DEFAULT);
    }
}
