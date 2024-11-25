<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Manage Transaction [Sub-]Types";
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
                                    <h3 class="card-title"></h3>
                                </div>
                                <div class="card-body">
                                    <table id="transaction-types" class="table table-hover table-bordered table-striped bu-data-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Transaction Type</th>
                                                <th>Description</th>
                                                <th>Used [Type]</th>
                                                <th>Used [Sub-Type]</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $counter = 1;

                                                $stmt = $pdo->prepare("
                                                    CREATE TEMPORARY TABLE
                                                        temp
                                                    SELECT 
                                                        tt1.type,
                                                        COUNT(t1.type) AS _used_subtype
                                                    FROM
                                                        bu_transaction_types AS tt1
                                                    LEFT JOIN
                                                        bu_transactions AS t1 ON tt1.type = t1.sub_type
                                                    GROUP BY 
                                                        tt1.type;
                                                ");
                                                $stmt->execute();
                                                $stmt= null;

                                                $stmt = $pdo->prepare("
                                                    SELECT 
                                                        tt1.id,
                                                        tt1.type,
                                                        tt1.description,
                                                        COUNT(t1.type) AS _used_type,
                                                        temp._used_subtype
                                                    FROM
                                                        bu_transaction_types AS tt1
                                                    LEFT JOIN
                                                        bu_transactions AS t1 ON tt1.type = t1.type
                                                    LEFT JOIN
                                                        temp ON tt1.type = temp.type
                                                    GROUP BY 
                                                        tt1.id, 
                                                        tt1.type, 
                                                        temp._used_subtype, 
                                                        tt1.description;
                                                ");
                                                $stmt->execute(); 

                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                            ?>
                                            <tr>
                                                <td><?php echo $counter; ?></td>
                                                <td><?php echo $row->type; ?></td>
                                                <td><?php echo $row->description; ?></td>
                                                <td>
                                                    <a href="bu_manage_transactions.php?search=<?php echo $row->type; ?>"><?php echo $row->_used_type; ?></a>
                                                </td>
                                                <td>
                                                    <a href="bu_manage_transactions.php?search=<?php echo $row->type; ?>"><?php echo $row->_used_subtype; ?></a>
                                                </td>
                                                <td>
                                                    <a class="btn btn-success btn-sm" href="bu_view_transaction_type.php?id=<?php echo $row->id; ?>&used_type=<?php echo $row->_used_type; ?>&used_subtype=<?php echo $row->_used_subtype; ?>&record=transaction">
                                                        <i class="fa fa-edit"></i>
                                                        <!-- Edit -->
                                                    </a>
                                                    <a data-mysql-table="bu_transaction_types" data-record-id="<?php echo $row->id; ?>" class="btn btn-danger btn-sm delete-record<?php echo (($row->_used_type + $row->_used_subtype) != 0 ? ' disabled' : '');?>" href="#">
                                                        <i class="fa fa-trash"></i>
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
                                    <a class="btn btn-success" href="bu_add_transaction_type.php">Add Transaction Type</a>
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
            var transaction_types = new DataTable('#transaction-types', {
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