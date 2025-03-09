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
                                <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="bu_dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="bu_manage_regular_debits.php">Manage Regular Debits</a></li>
                                    <li class="breadcrumb-item active"><?php echo $page_name; ?></li>
                                </ol>
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
                                                    <label for="account-id-alpha" class="col-sm-2 col-form-label">Account Name</label>
                                                    <div class="col-sm-5">
                                                        <?php
                                                            // This stored procedure uses a WHERE clause to select rows whose `status` column is equal to a specific value. This value is passed as a parameter to the procedure: 'open', 'closed' or '%' = ALL
                                                            $stmt = $pdo->prepare("
                                                                CALL 
                                                                    bu_accounts_dropdown(?);
                                                            ");
                                                            $stmt->execute(
                                                                [
                                                                    'open'
                                                                ]
                                                            );
                                                            
                                                            echo '<select name="account-id-alpha" id="account-id-alpha" class="form-control" required>';
                                                            echo '<option value="" selected disabled hidden>Account name...</option>';
                                                            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                                echo '<option value="' . $row->account_id_alpha . '">' . $row->_name .' - ' . $row->account_number . ' ['. $row->account_id_alpha . ']' . '</option>';
                                                            }
                                                            echo '</select>';

                                                            $stmt = null;

                                                        ?>
                                                    </div>
                                                </div>
                                            <!-- Amount -->
                                                <div class="form-group row">
                                                    <label for="amount" class="col-sm-2 col-form-label">Amount</label>
                                                    <div class="col-sm-2">
                                                        <!-- <input type="text" name="amount" id="amount" class="form-control" required placeholder="Transaction amount..."> -->
                                                        <input type="number" name="amount" id="amount" class="form-control" step=".01" size="20" required placeholder="Amount...">
                                                    </div>
                                                </div>
                                            <!-- Type -->
                                                <div class="form-group row">
                                                    <label for="type" class="col-sm-2 col-form-label">Type</label>
                                                    <div class="col-sm-3">
                                                        <?php
                                                            //$ret = "SELECT type, description FROM bu_transaction_types ORDER BY description ASC;";
                                                            $stmt = $pdo->prepare("
                                                                CALL 
                                                                    bu_transaction_types_dropdown();
                                                            ");
                                                            $stmt->execute();
                                                                                                            
                                                            echo '<select name="type" id="type" class="form-control" required>';
                                                            echo '<option value="" selected disabled hidden>Transaction type...</option>';
                                                            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                                echo '<option value="' . $row->type . '">' . $row->description . '</option>';
                                                            }
                                                            echo '</select>';

                                                            $stmt = null;
                                                            
                                                        ?>
                                                    </div>
                                                </div>
                                            <!-- Sub-Type -->
                                                <div class="form-group row">
                                                    <label for="sub-type" class="col-sm-2 col-form-label">Sub-Type</label>
                                                    <div class="col-sm-3">
                                                        <?php
                                                            //$ret = "SELECT type, description FROM bu_transaction_types ORDER BY description ASC;";
                                                            $stmt = $pdo->prepare("
                                                                CALL 
                                                                    bu_transaction_types_dropdown();
                                                            ");
                                                            $stmt->execute();
                                                            
                                                            echo '<select name="sub-type" id="sub-type" class="form-control">';
                                                            echo '<option value="" selected disabled hidden>Transaction sub-type...</option>';
                                                            echo '<option value="">&nbsp;</option>';
                                                            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                                echo '<option value="' . $row->type . '">' . $row->description . '</option>';
                                                            }
                                                            echo '</select>';

                                                            $stmt = null;
                                                            
                                                        ?>
                                                    </div>
                                                </div>
                                            <!-- Entity -->
                                                <div class="form-group row">
                                                    <label for="entity-description" class="col-sm-2 col-form-label">Entity</label>
                                                    <div class="col-sm-5">
                                                        <?php

                                                            $stmt = $pdo->prepare("
                                                            CALL 
                                                                bu_entities_dropdown();
                                                            ");
                                                            $stmt->execute();

                                                            echo '<select name="entity-id" name="entity-id" class="form-control" required>';
                                                            echo "<option value='' selected disabled hidden>Entity...</option>";
                                                            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                                echo '<option value="'.$row->entity_id.'">' . $row->entity_description .'</option>';
                                                            }
                                                            echo '</select>';
                                                            
                                                            $stmt = null;
                                                            
                                                        ?>
                                                    </div>
                                                </div>
                                            <!-- Regular Debit Type -->
                                                <div class="form-group row">
                                                    <label for="entity-description" class="col-sm-2 col-form-label" required>Regular Debit Type</label>
                                                    <div class="col-sm-3">
                                                        <?php

                                                            $stmt = $pdo->prepare("
                                                            CALL 
                                                                bu_regular_debit_types_dropdown();
                                                            ");
                                                            $stmt->execute();

                                                            echo '<select name="regular-debit-type" name="regular-debit-type" class="form-control" required>';
                                                            echo "<option value='' selected disabled hidden>Regular debit type...</option>";
                                                            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                                echo '<option value="'.$row->type.'">' . $row->description .'</option>';
                                                            }
                                                            echo '</select>';
                                                            
                                                            $stmt = null;

                                                        ?>
                                                    </div>
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
                                                    <label for="notes" class="col-sm-2 col-form-label">Notes</label>
                                                    <div class="col-sm-8">
                                                        <textarea name="notes" id="notes" class="form-control" rows="5" placeholder="Notes..." style="resize: none;"></textarea>
                                                    </div>
                                                </div>
                                            <!-- </div> -->
                                        </div>  <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="submit" name="add-regular-debit" class="btn btn-success">Add</button>
                                            <a class="btn btn-secondary float-right" href="bu_manage_regular_debits.php">Cancel</a>
                                        </div>
                                    </form>
                                </div>    <!-- /.card -->
                            </div>    <!-- /.col -->
                        <!-- </div> -->
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