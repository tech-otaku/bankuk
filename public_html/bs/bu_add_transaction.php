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
                                <div class="card w-50 mx-auto">
                                    <div class="card-header p-6">
                                    <!-- Pre-fill -->
                                        <div class="col-sm-4 float-right">
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
                                                            e1.entity_description
                                                        FROM
                                                            bu_prefills AS pf1
                                                        LEFT JOIN
                                                            bu_entities AS e1 ON pf1.entity_id = e1.entity_id
                                                        ORDER BY 
                                                            e1.entity_description ASC
                                                    ");
                                                    $stmt->execute();

                                                    echo '<select name="prefill" id="prefill" class="form-control">';
                                                    echo '<option value="" selected disabled hidden>Pre-fill (optional)...</option>';
                                                    echo '<option value="clear" data-account-id-alpha="" data-type="" data-entity-description="">Clear</option>';
                                                    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                        echo '<option value="' . $row->entity_description . '" data-account-id-alpha="' . $row->account_id_alpha . '" data-type="' . $row->type . '" data-entity-id="' . $row->entity_id . '">' . $row->entity_description . '</option>';
                                                    }
                                                    echo '</select>';

                                                    $stmt = null;

                                                }

                                            ?>
                                        </div>
                                        <h3 class="card-title"><?php echo $page_name; ?></h3>
                                    </div>
                                    <!-- form start -->
                                    <form id="add-transaction" class="add-form" method="post" enctype="multipart/form-data" role="form">
                                        <div class="card-body">
                                        <!-- Account Name -->
                                            <div class="row">
                                                <div class="form-group row">
                                                <!-- Account Name -->
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
                                                                    '%'
                                                                ]
                                                            );
                                                            
                                                            echo '<select name="account-id-alpha" id="account-id-alpha" class="form-control" required>';
                                                            echo '<option value="" selected disabled hidden>Account name...</option>';
                                                            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                                echo '<option value="' . $row->account_id_alpha . '">' . $row->_name .' - ' . $row->account_number . ' ['. $row->account_id_alpha . ']' . ($row->status === 'Closed' ? ' CLOSED' : '') . '</option>';
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
                                                        <input type="number" name="amount" id="amount" class="form-control" step="0.01" size="20" required placeholder="Amount...">
                                                    </div>
                                                </div>
                                            <!-- Type -->
                                                <div class="form-group row">
                                                    <label for="type" class="col-sm-2 col-form-label">Type</label>
                                                    <div class="col-sm-3">
                                                        <?php
                                                            $stmt = $pdo->prepare("
                                                                CALL 
                                                                    bu_transaction_types_dropdown();
                                                            ");
                                                            $stmt->execute();

                                                            echo '<select name="type" id="type" class="form-control" required>';
                                                            echo '<option value="" selected disabled hidden>Type...</option>';
                                                            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                                echo '<option value="' . $row->type . '">' . $row->description . '</option>';
                                                            }
                                                            echo '</select>';

                                                            $stmt = null;
                                                            
                                                        ?>
                                                    </div>                                
                                                </div>
                                            <!-- Sub Type -->
                                                <div class="form-group row">
                                                    <label for="sub-type" class="col-sm-2 col-form-label">Sub-Type</label>
                                                    <div class="col-sm-3">
                                                        <?php
                                                            $stmt = $pdo->prepare("
                                                                CALL 
                                                                    bu_transaction_types_dropdown();
                                                            ");
                                                            $stmt->execute();

                                                            echo '<select name="sub-type" id="sub-type" class="form-control">';
                                                            echo '<option value="" selected disabled hidden>Sub-type...</option>';
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

                                                            echo '<select name="entity-id" id="entity-id" class="form-control" required>';
                                                            echo "<option value='' selected disabled hidden>Entity...</option>";
                                                            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                                echo '<option value="'.$row->entity_id.'">' . $row->entity_description .'</option>';
                                                            }
                                                            echo '</select>';
                                                            
                                                            $stmt = null;

                                                        ?>
                                                    </div>  
                                                </div>
                                            <!-- Date -->
                                                <div class="form-group row">
                                                    <label for="date" class="col-sm-2 col-form-label">Date</label>
                                                    <div class="col-sm-2">
                                                        <input type="text" name="date" required class="form-control" id="datepicker" required readonly placeholder="Select transaction date..." style="cursor:text; background:white;">
                                                    </div>
                                                </div>
                                            <!-- Notes -->                   
                                                <div class="form-group row">
                                                    <label for="notes" class="col-sm-2 col-form-label">Notes</label>
                                                    <div class="col-sm-8">
                                                        <textarea name="notes" id="notes" class="form-control" rows="5" placeholder="Notes..." style="resize: none;"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="submit" name="add-transaction-submit" id="add-transaction-submit" class="btn btn-success">Add</button>
                                            <a class="btn btn-secondary float-right" href="bu_manage_transactions.php">Cancel</a>
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