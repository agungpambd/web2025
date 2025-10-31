<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div id="main-content">
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Data Karyawan</h3>
                    <p class="text-subtitle text-muted">PT. Koding Malam</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">HRS</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Data Karyawan</li>
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
                            <button class="btn icon icon-left btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddEmployee">
                                <i class="bi bi-plus-circle"></i> Tambah Data
                            </button>
                            <a href="/karyawan/print" target="_blank" class="btn icon icon-left btn-info text-white">
                                <i class="bi bi-printer"></i> Print Data
                            </a>
                            <a href="/karyawan/export" target="_blank" class="btn icon icon-left btn-success">
                                <i class="bi bi-file-earmark-excel"></i> Export Data (Spreadsheet)
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="tableEmp">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>ID</th>
                                            <th>Nama</th>
                                            <th>Departemen</th>
                                            <th>Tanggal Masuk</th>
                                            <th>Posisi</th>
                                            <th>Gaji ($)</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <script>
                                    $(document).ready(function() {
                                        $('#tableEmp').DataTable({
                                            processing: true,
                                            serverSide: true,
                                            responsive: true,
                                            ajax: {
                                                url: "<?= site_url('admin/karyawan/list'); ?>",
                                                type: "POST"
                                            },
                                            columns: [{
                                                    data: "no",
                                                    orderable: false
                                                },
                                                {
                                                    data: "employee_id"
                                                },
                                                {
                                                    data: "nama"
                                                },
                                                {
                                                    data: "department_name"
                                                },
                                                {
                                                    data: "hire_date"
                                                },
                                                {
                                                    data: "job_title"
                                                },
                                                {
                                                    data: "salary"
                                                },
                                                {
                                                    data: "actions",
                                                    orderable: false,
                                                    searchable: false
                                                }
                                            ],
                                            order: [
                                                [1, "asc"]
                                            ],
                                            pagingType: "full_numbers", // <--- penting
                                            language: {
                                                processing: "Memproses...",
                                                search: "Cari:",
                                                lengthMenu: "Tampilkan _MENU_ data",
                                                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                                                infoEmpty: "Tidak ada data tersedia",
                                                infoFiltered: "(difilter dari total _MAX_ data)",
                                                loadingRecords: "Memuat data...",
                                                zeroRecords: "Tidak ada data yang cocok",
                                                emptyTable: "Data tidak tersedia",
                                                paginate: {
                                                    first: '<i class="bi bi-skip-backward-fill"></i>',
                                                    previous: '<i class="bi bi-caret-left-fill"></i>',
                                                    next: '<i class="bi bi-caret-right-fill"></i>',
                                                    last: '<i class="bi bi-skip-forward-fill"></i>'
                                                },
                                            }
                                        });
                                    });
                                </script>

                            </div>
                        </div>
                    </div>

                    <!-- Toast Sukses -->
                    <div class="toast-container position-fixed top-0 end-0 p-3">
                        <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="toast-header">
                                <svg class="bd-placeholder-img rounded me-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" preserveAspectRatio="xMidYMid slice" focusable="false">
                                    <rect width="100%" height="100%" fill="#00a43aff"></rect>
                                </svg>
                                <strong class="me-auto">Berhasil!</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                            <div class="toast-body">
                                <?= session()->getFlashdata('success') ?>
                            </div>
                        </div>
                    </div>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const successMessage = <?= json_encode(session()->getFlashdata('success')) ?>;
                            if (successMessage) {
                                const toastEl = document.getElementById('successToast');
                                const toast = new bootstrap.Toast(toastEl, {
                                    delay: 4000
                                });
                                toast.show();
                            }
                        });
                    </script>

                    <!-- Modal Tambah Data -->
                    <div class="modal fade" id="modalAddEmployee" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title text-white">Tambah Data Karyawan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="/karyawan/add" method="post">
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label>First Name<small class="text-danger">*</small></label>
                                                <input type="text" name="first_name" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Last Name<small class="text-danger">*</small></label>
                                                <input type="text" name="last_name" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Username Email<small class="text-danger">*</small></label>
                                                <input type="text" name="email" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>No Telp<small class="text-danger">*</small></label>
                                                <input type="text" name="phone_number" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Tanggal Masuk<small class="text-danger">*</small></label>
                                                <input type="date" name="hire_date" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Departemen<small class="text-danger">*</small></label>
                                                <select name="department_id" class="form-select" required>
                                                    <option value="" selected disabled>Pilih Departemen</option>
                                                    <?php foreach ($listDept as $dept): ?>
                                                        <option value="<?= $dept->department_id; ?>"><?= $dept->department_id . ' - ' . $dept->department_name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-12">
                                                <label>Posisi<small class="text-danger">*</small></label>
                                                <select name="job_id" class="form-select" required>
                                                    <option value="" selected disabled>Pilih Posisi</option>
                                                    <?php foreach ($listJobs as $job): ?>
                                                        <option value="<?= $job->job_id; ?>"><?= $job->job_id . ' - ' . $job->job_title . ' (Salary: ' . number_format($job->min_salary) . '-' . number_format($job->max_salary) . ')'; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label>ID Karyawan<small class="text-danger">*</small></label>
                                                <input type="number" name="employee_id" class="form-control" value="<?= $lastEmpId->employee_id + 1; ?>" readonly required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Manajer<small class="text-danger">*</small></label>
                                                <select name="manager_id" class="form-select" required>
                                                    <option value="" selected disabled>Pilih Manajer</option>
                                                    <?php foreach ($listEmp as $manager): ?>
                                                        <option value="<?= $manager->employee_id; ?>"><?= $manager->employee_id . ' - ' . $manager->first_name . ' ' . $manager->last_name . ' (' . $manager->job_id . ')'; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Gaji ($)<small class="text-danger">*</small></label>
                                                <input type="number" name="salary" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Komisi (0-100%)</label>
                                                <input type="number" name="commission_pct" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- End Modal Tambah Data -->

                    <!-- Modal Detail Karyawan -->
                    <?php foreach ($listEmp as $employee): ?>
                        <div class="modal fade" id="modalDetail<?= $employee->employee_id; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-success">
                                        <h5 class="modal-title text-white">Detail Karyawan [ID: <?= $employee->employee_id; ?>]</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label><strong>Nama Depan</strong></label>
                                                <p><?= $employee->first_name; ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label><strong>Nama Belakang</strong></label>
                                                <p><?= $employee->last_name; ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label><strong>Email</strong></label>
                                                <p><?= $employee->email; ?>@kodingmalam.id</p>
                                            </div>
                                            <div class="col-md-6">
                                                <label><strong>No Telp</strong></label>
                                                <p><?= $employee->phone_number; ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label><strong>Tanggal Masuk</strong></label>
                                                <p><?= date("d-m-Y", strtotime($employee->hire_date)); ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label><strong>Departemen</strong></label>
                                                <p><?= $employee->department_name; ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label><strong>Posisi</strong></label>
                                                <p><?= $employee->job_title; ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label><strong>Manajer</strong></label>
                                                <p><?= $employee->manager_id . ' - ' . $employee->manager_name; ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label><strong>Gaji (USD)</strong></label>
                                                <p><?= number_format($employee->salary); ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label><strong>Komisi</strong></label>
                                                <p><?= $employee->commission_pct; ?> (<?= ($employee->commission_pct ? ($employee->commission_pct * 100) . '%' : '-'); ?>)</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <!-- End Modal Detail Karyawan -->

                    <!-- Modal Edit Karyawan -->
                    <?php foreach ($listEmp as $employee): ?>
                        <div class="modal fade" id="modalEdit<?= $employee->employee_id; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning">
                                        <h5 class="modal-title text-white">Edit Karyawan [ID: <?= $employee->employee_id; ?>]</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <form action="/karyawan/edit" method="post" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label><strong>Nama Depan</strong><small class="text-danger">*</small></label>
                                                    <input type="text" name="first_name" value="<?= $employee->first_name; ?>" class="form-control" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label><strong>Nama Belakang</strong><small class="text-danger">*</small></label>
                                                    <input type="text" name="last_name" value="<?= $employee->last_name; ?>" class="form-control" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label><strong>Username Email</strong><small class="text-danger">*</small></label>
                                                    <input type="text" name="email" value="<?= $employee->email; ?>" class="form-control" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label><strong>No Telp</strong><small class="text-danger">*</small></label>
                                                    <input type="text" name="phone_number" value="<?= $employee->phone_number; ?>" class="form-control" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label><strong>Tanggal Masuk</strong><small class="text-danger">*</small></label>
                                                    <input type="date" name="hire_date" value="<?= $employee->hire_date; ?>" class="form-control" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label><strong>Departemen</strong><small class="text-danger">*</small></label>
                                                    <select name="department_id" class="form-select" required>
                                                        <option value="<?= $employee->department_id; ?>" selected>
                                                            <?= $employee->department_id; ?> - <?= $employee->department_name; ?>
                                                        </option>
                                                        <option value="" disabled>-- Pilih Departemen Baru --</option>
                                                        <?php foreach ($listDept as $dept): ?>
                                                            <option value="<?= $dept->department_id; ?>"><?= $dept->department_id . ' - ' . $dept->department_name; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label><strong>Posisi</strong><small class="text-danger">*</small></label>
                                                    <select name="job_id" class="form-select" required>
                                                        <option value="<?= $employee->job_id; ?>" selected>
                                                            <?= $employee->job_id . ' - ' . $employee->job_title; ?>
                                                        </option>
                                                        <option value="" disabled>-- Pilih Posisi Baru --</option>
                                                        <?php foreach ($listJobs as $job): ?>
                                                            <option value="<?= $job->job_id; ?>">
                                                                <?= $job->job_id . ' - ' . $job->job_title . ' (Salary: ' . number_format($job->min_salary) . '-' . number_format($job->max_salary) . ')'; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label><strong>Manajer</strong><small class="text-danger">*</small></label>
                                                    <select name="manager_id" class="form-select" required>
                                                        <option value="<?= $employee->manager_emp_id; ?>" selected>
                                                            <?= $employee->manager_emp_id . ' - ' . $employee->manager_name; ?>
                                                        </option>
                                                        <option value="" disabled>-- Pilih Manajer Baru --</option>
                                                        <?php foreach ($listEmp as $manager): ?>
                                                            <option value="<?= $manager->employee_id; ?>">
                                                                <?= $manager->employee_id . ' - ' . $manager->first_name . ' ' . $manager->last_name . ' (' . $manager->job_id . ')'; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label><strong>Gaji (USD)</strong><small class="text-danger">*</small></label>
                                                    <input type="number" name="salary" value="<?= $employee->salary; ?>" class="form-control" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label><strong>Komisi (0-100%)</strong></label>
                                                    <input type="number" name="commission_pct" value="<?= $employee->commission_pct * 100; ?>" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <input type="text" name="employee_id" value="<?= $employee->employee_id; ?>" required hidden>

                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <!-- End Modal Edit Karyawan -->

                    <!-- Modal Hapus Karyawan -->
                    <?php foreach ($listEmp as $employee): ?>
                        <div class="modal fade" id="modalDelete<?= $employee->employee_id; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title text-white">Hapus Karyawan Berikut [ID: <?= $employee->employee_id; ?>] ?</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="/karyawan/delete" method="post">
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label><strong>Nama Depan</strong></label>
                                                    <p><?= $employee->first_name; ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label><strong>Nama Belakang</strong></label>
                                                    <p><?= $employee->last_name; ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label><strong>Email</strong></label>
                                                    <p><?= $employee->email; ?>@kodingmalam.id</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label><strong>No Telp</strong></label>
                                                    <p><?= $employee->phone_number; ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label><strong>Tanggal Masuk</strong></label>
                                                    <p><?= date("d-m-Y", strtotime($employee->hire_date)); ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label><strong>Departemen</strong></label>
                                                    <p><?= $employee->department_name; ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label><strong>Posisi</strong></label>
                                                    <p><?= $employee->job_title; ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label><strong>Manajer</strong></label>
                                                    <p><?= $employee->manager_id . ' - ' . $employee->manager_name; ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label><strong>Gaji</strong></label>
                                                    <p>$<?= number_format($employee->salary); ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label><strong>Komisi</strong></label>
                                                    <p><?= $employee->commission_pct; ?> (<?= ($employee->commission_pct ? ($employee->commission_pct * 100) . '%' : '-'); ?>)</p>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="text" name="employee_id" value="<?= $employee->employee_id; ?>" required hidden>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <!-- End Modal Detail Karyawan -->
                </div>
            </div>
        </section>
    </div>
</div>

<?= $this->endSection() ?>