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

        //ambil data email di database yang sama dengan inputan user
        $check = $this->auth->getUser($user);

        if ($check) { //cek apakah email ditemukan
            if ($check->password != $pass) { //cek password, jika salah arahkan kembali ke halaman login
                session()->setFlashdata('login_fail', 'Pasword salah!');
                return redirect()->to('/');
            }

            if (($check->password == $pass) && ($check->role == 0)) { // jika benar dan role = 0, arahkan user masuk ke halaman admin 

                $this->session->set([
                    'userSession' => true,
                    'userData'    => $check // simpan semua kolom admin sebagai satu objek
                ]);

                return redirect()->to('/admin');
            }

            if (($check->password == $pass) && ($check->role == 1)) { // jika benar dan role = 1, arahkan user masuk ke halaman user 

                $this->session->set([
                    'userSession' => true,
                    'userData'    => $check // simpan semua kolom user sebagai satu objek
                ]);

                return redirect()->to('/user');
            }
        } else {
            //jika username tidak ditemukan, balikkan ke halaman login
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
}
