<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Add Tax Year";
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
                                <?php BreadCrumb($page_name, $parent = array('title' => 'Manage Tax Years', 'url' => 'bu_manage_tax_years.php')); ?>
                            </div>
                        </div>
                    </div>    <!-- /.container-fluid -->
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
                                    <form id="add-tax-year" class="add-form" method="post" enctype="multipart/form-data" role="form">
                                        <div class="card-body">
                                            <?php
                                                    // Get the period and end date from the latest record.
                                                    $stmt = $pdo->prepare("
                                                        SELECT 
                                                            bu_tax_years.`tax_year_start`,
                                                            bu_tax_years.`tax_year_end`,
                                                            bu_tax_years.`tax_year`
                                                        FROM
                                                            bu_tax_years
                                                        ORDER BY 
                                                            bu_tax_years.`tax_year` DESC
                                                        LIMIT 1;
                                                    ");
                                                    $stmt->execute(); 

                                                    $bu_tax_year = $stmt->fetch(PDO::FETCH_ASSOC);
                                                    $stmt = null;
                                                    //var_dump($bu_accounting_period);

                                                    if ($bu_tax_year) {
                                                        $next_start = new DateTime($bu_tax_year['tax_year_start']);
                                                        $next_start->modify('+1 year');
                                                        $next_end = new DateTime($bu_tax_year['tax_year_end']);
                                                        $next_end->modify('+1 year');

                                                        $next_start_year = (int)substr($bu_tax_year['tax_year'],0,4) + 1;       // `tax_year` is YYYY/YY fortmat e.g. '2024/25'
                                                        $next_end_year = (int)substr($bu_tax_year['tax_year'],-2) + 2001;       // `tax_year` is YYYY/YY fortmat e.g. '2024/25'

                                                    } else {
                                                        // Get settings data
                                                            $stmt = $pdo->prepare("
                                                                CALL 
                                                                    bu_settings_get_settings();
                                                            ");
                                                            $stmt->execute();

                                                            $bu_settings = $stmt->fetch(PDO::FETCH_ASSOC);
                                                            $stmt = null;

                                                            
                                                            $next_start_year = $bu_settings['first_tax_year_start'];
                                                            $next_start = new DateTime($next_start_year . '-04-06');
                                                            $next_end_year = (int)$bu_settings['first_tax_year_start'] + 1;
                                                            $next_end = new DateTime($next_end_year . '-04-05');

                                                    }



                                                   
                                                    //$next_start_year = ++$bu_tax_year['start_year'];
                                                    //$next_end_year = ++$bu_tax_year['end_year'];
                                                    //echo date('Y-m-d', strtotime($bu_accounting_period['end'] . ' +1 day'));
                                                ?>
                                            <!-- <div class="row"> -->
                                            <!-- Start Year --> 
                                                <div class="form-group row">
                                                    <label for="start-year" class="col-sm-2 col-form-label">Start Year</label>
                                                    <div class="col-sm-2">
                                                        <input type="number" min="<?php echo $next_start_year; ?>" step="1" name="start-year" id="start-year" class="form-control" required value="<?php echo $next_start_year; ?>" onKeyDown="return false">
                                                    </div>
                                                </div>
                                            <!-- End Year --> 
                                                <div class="form-group row">
                                                    <label for="end-year" class="col-sm-2 col-form-label">End Year</label>
                                                    <div class="col-sm-2">
                                                        <input type="number" min="<?php echo $next_end_year; ?>" step="1" name="end-year" id="end-year" class="form-control" readonly required value="<?php echo $next_end_year; ?>" onKeyDown="return false">
                                                    </div>
                                                </div>
                                            <!-- Tax Year --> 
                                                <div class="form-group row">
                                                    <label for="tax-year" class="col-sm-2 col-form-label">Tax Year</label>
                                                    <div class="col-sm-2">
                                                        <input type="text" name="tax-year" id="tax-year" class="form-control" readonly value="<?php echo $next_start_year .'/' . substr($next_end_year, -2); ?>">
                                                    </div>
                                                </div>
                                            <!-- Start --> 
                                                <div class="form-group row">
                                                    <label for="tax-year-start" class="col-sm-2 col-form-label">Start Date</label>
                                                    <div class="col-sm-2">
                                                        <input type="text" name="tax-year-start" id="tax-year-start" class="form-control" readonly value="<?php echo $next_start->format('Y-m-d'); ?>">
                                                    </div>
                                                </div>
                                            <!-- End --> 
                                                <div class="form-group row">
                                                    <label for="tax-year-end" class="col-sm-2 col-form-label">End Date</label>
                                                    <div class="col-sm-2">
                                                        <input type="text" name="tax-year-end" id="tax-year-end" class="form-control" readonly value="<?php echo $next_end->format('Y-m-d'); ?>">
                                                    </div>
                                                </div>                                            
                                            <!-- </div> --> <!-- /.row -->
                                        </div>  <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="submit" name="add-tax-year-submit" id="add-tax-year-submit" class="btn btn-success">Add</button>
                                            <a class="btn btn-secondary float-end" href="bu_manage_tax_years.php">Cancel</a>
                                        </div>
                                    </form>
                                </div>    <!-- /.card -->
                            </div>    <!-- /.container-fluid -->
                        <!-- </div> --> <!-- /.row -->
                    </div>
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