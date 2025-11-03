<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthModel extends Model
{

    public function getUser($user)
    {
        return $this->db->table('employees')
            ->where('email', $user)
            ->get()
            ->getRow();
    }

    public function updatePassword($employee_id, $newHash)
    {
        return $this->update($employee_id, ['password' => $newHash]);
    }
}
