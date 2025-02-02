<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "View | Edit Accounting Period";

    // Get the accounting period record to view/update
    $stmt = $pdo->prepare("
        SELECT 
            * 
        FROM 
            bu_accounting_periods 
        WHERE 
            id = ?;"
    );

    $stmt->execute(
        [
            $_GET['id']
        ]
    );

    $bu_accounting_period = $stmt->fetch(PDO::FETCH_ASSOC);
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
                                    <li class="breadcrumb-item"><a href="bu_manage_accounting_periods.php">Manage Accounting Periods</a></li>
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
                                        <?php echo '<span class="record-id">Record ID</span> ' . $bu_accounting_period['id']; ?>
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
                                                <form id="update-accounting-period" class="update-form" method="post" enctype="multipart/form-data" role="form">
                                                <!-- Record ID [Hidden] -->
                                                    <input type="hidden" name="record-id" id="record-id" value="<?php echo $bu_accounting_period['id']; ?>">
                                                <!-- Start --> 
                                                    <div class="form-group row">
                                                        <label for="start" class="col-sm-2 col-form-label">Start Date</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="start" id="start" class="form-control" required readonly style="cursor:text; background:white;" value="<?php echo $bu_accounting_period['start']; ?>">
                                                        </div>
                                                    </div>
                                                <!-- End --> 
                                                <div class="form-group row">
                                                        <label for="end" class="col-sm-2 col-form-label">End Date</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="end" id="end" class="form-control" required readonly style="cursor:text; background:white;" value="<?php echo $bu_accounting_period['end']; ?>">
                                                        </div>
                                                    </div>
                                                <!-- Period --> 
                                                    <div class="form-group row">
                                                        <label for="period" class="col-sm-2 col-form-label">Period</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="period" id="period" class="form-control" required value="<?php echo $bu_accounting_period['period']; ?>">
                                                        </div>
                                                    </div>
                                                <!-- Form Submit -->
                                                    <div class="form-group row">
                                                        <div class="offset-sm-2 col-sm-10">
                                                            <button type="submit" name="update-accounting-period-submit" id="update-accounting-period-submit" class="btn btn-outline-success">Update Accounting Period</button>
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
            // DatePicker for Period Start
                var startDate = '<?php echo $bu_accounting_period['start']; ?>';
                //console.log(startDate)
                //startDate = startDate.substr(10,4) + '-' + startDate.substr(7,2)  + '-' + startDate.substr(4,2) // Date must be in YYYY-MM-DD format
                                
                $( "#start" ).datepicker({
                    dateFormat: "yy-mm-dd",
                    firstDay: 1,
                    maxDate: new Date('2030-12-31'),
                    beforeShowDay: function (date) {     // See https://stackoverflow.com/a/13514816/2518495
                        return excludedDates(
                            date, [
                                [6],    // Saturday
                                [0]     // Sunday
                            ]
                        );
                    }
                });

                $("#start").datepicker("setDate", startDate);

            // DatePicker for Period End
                var endDate = '<?php echo $bu_accounting_period['end']; ?>';
                //console.log(endDate)
                //endDate = endDate.substr(10,4) + '-' + endDate.substr(7,2)  + '-' + endDate.substr(4,2) // Date must be in YYYY-MM-DD format
                                
                $( "#end" ).datepicker({
                    dateFormat: "yy-mm-dd",
                    firstDay: 1,                    
                    beforeShowDay: function (date){     // See https://stackoverflow.com/a/13514816/2518495
                        return excludedDates(
                            date, [
                                [5],    // Friday
                                [6]     // Saturday
                            ]
                        );
                    }
                });


                $( "#end" ).datepicker("setDate", endDate);
            });
        </script>
    </body>
</html>