<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div id="main-content">
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>HR Dashboard</h3>
                    <p class="text-subtitle text-muted">PT. Koding Malam</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">HRS</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
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
                    <div class="col-6 col-lg-3 col-md-6">
                        <a href="/karyawan">
                            <div class="card">
                                <div class="card-body px-4 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                            <div class="stats-icon purple mb-2">
                                                <i class="iconly-boldShow"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                            <h6 class="text-muted font-semibold">Total Karyawan</h6>
                                            <h6 class="font-extrabold mb-0"><?= $totalEmp; ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                        <div class="stats-icon blue mb-2">
                                            <i class="iconly-boldProfile"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Total Gaji</h6>
                                        <h6 class="font-extrabold mb-0">$<?= number_format($totalSalary); ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                        <div class="stats-icon green mb-2">
                                            <i class="iconly-boldAdd-User"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Departemen</h6>
                                        <h6 class="font-extrabold mb-0"><?= $totalDept; ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                        <div class="stats-icon red mb-2">
                                            <i class="iconly-boldBookmark"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Jenis Pekerjaan</h6>
                                        <h6 class="font-extrabold mb-0"><?= $totalJobs; ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Google Donut/Pie Chart -->
                    <div class="col-lg-6 col-md-4 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Jumlah Karyawan per Departemen (Google Chart)</h4>
                            </div>
                            <div class="card-body">
                                <div id="google_chart_donut" style="width:100%; min-height:400px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Google Bar Chart -->
                    <div class="col-lg-6 col-md-4 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Jumlah Karyawan per Departemen (Google Chart)</h4>
                            </div>
                            <div class="card-body">
                                <div id="google_chart_column" style="width:100%; min-height:400px;"></div>
                            </div>
                        </div>
                    </div>
                    <script type="text/javascript">
                        google.charts.load("current", {
                            packages: ['corechart']
                        });
                        google.charts.setOnLoadCallback(drawCharts);

                        function drawCharts() {

                            // DATA 
                            var data = google.visualization.arrayToDataTable([
                                ['Department', 'Jumlah Karyawan'],
                                <?php foreach ($deptEmp as $row): ?>['<?= $row->department_name ?: "No Department"; ?>', <?= $row->num_employees; ?>],
                                <?php endforeach; ?>
                            ]);

                            // DONUT 
                            var donutOptions = {
                                legend: {
                                    position: 'bottom'
                                },
                                chartArea: {
                                    left: 10,
                                    top: 10,
                                    width: '90%',
                                    height: '75%'
                                },
                                backgroundColor: {
                                    fill: 'transparent'
                                },
                                pieHole: 0.25
                            };

                            // COLUMN 
                            var columnOptions = {
                                legend: {
                                    position: 'bottom'
                                },
                                backgroundColor: 'transparent',
                                chartArea: {
                                    left: 40,
                                    top: 10,
                                    width: '90%',
                                    height: '75%'
                                },
                                bar: {
                                    groupWidth: '60%'
                                },
                                hAxis: {
                                    textStyle: {
                                        color: 'inherit'
                                    }
                                },
                                vAxis: {
                                    minValue: 0,
                                    textStyle: {
                                        color: 'inherit'
                                    }
                                }
                            };

                            // DRAW DONUT
                            var donutChart = new google.visualization.PieChart(
                                document.getElementById('google_chart_donut')
                            );
                            donutChart.draw(data, donutOptions);

                            // DRAW COLUMN
                            var columnChart = new google.visualization.ColumnChart(
                                document.getElementById('google_chart_column')
                            );
                            columnChart.draw(data, columnOptions);
                        }

                        $(window).resize(function() {
                            drawCharts();
                        });
                    </script>
                </div>


                <div class="row">
                    <!-- Chart.js Pie -->
                    <div class="col-lg-6 col-md-4 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Jumlah Karyawan per Departemen (Chart.js)</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="chartjs_pie" style="min-height:400px;"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Chart.js BAR -->
                    <div class="col-lg-6 col-md-4 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Jumlah Karyawan per Departemen (Chart.js)</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="chartjs_bar" style="min-height:400px;"></canvas>
                            </div>
                        </div>
                    </div>
                    <script>
                        // DATA
                        const deptLabels = [
                            <?php foreach ($deptEmp as $row): ?> "<?= $row->department_name ?: 'No Department'; ?>",
                            <?php endforeach; ?>
                        ];

                        const deptValues = [
                            <?php foreach ($deptEmp as $row): ?>
                                <?= $row->num_employees; ?>,
                            <?php endforeach; ?>
                        ];

                        // Warna random untuk setiap departemen
                        const colors = deptValues.map(() => "#" + Math.floor(Math.random() * 16777215).toString(16));

                        // PIE / DONUT CHART
                        const ctxPie = document.getElementById('chartjs_pie');
                        new Chart(ctxPie, {
                            type: 'doughnut', // opsi lain: pie
                            data: {
                                labels: deptLabels,
                                datasets: [{
                                    data: deptValues,
                                    backgroundColor: colors
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            }
                        });

                        // BAR / COLUMN CHART
                        const ctxBar = document.getElementById('chartjs_bar');
                        new Chart(ctxBar, {
                            type: 'bar', // opsi lain: line
                            data: {
                                labels: deptLabels,
                                datasets: [{
                                    label: 'Jumlah Karyawan',
                                    data: deptValues,
                                    backgroundColor: colors
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    </script>
                </div>


                <div class="row">
                    <!-- Highchart Pie -->
                    <div class="col-lg-6 col-md-4 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Jumlah Karyawan per Departemen (Highchart)</h4>
                            </div>
                            <div class="card-body">
                                <div id="hc_pie" style="width:100%; min-height:400px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Highchart Column -->
                    <div class="col-lg-6 col-md-4 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Jumlah Karyawan per Departemen (Highchart)</h4>
                            </div>
                            <div class="card-body">
                                <div id="hc_column" style="width:100%; min-height:400px;"></div>
                            </div>
                        </div>
                    </div>
                    <script type="text/javascript">
                        // DATA
                        const deptData = [
                            <?php foreach ($deptEmp as $row): ?>['<?= $row->department_name ?: "No Department"; ?>', <?= $row->num_employees; ?>],
                            <?php endforeach; ?>
                        ];

                        // PIE / DONUT Highchart
                        Highcharts.chart('hc_pie', {
                            chart: {
                                type: 'pie',
                                backgroundColor: 'transparent'
                            },
                            title: {
                                text: 'Jumlah Karyawan per Departemen'
                            },
                            plotOptions: {
                                pie: {
                                    innerSize: '40%', // Jika ingin pie biasa hapus baris ini
                                    dataLabels: {
                                        enabled: true,
                                        format: '{point.name}: {point.y}'
                                    }
                                }
                            },
                            series: [{
                                name: 'Jumlah',
                                data: deptData
                            }]
                        });

                        // COLUMN Highchart
                        Highcharts.chart('hc_column', {
                            chart: {
                                type: 'column', // opsi lain: bar, line, area
                                backgroundColor: 'transparent'
                            },
                            title: {
                                text: 'Jumlah Karyawan per Departemen'
                            },
                            xAxis: {
                                type: 'category',
                                labels: {
                                    style: {
                                        color: 'inherit'
                                    }
                                }
                            },
                            yAxis: {
                                min: 0,
                                title: {
                                    text: 'Jumlah Karyawan'
                                },
                                labels: {
                                    style: {
                                        color: 'inherit'
                                    }
                                }
                            },
                            plotOptions: {
                                column: {
                                    borderWidth: 0,
                                    dataLabels: {
                                        enabled: true,
                                        format: '{point.y}'
                                    }
                                }
                            },
                            legend: {
                                enabled: false
                            },
                            series: [{
                                name: 'Jumlah',
                                data: deptData
                            }]
                        });
                    </script>
                </div>
            </div>
        </section>
    </div>
</div>
<?= $this->endSection() ?>