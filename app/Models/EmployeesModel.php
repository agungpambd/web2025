<?php

namespace App\Models;

use CodeIgniter\Model;


class EmployeesModel extends Model
{
    public function listEmployees()
    {
        return $this->db->table('employees e')
            ->select('e.*, d.*, j.*, m.employee_id AS manager_emp_id, CONCAT(m.first_name, " ", m.last_name) AS manager_name')
            ->join('departments d', 'e.department_id = d.department_id', 'left')
            ->join('jobs j', 'e.job_id = j.job_id', 'left')
            ->join('employees m', 'e.manager_id = m.employee_id', 'left')
            ->get()
            ->getResult();
    }

    public function getEmployeesServerSide($search = null, $start = 0, $length = 10, $orderColumn = 'e.employee_id', $orderDir = 'asc')
    {
        $builder = $this->db->table('employees e')
            ->select('e.*, d.department_name, j.job_title')
            ->join('departments d', 'e.department_id = d.department_id', 'left')
            ->join('jobs j', 'e.job_id = j.job_id', 'left');

        // Filter untuk pencarian
        if (!empty($search)) {
            $builder->groupStart()
                ->like('e.first_name', $search)
                ->orLike('e.last_name', $search)
                ->orLike('e.employee_id', $search)
                ->orLike('e.hire_date', $search)
                ->orLike('d.department_name', $search)
                ->orLike('j.job_title', $search)
                ->groupEnd();
        }

        // Mapping kolom berdasarkan urutan di DataTables
        $columns = [
            0 => 'no',
            1 => 'e.employee_id',
            2 => 'e.first_name',
            3 => 'd.department_name',
            4 => 'e.hire_date',
            5 => 'j.job_title',
            6 => 'e.salary'
        ];

        // Sorting jika kolom valid
        if ($orderColumn !== null && isset($columns[$orderColumn])) {
            $builder->orderBy($columns[$orderColumn], $orderDir);
        } else {
            $builder->orderBy('e.employee_id', 'asc');
        }

        // Untuk pagination
        $builder->limit($length, $start);

        // Eksekusi query dan kembalikan hasil
        return $builder->get()->getResult();
    }

    public function countAllEmployees()
    {
        return $this->db->table('employees')->countAllResults();
    }

    public function countFilteredEmployees($search = null)
    {
        // Membangun query dengan filter pencarian
        $builder = $this->db->table('employees e')
            ->join('departments d', 'e.department_id = d.department_id', 'left')
            ->join('jobs j', 'e.job_id = j.job_id', 'left');

        if ($search) {
            $builder->groupStart()
                ->like('e.first_name', $search)
                ->orLike('e.last_name', $search)
                ->orLike('e.employee_id', $search)
                ->orLike('e.hire_date', $search)
                ->orLike('d.department_name', $search)
                ->orLike('j.job_title', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }


    public function listDepartment()
    {
        return $this->db->table('departments')
            ->get()
            ->getResult();
    }

    public function listJobs()
    {
        return $this->db->table('jobs')
            ->get()
            ->getResult();
    }

    public function lastEmpId()
    {
        return $this->db->table('employees')
            ->selectMax('employee_id')
            ->get()
            ->getRow();
    }

    public function addEmployee($data)
    {
        return $this->db->table('employees')
            ->insert($data);
    }

    public function editEmployee($id, $data)
    {
        return $this->db->table('employees')
            ->where('employee_id', $id)
            ->update($data);
    }

    public function deleteEmployee($employeeId)
    {
        return $this->db->table('employees')
            ->where('employee_id', $employeeId)
            ->delete();
    }
}
