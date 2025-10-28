<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Halaman utama
$routes->get('/', 'HrController::index');

// Halaman daftar karyawan (menampilkan view)
$routes->get('/karyawan', 'EmployeesController::employees');

// Endpoint server-side DataTables (mengembalikan JSON)
$routes->post('/karyawan/list', 'EmployeesController::getEmployeesAjax');

// CRUD routes
$routes->post('/karyawan/add', 'EmployeesController::empAdd');
$routes->post('/karyawan/edit', 'EmployeesController::empEdit');
$routes->post('/karyawan/delete', 'EmployeesController::empDelete');

// Print, Export, Import
$routes->get('/karyawan/print', 'EmployeesController::empPrint');
$routes->get('/karyawan/export', 'EmployeesController::empExport');
$routes->add('/karyawan/import', 'EmployeesController::empImport');
$routes->get('/karyawan/import/template', 'EmployeesController::empImportTemplate');
$routes->post('/karyawan/import/process', 'EmployeesController::empImportProcess');
