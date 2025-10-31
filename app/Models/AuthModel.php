<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthModel extends Model
{

    public function getUser($email)
    {
        return $this->db->table('employees')
            ->where('email', $email)
            ->get()
            ->getRow();
    }
}
