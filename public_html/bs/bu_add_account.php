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
        <!-- Page Content -->
            <div class="content-wrapper ">
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1><?php echo $page_name; ?></h1>
                            </div>
                            <div class="col-sm-6">
                                <?php BreadCrumb($page_name, $parent = array('title' => 'Manage Accounts', 'url' => 'bu_manage_accounts.php')); ?>
                            </div>
                        </div>
                    </div>    <!-- /.container-fluid -->
                </section>
            <!-- Main Content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="col-md-12">
                            <!-- general form elements -->
                            <div class="card w-50 mx-auto">
                                <div class="card-header p-6">
                                    <h3 class="card-title"><?php echo $page_name; ?></h3>
                                </div>  <!-- /.card-header -->
                                <form id="add-account" class="add-form" method="post" enctype="multipart/form-data" role="form">
                                    <div class="card-body">
                                    <!-- Account ID [Alpha] -->
                                        <?php
                                            // Get the last account_id_alpha (I, J, K etc) used.
                                            $stmt = $pdo->prepare("
                                                SELECT 
                                                    bu_accounts.`account_id_alpha`
                                                FROM
                                                    bu_accounts
                                                ORDER BY 
                                                    bu_accounts.`account_id_alpha` DESC          
                                                LIMIT 1;
                                            ");
                                            $stmt->execute(); 

                                            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                $next_account_id_alpha = ++$row->account_id_alpha;  // Increment the last account_id_alpha. 'K' becomes 'L', 'L' becomes 'M' etc
                                            }

                                            $stmt = null;
                                            
                                        ?>
                                        <div class="form-group row">
                                            <label for="account-id-alpha" class="col-sm-2 col-form-label">Account ID [Alpha]</label>
                                            <div class="col-sm-1">
                                                <input type="text" name="account-id-alpha" id="account-id-alpha" class="form-control" readonly value="<?php echo $next_account_id_alpha; ?>">
                                            </div>
                                        </div>
                                    <!-- Account ID -->
                                        <div class="form-group row">
                                            <label for="account-id" class="col-sm-2 col-form-label">Account ID</label>
                                            <div class="col-sm-1">
                                                <input type="text" name="account-id" id="account-id" class="form-control" readonly value="<?php echo "A" . str_pad(rand(0,9999), 4, "0", STR_PAD_LEFT); ?>">
                                            </div>
                                        </div>
                                    <!-- Bank Name -->
                                        <div class="form-group row">
                                            <?php InputElementBankName ($pdo); ?>
                                        </div>
                                    <!-- Account Name -->
                                        <div class="form-group row">
                                            <label for="account-name" class="col-sm-2 col-form-label">Account Name</label>
                                            <div class="col-sm-6">
                                                <input type="text" name="account-name" id="account-name" class="form-control" required placeholder="Account name...">
                                            </div>
                                        </div>
                                    <!-- Sort Code -->
                                        <div class="form-group row">
                                            <label for="sort-code" class="col-sm-2 col-form-label">Sort Code</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="sort-code" id="sort-code" class="form-control" required pattern="[0-9]{2}-[0-9]{2}-[0-9]{2}" placeholder="Sort code...">
                                            </div>
                                        </div>
                                    <!-- Account Number -->
                                        <div class="form-group row">
                                            <label for="account-number" class="col-sm-2 col-form-label">Account Number</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="account-number" id="account-number" class="form-control" required pattern="[A-Z]{4}[0-9]{4}" placeholder="Account number...">
                                            </div>
                                        </div>
                                    <!-- Status -->
                                        <div class="form-group row">
                                            <?php InputElementAccountStatus (); ?>
                                        </div>
                                    <!-- Notes -->
                                        <div class="form-group row">
                                            <?php InputElementNotes (); ?>
                                        </div>
                                    </div>    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <button type="submit" name="add-account-submit" class="btn btn-success">Add</button>
                                        <a class="btn btn-secondary float-end" href="bu_manage_accounts.php">Cancel</a>
                                    </div>
                                </form>
                            </div>    <!-- /.card -->
                        </div>    <!-- /.col -->
                    </div>  <!-- /.container-fluid -->
                </section>    <!-- /.content -->
            </div>  <!-- /.dummy -->
        <!-- Common Footer -->
            <?php include("partials/footer.php"); ?>
        </div>    <!-- ./wrapper -->    
    <!-- Common Scripts -->
        <?php include("partials/scripts.php"); ?>
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