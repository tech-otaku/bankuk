<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Add Accounting Period";    
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
                                    <li class="breadcrumb-item"><a href="bu_dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="bu_manage_accounting_periods.php">Manage Accounting Periods</a></li>
                                    <li class="breadcrumb-item active"><?php echo $page_name; ?></li>
                                </ol>
                            </div>
                        </div>
                    </div>    <!-- /.container-fluid -->
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
                                    <form id="add-accounting-period" class="add-form" method="post" enctype="multipart/form-data" role="form">
                                        <div class="card-body">
                                            <?php
                                                    // Get the period and end date from the latest record.
                                                    $stmt = $pdo->prepare("
                                                        SELECT 
                                                            period,
                                                            `start`,
                                                            `end`
                                                        FROM
                                                            bu_accounting_periods
                                                        ORDER BY 
                                                            period DESC
                                                        LIMIT 1;
                                                    ");
                                                    $stmt->execute(); 

                                                    $bu_accounting_period = $stmt->fetch(PDO::FETCH_ASSOC);
                                                    $stmt = null;
                                                    //var_dump($bu_accounting_period);
                                                    $next_period = ++$bu_accounting_period['period'];
                                                    //echo date('Y-m-d', strtotime($bu_accounting_period['end'] . ' +1 day'));


                                                    /*
                                                        - 'start' is normally the 25th of one month
                                                        - 'end' is normally the 24th of the following month

                                                        - 'start' can't be a Saturday (6) or Sunday (7)
                                                            - if 'start' is Saturday, then change 'start' to previous Friday ('start' -1 day) i.e. Saturday, 25th January 2025 -> Friday, 24th January 2025
                                                            - if 'start' is Sunday, then change 'start' to previous Friday ('start' -2 days) i.e. Sunday, 25th May 2025 -> Friday, 23th May 2025

                                                        - 'end' can't be a Friday (5) or a Saturday (6), but can be a Sunday (7)
                                                            - if 'end' is Friday, then change 'end' to previous Thursday ('start' -1 day) i.e. Friday, 24th January 2025 -> Thursday, 23rd January 2025
                                                            - if 'end' is Saturday, then change 'end' to previous Thursday ('start' -2 days) i.e. Saturday, 24th May 2025 -> Thursday, 22nd May 2025  
                                                    */

                                                    $next_start = new DateTime($bu_accounting_period['end']);
                                                    $next_start->modify('+1 day');

                                                    if ($next_start->format('N') == 6 ) {
                                                        $next_start->modify('-1 day');
                                                    } else if ($next_start->format('N') == 7 ) {
                                                        $next_start->modify('-2 day');
                                                    }

                                                    $temp_end = new DateTime($next_start->format('Y-m-d'));
                                                    //print_r($temp_end);
                                                    $temp_end->modify('+1 month');
                                                    //print_r($temp_end);
                                                    $temp_end_array = getdate($temp_end->getTimestamp());
                                                    var_dump(getdate($temp_end->getTimestamp()));

                                                    $next_end = new DateTime($temp_end_array['year'] . '-' . $temp_end_array['mon'] . '-24');


                            
                                                    
                                                    if ($next_end->format('N') == 5 ) {
                                                        $next_end->modify('-1 day');
                                                    } else if ($next_end->format('N') == 6 ) {
                                                        $next_end->modify('-2 day');
                                                    }
                                                    

                                                    
                                                    
                                                    //echo $next_end->getTimestamp();

                                                    //echo $next_end->format('N');

                                                    //$next_end = $next_end->format('Y-m-d');

                                                    //echo $next_start;
                                                    //echo gettype($next_start);
                                                    
                                                ?>
                                            <div class="row">
                                            <!-- Start --> 
                                                <div class="col-md-2 form-group">
                                                    <label for="start" class="col-sm-4 col-form-label">Start Date</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" name="start" id="start" class="form-control" required readonly style="cursor:text; background:white;">
                                                    </div>
                                                </div>
                                            <!-- End --> 
                                                <div class="col-md-2 form-group">
                                                    <label for="end" class="col-sm-4 col-form-label">End Date</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" name="end" id="end" class="form-control" required readonly style="cursor:text; background:white;">
                                                    </div>
                                                </div>
                                            <!-- Period --> 
                                                <div class="col-md-2 form-group">
                                                    <label for="period" class="col-sm-4 col-form-label">Period</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" name="period" id="period" class="form-control" required value="<?php echo $next_period; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                            </div>
                                            <div class="row">
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="submit" name="add-accounting-period-submit" class="btn btn-success">Add</button>
                                        </div>
                                    </form>
                                </div>    <!-- /.card -->
                            </div>    <!-- /.container-fluid -->
                        </div>
                    </div>
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
                //bsCustomFileInput.init();
            });
        </script>
    <!-- AJAX Add -->
        <script src="ajax/bu_ajax_add.js"></script>
    <!-- Page Script -->
        <script>
            $(function() {
            // DatePicker for Period Start
                var startDate = '<?php echo $next_start->format('Y-m-d'); ?>';
                console.log(startDate)
                                
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
                var endDate = '<?php echo $next_end->format('Y-m-d'); ?>';
                                
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