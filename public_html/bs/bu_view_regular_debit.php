<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "View | Edit Regular Debit";

    // Get the regular debit record to view/update
    $stmt = $pdo->prepare("
        SELECT 
            rd1.id,
            rd1.account_id_alpha,
            CONCAT(b1.trading_name, ' ', a1.`name`) as _name,
            CONCAT(a1.sort_code, ' ', a1.account_number) as _account,
            rd1.amount,
            rd1.`type`,
            rd1.sub_type,
            rd1.entity_id,
            rd1.`day`,
            -- rd1.period,
            rd1.notes,
            rd1.regular_debit_type,
            rd1.`last`,
            rd1.`next`,
            a1.bank_id,
            b1.bank_id
        FROM 
            bu_regular_debits AS rd1
        LEFT JOIN 
            bu_accounts AS a1 on rd1.account_id_alpha = a1.account_id_alpha 
        LEFT JOIN 
            bu_banks AS b1 on a1.bank_id = b1.bank_id 
        WHERE 
            rd1.id = ?;
    ");

    $stmt->execute(
        [
            $_GET['id']
        ]
    );
    
    $bu_regular_debit = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = null;
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
                                    <li class="breadcrumb-item"><a href="bu_manage_regular_debits.php">Manage Regular Debits</a></li>
                                    <li class="breadcrumb-item"><?php echo $page_name; ?></li>
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
                                        <?php echo '<span class="record-id">Record ID</span> ' . $bu_regular_debit['id']; ?>
                                        <!-- 
                                        <ul class="nav nav-pills">
                                            <li class="nav-item"><a class="nav-link active" href="#update_Profile" data-toggle="tab">Update Transaction</a></li>
                                            <li class="nav-item"><a class="nav-link" href="#Change_Password" data-toggle="tab">Change Password</a></li>
                                        </ul>
                                        -->
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <!-- / Update Profile -->
                                            <div class="tab-pane active" id="update_Profile">
                                                <form id="update-regular-debit" class="update-form" method="post" enctype="multipart/form-data" role="form">
                                                <!-- Record ID [Hidden] -->
                                                    <input type="hidden" name="record-id" id="record-id" value="<?php echo $bu_regular_debit['id']; ?>">
                                                <!-- Account ID [Read-only] -->
                                                    <div class="form-group row">
                                                        <label for="account-id-ignore" class="col-sm-2 col-form-label">Account ID</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="account-id-ignore" id="account-id-ignore" class="form-control" readonly value="<?php echo $bu_regular_debit['account_id_alpha']; ?>" >
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
                                                                //var_dump($stmt);
                                                                $stmt->execute(
                                                                    [
                                                                        'open'
                                                                    ]
                                                                );
                                                                
                                                                echo '<select name="account-id-alpha", id="account-id-alpha" class="form-control" required>';
                                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                                    echo '<option value="'.$row->account_id_alpha.'" '.($row->account_id_alpha === $bu_regular_debit['account_id_alpha'] ? 'selected="selected"' : '').'> '.$row->_name .' - ' . $row->account_number . ' ['. $row->account_id_alpha . ']' . ($row->status === 'Closed' ? ' CLOSED' : '') . '</option>';
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
                                                            <input type="text" name="amount" id="amount" class="form-control <?php echo ($bu_regular_debit['amount'] < 0 ? 'debit' : '') ?>" required value="<?php echo $bu_regular_debit['amount']; ?>" >
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
                                                                    echo '<option value="'.$row->type.'" '.($row->type === $bu_regular_debit['type'] ? 'selected="selected"' : '').'>'.$row->description .'</option>';
                                                                }
                                                                echo '</select>';

                                                                $stmt = null;
                                                            ?>   
                                                        </div>
                                                    </div>
                                                <!-- Sub-Type -->
                                                    <div class="form-group row">
                                                        <label for="type" class="col-sm-2 col-form-label">Sub-Type</label>
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
                                                                    echo '<option value="'.$row->type.'" '.($row->type === $bu_regular_debit['sub_type'] ? 'selected="selected"' : '').'>'.$row->description .'</option>';
                                                                }
                                                                echo '</select>';

                                                                $stmt = null;
                                                            ?>   
                                                        </div>
                                                    </div>
                                                <!-- Regular Debit Type -->
                                                    <div class="form-group row">
                                                        <label for="regular-debit-type" class="col-sm-2 col-form-label">Regular Debit Type</label>
                                                        <div class="col-sm-10">
                                                            <?php
                                                                $stmt = $pdo->prepare("
                                                                    CALL 
                                                                        bu_regular_debit_types_dropdown();
                                                                ");
                                                                $stmt->execute();

                                                                echo '<select name="regular-debit-type" id="regular-debit-type" class="form-control">';
                                                                //echo '<option value="">&nbsp;</option>';
                                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                                    echo '<option value="'.$row->type.'" '.($row->type === $bu_regular_debit['regular_debit_type'] ? 'selected="selected"' : '').'>'.$row->description .'</option>';
                                                                }
                                                                echo '</select>';

                                                                $stmt = null;
                                                            ?>   
                                                        </div>
                                                    </div>
                                                <!-- Entity -->
                                                    <div class="form-group row">
                                                        <label for="type" class="col-sm-2 col-form-label">Entity</label>
                                                        <div class="col-sm-10">
                                                            <?php
                                                                $stmt = $pdo->prepare("
                                                                    CALL 
                                                                        bu_entities_dropdown();
                                                                ");
                                                                $stmt->execute();

                                                                echo '<select name="entity-id" name="entity-id" class="form-control" required>';
                                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                                    echo '<option value="'.$row->entity_id.'" '.($row->entity_id === $bu_regular_debit['entity_id'] ? 'selected="selected"' : '').'>'.$row->entity_description .'</option>';
                                                                }
                                                                echo '</select>';

                                                                $stmt = null;
                                                                
                                                            ?>   
                                                        </div>
                                                    </div>
                                                <!-- Day -->
                                                    <div class="form-group row">
                                                        <label for="day" class="col-sm-2 col-form-label">Day</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="day" id="day" class="form-control" required value=<?php echo $bu_regular_debit['day']; ?> >
                                                        </div>
                                                    </div>
                                                <!-- Last --> 
                                                    <div class="form-group row">
                                                        <label for="last" class="col-sm-2 col-form-label">Last</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="last" id="last" class="form-control" required readonly style="cursor:text; background:white;">
                                                        </div>
                                                    </div>
                                                <!-- Next --> 
                                                    <div class="form-group row">
                                                        <label for="next" class="col-sm-2 col-form-label">Next</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="next" id="next" class="form-control" required readonly style="cursor:text; background:white;">
                                                        </div>
                                                    </div>
                                                <!-- Notes -->
                                                    <div class="form-group row">
                                                        <label for="type" class="col-sm-2 col-form-label">Notes</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="notes" id="notes" class="form-control" value="<?php echo $bu_regular_debit['notes']; ?>">
                                                        </div>
                                                    </div>
                                                <!-- Form Submit -->
                                                    <div class="form-group row">
                                                        <div class="offset-sm-2 col-sm-10">
                                                            <button type="submit" name="update-regular-debit-submit" id="update-regular-debit-submit" class="btn btn-outline-success">Update Regular Debit</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <!-- /.tab-pane -->
                                        </div>
                                        <!-- /.tab-content -->
                                    </div>
                                    <!-- /.card-body -->
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

            // DatePicker for Last
                var last = '<?php echo $bu_regular_debit['last']; ?>';
                //console.log(last)
                //startDate = startDate.substr(10,4) + '-' + startDate.substr(7,2)  + '-' + startDate.substr(4,2) // Date must be in YYYY-MM-DD format
                                
                $( "#last" ).datepicker(
                    {
                        dateFormat: "yy-mm-dd",
                        firstDay: 1,
                        beforeShowDay: $.datepicker.noWeekends
                    }
                );

                $("#last").datepicker("setDate", last);

            // DatePicker for Next
                var next = '<?php echo $bu_regular_debit['next']; ?>';
                //console.log(next)
                //startDate = startDate.substr(10,4) + '-' + startDate.substr(7,2)  + '-' + startDate.substr(4,2) // Date must be in YYYY-MM-DD format
                                
                $( "#next" ).datepicker(
                    {
                        dateFormat: "yy-mm-dd",
                        firstDay: 1,
                        beforeShowDay: $.datepicker.noWeekends
                    }
                );

                $("#next").datepicker("setDate", next);
            });
        </script>
    </body>
</html>