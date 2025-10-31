<tbody>
    <?php
    $no = 1;
    foreach ($listEmp as $employee):; ?>
        <tr>
            <td><?= $no; ?></td>
            <td><?= $employee->employee_id; ?></td>
            <td><?= $employee->first_name . ' ' . $employee->last_name; ?></td>
            <td><?= $employee->department_name; ?></td>
            <td><?= date("d-m-Y", strtotime($employee->hire_date)); ?></td>
            <td><?= $employee->job_title; ?></td>
            <td><?= '$' . number_format($employee->salary); ?></td>
            <td>
                <button class="btn btn-success btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#modalDetail<?= $employee->employee_id; ?>">
                    <i class="bi bi-eye-fill"></i>
                </button>
                <button class="btn btn-warning btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEdit<?= $employee->employee_id; ?>">
                    <i class="bi bi-pencil-fill text-white"></i></button>
                <button class="btn btn-danger btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#modalDelete<?= $employee->employee_id; ?>">
                    <i class="bi bi-trash-fill"></i></button>
            </td>
        </tr>
    <?php $no++;
    endforeach; ?>
</tbody>