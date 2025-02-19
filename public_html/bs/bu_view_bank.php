<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];    $page_name = "View | Edit Bank";

    // Get the bank record to view/update
    $stmt = $pdo->prepare("
        SELECT 
            * 
        FROM 
            bu_banks 
        WHERE 
            id = ?;
    ");

    $stmt->execute(
        [
            $_GET['id']
        ]
    );

    $bu_bank = $stmt->fetch(PDO::FETCH_ASSOC);
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
                                    <li class="breadcrumb-item"><a href="bu_manage_banks.php">Manage Banks</a></li>
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
                                    <?php echo '<span class="record-id">Record ID</span> ' . $bu_bank['id']; ?>
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
                                                <form id="update-bank" class="update-form" method="post" enctype="multipart/form-data" role="form">
                                                <!-- Record ID [Hidden] -->
                                                    <input type="hidden" name="record-id" id="record-id" value="<?php echo $bu_bank['id']; ?>">
                                                <!-- Bank ID --> 
                                                    <div class="form-group row">
                                                        <label for="bank-id" class="col-sm-2 col-form-label">Bank ID</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="bank-id" id="bank-id" class="form-control" readonly value="<?php echo $bu_bank['bank_id']; ?>">
                                                        </div>
                                                    </div>
                                                <!-- Legal Name -->
                                                    <div class="form-group row">
                                                        <label for="legal-name" class="col-sm-2 col-form-label">Legal Name</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="legal-name" id="legal-name" class="form-control" required value="<?php echo $bu_bank['legal_name']; ?>">
                                                        </div>
                                                    </div>
                                                <!-- Legal Name -->
                                                    <div class="form-group row">
                                                        <label for="trading-name" class="col-sm-2 col-form-label">Common Name</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="trading-name" id="trading-name" class="form-control" required value="<?php echo $bu_bank['trading_name']; ?>">
                                                        </div>
                                                    </div>
                                                <!-- Form Submit -->
                                                    <div class="form-group row">
                                                        <div class="offset-sm-2 col-sm-10">
                                                            <button type="submit" name="update-bank-submit" id="update-bank-submit" class="btn btn-outline-success">Update Bank</button>
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