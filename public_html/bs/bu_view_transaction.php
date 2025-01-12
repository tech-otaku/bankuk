<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "View | Edit Transactions";

    // Get the transaction record to view/update
    $stmt = $pdo->prepare("
        SELECT 
            t1.id, 
            t1.account_id, 
            t1.account_id_alpha, 
            CONCAT(b1.trading_name, ' ', a1.`name`) AS _name, 
            CONCAT(a1.sort_code, ' ', a1.account_number) AS _account, 
            t1.amount, 
            t1.`type`, 
            t1.sub_type, 
            t1.entity_id, 
            t1.`date`, 
            t1.period, 
            t1.notes, 
            a1.bank_id, 
            b1.bank_id 
        FROM 
            bu_transactions t1  
        LEFT JOIN 
            bu_accounts a1 ON t1.account_id_alpha = a1.account_id_alpha 
        LEFT JOIN 
            bu_banks b1 ON a1.bank_id = b1.bank_id 
        WHERE 
            t1.id = ?;
    ");

    $stmt->execute(
        [
            $_GET['id']
        ]
    ); 

    $bu_transaction = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = null;
?>

<!doctype html>
<html lang="en" class="h-100">
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
                <!-- Content Header with logged in user details (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1><?php echo $page_name; ?></h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="bu_dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="bu_manage_transactions.php">Manage Transactions</a></li>
                                    <li class="breadcrumb-item"><?php echo $page_name; ?></li>
                                    <!-- <li class="breadcrumb-item active"><?php //echo $row->name; ?></li> -->
                                </ol>
                            </div>
                        </div>
                    </div>
                    <!-- /.container-fluid -->
                </section>
                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-3">
                            </div>
                            <!-- /.col -->
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header p-6">
                                        <?php echo '<span class="record-id">Record ID</span> ' . $bu_transaction['id']; ?>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <!-- / Update Profile -->
                                            <div class="tab-pane active" id="update_Profile">
                                                <form id="update-transaction" class="update-form" method="post" enctype="multipart/form-data" role="form">
                                                <!-- Record ID [Hidden] -->
                                                    <input type="hidden" name="record-id" id="record-id" value="<?php echo $bu_transaction['id']; ?>">
                                                <!-- Account ID [Read-only] -->
                                                    <div class="form-group row">
                                                        <label for="account-id-ignore" class="col-sm-2 col-form-label">Account ID</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="account-id-ignore" id="account-id-ignore" class="form-control" readonly value="<?php echo $bu_transaction['account_id_alpha']; ?>" >
                                                        </div>
                                                    </div>
                                                <!-- Account Name --> 
                                                    <div class="form-group row">
                                                        <label for="account-id-alpha" class="col-sm-2 col-form-label">Account Name</label>
                                                        <div class="col-sm-10">
                                                            <?php
                                                                // This stored procedure uses a WHERE clause to select rows whose `status` column is equal to a specific value. This value is passed as a parameter to the procedure: 'open', 'closed' or '%' = ALL
                                                                $stmt = $pdo->prepare("
                                                                    CALL 
                                                                        bu_accounts_dropdown(?);
                                                                ");
                                                                $stmt->execute(
                                                                    [
                                                                        '%'
                                                                    ]
                                                                );
                                                            
                                                                echo '<select name="account-id-alpha", id="account-id-alpha" class="form-control" required>';
                                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                                    echo '<option value="'.$row->account_id_alpha.'" '.($row->account_id_alpha === $bu_transaction['account_id_alpha'] ? 'selected="selected"' : '').'> '.$row->_name .' - ' . $row->account_number . ' ['. $row->account_id_alpha . ']' . ($row->status === 'Closed' ? ' CLOSED' : '') . '</option>';
                                                                }
                                                                echo '</select>';

                                                                $stmt = null;
                                                            ?>
                                                        </div>
                                                    </div>
                                                <!-- Amount -->
                                                    <div class="form-group row">
                                                        <label for="amount" class="col-sm-2 col-form-label">Amount</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="amount" id="amount" class="form-control <?php echo ($bu_transaction['amount'] < 0 ? 'debit' : '') ?>" required value="<?php echo $bu_transaction['amount']; ?>" >
                                                        </div>
                                                    </div>
                                                <!-- Type -->
                                                    <div class="form-group row">
                                                        <label for="type" class="col-sm-2 col-form-label">Type</label>
                                                        <div class="col-sm-10">
                                                            <?php
                                                                $stmt = $pdo->prepare("
                                                                    CALL 
                                                                        bu_transaction_types_dropdown();
                                                                ");
                                                                $stmt->execute();
                                                                
                                                                echo '<select name="type" id="type" class="form-control" required>';
                                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                                    echo '<option value="'.$row->type.'" '.($row->type === $bu_transaction['type'] ? 'selected="selected"' : '').'>'.$row->description .'</option>';
                                                                }
                                                                echo '</select>';

                                                                $stmt = null;
                                                            ?>   
                                                        </div>
                                                    </div>
                                                <!-- Sub-Type -->
                                                    <div class="form-group row">
                                                        <label for="sub-type" class="col-sm-2 col-form-label">Sub-Type</label>
                                                        <div class="col-sm-10">
                                                            <?php
                                                                $stmt = $pdo->prepare("
                                                                    CALL 
                                                                        bu_transaction_types_dropdown();
                                                                ");
                                                                $stmt->execute();

                                                                echo '<select name="sub-type" id="sub-type" class="form-control">';
                                                                echo '<option value="">&nbsp;</option>';
                                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                                    echo '<option value="'.$row->type.'" '.($row->type === $bu_transaction['sub_type'] ? 'selected="selected"' : '').'>'.$row->description .'</option>';
                                                                }
                                                                echo '</select>';

                                                                $stmt = null;
                                                            ?>   
                                                        </div>
                                                    </div>
                                                <!-- Entity -->
                                                    <div class="form-group row">
                                                        <label for="entity-id" class="col-sm-2 col-form-label">Entity</label>
                                                        <div class="col-sm-10">
                                                            <?php
                                                                $stmt = $pdo->prepare("
                                                                    CALL 
                                                                        bu_entities_dropdown();
                                                                ");
                                                                $stmt->execute();
                                                                
                                                                echo '<select name="entity-id" name="entity-id" class="form-control" required>';
                                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                                    echo '<option value="'.$row->entity_id.'" '.($row->entity_id === $bu_transaction['entity_id'] ? 'selected="selected"' : '').'>'.$row->entity_name .'</option>';
                                                                }
                                                                echo '</select>';

                                                                $stmt = null;
                                                                
                                                            ?>   
                                                        </div>
                                                    </div>
                                                <!-- Date -->
                                                    <div class="form-group row">
                                                        <label for="datepicker" class="col-sm-2 col-form-label">Date</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="date" id="datepicker" class="form-control" required readonly style="cursor:text; background:white;">
                                                        </div>
                                                    </div>
                                                <!-- Period -->
                                                    <div class="form-group row">
                                                        <label for="period-ignore" class="col-sm-2 col-form-label">Period</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="period-ignore" id="period-ignore" class="form-control" readonly value="<?php echo $bu_transaction['period']; ?>">
                                                        </div>
                                                    </div>
                                                <!-- Notes -->
                                                    <div class="form-group row">
                                                        <label for="notes" class="col-sm-2 col-form-label">Notes</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="notes" id="notes" class="form-control" value="<?php echo $bu_transaction['notes']; ?>">
                                                        </div>
                                                    </div>
                                                <!-- Form Submit -->
                                                    <div class="form-group row">
                                                        <div class="offset-sm-2 col-sm-10">
                                                            <button type="submit" name="update-transaction-submit" id="update-transaction-submit" class="btn btn-outline-success">Update Transaction</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <!-- /.tab-pane -->
                                        </div>
                                        <!-- /.tab-content -->
                                    </div>
                                    <!-- /.card-body -->
                                    <!-- Card Footer -->
                                    <div class="card-footer text-muted">
                                            Card Footer
                                        </div>
                                    </div>
                                </div>  <!-- /.nav-tabs-custom -->
                            </div>  <!-- /.col -->
                        </div>  <!-- /.row -->
                    </div>  <!-- /.container-fluid -->
                </section>  <!-- /.content -->
                <?php //} ?>
            </div>  <!-- /.dummy -->
        <!-- Common Footer -->
            <?php include("partials/footer.php"); ?>
        </div>  <!-- ./wrapper -->
    <!-- Common Scripts -->
        <?php include("partials/scripts.php"); ?>
    <!-- AJAX Update -->
        <script src="ajax/bu_ajax_update.js"></script>
    <!-- Page Script -->
        <script>
            $(function() {
                // DatePicker
                var transactionDate = '<?php echo $bu_transaction['date']; ?>';
                console.log('<?php echo $bu_transaction['date']; ?>');
                //transactionDate = transactionDate.substr(10,4) + '-' + transactionDate.substr(7,2)  + '-' + transactionDate.substr(4,2) // Date must be in YYYY-MM-DD format
                                
                $( "#datepicker" ).datepicker({
                    dateFormat: "yy-mm-dd",
                    firstDay: 1
                });

                $( "#datepicker" ).datepicker("setDate", transactionDate);
            });
        </script>
    </body>
</html>
