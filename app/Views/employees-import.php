<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div id="main-content">
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Import Data Karyawan</h3>
                    <p class="text-subtitle text-muted">PT. Koding Malam</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">HRS</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Import Karyawan</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <div class="row">
                    <div class="card">
                        <div class="card-header">
                            <p>
                                Silakan impor data Karyawan menggunakan file Excel sesuai format yang telah disediakan.
                                Klik <strong class="text-primary">Download Template</strong> untuk mengunduh format Excel tersebut.
                            </p>
                            <a href="/karyawan/import/template" class="btn icon icon-left btn-primary">
                                <i class="bi bi-download"></i> Download Template
                            </a>
                            <hr>
                            <p>
                                Setelah Anda mengisi data Karyawan pada file tersebut, unggah kembali file Excel-nya melalui form di bawah ini,
                                lalu klik <strong class="text-warning">Preview Data</strong> untuk meninjau dan memastikan isi datanya sudah benar sebelum disimpan.
                                <br>
                                <b>Catatan</b>: Hanya file dengan ekstensi <strong class="text-danger">.xlsx</strong> yang dapat diunggah.
                            </p>
                            <form method="post" enctype="multipart/form-data" action="/karyawan/import">
                                <div class="form-group">
                                    <input type="file" name="tmp_file" class="form-control" accept=".xlsx" onchange="ExcelFile(this);">
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <button type="submit" name="preview" class="btn icon icon-left btn-warning text-white">
                                            <i class="bi bi-search"></i> Preview Data
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="section-body">
                        <div class="row">
                            <div class="col-12">
                                <?php

                                use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

                                if (isset($_POST['preview'])) {
                                    $tgl_sekarang   = date('YmdHis');
                                    $nama_file_baru = 'data' . $tgl_sekarang . '.xlsx';

                                    // Path aman di writable
                                    $tmpPath = WRITEPATH . 'berkas/tmp/';

                                    // Pastikan folder ada
                                    if (!is_dir($tmpPath)) {
                                        mkdir($tmpPath, 0755, true);
                                    }

                                    // Hapus file lama jika ada
                                    $fullPath = $tmpPath . $nama_file_baru;
                                    if (is_file($fullPath)) unlink($fullPath);

                                    $ext = pathinfo($_FILES['tmp_file']['name'], PATHINFO_EXTENSION);
                                    $tmp_file = $_FILES['tmp_file']['tmp_name'];

                                    if ($ext === "xlsx") {
                                        move_uploaded_file($tmp_file, $fullPath);

                                        $reader = new Xlsx();
                                        $spreadsheet = $reader->load($fullPath);
                                        $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                                ?>
                                        <form method="post" action="/karyawan/import/process">
                                            <!-- Kirim nama file ke proses import -->
                                            <input type="hidden" name="new_file" value="<?= $nama_file_baru; ?>">
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h5 class="text-primary">Preview Data</h5>
                                                        </div>

                                                        <div class="card-body">
                                                            <div class="table-responsive">
                                                                <table class="table" id="tablePreview" width="100%" cellspacing="0">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>No</th>
                                                                            <th>ID</th>
                                                                            <th>Nama Depan</th>
                                                                            <th>Nama Belakang</th>
                                                                            <th>Email</th>
                                                                            <th>No. Telepon</th>
                                                                            <th>Tanggal Masuk</th>
                                                                            <th>Posisi</th>
                                                                            <th>Gaji (USD)</th>
                                                                            <th>Komisi</th>
                                                                            <th>Manager ID</th>
                                                                            <th>Departemen ID</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                                        $no = 1;
                                                                        $numrow = 1;
                                                                        $kosong = 0;

                                                                        foreach ($sheet as $row) {
                                                                            $employee_id    = trim($row['A']);
                                                                            $first_name     = trim($row['B']);
                                                                            $last_name      = trim($row['C']);
                                                                            $email          = trim($row['D']);
                                                                            $phone_number   = trim($row['E']);
                                                                            $hire_date      = trim($row['F']);
                                                                            $job_id         = trim($row['G']);
                                                                            $salary         = trim($row['H']);
                                                                            $commission_pct = trim($row['I']);
                                                                            $manager_id     = trim($row['J']);
                                                                            $department_id  = trim($row['K']);

                                                                            // Lewat header & baris kosong
                                                                            if ($numrow == 1 || ($employee_id == '' && $first_name == '' && $last_name == '' && $email == '' && $phone_number == '' && $hire_date == '' && $job_id == '' && $salary == '' && $commission_pct == '' && $manager_id == '' && $department_id == '')) {
                                                                                $numrow++;
                                                                                continue;
                                                                            }

                                                                            // Warna merah jika kolom kosong
                                                                            $style = fn($v) => empty($v) ? 'style="background:#fadbd8;"' : '';

                                                                            if (
                                                                                empty($employee_id) || empty($first_name) || empty($last_name) ||
                                                                                empty($email) || empty($phone_number) || empty($hire_date) ||
                                                                                empty($job_id) || empty($salary) || empty($commission_pct) ||
                                                                                empty($manager_id) || empty($department_id)
                                                                            ) {
                                                                                $kosong++;
                                                                            }
                                                                        ?>
                                                                            <tr>
                                                                                <td class="text-center"><?= $no++; ?></td>
                                                                                <td <?= $style($employee_id); ?>><?= esc($employee_id); ?></td>
                                                                                <td <?= $style($first_name); ?>><?= esc($first_name); ?></td>
                                                                                <td <?= $style($last_name); ?>><?= esc($last_name); ?></td>
                                                                                <td <?= $style($email); ?>><?= esc($email); ?></td>
                                                                                <td <?= $style($phone_number); ?>><?= esc($phone_number); ?></td>
                                                                                <td <?= $style($hire_date); ?>><?= esc($hire_date); ?></td>
                                                                                <td <?= $style($job_id); ?>><?= esc($job_id); ?></td>
                                                                                <td <?= $style($salary); ?>><?= esc($salary); ?></td>
                                                                                <td <?= $style($commission_pct); ?>><?= esc($commission_pct); ?></td>
                                                                                <td <?= $style($manager_id); ?>><?= esc($manager_id); ?></td>
                                                                                <td <?= $style($department_id); ?>><?= esc($department_id); ?></td>
                                                                            </tr>
                                                                        <?php
                                                                            $numrow++;
                                                                        }
                                                                        ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <script>
                                                                $(document).ready(function() {
                                                                    $('#tablePreview').DataTable({
                                                                        responsive: true,
                                                                        pageLength: 10,
                                                                        language: {
                                                                            search: "Cari:",
                                                                            lengthMenu: "Tampilkan _MENU_ data per halaman",
                                                                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                                                                            paginate: {
                                                                                first: '<i class="bi bi-skip-backward-fill"></i>',
                                                                                previous: '<i class="bi bi-caret-left-fill"></i>',
                                                                                next: '<i class="bi bi-caret-right-fill"></i>',
                                                                                last: '<i class="bi bi-skip-forward-fill"></i>'
                                                                            }
                                                                        }
                                                                    });
                                                                });
                                                            </script>


                                                            <?php if ($kosong > 0): ?>
                                                                <div class="alert alert-danger mt-3">
                                                                    Ops! Ada <strong><?= $kosong; ?></strong> baris data yang tidak lengkap!
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="mt-3">
                                                                    <button type="submit" name="emp_import" class="btn icon icon-left btn-success">
                                                                        <i class="bi bi-upload"></i> Import Data
                                                                    </button>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    <?php
                                    } else {
                                    ?>
                                        <div class="alert alert-danger">
                                            Ops! Hanya file Excel 2010+ (.xlsx) yang diperbolehkan!
                                        </div>
                                <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script type="text/javascript">
    var _validFileExtensions = [".xlsx"];

    function ExcelFile(oInput) {
        if (oInput.type == "file") {
            var sFileName = oInput.value;
            if (sFileName.length > 0) {
                var blnValid = false;
                for (var j = 0; j < _validFileExtensions.length; j++) {
                    var sCurExtension = _validFileExtensions[j];
                    if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                        blnValid = true;
                        break;
                    }
                }
                if (!blnValid) {
                    alert("Maaf, file " + sFileName + " tidak diperbolehkan! Ekstensi file yang diperbolehkan: " + _validFileExtensions.join(", "));
                    oInput.value = "";
                    return false;
                }
            }
        }
        return true;
    }
</script>

<?= $this->endSection() ?>