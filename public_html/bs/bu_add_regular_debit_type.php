<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Add Regular Debit Type";
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
                                <?php BreadCrumb($page_name, $parent = array('title' => 'Manage Regular Debit Types', 'url' => 'bu_manage_regular_debit_types.php')); ?>
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
                                <div class="card w-50 mx-auto"">
                                    <div class="card-header p-6">
                                        <h3 class="card-title"><?php echo $page_name; ?></h3>
                                    </div>
                                    <!-- form start -->
                                    <form id="add-regular-debit-type" class="add-form" method="post" enctype="multipart/form-data" role="form">
                                        <div class="card-body">
                                            <!-- <div class="row"> -->
                                            <!-- Type -->
                                                <?php
                                                    // Get the last account_id_alpha (I, J, K etc) used.
                                                    $stmt = $pdo->prepare("
                                                        SELECT 
                                                            bu_regular_debit_types.`type`
                                                        FROM
                                                            bu_regular_debit_types
                                                        ORDER BY 
                                                            bu_regular_debit_types.`type` DESC          
                                                        LIMIT 1;
                                                    ");
                                                    $stmt->execute(); 

                                                    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                        $next_type = ++$row->type;  // Increment the last type.
                                                    }

                                                    $stmt = null;
                                                    
                                                ?>
                                                <div class="form-group row">
                                                    <label for="type" class="col-sm-2 col-form-label">Type</label>
                                                    <div class="col-sm-1">
                                                        <input type="text" name="type" id="type" class="form-control" readonly value="<?php echo $next_type; ?>">
                                                    </div>  
                                                </div>
                                            <!-- Description -->
                                                <div class="form-group row">
                                                    <label for="description" class="col-sm-2 col-form-label">Description</label>
                                                    <div class="col-sm-5">
                                                        <input type="text" name="description" id="description" class="form-control" required placeholder="Enter type description...">
                                                    </div>
                                                </div>
                                            <!-- </div> --> <!-- /.row -->
                                        </div>  <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="submit" name="add-regular-debit-type-submit" id="add-regular-debit-type-submit" class="btn btn-success">Add</button>
                                            <a class="btn btn-secondary float-end" href="bu_manage_regular_debit_types.php">Cancel</a>
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