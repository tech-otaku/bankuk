<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Add Prefill";
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
                                    <li class="breadcrumb-item"><a href="bu_manage_prefills.php">Manage Pre-fills</a></li>
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
                                    <form id="add-prefill" class="add-form" method="post" enctype="multipart/form-data" role="form">
                                        <div class="card-body">
                                            <!-- <div class="row"> -->
                                            <!-- Entity -->
                                                <div class="form-group row">
                                                    <label for="entity-description" class="col-sm-2 col-form-label">Entity</label>
                                                    <div class="col-sm-6">
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
                                            <!-- Account Name -->
                                                <div class="form-group row">
                                                    <label for="account-id-alpha" class="col-sm-2 col-form-label">Account Name</label>
                                                    <div class="col-sm-6">
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
                                                                echo '<option value="' . $row->account_id_alpha . '">' . $row->_name .' - ' . $row->account_number . ' ['. $row->account_id_alpha . ']' . ($row->status === 'Closed' ? ' CLOSED' : '') . '</option>';
                                                            }
                                                            echo '</select>';

                                                            

                                                            $stmt = null;
                                                        ?>
                                                    </div>
                                                </div>
                                            <!-- Type -->
                                                <div class="form-group row">
                                                    <label for="type" class="col-sm-2 col-form-label">Type</label>
                                                    <div class="col-sm-4">
                                                        <?php
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
                                            <!-- Sub Type -->
                                                <div class="form-group row">
                                                    <label for="sub-type" class="col-sm-2 col-form-label">Sub-Type</label>
                                                    <div class="col-sm-4">
                                                        <?php
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
                                            <!-- </div> --> <!-- /.row -->
                                        </div>  <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="submit" name="add-transaction-submit" id="add-transaction-submit" class="btn btn-success">Add</button>
                                            <a class="btn btn-secondary float-right" href="bu_manage_prefills.php">Cancel</a>
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
                // Something here
            });
        </script>
    </body>
</html>