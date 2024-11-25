<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Add Bank";

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
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="bu_manage_banks.php">Manage Banks</a></li>
                                    <li class="breadcrumb-item active"><?php echo $page_name; ?></li>
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
                            <!-- left column -->
                            <div class="col-md-12">
                                <!-- general form elements -->
                                <div class="card">
                                    <div class="card-header p-6">
                                        <h3 class="card-title">Card Header</h3>
                                    </div>
                                    <!-- form start -->
                                    <form id="add-bank" class="add-form" method="post" enctype="multipart/form-data" role="form">
                                        <div class="card-body">
                                            <div class="row">
                                            <!-- Bank ID -->
                                                <div class="col-md-2 form-group">
                                                    <label for="bank-id">Bank ID</label>
                                                    <input type="text" name="bank-id" id="bank-id" class="form-control" readonly value="<?php echo "B" . str_pad(rand(0,9999), 4, "0", STR_PAD_LEFT); ?>">
                                                </div>
                                            <!-- Legal Name -->
                                                <div class="col-md-2 form-group">
                                                    <label for="legal-name">Legal Name</label>
                                                    <input type="text" name="legal-name" id="legal-name" class="form-control" required placeholder="Enter bank's legal name...">
                                                </div>
                                            <!-- Trading Name -->
                                                <div class="col-md-2 form-group">
                                                    <label for="trading-name">Trading Name</label>
                                                    <input type="text" name="trading-name" id="trading-name" class="form-control" required placeholder="Enter bank's trading name...">
                                                </div>
                                            </div>
                                            <div class="row">
                                            </div>
                                            <div class="row">
                                            </div>
                                            <!--
                                                <div class="form-group">
                                                    <label for="exampleInputFile">Staff Profile Picture</label>
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input type="file" name="profile_pic" class="custom-file-input" id="exampleInputFile">
                                                            <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                                        </div>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="">Upload</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                -->
                                        </div>
                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="submit" name="add-bank-submit" class="btn btn-success">Add</button>
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
                bsCustomFileInput.init();
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