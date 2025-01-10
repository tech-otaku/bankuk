<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "View | Edit Pre-fill";

    // Get the transaction record to view/update
    $stmt = $pdo->prepare("
        SELECT 
            pf1.id, 
            pf1.account_id_alpha, 
            CONCAT(b1.trading_name, ' ', a1.`name`) AS _name, 
            CONCAT(a1.sort_code, ' ', a1.account_number) AS _account, 
            pf1.`type`, 
            pf1.sub_type, 
            pf1.party_id, 
            a1.bank_id, 
            b1.bank_id 
        FROM 
            bu_prefills pf1  
        LEFT JOIN 
            bu_accounts a1 ON pf1.account_id_alpha = a1.account_id_alpha 
        LEFT JOIN 
            bu_banks b1 ON a1.bank_id = b1.bank_id 
        WHERE 
            pf1.id = ?;
    ");

    $stmt->execute(
        [
            $_GET['id']
        ]
    ); 

    $bu_prefill = $stmt->fetch(PDO::FETCH_ASSOC);
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
                                    <li class="breadcrumb-item"><a href="bu_manage_prefills.php">Manage Pre-fills</a></li>
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
                                        <?php echo '<span class="record-id">Record ID</span> ' . $bu_prefill['id']; ?>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <!-- / Update Profile -->
                                            <div class="tab-pane active" id="update_Profile">
                                                <form id="update-prefill" class="update-form" method="post" enctype="multipart/form-data" role="form">
                                                <!-- Record ID [Hidden] -->
                                                    <input type="hidden" name="record-id" id="record-id" value="<?php echo $bu_prefill['id']; ?>">
                                                <!-- Party -->
                                                    <div class="form-group row">
                                                        <label for="party-id" class="col-sm-2 col-form-label">Party</label>
                                                        <div class="col-sm-10">
                                                            <?php
                                                                $stmt = $pdo->prepare("
                                                                    CALL 
                                                                        bu_parties_dropdown();
                                                                ");
                                                                $stmt->execute();
                                                                
                                                                echo '<select name="party-id" name="party-id" class="form-control" required>';
                                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                                    echo '<option value="'.$row->party_id.'" '.($row->party_id === $bu_prefill['party_id'] ? 'selected="selected"' : '').'>'.$row->party .'</option>';
                                                                }
                                                                echo '</select>';

                                                                $stmt = null;
                                                                
                                                            ?>   
                                                        </div>
                                                    </div>
                                                <!-- Account ID [Read-only] -->
                                                    <div class="form-group row">
                                                        <label for="account-id-ignore" class="col-sm-2 col-form-label">Account ID</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="account-id-ignore" id="account-id-ignore" class="form-control" readonly value="<?php echo $bu_prefill['account_id_alpha']; ?>" >
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
                                                                    echo '<option value="'.$row->account_id_alpha.'" '.($row->account_id_alpha === $bu_prefill['account_id_alpha'] ? 'selected="selected"' : '').'> '.$row->_name .' - ' . $row->account_number . ' ['. $row->account_id_alpha . ']' . ($row->status === 'Closed' ? ' CLOSED' : '') . '</option>';
                                                                }
                                                                echo '</select>';

                                                                $stmt = null;
                                                            ?>
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
                                                                    echo '<option value="'.$row->type.'" '.($row->type === $bu_prefill['type'] ? 'selected="selected"' : '').'>'.$row->description .'</option>';
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
                                                                    echo '<option value="'.$row->type.'" '.($row->type === $bu_prefill['sub_type'] ? 'selected="selected"' : '').'>'.$row->description .'</option>';
                                                                }
                                                                echo '</select>';

                                                                $stmt = null;
                                                            ?>   
                                                        </div>
                                                    </div>
                                                <!-- Form Submit -->
                                                    <div class="form-group row">
                                                        <div class="offset-sm-2 col-sm-10">
                                                            <button type="submit" name="update-prefill-submit" id="update-prefill-submit" class="btn btn-outline-success">Update Prefill</button>
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
        </script>
    </body>
</html>
