<?php

namespace App\Controllers;

class UserController extends BaseController
{
    protected $requireAuth  = true; // Controller ini membutuhkan login
    protected $allowedRoles = [1]; // Hanya user (role 1)

    public function index()
    {
        return printf("Ini halaman user <br> <a href='/logout'>Logout</a>");
    }
}
