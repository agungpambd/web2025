<?= $this->extend('admin/layout') ?>

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
                            <a href="/admin/karyawan/import/template" class="btn icon icon-left btn-primary">
                                <i class="bi bi-download"></i> Download Template
                            </a>
                            <hr>
                            <p>
                                Setelah Anda mengisi data Karyawan pada file tersebut, unggah kembali file Excel-nya melalui form di bawah ini,
                                lalu klik <strong class="text-warning">Preview Data</strong> untuk meninjau dan memastikan isi datanya sudah benar sebelum disimpan.
                                <br>
                                <b>Catatan</b>: Hanya file dengan ekstensi <strong class="text-danger">.xlsx</strong> yang dapat diunggah.
                            </p>
                            <form method="post" enctype="multipart/form-data" action="/admin/karyawan/import">
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
                                // Load model dan library PhpSpreadsheet
                                use App\Models\EmployeesModel;
                                use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

                                $emp = new EmployeesModel(); // Inisialisasi model EmployeesModel
                                $existingPairs = $emp->getAllEmpKeys(); // Ambil semua pasangan employee_id dan email dari DB

                                if (isset($_POST['preview'])) {
                                    // Untuk penamaan file secara unik
                                    $tgl_sekarang   = date('YmdHis');
                                    $nama_file_baru = 'data' . $tgl_sekarang . '.xlsx';

                                    // Path penyimpanan file sementara
                                    $tmpPath = WRITEPATH . 'berkas/tmp/';

                                    // Pastikan folder sementara ada
                                    if (!is_dir($tmpPath)) {
                                        mkdir($tmpPath, 0755, true); // Buat folder jika belum ada
                                    }

                                    // Hapus file lama jika ada (untuk menghindari duplikat)
                                    $fullPath = $tmpPath . $nama_file_baru;
                                    if (is_file($fullPath)) unlink($fullPath);

                                    // Proses unggah file sementara
                                    $ext = pathinfo($_FILES['tmp_file']['name'], PATHINFO_EXTENSION); // Mendapatkan informasi ekstensi file
                                    $tmp_file = $_FILES['tmp_file']['tmp_name']; // Mendapatkan lokasi file sementara di server

                                    if ($ext === "xlsx") { // Cek ekstensi file
                                        move_uploaded_file($tmp_file, $fullPath); // Pindahkan file ke folder sementara

                                        $reader = new Xlsx(); // Inisialisasi reader dari library PhpSpreadsheet, khusus untuk file .xlsx
                                        $spreadsheet = $reader->load($fullPath); // Load file Excel yang diunggah, diambil dari folder sementara
                                        $sheet = $spreadsheet->getSheetByName('ImportData')->toArray(null, true, true, true);
                                        /* Penjelasan setiap parameter pada fungsi toArray():   
                                                1. $nullValue: null → nilai pada sel kosong akan di-set sebagai null
                                                2. $calculateFormulas: true → rumus pada sel akan dihitung dan hasilnya yang diambil
                                                3. $formatData: true → data pada sel akan diformat sesuai tipe data aslinya
                                                4. $returnCellRef: true → array yang dihasilkan akan menggunakan referensi kolom Excel (A, B, C, ...) sebagai kunci array
                                            */
                                ?>
                                        <form method="post" action="/admin/karyawan/import/process">
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
                                                                            <th>Sandi</th>
                                                                            <th>Role</th>
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
                                                                        $no = 1; // nomor urut tabel
                                                                        $numrow = 1; // nomor baris awal pada file Excel (untuk melewati header)
                                                                        $kosong = 0; // untuk menghitung jumlah data kosong
                                                                        $duplikat_db = 0; // untuk menghitung jumlah data duplikat di database
                                                                        $duplikat_ex = 0; // untuk menghitung jumlah data duplikat antar baris Excel

                                                                        $existingMap = []; // Untuk menyimpan pasangan employee_id dan email dari database, dibuat dalam bentuk array asosiatif
                                                                        $excelPairs  = []; // Untuk menyimpan pasangan employee_id dan email dari file Excel yang diunggah

                                                                        foreach ($existingPairs as $pair) { // bangun array asosiatif dari data database
                                                                            $existingMap[$pair['employee_id']] = strtoupper($pair['email']); // gunakan employee_id sebagai kunci, email sebagai nilai (email dikonversi ke uppercase untuk perbandingan case-insensitive)
                                                                        }

                                                                        foreach ($sheet as $row) {
                                                                            $employee_id    = trim($row['A']); // trim() untuk menghilangkan spasi berlebih, contoh: " E001   " → "E001"
                                                                            $first_name     = trim($row['B']);
                                                                            $last_name      = trim($row['C']);
                                                                            $email          = trim($row['D']);
                                                                            $password       = trim($row['E']);
                                                                            $role           = trim($row['F']);
                                                                            $phone_number   = str_replace(',', '.', trim($row['G']));
                                                                            $hire_date      = trim($row['H']);
                                                                            $job_id         = trim($row['I']);
                                                                            $salary         = trim($row['J']);
                                                                            $commission_pct = trim($row['K']);
                                                                            $manager_id     = trim($row['L']);
                                                                            $department_id  = trim($row['M']);

                                                                            // Digunakan untuk melewati baris header dan apabila ada baris kosong di file Excel
                                                                            if (
                                                                                $numrow == 1 || // melewati baris pertama (header)
                                                                                // Lewati baris jika semua kolom kosong
                                                                                (
                                                                                    $employee_id === '' && $first_name === '' && $last_name === '' &&
                                                                                    $email === '' && $password === '' && $role === '' && $phone_number === '' &&
                                                                                    $hire_date === '' && $job_id === '' && $salary === '' && $commission_pct === '' &&
                                                                                    $manager_id === '' && $department_id === '')
                                                                            ) {
                                                                                $numrow++;
                                                                                continue; // Jika kosong semua kolomnya, maka lewati baris ini (jangan diproses)
                                                                            }

                                                                            /* Import Constraints:
                                                                            Kita tidak boleh mengimpor:
                                                                            - Jika employee_id atau email di Excel sudah ada di database.
                                                                            - Jika employee_id atau email di Excel muncul dua kali/lebih dalam file Excel itu sendiri.
                                                                            */
                                                                            // Cek duplikat di database (ID atau Email)
                                                                            $duplicateDB = false; // reset status duplikat untuk setiap baris/loop
                                                                            foreach ($existingMap as $id => $mail) { // $existingMap adalah data dari database dalam bentuk seperti ini: ["101" => "SKING", ...]
                                                                                /*
                                                                                Lalu disini akan melakukan pengecekan satu per satu:
                                                                                - Apakah employee_id di Excel sama dengan employee_id yang sudah ada di database?
                                                                                - ATAU apakah email di Excel sama dengan email yang sudah ada di database?
                                                                                */
                                                                                if ($id === $employee_id || strtoupper($email) === $mail) {
                                                                                    $duplicateDB = true; // tandai sebagai duplikat
                                                                                    $duplikat_db++;
                                                                                    break; // hentikan pengecekan lebih lanjut (pada baris lainnya) karena sudah dipastikan ada duplikat (employee_id atau email)
                                                                                }
                                                                            }

                                                                            // Cek duplikat antar baris Excel (ID atau Email)
                                                                            $duplicateEX = false;
                                                                            foreach ($excelPairs as $pair) { // $excelPairs adalah data dari file Excel yang sudah diproses sebelumnya dalam bentuk seperti ini: [ ["employee_id" => "101", "email" => "SKING"], ... ]
                                                                                /*
                                                                                Lalu disini akan melakukan pengecekan satu per satu:
                                                                                - Apakah employee_id di Excel sama dengan employee_id yang sudah ada di baris lain pada file Excel itu sendiri?
                                                                                - ATAU apakah email di Excel sama dengan email yang sudah ada di baris lain pada file Excel itu sendiri?
                                                                                */
                                                                                if ($pair['employee_id'] === $employee_id || strtoupper($pair['email']) === strtoupper($email)) {
                                                                                    $duplicateEX = true;
                                                                                    $duplikat_ex++;
                                                                                    break;
                                                                                }
                                                                            }
                                                                            // Simpan pasangan employee_id dan email dari baris Excel yang sedang diproses, untuk pengecekan duplikat di baris berikutnya
                                                                            $excelPairs[] = ['employee_id' => $employee_id, 'email' => $email];

                                                                            // Hitung baris kosong (apabila ada) untuk validasi sebelum import ke database
                                                                            if (
                                                                                $employee_id === '' || $first_name === '' || $last_name === '' ||
                                                                                $email === '' || $password === '' || $role === '' ||
                                                                                $phone_number === '' || $hire_date === '' || $job_id === '' ||
                                                                                $salary === '' || $commission_pct === '' ||
                                                                                $manager_id === '' && $department_id === ''
                                                                            ) {
                                                                                $kosong++;
                                                                            }

                                                                            // Pemberian style warna berdasarkan kondisi
                                                                            $style = function ($column, $isDuplicateDB = false, $isDuplicateEX = false) {
                                                                                if ($isDuplicateDB) return 'style="background:#fffcc9;"';   // kuning untuk duplikat di DB
                                                                                if ($isDuplicateEX) return 'style="background:#fffcc9;"';   // kuning untuk duplikat antar Excel
                                                                                return ($column === '' || $column === null) ? 'style="background:#ffcfcf;"' : ''; // merah untuk nilai kosong
                                                                            };
                                                                        ?>
                                                                            <tr>
                                                                                <td class="text-center"><?= $no++; ?></td>
                                                                                <td <?= $style($employee_id, $duplicateDB, $duplicateEX); ?>><?= esc($employee_id); ?></td>
                                                                                <td <?= $style($first_name); ?>><?= esc($first_name); ?></td>
                                                                                <td <?= $style($last_name); ?>><?= esc($last_name); ?></td>
                                                                                <td <?= $style($email, $duplicateDB, $duplicateEX); ?>><?= esc($email); ?></td>
                                                                                <td <?= $style($password); ?>><?= esc($password); ?></td>
                                                                                <td <?= $style($role); ?>> <?= esc($role) == '0' ? 'Admin' : (esc($role) == '1' ? 'User' : ''); ?> </td>
                                                                                <td <?= $style($phone_number); ?>><?= str_replace(',', '.', esc($phone_number)); ?></td>
                                                                                <td <?= $style($hire_date); ?>><?= esc($hire_date); ?></td>
                                                                                <td <?= $style($job_id); ?>><?= esc($job_id); ?></td>
                                                                                <td <?= $style($salary); ?>><?= number_format(esc($salary)); ?></td>
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

                                                            <?php if ($kosong > 0 || $duplikat_db > 0 || $duplikat_ex > 0): ?>
                                                                <div class="alert alert-light-danger color-danger mt-3">
                                                                    <?php if ($kosong > 0): ?>
                                                                        <p>Ops! Ada <strong><?= $kosong; ?></strong> baris data yang tidak lengkap.</p>
                                                                    <?php endif; ?>

                                                                    <?php if ($duplikat_db > 0): ?>
                                                                        <p>Ops! Ada <strong><?= $duplikat_db; ?></strong> data dengan <strong>ID Karyawan</strong> atau <strong>Email</strong> yang sudah terdaftar di database.</p>
                                                                    <?php endif; ?>

                                                                    <?php if ($duplikat_ex > 0): ?>
                                                                        <p>Ops! Ada <strong><?= $duplikat_ex; ?></strong> data dengan <strong>ID Karyawan</strong> atau <strong>Email</strong> yang duplikat di dalam file Excel itu sendiri.</p>
                                                                    <?php endif; ?>
                                                                    <p>Silahkan perbaiki data pada file Excel Anda, lalu unggah ulang.</p>
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
                                            Ops! Hanya file Excel 2010+ (.xlsx) yang diperbolehkan! Silahkan unggah ulang dengan format yang benar.
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
                    alert(
                        "Maaf, file " + sFileName +
                        " tidak diperbolehkan! Ekstensi file yang diperbolehkan hanya " +
                        _validFileExtensions.join(", ") + " !"
                    );
                    oInput.value = "";
                    return false;
                }
            }
        }
        return true;
    }
</script>

<?= $this->endSection() ?>