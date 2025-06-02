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
                                    <a class="btn btn-success" href="bu_add_bank.php">Add Bank</a>
                                </div>
                                <div class="card-body">
                                    <table id="banks" class="table table-hover table-bordered table-striped bu-data-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Bank ID</th>
                                                <th>Legal Name</th>
                                                <th>Trading Name</th>
                                                <th>Used</th>
                                                <th style="text-align: center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $counter = 1;

                                                $stmt = $pdo->prepare("
                                                    SELECT 
                                                        bu_banks.`id`,
                                                        bu_banks.`bank_id`,
                                                        bu_banks.`legal_name`,
                                                        bu_banks.`trading_name`,
                                                        COUNT(bu_accounts.`bank_id`) AS _used
                                                    FROM
                                                        bu_banks
                                                    LEFT JOIN
                                                        bu_accounts ON bu_banks.`bank_id` = bu_accounts.`bank_id`
                                                    GROUP BY 
                                                        bu_banks.`id`
                                                    ORDER BY 
                                                        bu_banks.`legal_name` ASC;
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
                                                <td style="text-align: center">
                                                    <a class="btn btn-success btn-sm view-record" href="#" data-bs-toggle="modal" data-bs-target="#update-bank-modal" data-mysql-table="bu_banks" data-record-id="<?php echo $row->id; ?>" data-used-by="<?php echo $row->_used; ?>" data-record-type="account">
                                                        <i class="fa fa-edit"></i>
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
    <!-- Update Bank Modal -->
        <div class="modal fade" id="update-bank-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">   <!-- `.modal-dialog-centered` to centre on screen -->
                <div class="modal-content"  style="position: relative;">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">View | Update Bank</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <!-- Inject the update bank form -->
                        <?php include("forms/form_update_bank.php"); ?>
                    </div>
                    <div class="modal-footer justify-content-between">
                    <!-- Update-form's submit button -->
                        <button type="submit" form="update-bank" name="update-bank-submit" id="update-bank-submit" class="btn btn-success">Update</button>
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
                    {name: 'counter', className: 'counter', width: '50px'}, 
                    {name: 'bank_id', className: 'bank-id'},
                    {name: 'legal_name', className: 'legal-name'},
                    {name: 'trading_name', className: 'trading-name'},
                    {name: 'used', className: 'used', width: '50px'},
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
        <!-- <script src="ajax/bu_ajax_update_bank.js"></script> -->
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