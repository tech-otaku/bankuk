<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Add Transaction Sub-Type";
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
                                <?php BreadCrumb($page_name, $parent = array('title' => 'Manage Transaction Sub-Types', 'url' => 'bu_manage_transaction_sub_types.php')); ?>
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
                                    <form id="add-transaction-sub-type" class="add-form" method="post" enctype="multipart/form-data" role="form">
                                        <div class="card-body">
                                            <!-- <div class="row"> -->
                                            <!-- Sub-Type -->
                                                <div class="form-group row">
                                                    <label for="sub-type-id" class="col-sm-2 col-form-label">Type</label>
                                                    <div class="col-sm-1">
                                                        <input type="text" name="sub-type-id" id="sub-type-id" class="form-control" required placeholder="Sub-type...">
                                                    </div>
                                                </div>
                                            <!-- Description -->
                                                <div class="form-group row">
                                                    <label for="sub-type-description" class="col-sm-2 col-form-label">Description</label>
                                                    <div class="col-sm-5">
                                                        <input type="text" name="sub-type-description" id="sub-type-description" class="form-control" required placeholder="Sub-type description...">
                                                    </div>
                                                </div>
                                            <!-- </div> --> <!-- /.row -->
                                        </div>  <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="submit" name="add-transaction-sub-type" class="btn btn-success">Add</button>
                                            <a class="btn btn-secondary float-end" href="bu_manage_transaction_sub_types.php">Cancel</a>
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