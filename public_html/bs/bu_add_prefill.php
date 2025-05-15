<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Add Prefill";
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
                                <?php BreadCrumb($page_name, $parent = array('title' => 'Manage Pre-fills', 'url' => 'bu_manage_prefills.php')); ?>
                            </div>
                        </div>
                    </div>
                    <!-- /.container-fluid -->
                </section>
            <!-- Main Content -->
                <section class="content">
                    <div class="container-fluid">
                        <!-- <div class="row"> -->
                            <!-- left column -->
                            <div class="col-md-12">
                                <!-- general form elements -->
                                <div class="card w-50 mx-auto">
                                    <div class="card-header p-6">
                                        <h3 class="card-title"><?php echo $page_name; ?></h3>
                                    </div>
                                    <!-- form start -->
                                    <form id="add-prefill" class="add-form" method="post" enctype="multipart/form-data" role="form">
                                        <div class="card-body">
                                            <!-- <div class="row"> -->
                                            <!-- Name -->
                                                <div class="form-group row">
                                                    <label for="prefill-name" class="col-sm-2 col-form-label">Name</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="prefill-name" id="prefill-name" class="form-control" required>
                                                    </div>
                                                </div>
                                            <!-- Entity -->
                                                <div class="form-group row">
                                                    <?php InputElementEntity ($pdo); ?>
                                                </div>
                                            <!-- Account Name -->
                                                <div class="form-group row">
                                                    <?php InputElementAccountData ($pdo, 2, 'open'); ?>
                                                </div>
                                            <!-- Type -->
                                                <div class="form-group row">
                                                    <?php InputElementTransactionType ($pdo); ?>
                                                </div>
                                            <!-- Sub Type -->
                                                <div class="form-group row">
                                                    <?php InputElementTransactionSubType ($pdo); ?>
                                                </div>
                                            <!-- Method -->
                                                <div class="form-group row">
                                                    <?php InputElementTransactionMethod ($pdo); ?>
                                                </div>
                                            <!-- Notes -->                   
                                                <div class="form-group row">
                                                    <?php InputElementNotes (); ?>
                                                </div>
                                            <!-- </div> --> <!-- /.row -->
                                        </div>  <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="submit" name="add-transaction-submit" id="add-transaction-submit" class="btn btn-success">Add</button>
                                            <a class="btn btn-secondary float-end" href="bu_manage_prefills.php">Cancel</a>
                                        </div>
                                    </form>
                                </div>    <!-- /.card -->
                            </div>    <!-- /.col -->
                        <!-- </div> --> <!-- /.row -->
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