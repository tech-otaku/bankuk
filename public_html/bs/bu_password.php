<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Change Password";

    // Get the settings record to view/update. There is only one, so no id required
    $stmt = $pdo->prepare("
        CALL 
            bu_settings_get_settings();
    ");

    $stmt->execute(); 

    $bu_settings = $stmt->fetch(PDO::FETCH_ASSOC);
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
                                    <!-- <li class="breadcrumb-item"><a href="bu_manage_entities.php">Manage Entities</a></li> -->
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
                                        <?php //echo '<span class="record-id">Record ID</span> ' . $bu_entity['id']; ?>
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
                                                <form id="update-password" class="update-form" method="post" enctype="multipart/form-data" role="form">
                                                <!-- Record ID [Hidden] -->
                                                    <input type="hidden" name="email" id="email" value="<?php echo $_SESSION['email']; ?>"> <!-- The email of the logged-in user. Set when user logs in. -->
                                                <!-- Current Password --> 
                                                    <div class="form-group row">
                                                        <label for="current-password" class="col-sm-2 col-form-label">Current Password</label>
                                                        <div class="col-sm-1">
                                                            <input type="password" name="current-password" id="current-password" class="form-control" required>
                                                        </div>
                                                    </div>
                                                <!-- New Password --> 
                                                    <div class="form-group row">
                                                        <label for="new-password" class="col-sm-2 col-form-label">New Password</label>
                                                        <div class="col-sm-1">
                                                            <input type="password" name="new-password" id="new-password" class="form-control" required>
                                                        </div>
                                                    </div>
                                                <!-- Confirm New Password --> 
                                                    <div class="form-group row">
                                                        <label for="new-password-confirm" class="col-sm-2 col-form-label">Confirm New Password</label>
                                                        <div class="col-sm-1">
                                                            <input type="password" name="new-password-confirm" id="new-password-confirm" class="form-control" required>
                                                        </div>
                                                    </div>
                                                <!-- Form Submit -->
                                                    <div class="form-group row">
                                                        <div class="offset-sm-2 col-sm-10">
                                                            <button type="submit" name="update-password-submit" id="update-password-submit" class="btn btn-outline-success">Change Password</button>
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
                                        Used by <?php// echo $_GET['used'] .' '. $_GET['record']; ?> records
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
    <!-- AJAX Update Password -->
        <script src="ajax/bu_ajax_password.js"></script>
        <script>
            $(function() {
                // Something here
            });
        </script>
    </body>
</html>