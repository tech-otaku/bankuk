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
                            <div class="card w-50 mx-auto">
                                <div class="card-header p-6">
                                    <a class="btn btn-success" href="bu_add_transaction_type.php">Add Transaction Type</a>
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
                                                <th style="text-align: center">Actions</th>
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
                                                    <?php if ($row->_used_type != 0) { ?>
                                                        <a href="bu_manage_transactions.php?filter=filter-col-4&value=<?php echo rawurlencode($row->description); ?>"><?php echo $row->_used_type; ?></a>
                                                    <?php } else { 
                                                        echo $row->_used_type;
                                                    } ?>
                                                </td>
                                                <td>
                                                    <?php if ($row->_used_subtype != 0) { ?>
                                                        <a href="bu_manage_transactions.php?filter=filter-col-5&value=<?php echo rawurlencode($row->description); ?>"><?php echo $row->_used_subtype; ?></a>
                                                    <?php } else { 
                                                        echo $row->_used_subtype;
                                                    } ?>
                                                </td>
                                                <td style="text-align: center">
                                                    <a class="btn btn-success btn-sm view-record" href="#" data-bs-toggle="modal" data-bs-target="#update-transaction-type-modal" data-mysql-table="bu_transaction_types" data-record-id="<?php echo $row->id; ?>" data-type-used-by="<?php echo $row->_used_type; ?>" data-subtype-used-by="<?php echo $row->_used_subtype; ?>" data-record-type="transaction">
                                                        <i class="fa fa-edit"></i>
                                                    </a>

                                                    <a data-mysql-table="bu_transaction_types" data-record-id="<?php echo $row->id; ?>" data-record-type="transaction type" data-record-identifier="<?php echo $row->description; ?>"  class="btn btn-danger btn-sm delete-record<?php echo (($row->_used_type + $row->_used_subtype) != 0 ? ' disabled' : '');?>" href="#">
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
    <!-- Update Entity Modal -->
        <div class="modal fade" id="update-transaction-type-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">   <!-- `.modal-dialog-centered` to centre on screen -->
                <div class="modal-content"  style="position: relative;">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">View | Update Transaction TYpe</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <!-- Inject the update transaction-type form -->
                        <?php include("forms/form_update_transaction_type.php"); ?>
                    </div>
                    <div class="modal-footer justify-content-between">
                    <!-- Update-form's submit button -->
                        <button type="submit" form="update-transaction-type" name="update-transaction-type-submit" id="update-transaction-type-submit" class="btn btn-success">Update</button>
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
                    {className: 'counter', width: '50px'}, 
                    {className: 'type', type: 'num'},
                    {className: 'description'},
                    {className: 'used', type: 'num'},
                    {className: 'used', type: 'num'},
                    {className: 'actions', width: '95px', orderable: false}
                ],
                layout: {
                    topStart: null,
                    topEnd: null,
                    //bottomEnd: null
                }
            });
        </script>
    <!-- AJAX Update -->
        <script src="ajax/bu_ajax_update_transaction_type.js"></script>
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