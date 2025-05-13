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
        <!-- Page Content -->
            <div class="content-wrapper ">
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1><?php echo $page_name; ?></h1>
                            </div>
                            <div class="col-sm-6">
                                <?php BreadCrumb($page_name, $parent = array('title' => 'Manage Transactions', 'url' => 'bu_manage_transactions.php')); ?>
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
                                    <!-- Pre-fill -->
                                        <div class="col-sm-4 float-end">
                                            <?php
                                                // Count the number of rows (records) in the bu_prefills table
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
                                                            bu_prefills.`name`,
                                                            bu_prefills.`account_id_alpha`,
                                                            bu_prefills.`entity_id`,
                                                            bu_entities.`entity_description`,
                                                            bu_prefills.`type_id`,
                                                            bu_prefills.`sub_type_id`,
                                                            bu_prefills.`method_id`,
                                                            bu_prefills.`notes`
                                                        FROM
                                                            bu_prefills
                                                        LEFT JOIN
                                                            bu_entities ON bu_prefills.`entity_id` = bu_entities.`entity_id`
                                                        ORDER BY 
                                                            bu_entities.`entity_description` ASC
                                                    ");
                                                    $stmt->execute();

                                                    echo '<select name="prefill" id="prefill" class="form-control">';
                                                    echo '<option value="" selected disabled hidden>Pre-fill (optional)...</option>';
                                                    echo '<option value="clear" data-account-id-alpha="" data-type="" data-entity-description="">Clear</option>';
                                                    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                                        echo '<option value="' . $row->entity_description . '" data-account-id-alpha="' . $row->account_id_alpha . '" data-type-id="' . $row->type_id . '" data-sub-type-id="' . $row->sub_type_id . '" data-entity-id="' . $row->entity_id . '" data-method-id="' . $row->method_id . '" data-notes="' . $row->notes. '">' . $row->entity_description . '</option>';
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
                                            <!-- <div class="row"> -->
                                            <!-- Account Name -->
                                                <div class="form-group row">
                                                    <?php InputElementAccountData ($pdo); ?>
                                                </div>
                                            <!-- Amount -->
                                                <div class="form-group row">
                                                    <?php InputElementTransactionAmount (); ?>
                                                </div>
                                            <!-- Type -->
                                                <div class="form-group row">
                                                    <?php InputElementTransactionType ($pdo); ?>                                                
                                                </div>
                                            <!-- Sub Type -->
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
                                            <!-- Date -->
                                                <div class="form-group row">
                                                    <label for="transaction-date" class="col-sm-2 col-form-label">Date</label>
                                                    <div class="col-sm-2">
                                                        <input type="text" name="transaction-date" id="transaction-date" class="form-control" required readonly placeholder="Select transaction date..." style="cursor:text; background:white;">
                                                    </div>
                                                </div>
                                            <!-- Notes -->                   
                                                <div class="form-group row">
                                                    <?php InputElementNotes (); ?>
                                                </div>
                                            <!-- Create Pre-fill from transaction -->                   
                                                <div class="form-group row">
                                                    <div class="col-sm-9">
                                                        <input class="form-check-input" type="checkbox" value="" name="create-prefill" id="create-prefill">
                                                        <label class="form-check-label" for="create-prefill">Create Pre-fill from transaction</label>
                                                    </div>
                                                </div>

                                            <!-- </div> --> <!-- /.row -->
                                        </div>  <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="submit" name="add-transaction-submit" id="add-transaction-submit" class="btn btn-success">Add</button>
                                            <a class="btn btn-secondary float-end" href="bu_manage_transactions.php">Cancel</a>
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
                                
                $( "#transaction-date" ).datepicker({
                    dateFormat: "yy-mm-dd",
                    firstDay: 1
                });

                $( "#transaction-date" ).datepicker("setDate", transactionDate);

                $( "#add-transaction" ).on( "submit", function( event ) {
                    //alert( "Handler for `submit` called." );
                    //event.preventDefault();
                });

            });
        </script>
    </body>
</html>