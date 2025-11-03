<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    protected $session;

    // Properti untuk kontrol autentikasi di child controller
    protected $requireAuth  = false;  // Apakah perlu login utk akses controller ini?
    protected $allowedRoles = [];     // Role mana saja yang boleh akses? (kosong = semua role boleh)

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->session = service('session');

        // Jalankan pengecekan autentikasi
        $this->checkAuth();
    }

    // Cek autentikasi dan role secara otomatis
    protected function checkAuth()
    {
        // Jika controller tidak butuh auth → skip
        if (!$this->requireAuth) {
            return;
        }

        // Cek apakah sudah login
        if (!$this->session->get('userSession')) {
            redirect()->to('/?error=login_terlebih_dahulu')->send();
            exit;
        }

        // Jika ada pembatasan role
        if (!empty($this->allowedRoles)) {
            $user = $this->session->get('userData');

            // Jika role user tidak ada di daftar allowedRoles
            if (!in_array($user->role, $this->allowedRoles)) {
                // Redirect ke halaman sesuai role mereka
                $redirects = [
                    0 => '/admin',
                    1 => '/user'
                ];

                if (isset($redirects[$user->role])) {
                    redirect()->to($redirects[$user->role])->send();
                    exit;
                }
            }
        }
    }

    // Helper untuk mendapatkan data user yang sedang login
    protected function getCurrentUser()
    {
        return $this->session->get('userData');
    }

    // Helper untuk cek apakah user sudah login
    protected function isLoggedIn()
    {
        return (bool) $this->session->get('userSession');
    }
}
