<?php

namespace App\Controllers;

use App\Models\HrModel;

class HrController extends BaseController
{
    private $hr;

    public function __construct()
    {
        $session = session();

        // Jika belum login atau bukan admin (role ≠ 0)
        if (!$session->get('userSession') || (int)$session->get('role') !== 0) {
            header('Location: ' . base_url('/?login_terlebih_dahulu'));
            exit; // penting: hentikan eksekusi agar method tidak lanjut
        }

        $this->hr = new HrModel();
    }

    public function index()
    {

        $data = [
            'title'       => 'HRS | Dashboard',
            'pageId'      => 'dashboard',
            'pageSub'     => 'dashboard-index',
            'totalEmp'    => $this->hr->totalEmployees(),
            'totalSalary' => $this->hr->totalSalary(),
            'totalDept'   => $this->hr->totalDepartments(),
            'totalJobs'   => $this->hr->totalJobs(),
            'deptEmp'     => $this->hr->getEmployeesByDepartment(),
        ];

        return view('/admin/dashboard', $data);
    }
}
