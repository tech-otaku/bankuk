<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Add Transaction";

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
                                    <li class="breadcrumb-item"><a href="bu_manage_transactions.php">Manage Transactions</a></li>
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
                                        <h3 class="card-title">
                                            <!--
                                            <a class="btn btn-outline-primary btn-sm prefill-supermarket">Supermarket</a>
                                            -->
                                            <!-- <select id="prefill" class="form-select form-select-sm" aria-label=".form-select-sm example"> -->
                                            <?php

                                                $stmt = $pdo->prepare("
                                                    SELECT 
                                                        COUNT(
                                                            *
                                                        )
                                                    FROM
                                                        bu_prefills
                                                ");
                                                $stmt->execute();
                                                $nRows = $stmt->fetchColumn();
                                                $stmt = null;
                                                //echo $nRows;

                                                if ($nRows > 0) {

                                                    $stmt = $pdo->prepare("
                                                        SELECT 
                                                            pf1.account_id_alpha,
                                                            pf1.entity_id,
                                                            pf1.type,
                                                            e1.entity_name
                                                        FROM
                                                            bu_prefills AS pf1
                                                        LEFT JOIN
                                                            bu_entities AS e1 ON pf1.entity_id = e1.entity_id
                                                        ORDER BY 
                                                            e1.entity_name ASC
                                                    ");
                                                    $stmt->execute();

                                                    echo '<select name="prefill" id="prefill" class="form-control">';
                                                    echo '<option value="" selected disabled hidden>Pre-fill (optional)...</option>';
                                                    echo '<option value="clear" data-account-id-alpha="" data-type="" data-entity-name="">Clear</option>';
                                                    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                        echo '<option value="' . $row->entity_name . '" data-account-id-alpha="' . $row->account_id_alpha . '" data-type="' . $row->type . '" data-entity-id="' . $row->entity_id . '">' . $row->entity_name . '</option>';
                                                    }
                                                    echo '</select>';

                                                    $stmt = null;

                                                }

                                            ?>
                                            <!--
                                            <select id="prefill" class="form-control">
                                                <option value="" selected disabled hidden>Pre-fill...</option>';
                                                <option value="co-op" data-type="5" data-entity-name="P5723">Co-op</option>
                                                <option value="dunelm" data-type="6" data-entity-name="P6038">Dunelm</option>
                                                <option value="national-lottery" data-type="6" data-entity-name="P0700">National Lottery</option>
                                                <option value="sainsburys" data-type="5" data-entity-name="P1280">Sainsbury's</option>
                                                <option value="tesco-express" data-type="5" data-entity-name="P0186">Tesco Express</option>
                                            </select>
                                            -->
                                        </h3>
                                    </div>
                                    <!-- form start -->
                                    <form id="add-transaction" class="add-form" method="post" enctype="multipart/form-data" role="form">
                                        <div class="card-body">
                                        <!-- Account Name -->
                                            <div class="row">
                                                <div class="col-md-2 form-group">
                                                <!-- Account Name -->
                                                    <label for="account-id-alpha">Account Name</label>
                                                    <?php
                                                        // This stored procedure uses a WHERE clause to select rows whose `status` column is equal to a specific value. This value is passed as a parameter to the procedure: 'open', 'closed' or '%' = ALL
                                                        $stmt = $pdo->prepare("
                                                            CALL 
                                                                bu_accounts_dropdown(?);
                                                        ");
                                                        $stmt->execute(
                                                            [
                                                                '%'
                                                            ]
                                                        );
                                                        
                                                        echo '<select name="account-id-alpha" id="account-id-alpha" class="form-control" required>';
                                                        echo '<option value="" selected disabled hidden>Select account...</option>';
                                                        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                            echo '<option value="' . $row->account_id_alpha . '">' . $row->_name .' - ' . $row->account_number . ' ['. $row->account_id_alpha . ']' . ($row->status === 'Closed' ? ' CLOSED' : '') . '</option>';
                                                        }
                                                        echo '</select>';

                                                        

                                                        $stmt = null;
                                                    ?>
                                                </div>
                                            <!-- Amount -->
                                                <div class="col-md-2 form-group">
                                                    <label for="amount">Amount</label>
                                                    <input type="text" name="amount" id="amount" class="form-control" required placeholder="Enter transaction amount...">
                                                </div>
                                            <!-- Type -->
                                                <div class="col-md-2 form-group">
                                                    <label for="type">Type</label>
                                                    <?php
                                                        $stmt = $pdo->prepare("
                                                            CALL 
                                                                bu_transaction_types_dropdown();
                                                        ");
                                                        $stmt->execute();

                                                        echo '<select name="type" id="type" class="form-control" required>';
                                                        echo '<option value="" selected disabled hidden>Select transaction type...</option>';
                                                        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                            echo '<option value="' . $row->type . '">' . $row->description . '</option>';
                                                        }
                                                        echo '</select>';

                                                        $stmt = null;
                                                        
                                                    ?>                                            
                                                </div>
                                            <!-- Sub Type -->
                                                <div class="col-md-2 form-group">
                                                    <label for="sub-type">Sub-Type</label>
                                                    <?php
                                                        $stmt = $pdo->prepare("
                                                            CALL 
                                                                bu_transaction_types_dropdown();
                                                        ");
                                                        $stmt->execute();

                                                        echo '<select name="sub-type" id="sub-type" class="form-control">';
                                                        echo '<option value="" selected disabled hidden>Select transaction sub-type...</option>';
                                                        echo '<option value="">&nbsp;</option>';
                                                        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                            echo '<option value="' . $row->type . '">' . $row->description . '</option>';
                                                        }
                                                        echo '</select>';
                                                        
                                                        $stmt = null;

                                                    ?>
                                                </div>
                                            <!-- Entity -->
                                                <div class="col-md-2 form-group">
                                                    <label for="entity-name">Entity</label>
                                                    <?php
                                                        $stmt = $pdo->prepare("
                                                            CALL 
                                                                bu_entities_dropdown();
                                                        ");
                                                        $stmt->execute();

                                                        echo '<select name="entity-id" id="entity-id" class="form-control" required>';
                                                        echo "<option value='' selected disabled hidden>Select entity...</option>";
                                                        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                            echo '<option value="'.$row->entity_id.'">' . $row->entity_name .'</option>';
                                                        }
                                                        echo '</select>';
                                                        
                                                        $stmt = null;

                                                    ?>    
                                                </div>
                                            <!-- Date -->
                                                <div class="col-md-2 form-group">
                                                    <label for="date">Date</label>
                                                    <input type="text" name="date" required class="form-control" id="datepicker" required readonly placeholder="Select transaction date..." style="cursor:text; background:white;">
                                                </div>
                                            </div>

                                            <div class="row">
                                            </div>
                                            <div class="row">
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 form-group">
                                                    <label for="notes">Notes</label>
                                                    <textarea name="notes" id="notes" class="form-control" placeholder="Enter note..."></textarea>
                                                </div>
                                            </div>

                                        </div>
                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="submit" name="add-transaction-submit" id="add-transaction-submit" class="btn btn-success">Add</button>
                                            
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
                //bsCustomFileInput.init();
            });
        </script>
    <!-- AJAX Add -->
        <script src="ajax/bu_ajax_add.js"></script>
    <!-- Page Script -->
        <script>
            $(function() {

                const date = new Date();    // Today's date
                transactionDate = date.getFullYear() + '-' + (date.getMonth() + 1)  + '-' + date.getDate()  // Date must be in YYYY-MM-DD format
                                
                $( "#datepicker" ).datepicker({
                    dateFormat: "yy-mm-dd",
                    firstDay: 1
                });

                $( "#datepicker" ).datepicker("setDate", transactionDate);

                $( "#add-transaction" ).on( "submit", function( event ) {
                    //alert( "Handler for `submit` called." );
                    //event.preventDefault();
                });

            });
        </script>
    </body>
</html>