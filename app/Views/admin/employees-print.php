<?php
date_default_timezone_set("Asia/Jakarta");
?>
<div>
    <div>
        <h1 align="center">Data Karyawan PT. Koding Malam</h1>
    </div>
    <div>
        <div>
            <table border="1" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Tanggal Masuk</th>
                        <th>Departemen</th>
                        <th>Posisi</th>
                        <th>Manager</th>
                        <th>Gaji (USD)</th>
                        <th>Komisi</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($dataEmp as $row): ?>
                        <tr>
                            <td align="center"><?= $no; ?></td>
                            <td align="center"><?= $row->employee_id; ?></td>
                            <td><?= $row->first_name . ' ' . $row->last_name; ?></td>
                            <td><?= $row->email; ?></td>
                            <td><?= $row->phone_number; ?></td>
                            <td align="center"><?= date("d-m-Y", strtotime($row->hire_date)); ?></td>
                            <td><?= $row->department_name; ?></td>
                            <td><?= $row->job_title; ?></td>
                            <td><?= $row->manager_name; ?></td>
                            <td align="right"><?= number_format($row->salary, 0, ',', '.'); ?></td>
                            <td align="right"><?= $row->commission_pct ? ($row->commission_pct * 100) . ' %' : '-'; ?></td>
                            <td align="center"><?= ($row->role == 0) ? "Admin" : "User"; ?></td>
                        </tr>
                    <?php
                        $no++;
                    endforeach;
                    ?>
                </tbody>
            </table>
            <br>
            <p>Dicetak pada: <?= (date("d-m-Y H:i:s")); ?> WIB </p>
        </div>
    </div>
</div>

<script type="text/javascript">
    window.print();
</script>