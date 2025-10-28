<?php

namespace App\Controllers;

use App\Models\HrModel;

class HrController extends BaseController
{
    private $hr;

    public function __construct()
    {
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

        return view('dashboard', $data);
    }
}
