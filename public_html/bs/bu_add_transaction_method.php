<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Add Transaction Method";
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
                                <?php BreadCrumb($page_name, $parent = array('title' => 'Manage Transaction Methods', 'url' => 'bu_manage_transaction_methods.php')); ?>
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
                                <form id="add-transaction-method" class="add-form" method="post" enctype="multipart/form-data" role="form">
                                    <div class="card-body">
                                    <!-- Method ID -->
                                        <div class="form-group row">
                                            <label for="method-id" class="col-sm-2 col-form-label">Method ID</label>
                                            <div class="col-sm-1">
                                                <input type="text" name="method-id" id="method-id" class="form-control" readonly value="<?php echo "M" . str_pad(rand(0,9999), 4, "0", STR_PAD_LEFT); ?>">
                                            </div>
                                        </div>
                                    <!-- Method Description -->
                                        <div class="form-group row">
                                            <label for="method-description" class="col-sm-2 col-form-label">Account Name</label>
                                            <div class="col-sm-6">
                                                <input type="text" name="method-description" id="method-description" class="form-control" required placeholder="Transaction method...">
                                            </div>
                                        </div>
                                    </div>    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <button type="submit" name="add-account-submit" class="btn btn-success">Add</button>
                                        <a class="btn btn-secondary float-end" href="bu_manage_transaction_methods.php">Cancel</a>
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