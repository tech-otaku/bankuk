<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Add Regular Debit";
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
                                <?php BreadCrumb($page_name, $parent = array('title' => 'Manage Regular Debits', 'url' => 'bu_manage_regular_debits.php')); ?>
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
                                    <form id="add-regular-debit" class="add-form" method="post" enctype="multipart/form-data" role="form">
                                        <div class="card-body">
                                            <!-- Account Name -->
                                            <!-- <div class="row"> -->
                                                <div class="form-group row">
                                                    <?php InputElementAccountData ($pdo, 2, 'open'); ?>
                                                </div>
                                            <!-- Amount -->
                                                <div class="form-group row">
                                                    <?php InputElementTransactionAmount (); ?>
                                                </div>
                                            <!-- Type -->
                                                <div class="form-group row">
                                                    <?php InputElementTransactionType ($pdo); ?>
                                                </div>
                                            <!-- Sub-Type -->
                                                <div class="form-group row">
                                                    <?php InputElementTransactionSubType ($pdo); ?>
                                                </div>
                                            <!-- Entity -->
                                                <div class="form-group row">
                                                    <?php InputElementEntity ($pdo); ?>
                                                </div>
                                            <!-- Method -->
                                                <div class="form-group row">
                                                    <?php InputElementTransactionMethod ($pdo); ?>
                                                </div>
                                            <!-- Day -->
                                                <div class="form-group row">
                                                    <label for="day" class="col-sm-2 col-form-label">Day</label>
                                                    <div class="col-sm-3">
                                                        <input type="number" name="day" id="day" class="form-control" min="1" max="31" step="1" size="20" value="1" required placeholder="Transaction day...">
                                                    </div>
                                                    <!-- <input type="text" name="day" required class="form-control" id="day" placeholder="Select transaction day..."> -->
                                                </div>
                                            <!-- Period -->
                                                <div class="form-group row">
                                                    <label for="period" class="col-sm-2 col-form-label">Period</label>
                                                    <div class="col-sm-1">
                                                        <input type="number" name="period" id="period" class="form-control" step="1" value="0" readonly>
                                                    </div>
                                                </div>
                                            <!-- Last -->
                                                <div class="form-group row">
                                                    <label for="last" class="col-sm-2 col-form-label">Last</label>
                                                    <div class="col-sm-2">
                                                        <input type="text" name="last" id="last" required class="form-control" readonly value="1970-01-01">
                                                    </div>
                                                </div>
                                            <!-- Next [Synonym for `First`] -->
                                                <div class="form-group row">
                                                    <label for="next" class="col-sm-2 col-form-label">Next [First]</label>
                                                    <div class="col-sm-3">
                                                        <input type="text" name="next" id="next" class="form-control" required readonly placeholder="Date of first transaction..." style="cursor:text; background:white;">
                                                    </div>
                                                </div>
                                            <!-- Notes -->    
                                                <div class="form-group row">
                                                    <?php InputElementNotes (); ?>
                                                </div>
                                            <!-- </div> --> <!-- /.row -->
                                        </div>  <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="submit" name="add-regular-debit" class="btn btn-success">Add</button>
                                            <a class="btn btn-secondary float-end" href="bu_manage_regular_debits.php">Cancel</a>
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

                const date = new Date();    // Today's date
                transactionDate = date.getFullYear() + '-' + (date.getMonth() + 1)  + '-' + date.getDate()  // Date must be in YYYY-MM-DD format
                                
                $( "#next" ).datepicker({
                    /*
                    beforeShow: function (
                        element,    // Represents the input field `div#datepicker` 
                        instance    // A JQuery object representing the current datepicker instance `div#ui-datepicker-div`
                    ) {
                        var placement = 0   // Represents the placement of the datepicker instance relative to the input field `div#datepicker`: 0 = to the right of, 1 = above and 2 = below
                        return DatePickerPlacement (
                            element,        
                            instance,       
                            placement        
                        )
                    },
                    */
                    beforeShowDay: function (date){     // See https://stackoverflow.com/a/13514816/2518495
                        return ExcludedDates(
                            date, [
                                [6],    // Saturday
                                [0]     // Sunday
                            ]
                        );
                    },
                    dateFormat: "yy-mm-dd",
                    firstDay: 1
                });

                $( "#datepicker" ).datepicker("setDate", transactionDate);

            });
        </script>
    </body>
</html>