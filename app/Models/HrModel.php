<?php

namespace App\Models;

use CodeIgniter\Model;


class HrModel extends Model
{
    public function totalEmployees()
    {
        return $this->db->table('employees')
            ->countAllResults();
    }

    public function totalSalary()
    {
        $result = $this->db->table('employees')
            ->selectSum('salary')
            ->get()
            ->getRow();

        return $result ? $result->salary : 0;
    }

    public function totalDepartments()
    {
        return $this->db->table('departments')
            ->countAllResults();
    }

    public function totalJobs()
    {
        return $this->db->table('jobs')
            ->countAllResults();
    }

    public function getEmployeesByDepartment()
    {
        return $this->db->table('departments d')
            ->select('d.department_name, COUNT(e.employee_id) AS num_employees')
            ->join('employees e', 'e.department_id = d.department_id', 'left')
            ->groupBy('d.department_name')
            ->orderBy('num_employees', 'DESC')
            ->get()
            ->getResult();
    }
}
