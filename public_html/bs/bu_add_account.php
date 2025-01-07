<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Add Account";

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
                                    <li class="breadcrumb-item"><a href="bu_manage_accounts.php">Manage Accounts</a></li>
                                    <li class="breadcrumb-item active"><?php echo $page_name; ?></li>
                                </ol>
                            </div>
                        </div>
                    </div>    <!-- /.container-fluid -->
                </section>
                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <!-- left column -->
                            <div class="col-md-12">
                                <!-- general form elements -->
                                <div class="card">
                                    <div class="card-header p-6">
                                        <h3 class="card-title">Card Header</h3>
                                    </div>  <!-- /.card-header -->
                                    <form id="add-account" class="add-form" method="post" enctype="multipart/form-data" role="form">
                                        <div class="card-body">
                                            <div class="row">
                                            <!-- Account ID -->
                                                <div class="col-md-1 form-group">
                                                    <label for="account-id">Account ID</label>
                                                    <input type="text" name="account-id" id="account-id" class="form-control" readonly value="<?php echo "A" . str_pad(rand(0,9999), 4, "0", STR_PAD_LEFT); ?>">
                                                </div>
                                            <!-- Account ID [Alpha] -->
                                                <?php
                                                    // Get the last account_id_alpha (I, J, K etc) used.
                                                    $stmt = $pdo->prepare("
                                                        SELECT 
                                                            account_id_alpha
                                                        FROM
                                                            bu_accounts
                                                        ORDER BY 
                                                            account_id_alpha DESC          
                                                        LIMIT 1;
                                                    ");
                                                    $stmt->execute(); 

                                                    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                        $next_account_id_alpha = ++$row->account_id_alpha;  // Increment the last account_id_alpha. 'K' becomes 'L', 'L' becomes 'M' etc
                                                    }

                                                    $stmt = null;
                                                    
                                                ?>
                                                <div class="col-md-1 form-group">
                                                    <label for="account-id-alpha">Account ID [Alpha]</label>
                                                    <input type="text" name="account-id-alpha" id="account-id-alpha" class="form-control" readonly value="<?php echo $next_account_id_alpha; ?>">
                                                </div>
                                            <!-- Bank Name -->
                                                <div class="col-md-2 form-group">
                                                    <label for="bank-id">Bank Name</label>
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
                                                        }
                                                        echo '</select>';

                                                        $stmt = null;
                                                    ?>
                                                </div>
                                            <!-- Account Name -->
                                                <div class="col-md-2 form-group">
                                                    <label for="account-name">Account Name</label>
                                                    <input type="text" name="account-name" id="account-name" class="form-control" required placeholder="Enter account name...">
                                                </div>
                                            <!-- Sort Code -->
                                                <div class="col-md-2 form-group">
                                                    <label for="sort-code">Sort Code</label>
                                                    <input type="text" name="sort-code" id="sort-code" class="form-control" required pattern="[0-9]{2}-[0-9]{2}-[0-9]{2}" placeholder="Enter sort code...">
                                                </div>
                                            <!-- Account Number -->
                                                <div class="col-md-2 form-group">
                                                    <label for="account-number">Account Number</label>
                                                    <input type="text" name="account-number" id="account-number" class="form-control" required pattern="[A-Z]{4}[0-9]{4}" placeholder="Enter account number...">
                                                </div>
                                                <div class="col-md-1 form-group">
                                                    <label for="status">Status</label>
                                                    <?php
                                                        $status = array (
                                                            "Open",
                                                            "Closed"
                                                        );

                                                        echo '<select name="status" id="status" class="form-control" required>';
                                                        echo '<option value="" selected disabled hidden>Select status...</option>';
                                                        foreach ($status as $option) {
                                                            echo '<option value="'.$option.'" '.($option === 'Open' ? 'selected="selected"' : '').'>'.$option .'</option>';
                                                        }
                                                        echo '</select>';
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="row">
                                            </div>
                                            <div class="row">
                                            </div>
                                        </div>    <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="submit" name="add-account-submit" class="btn btn-success">Add</button>
                                        </div>
                                    </form>
                                </div>    <!-- /.card -->
                            </div>    <!-- /.col -->
                        </div>  <!-- /.row -->
                    </div>  <!-- /.container-fluid -->
                </section>    <!-- /.content -->
            </div>  <!-- /.dummy -->
        <!-- Common Footer -->
            <?php include("partials/footer.php"); ?>
        </div>    <!-- ./wrapper -->    
    <!-- Common Scripts -->
        <?php include("partials/scripts.php"); ?>
    <!-- IS THIS NEEDED ? -->
        <script type="text/javascript">
            $(document).ready(function() {
                //bsCustomFileInput.init();
            });
        </script>
    <!-- AJAX Add -->
        <script src="ajax/bu_ajax_add.js"></script>
    <!-- Page Script -->
        <script>
            $(function() {
                // Something here
            });
        </script>
    </body>
</html>