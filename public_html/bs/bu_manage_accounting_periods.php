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
            <div class="content-wrapper ">              
                <section class="content-header">       
                    <div class="container-fluid">       
                        <div class="row mb-2">          
                            <div class="col-sm-6">      
                                <h1><?php echo $page_name; ?></h1>
                            </div>
                            <div class="col-sm-6">      
                                <?php BreadCrumb($page_name); ?>
                            </div>
                        </div>
                    </div>
                    <!-- /.container-fluid -->
                </section>
                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-12">
                            <div class="card w-50 mx-auto">
                                <div class="card-header p-6">
                                    <a class="btn btn-success" href="bu_add_accounting_period.php">Add Accounting Period</a>
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

                                        <p style="float: right;">Current period is <span class="current-period"><?php echo $bu_settings['current_period']; ?></span> starting on <span class="period-start"><?php echo $start->format('D d/m/Y'); ?></span> and ending on <span class="period-end"><?php echo $end->format('D d/m/Y'); ?></span></p>
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
                                                <th style="text-align: center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $counter = 1;

                                                $stmt = $pdo->prepare("
                                                    SELECT 
                                                        bu_accounting_periods.`id`,
                                                        bu_accounting_periods.`period_start`,
                                                        bu_accounting_periods.`period_end`,
                                                        bu_accounting_periods.`period`,
                                                        COUNT(bu_transactions.`period`) AS _used
                                                    FROM
                                                        bu_accounting_periods
                                                    LEFT JOIN
                                                        bu_transactions ON bu_transactions.`period` = bu_accounting_periods.`period`
                                                    GROUP BY 
                                                        bu_accounting_periods.`id`,
                                                        bu_accounting_periods.`period`
                                                    ORDER BY 
                                                        bu_accounting_periods.`period` DESC;
                                                ");
                                                $stmt->execute();

                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                            ?>
                                            <tr>
                                                <td><?php echo $counter; ?></td>
                                                <td><?php echo $row->period_start; ?></td>
                                                <td><?php echo $row->period_end; ?></td>
                                                <td><?php echo $row->period; ?></td>
                                                <td>
                                                    <?php if ($row->_used != 0) { ?>
                                                        <a class="text-decoration-none" href="bu_manage_transactions.php?filter-col-10=<?php echo rawurlencode($row->period); ?>"><?php echo $row->_used; ?></a>
                                                    <?php } else { 
                                                        echo $row->_used;
                                                    } ?>
                                                </td>
                                                <td style="text-align: center">

                                                    <a class="btn btn-success btn-sm view-record" href="#" data-bs-toggle="modal" data-bs-target="#update-accounting-period-modal" data-mysql-table="bu_accounting_periods" data-record-id="<?php echo $row->id; ?>" data-used-by="<?php echo $row->_used; ?>" data-record-type="transaction">
                                                        <i class="fa fa-edit"></i>
                                                    </a>

                                                    <a data-mysql-table="bu_accounting_periods" data-record-id="<?php echo $row->id; ?>" data-record-type="accounting period" data-record-identifier="<?php echo $row->period; ?>"  class="btn btn-danger btn-sm delete-record<?php echo ($row->_used != 0 ? ' disabled' : ''); ?>" href="#">
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
    <!-- Update Account Modal -->
        <div class="modal fade" id="update-accounting-period-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">   <!-- `.modal-dialog-centered` to centre on screen -->
                <div class="modal-content"  style="position: relative;">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">View | Update Accounting Period</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <!-- Inject the update account form -->
                        <?php include("forms/form_update_accounting_period.php"); ?>
                    </div>
                    <div class="modal-footer justify-content-between">
                    <!-- Update-form's submit button -->
                        <button type="submit" form="update-accounting-period" name="update-accounting-period-submit" id="update-accounting-period-submit" class="btn btn-success">Update</button>
                    <!-- Update-modal's close button -->
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    <!-- Common Scripts -->
        <?php include("partials/scripts.php"); ?>
    <!-- DataTable Table -->
        <script>
            var periods = new DataTable('#periods', {
                drawCallback: function (settings) {
                },
                pageLength: 25,
                search: false,
                columns: [
                    {name: 'counter', className: 'counter', width: '50px'}, 
                    {
                        name: 'start', 
                        className: 'period-start', 
                        type: 'date', 
                        render: DataTable.render.datetime('ddd DD/MM/YYYY'),  // requires moment.js
                        createdCell: function (td, cellData, rowData, row, col) {
                            $(td).addClass(Chronology(cellData));
                        }
                    },
                    {
                        name: 'end', 
                        className: 'period-end', 
                        type: 'date', 
                        render: DataTable.render.datetime('ddd DD/MM/YYYY'),  // requires moment.js
                        createdCell: function (td, cellData, rowData, row, col) {
                            $(td).addClass(Chronology(cellData));
                        }
                    },
                    {name: 'period', className: 'period'},
                    {name: 'used', className: 'used'},
                    {name: 'actions', className: 'actions', width: '95px', orderable: false}
                ],
                layout: {
                    topStart: null,
                    topEnd: null,
                    //bottomEnd: null
                }
            });
        </script>
    <!-- AJAX Update -->
        <!-- <script src="ajax/bu_ajax_update_accounting_period.js"></script> -->
        <script src="ajax/bu_ajax_update_form.js"></script>
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