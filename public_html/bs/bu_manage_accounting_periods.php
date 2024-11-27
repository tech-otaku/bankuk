<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Manage Accounting Periods";
?>

<!DOCTYPE html>
<html>
    <head>
    <!-- Common Head -->
        <?php include("partials/head.php"); ?>
    </head>
    <body class="d-flex flex-column h-100">
        <div class="wrapper">
            <!-- Navigation Bar -->
            <?php include("partials/navigation.php"); ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper ">   <!-- Temporarily .dummy -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1><?php echo $page_name; ?></h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><?php echo $page_name; ?></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <!-- /.container-fluid -->
                </section>
                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header p-6">
                                    <h3 class="card-title">
                                        <?php

                                        // Get settings data
                                            $stmt = $pdo->prepare("
                                                CALL 
                                                    bu_settings_get_settings();
                                            ");
                                            $stmt->execute();

                                            $bu_settings = $stmt->fetch(PDO::FETCH_ASSOC);
                                            $stmt = null;

                                            $start = new DateTime($bu_settings['current_start']);
                                            $end = new DateTime($bu_settings['current_end']);
                                        ?>

                                        <p>Current period is <span class="current-period"><?php echo $bu_settings['current_period']; ?></span> starting on <span class="period-start"><?php echo $start->format('D d/m/Y'); ?></span> and ending on <span class="period-end"><?php echo $end->format('D d/m/Y'); ?></span></p>
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <table id="periods" class="table table-hover table-bordered table-striped bu-data-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Start</th>
                                                <th>End</th>
                                                <th>Period</th>
                                                <th>Used</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $counter = 1;

                                                $stmt = $pdo->prepare("
                                                    SELECT 
                                                        ac1.id,
                                                        ac1.start,
                                                        ac1.end,
                                                        ac1.period,
                                                        COUNT(t1.period) AS _used
                                                    FROM
                                                        bu_accounting_periods ac1
                                                    LEFT JOIN
                                                        bu_transactions t1 ON ac1.period = t1.period
                                                    GROUP BY 
                                                        ac1.id , ac1.period
                                                    ORDER BY 
                                                        ac1.period DESC;
                                                ");
                                                $stmt->execute();

                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                            ?>
                                            <tr>
                                                <td><?php echo $counter; ?></td>
                                                <td><?php echo $row->start; ?></td>
                                                <td><?php echo $row->end; ?></td>
                                                <td><?php echo $row->period; ?></td>
                                                <td>
                                                    <a href="bu_manage_transactions.php?search=<?php echo $row->period; ?>"><?php echo $row->_used; ?></a>
                                                </td>
                                                <td>
                                                    <a class="btn btn-success btn-sm" href="bu_view_accounting_period.php?id=<?php echo $row->id; ?>&used=<?php echo $row->_used; ?>&record=transaction">
                                                        <i class="fa fa-edit"></i>
                                                        <!-- Edit -->
                                                    </a>
                                                    <a data-mysql-table="bu_accounting_periods" data-record-id="<?php echo $row->id; ?>" class="btn btn-danger btn-sm delete-record<?php echo ($row->_used != 0 ? ' disabled' : ''); ?>" href="#">
                                                        <i class="fa fa-trash"></i>
                                                        <!-- Delete -->
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php 
                                                    $counter++;
                                                } // while
                                                
                                                $stmt = null;
                                            ?>
                                            </tfoot>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <a class="btn btn-success" href="bu_add_accounting_period.php">Add Accounting Period</a>
                                </div>
                            </div>  <!-- /.card -->
                        </div>  <!-- /.col -->
                    </div>  <!-- /.row -->
                </section>  <!-- /.content -->
            </div>  <!-- /.dummy -->
        <!-- Common Footer -->
            <?php include("partials/footer.php"); ?>
        </div>  <!-- ./wrapper -->
    <!-- Common Scripts -->
        <?php include("partials/scripts.php"); ?>
    <!-- DataTable Table -->
        <script>
            var periods = new DataTable('#periods', {
                drawCallback: function (settings) {
                },
                pageLength: 25,
                lengthMenu: [
                    25,
                    50,
                    100, 
                    {label: 'All', value: -1 }
                ],
                columns: [
                    {className: 'counter'}, 
                    {className: 'period-start', type: 'date', render: DataTable.render.datetime('ddd DD/MM/YYYY')}, // requires moment.js
                    {className: 'period-end', type: 'date', render: DataTable.render.datetime('ddd DD/MM/YYYY')},   // requires moment.js 
                    {className: 'period'},
                    {className: 'used'},
                    {className: 'actions', orderable: false}
                ]
            });
        </script>
    <!-- Ajax Delete -->
        <script src="ajax/bu_ajax_delete.js"></script>
    <!-- Page Script -->
        <script>
            $(function() {
                // Something here
            });
        </script>
    </body>
</html>