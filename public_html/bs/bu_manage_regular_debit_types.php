<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Manage Regular Debit Types";
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
                                    <li class="breadcrumb-item"><a href="bu_dashboard.php">Dashboard</a></li>
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
                                    <h3 class="card-title"></h3>
                                </div>
                                <div class="card-body">
                                    <table id="regular-debit-types" class="table table-hover table-bordered table-striped bu-data-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Type</th>
                                                <th>Description</th>
                                                <th>Used</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $counter = 1;

                                                $stmt = $pdo->prepare("
                                                    SELECT 
                                                        rdt1.id,
                                                        rdt1.type,
                                                        rdt1.description,
                                                        COUNT(rd1.regular_debit_type) AS _used
                                                    FROM
                                                        bu_regular_debit_types AS rdt1
                                                    LEFT JOIN
                                                        bu_regular_debits AS rd1 ON rdt1.type = rd1.regular_debit_type
                                                    GROUP BY 
                                                        rdt1.id, rdt1.type, rdt1.description;
                                                ");
                                                $stmt->execute();

                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                            ?>
                                            <tr>
                                                <td><?php echo $counter; ?></td>
                                                <td><?php echo $row->type; ?></td>
                                                <td><?php echo $row->description; ?></td>
                                                <td><?php echo $row->_used; ?></td>
                                                <td>
                                                    <a class="btn btn-success btn-sm" href="bu_view_regular_debit_type.php?id=<?php echo $row->id; ?>&used_type=<?php echo $row->_used; ?>&record=regular debit">
                                                        <i class="fa fa-edit"></i>
                                                        <!-- Edit -->
                                                    </a>
                                                    <a data-mysql-table="bu_regular_debit_types" data-record-id="<?php echo $row->id; ?>" class="btn btn-danger btn-sm delete-record<?php echo ($row->_used != 0 ? ' disabled' : ''); ?>" href="#">
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
                                        <tfoot>
                                            <tr>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <a class="btn btn-success" href="bu_add_regular_debit_type.php">Add Regular Debit Type</a>
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
            var regular_debit_types = new DataTable('#regular-debit-types', {
                order: [[2, 'asc']],
                pageLength: 25,
                lengthMenu: [
                    25,
                    50,
                    100, 
                    {label: 'All', value: -1 }
                ],
                columns: [
                    {className: 'counter'}, 
                    {className: 'type'},
                    {className: 'description'},
                    {className: 'used', type: 'num'},
                    {className: 'actions', orderable: false}
                ],
                layout: {
                    topStart: null,
                    topEnd: null,
                    //bottomEnd: null
                }
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