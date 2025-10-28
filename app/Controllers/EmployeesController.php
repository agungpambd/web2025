<?php

namespace App\Controllers;

use App\Models\EmployeesModel;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EmployeesController extends BaseController
{
    private $emp;

    public function __construct()
    {
        $this->emp = new EmployeesModel();
    }


    public function employees()
    {
        $data = [
            'title'     => 'HRS | Data Karyawan',
            'pageId'    => 'karyawan',
            'listEmp'   => $this->emp->listEmployees(),
            'listDept'  => $this->emp->listDepartment(),
            'listJobs'  => $this->emp->listJobs(),
            'lastEmpId' => $this->emp->lastEmpId(),
        ];

        return view('employees', $data);
    }

    public function getEmployeesAjax()
    {
        // Ambil parameter dari DataTables
        $request = service('request');

        // Ambil parameter pencarian, paging, dan sorting
        $search  = $request->getPost('search')['value'] ?? null;
        $start   = $request->getPost('start') ?? 0;
        $length  = $request->getPost('length') ?? 10;

        // Ambil parameter sorting
        $order   = $request->getPost('order')[0]['column'] ?? null;
        $dir     = $request->getPost('order')[0]['dir'] ?? 'asc';

        // Ambil data karyawan dengan filter, paging, dan sorting
        $employees = $this->emp->getEmployeesServerSide($search, $start, $length, $order, $dir);
        $total     = $this->emp->countAllEmployees();
        $filtered  = $this->emp->countFilteredEmployees($search);

        $data = []; // Menyimpan/inisiasi data untuk dikirim ke DataTables
        $no = $start + 1;

        foreach ($employees as $emp) {
            $data[] = [
                'no'                => $no++,
                'employee_id'       => $emp->employee_id,
                'nama'              => $emp->first_name . ' ' . $emp->last_name,
                'department_name'   => $emp->department_name,
                'hire_date'         => date("d-m-Y", strtotime($emp->hire_date)),
                'job_title'         => $emp->job_title,
                'salary'            => number_format($emp->salary),
                'actions'           => '
                <button class="btn btn-success btn-sm" data-id="' . $emp->employee_id . '" onclick="viewEmployee(' . $emp->employee_id . ')">
                    <i class="bi bi-eye-fill"></i>
                </button>
                <button class="btn btn-warning btn-sm text-white" data-id="' . $emp->employee_id . '" onclick="editEmployee(' . $emp->employee_id . ')">
                    <i class="bi bi-pencil-fill"></i>
                </button>
                <button class="btn btn-danger btn-sm" data-id="' . $emp->employee_id . '" onclick="deleteEmployee(' . $emp->employee_id . ')">
                    <i class="bi bi-trash-fill"></i>
                </button>'
            ];
        }

        // Kirim response dalam format JSON sesuai kebutuhan DataTables
        return $this->response->setJSON([
            'draw'              => intval($request->getPost('draw')), // untuk keamanan dari serangan XSS
            'recordsTotal'      => $total, // total data tanpa filter
            'recordsFiltered'   => $filtered, // total data dengan filter
            'data'              => $data // data semua karyawan
        ]);
    }

    public function empAdd()
    {
        $commission = $this->request->getPost('commission_pct');

        if (!empty($commission)) {
            $commission = $commission / 100;
        } else {
            $commission = null;
        }

        $data = [
            'employee_id'       => $this->request->getPost('employee_id'),
            'first_name'        => $this->request->getPost('first_name'),
            'last_name'         => $this->request->getPost('last_name'),
            'email'             => $this->request->getPost('email'),
            'phone_number'      => $this->request->getPost('phone_number'),
            'hire_date'         => $this->request->getPost('hire_date'),
            'job_id'            => $this->request->getPost('job_id'),
            'salary'            => $this->request->getPost('salary'),
            'commission_pct'    => $commission,
            'manager_id'        => $this->request->getPost('manager_id'),
            'department_id'     => $this->request->getPost('department_id'),
        ];

        $this->emp->addEmployee($data);
        return redirect()->to('/karyawan')->with('success', 'Data karyawan berhasil ditambahkan.');
    }

    public function empEdit()
    {
        $employeeId = $this->request->getPost('employee_id');

        $commission = $this->request->getPost('commission_pct');
        if (!empty($commission)) {
            $commission = $commission / 100;
        } else {
            $commission = null;
        }

        $data = [
            'first_name'        => $this->request->getPost('first_name'),
            'last_name'         => $this->request->getPost('last_name'),
            'email'             => $this->request->getPost('email'),
            'phone_number'      => $this->request->getPost('phone_number'),
            'hire_date'         => $this->request->getPost('hire_date'),
            'job_id'            => $this->request->getPost('job_id'),
            'salary'            => $this->request->getPost('salary'),
            'commission_pct'    => $commission,
            'manager_id'        => $this->request->getPost('manager_id'),
            'department_id'     => $this->request->getPost('department_id'),
        ];

        $this->emp->editEmployee($employeeId, $data);

        return redirect()->to('/karyawan')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function empDelete()
    {
        $employeeId = $this->request->getPost('employee_id');

        $this->emp->deleteEmployee($employeeId);

        return redirect()->to('/karyawan')->with('success', 'Data karyawan berhasil dihapus.');
    }

    public function empPrint()
    {
        $data = [
            'dataEmp' => $this->emp->listEmployees()
        ];

        return view('/employees-print', $data);
    }

    public function empExport()
    {
        // === 1. Ambil data karyawan ===
        $employees = $this->emp->listEmployees();

        // === 2. Inisialisasi Spreadsheet ===
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Ubah nama sheet aktif
        $sheet->setTitle('Data Karyawan');

        // === 3. Buat Header / Judul Laporan ===
        $sheet->setCellValue('A1', 'Data Karyawan PT. Koding Malam');
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14); // Font bold & size 14
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // === 4. Menulis Header Kolom ===
        // Daftar header kolom
        $headers = [
            'A3' => 'No',
            'B3' => 'ID',
            'C3' => 'Nama Depan',
            'D3' => 'Nama Belakang',
            'E3' => 'Email',
            'F3' => 'Telepon',
            'G3' => 'Tanggal Masuk',
            'H3' => 'Departemen',
            'I3' => 'Posisi',
            'J3' => 'Manajer',
            'K3' => 'Gaji (USD)',
            'L3' => 'Komisi',
            // Tambahkan header kolom lainnya sesuai kebutuhan
        ];

        // Menulis header dari daftar $headers ke dalam sheet
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // === 5. Style untuk Header Kolom ===
        $headerStyleArray = [
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFF00'], // Kuning
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $sheet->getStyle('A3:L3')->applyFromArray($headerStyleArray);

        // === 6. Menulis Data Karyawan ===
        $row = 4;
        $no  = 1;

        foreach ($employees as $data) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $data->employee_id);
            $sheet->setCellValue('C' . $row, $data->first_name);
            $sheet->setCellValue('D' . $row, $data->last_name);
            $sheet->setCellValue('E' . $row, $data->email);
            $sheet->setCellValue('F' . $row, $data->phone_number);
            $sheet->setCellValue('G' . $row, date("d-m-Y", strtotime($data->hire_date)));
            $sheet->setCellValue('H' . $row, $data->department_name);
            $sheet->setCellValue('I' . $row, $data->job_title);
            $sheet->setCellValue('J' . $row, $data->manager_name);
            $sheet->setCellValue('K' . $row, $data->salary);
            $sheet->setCellValue('L' . $row, $data->commission_pct);

            $row++;
            $no++;
        }

        // === 7. Style Umum untuk Tabel ===
        $tableStyleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'size' => 11,
                'name' => 'Calibri',
            ],
        ];

        $sheet->getStyle('A3:L' . ($row - 1))->applyFromArray($tableStyleArray);

        // === 8. Style Khusus Kolom Gaji dan Komisi ===
        // Kolom K (Gaji) rata kanan & format ribuan
        $sheet->getStyle('K4:K' . ($row - 1))
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('K4:K' . ($row - 1))
            ->getNumberFormat()
            ->setFormatCode('#,##0'); // contoh: 12,500

        // Kolom L (Komisi) rata kanan & format 2 digit desimal
        $sheet->getStyle('L4:L' . ($row - 1))
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('L4:L' . ($row - 1))
            ->getNumberFormat()
            ->setFormatCode('0.00'); // contoh: 0.25

        // === 9. Penyesuaian Lebar Kolom ===
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // === 10. Output File ===
        $timestamp = date('Ymd_His');
        $filename  = 'data_employees_' . $timestamp . '.xlsx';

        // Header HTTP untuk download file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tulis ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
