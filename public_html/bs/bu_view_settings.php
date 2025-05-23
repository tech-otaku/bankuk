<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "View | Edit Settings";
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
                                <?php BreadCrumb($page_name); ?>
                            </div>
                        </div>
                    </div>
                    <!-- /.container-fluid -->
                </section>
                <!-- Main content -->
                <section class="content">
                    <?php
                    // Get the settings record to view/update. There is only one, so no id required
                        $stmt = $pdo->prepare("
                            CALL 
                                bu_settings_get_settings();
                        ");

                        $stmt->execute(); 

                        $bu_settings = $stmt->fetch(PDO::FETCH_ASSOC);
                        $stmt = null;
                    ?>
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
                                                <form id="update-settings" class="update-form" method="post" enctype="multipart/form-data" role="form">
                                                <!-- Record ID [Hidden] -->
                                                    <input type="hidden" name="record-id" id="record-id" value="<?php echo $bu_settings['id']; ?>">
                                                <!-- Current Period --> 
                                                    <div class="form-group row">
                                                        <label for="current-period" class="col-sm-2 col-form-label">Current Period</label>
                                                        <div class="col-sm-1">
                                                            <input type="text" name="current-period" id="current-period" class="form-control" readonly value="<?php echo $bu_settings['current_period']; ?>">
                                                        </div>
                                                    </div>
                                                <!-- Current Period Start --> 
                                                    <div class="form-group row">
                                                        <label for="current-start" class="col-sm-2 col-form-label">Current Period Start</label>
                                                        <div class="col-sm-1">
                                                            <input type="text" name="current-start" id="current-start" class="form-control" readonly value="<?php echo date('D d/m/Y', strtotime($bu_settings['current_start'])); ?>">
                                                        </div>
                                                    </div>
                                                <!-- Current Period End --> 
                                                    <div class="form-group row">
                                                        <label for="current-end" class="col-sm-2 col-form-label">Current Period End</label>
                                                        <div class="col-sm-1">
                                                            <input type="text" name="current-end" id="ccurrent-end" class="form-control" readonly value="<?php echo date('D d/m/Y', strtotime($bu_settings['current_end'])); ?>">
                                                        </div>
                                                    </div>
                                                <!-- First Tax Year Start --> 
                                                    <div class="form-group row">
                                                        <label for="first-tax-year-start" class="col-sm-2 col-form-label">First Tax Year Start</label>
                                                        <div class="col-sm-1">
                                                            <input type="text" name="first-tax-year-start" id="first-tax-year-start" class="form-control" readonly value="<?php echo $bu_settings['first_tax_year_start']; ?>">
                                                        </div>
                                                    </div>
                                                <!-- Number of Tax Years to generate --> 
                                                    <div class="form-group row">
                                                        <label for="number-of-tax-years" class="col-sm-2 col-form-label">Number of Tax Years to Generate</label>
                                                        <div class="col-sm-1">
                                                            <input type="text" name="number-of-tax-years" id="number-of-tax-years" class="form-control" readonly value="<?php echo $bu_settings['number_of_tax_years']; ?>">
                                                        </div>
                                                    </div>
                                                <!-- Reconcilliation First Period --> 
                                                    <div class="form-group row">
                                                        <label for="reconcilliation-first" class="col-sm-2 col-form-label">First Period for Reconcilliation</label>
                                                        <div class="col-sm-1">
                                                            <input type="text" name="reconcilliation-first" id="reconcilliation-first" class="form-control" required value="<?php echo $bu_settings['reconcilliation_first_period']; ?>">
                                                        </div>
                                                    </div>
                                                <!-- Reconcilliation Opening Balance --> 
                                                    <div class="form-group row">
                                                        <label for="reconcilliation-opening-balance" class="col-sm-2 col-form-label">Reconcilliation Opening Balance</label>
                                                        <div class="col-sm-1">
                                                            <input type="text" name="reconcilliation-opening-balance" id="reconcilliation-opening-balance" class="form-control" required value="<?php echo $bu_settings['reconcilliation_opening_balance']; ?>">
                                                        </div>
                                                    </div>
                                                <!-- Monthly Spend First Period --> 
                                                    <div class="form-group row">
                                                        <label for="monthly-spend-first" class="col-sm-2 col-form-label">First Period for Monthly Spend</label>
                                                        <div class="col-sm-1">
                                                            <input type="text" name="monthly-spend-first" id="monthly-spend-first" class="form-control" required value="<?php echo $bu_settings['monthly_spend_first_period']; ?>">
                                                        </div>
                                                    </div>
                                                <!-- Monthly Spend Opening Balance --> 
                                                    <div class="form-group row">
                                                        <label for="monthly-spend-opening-balance" class="col-sm-2 col-form-label">Monthly Spend Opening Balance</label>
                                                        <div class="col-sm-1">
                                                            <input type="text" name="monthly-spend-opening-balance" id="monthly-spend-opening-balance" class="form-control" required value="<?php echo $bu_settings['monthly_spend_opening_balance']; ?>">
                                                        </div>
                                                    </div>
                                                <!-- Form Submit -->
                                                    <div class="form-group row">
                                                        <div class="offset-sm-2 col-sm-10">
                                                            <button type="submit" name="update-settings-submit" id="update-settings-submit" class="btn btn-outline-success">Update Settings</button>
                                                            <a id="reset-settings" class="btn btn-success" href="#">Reset</a>
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
    <!-- AJAX Update -->
        <script src="ajax/bu_ajax_update.js"></script>
    <!-- AJAX Reset Settings -->
        <script src="ajax/bu_ajax_reset_settings.js"></script>
        <script>
            $(function() {
                // Something here
            });
        </script>
    </body>
</html>