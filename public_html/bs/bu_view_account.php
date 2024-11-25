<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "View | Edit Account";

    // Get the account record to view/update
    $stmt = $pdo->prepare("
        SELECT 
            * 
        FROM 
            bu_accounts 
        WHERE 
            id = ?;
    ");

    $stmt->execute(
        [
            $_GET['id']
        ]
    );

    $bu_account = $stmt->fetch(PDO::FETCH_ASSOC);
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
                                    <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="bu_manage_accounts.php">Manage Accounts</a></li>
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
                                    <?php echo '<span class="record-id">Record ID</span> ' . $bu_account['id']; ?>
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
                                                <form id="update-account" class="update-form" method="post" enctype="multipart/form-data" role="form">
                                                <!-- Record ID [Hidden] -->
                                                    <input type="hidden" name="record-id" id="record-id" value="<?php echo $bu_account['id']; ?>">
                                                <!-- Account ID [Alpha] --> 
                                                    <div class="form-group row">
                                                        <label for="account-id-alpha" class="col-sm-2 col-form-label">Account ID [Alpha]</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="account-id-alpha" id="account-id-alpha" class="form-control" readonly value="<?php echo $bu_account['account_id_alpha']; ?>">
                                                        </div>
                                                    </div>
                                                <!-- Account ID --> 
                                                    <div class="form-group row">
                                                        <label for="account-id" class="col-sm-2 col-form-label">Account ID</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="account-id" id="account-id" class="form-control" readonly value="<?php echo $bu_account['account_id']; ?>">
                                                        </div>
                                                    </div>
                                                <!-- Bank Name -->
                                                    <div class="form-group row">
                                                        <label for="bank-id" class="col-sm-2 col-form-label">Bank Name</label>
                                                        <div class="col-sm-10">
                                                            <?php
                                                                $stmt = $pdo->prepare("
                                                                    CALL 
                                                                        bu_banks_dropdown();
                                                                ");
                                                                $stmt->execute();
                                                                
                                                                echo '<select name="bank-id" id="bank-id" class="form-control" required>';
                                                                echo '<option value="" selected disabled hidden>Select bank...</option>';
                                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                                    echo '<option value="'.$row->bank_id.'" '.($row->bank_id === $bu_account['bank_id'] ? 'selected="selected"' : '').'>'.$row->legal_name .'</option>';
                                                                    //echo '<option value="' . $row->bank_id . '">' . $row->legal_name. '</option>';
                                                                }
                                                                echo '</select>';

                                                                $stmt = null;
                                                            ?>
                                                        </div>
                                                    </div>
                                                <!-- Account Name -->
                                                    <div class="form-group row">
                                                        <label for="account-name" class="col-sm-2 col-form-label">Account Name</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="account-name" id="account-name" class="form-control" required value="<?php echo $bu_account['name']; ?>">
                                                        </div>
                                                    </div>
                                                
                                                <!-- Sort Code -->
                                                    <div class="form-group row">
                                                        <label for="sort-code" class="col-sm-2 col-form-label">Sort Code</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="sort-code" id="sort-code" class="form-control" required value="<?php echo $bu_account['sort_code']; ?>">
                                                        </div>
                                                    </div>
                                                <!-- Account Number -->
                                                    <div class="form-group row">
                                                        <label for="account-number" class="col-sm-2 col-form-label">Account Number</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="account-number" id="account-number" class="form-control" required value="<?php echo $bu_account['account_number']; ?>">
                                                        </div>
                                                    </div>
                                                <!-- Status -->
                                                    <div class="form-group row">
                                                        <label for="status" class="col-sm-2 col-form-label">Status</label>
                                                        <div class="col-sm-10">
                                                            <?php
                                                                $status = array (
                                                                    "Open",
                                                                    "Closed"
                                                                );

                                                                echo '<select name="status" id="status" class="form-control" required>';
                                                                echo '<option value="" selected disabled hidden>Select status...</option>';
                                                                foreach ($status as $option) {
                                                                    echo '<option value="'.$option.'" '.($option === $bu_account['status'] ? 'selected="selected"' : '').'>'.$option .'</option>';
                                                                    //echo '<option value="' . $row->bank_id . '">' . $row->legal_name. '</option>';
                                                                }
                                                                echo '</select>';


                                                            ?>
                                                            <!-- 
                                                                <option value="open" <?php //($bu_account['status'] === 'Open' ? 'selected="selected"' : '') ?>>Open</option>
                                                                <option value="closed" <?php //($bu_account['status'] === 'Closed' ? 'selected="selected"' : '') ?>>Closed</option>
                                                            </select>
                                                            -->
                                                        </div>
                                                    </div>
                                                <!-- Form Submit -->
                                                    <div class="form-group row">
                                                        <div class="offset-sm-2 col-sm-10">
                                                            <button type="submit" name="update-account-submit" id="update-account-submit" class="btn btn-outline-success">Update Account</button>
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
                                        Used by <?php echo $_GET['used'] .' '. $_GET['record']; ?> records
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
                // Something here
            });
        </script>
    </body>
</html>