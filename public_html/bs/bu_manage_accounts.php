<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Manage Accounts";
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
                                    <a class="btn btn-success" href="bu_add_account.php">Add Account</a>
                                </div>
                                <div class="card-body">
                                    <table id="accounts" class="table table-hover table-bordered table-striped bu-data-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th></th>
                                                <th>A/C ID</th>
                                                <th>Bank</th>
                                                <th>A/C Name</th>
                                                <th>Sort Code</th>
                                                <th>A/C Number</th>
                                                <th>Status</th>
                                                <th>Used</th>
                                                <th style="text-align: center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $counter = 1;

                                                $stmt = $pdo->prepare("
                                                    SELECT 
                                                        a1.id,
                                                        a1.account_id_alpha,
                                                        a1.account_id,
                                                        b1.legal_name,
                                                        b1.trading_name,
                                                        a1.name,
                                                        a1.sort_code,
                                                        a1.account_number,
                                                        a1.status,
                                                        a1.notes,
                                                        COUNT(t1.account_id) AS _used
                                                    FROM
                                                        bu_accounts AS a1
                                                    LEFT JOIN
                                                        bu_banks AS b1 ON a1.bank_id = b1.bank_id
                                                    LEFT JOIN
                                                        bu_transactions AS t1 ON a1.account_id = t1.account_id
                                                    GROUP BY 
                                                        a1.id, 
                                                        a1.account_id_alpha, 
                                                        a1.account_id, 
                                                        b1.legal_name,
                                                        b1.trading_name, 
                                                        a1.name, 
                                                        a1.sort_code, 
                                                        a1.account_number, 
                                                        a1.status,
                                                        a1.notes
                                                ;");
                                                $stmt->execute(); 

                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                            ?>
                                            <tr>
                                                <td <?php echo ((!empty($row->notes)) ? 'class="has-note" data-counter="' . $counter .'" data-note="' . nl2br($row->notes) .'" data-account-name="' . $row->trading_name . ' ' . $row->name .'"' : "") . '>' . $counter; ?>
                                                </td>
                                                <td><?php echo $row->account_id_alpha; ?></td>
                                                <td><?php echo $row->account_id; ?></td>
                                                <td><?php echo $row->trading_name ?></td>
                                                <td><?php echo $row->name; ?></td>
                                                <td><?php echo $row->sort_code ;?></td>
                                                <td><?php echo $row->account_number; ?></td>
                                                <td><?php echo $row->status; ?></td>
                                                <td>
                                                    <?php if ($row->_used != 0) { ?>
                                                        <a href="bu_manage_transactions.php?filter=filter-col-1&value=<?php echo rawurlencode($row->account_id_alpha); ?>"><?php echo $row->_used; ?></a>
                                                    <?php } else { 
                                                        echo $row->_used;
                                                    } ?>   
                                                </td>
                                                <td style="text-align: center">

                                                    <a class="btn btn-success btn-sm view-record" href="#" data-bs-toggle="modal" data-bs-target="#update-account-modal" data-mysql-table="bu_accounts" data-record-id="<?php echo $row->id; ?>" data-used-by="<?php echo $row->_used; ?>" data-record-type="transaction">
                                                        <i class="fa fa-edit"></i>
                                                    </a>

                                                    <a data-mysql-table="bu_accounts" data-record-id="<?php echo $row->id; ?>" data-record-type="account" data-record-identifier="<?php echo $row->name; ?>"  class="btn btn-danger btn-sm  delete-record<?php echo ($row->_used != 0 ? ' disabled' : ''); ?>" href="#">
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
                                    <a class="btn btn-success" href="bu_add_account.php">Add Account</a>
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
        <div class="modal fade" id="update-account-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">   <!-- `.modal-dialog-centered` to centre on screen -->
                <div class="modal-content"  style="position: relative;">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">View | Update Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <!-- Inject the update account form -->
                        <?php include("forms/form_update_account.php"); ?>
                    </div>
                    <div class="modal-footer justify-content-between">
                    <!-- Update-form's submit button -->
                        <button type="submit" form="update-account" name="update-account-submit" id="update-account-submit" class="btn btn-success">Update</button>
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
            var accounts = new DataTable('#accounts', {
                order: [[3, 'asc']],
                pageLength: 25,
                lengthMenu: [
                    25,
                    50,
                    100, 
                    {label: 'All', value: -1 }
                ],
                columns: [
                    {className: 'counter', width: '60px'}, 
                    {className: 'account-id-alpha', width: '60px'},
                    {className: 'account-id'},
                    {className: 'bank-name'},
                    {className: 'account-name'},
                    {className: 'sort-code'},
                    {className: 'account-number'},
                    {className: 'status'},
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
    <!-- Ajax Modal -->
        <!-- <script src="ajax/bu_ajax_account_modal.js"></script> -->
    <!-- AJAX Update -->
        <script src="ajax/bu_ajax_update_account.js"></script>
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