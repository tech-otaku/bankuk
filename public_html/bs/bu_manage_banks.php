<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Manage Banks";
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
                                    <table id="banks" class="table table-hover table-bordered table-striped bu-data-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Bank ID</th>
                                                <th>Legal Name</th>
                                                <th>Common Name</th>
                                                <th>Used</th>
                                                <th>actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $counter = 1;

                                                $stmt = $pdo->prepare("
                                                    SELECT 
                                                        b1.id,
                                                        b1.bank_id,
                                                        b1.legal_name,
                                                        b1.trading_name,
                                                        COUNT(a1.bank_id) AS _used
                                                    FROM
                                                        bu_banks AS b1
                                                    LEFT JOIN
                                                        bu_accounts AS a1 ON b1.bank_id = a1.bank_id
                                                    GROUP BY 
                                                        b1.id
                                                    ORDER BY 
                                                        b1.legal_name ASC;
                                                ");
                                                $stmt->execute(); 

                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                            ?>
                                            <tr>
                                                <td><?php echo $counter; ?></td>
                                                <td><?php echo $row->bank_id; ?></td>
                                                <td><?php echo $row->legal_name; ?></td>
                                                <td><?php echo $row->trading_name; ?></td>
                                                <td><?php echo $row->_used; ?></td>
                                                <td>
                                                    <a class="btn btn-success btn-sm" href="bu_view_bank.php?id=<?php echo $row->id; ?>&used=<?php echo $row->_used; ?>&record=account">
                                                        <i class="fa fa-edit"></i>
                                                        <!-- Edit -->
                                                    </a>
                                                    <a data-mysql-table="bu_banks" data-record-id="<?php echo $row->id; ?>" data-record-type="bank" data-record-identifier="<?php echo $row->trading_name; ?>"  class="btn btn-danger btn-sm  delete-record<?php echo ($row->_used != 0 ? ' disabled' : ''); ?>" href="#">
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
                                    <a class="btn btn-success" href="bu_add_bank.php">Add Bank</a>
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
            var banks = new DataTable('#banks', {
                order: [[3, 'asc']],
                pageLength: 25,
                lengthMenu: [
                    25,
                    50,
                    100, 
                    {label: 'All', value: -1 }
                ],
                columns: [
                    {className: 'counter'}, 
                    {className: 'bank-id'},
                    {className: 'legal-name'},
                    {className: 'trading-name'},
                    {className: 'used'},
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