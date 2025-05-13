<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Manage Pre-fills";
    
    // NOTE: $pdo is an instance of a pdo() object declared in conf/pdoconfig.php

// Get settings data
    $stmt = $pdo->prepare("
    CALL 
        bu_settings_get_settings();
    ");
    $stmt->execute();

    $bu_settings = $stmt->fetch(PDO::FETCH_ASSOC);
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
            <div class="content-wrapper">   <!-- Was .content-wrapper -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col">
                                <h1>
                                    <?php echo $page_name; ?>
                                    <!--
                                    &nbsp;
                                    [
                                        <span class="text-grey">Filter</span>
                                        &nbsp;
                                        <span class="all-total currency"></span>
                                    ]
                                    -->
                                </h1>
                            </div>
                            <div class="col-5">
                                <h1 class="card-title">
                                </h1>
                            </div>
                            <div class="col">
                                <?php BreadCrumb($page_name); ?>
                            </div>
                        </div>
                    </div>
                    <!-- /.container-fluid -->
                </section>
                <!-- Main content -->
                <section class="content">
                
                    <div class="row">
                        <div class="col-12">
                            <div class="card w-75 mx-auto">
                                <div class="card-header p-6">
                                    <a class="btn btn-success" href="bu_add_prefill.php">Add Pre-fill</a>
                                    <button type="button" id="clear-filters" class="btn btn-warning btn-sm float-end d-none">Clear Filters</a>  <!-- `d-none` Bootstrap class initially hides the button -->
                                </div>
                                
                                <div class="card-body">
                                    <table id="prefills" class="table table-hover table-bordered table-striped bu-data-table">
                                        <thead>
                                            <tr>
                                                <th class="text-left">#</th>
                                                <!-- <th>Name</th> -->
                                                <th>Entity</th>                                                
                                                <th>Account Details</th>
                                                <th>Type</th>
                                                <th>Sub-type</th>
                                                <th>Method</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tfoot class="place-below-table-header">    <!-- The `place-below-table-header` class uses `display: table-header-group` to place the footer immediately below the table header before the table body -->
                                            <tr>
                                                <th></th>
                                                <!-- <th></th> -->
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <?php
                                                $counter = 1;

                                                $stmt = $pdo->prepare("
                                                    SELECT
                                                        bu_prefills.`id`,
                                                        -- bu_prefills.`name`,
                                                        bu_prefills.`account_id_alpha`,
                                                        bu_prefills.`notes`,
                                                        bu_banks.`trading_name`,
                                                        bu_accounts.`name` AS _account_name,
                                                        bu_accounts.`sort_code`,
                                                        bu_accounts.`account_number`,
                                                        bu_accounts.`status`,
                                                        bu_transaction_types.`type_description`,
                                                        bu_transaction_sub_types.`sub_type_description`,
                                                        bu_transaction_methods.`method_description`,
                                                        bu_entities.`entity_description`
                                                    FROM
                                                        bu_prefills
                                                    LEFT JOIN
                                                        bu_accounts ON bu_prefills.`account_id_alpha` = bu_accounts.`account_id_alpha`
                                                    LEFT JOIN
                                                        bu_banks ON bu_accounts.`bank_id` = bu_banks.`bank_id`
                                                    LEFT JOIN
                                                        bu_entities ON bu_prefills.`entity_id` = bu_entities.`entity_id`
                                                    LEFT JOIN
                                                        bu_transaction_types ON bu_prefills.`type_id` = bu_transaction_types.`type_id`
                                                    LEFT JOIN
                                                        bu_transaction_sub_types ON bu_prefills.`sub_type_id` = bu_transaction_sub_types.`sub_type_id`
                                                    LEFT JOIN
                                                        bu_transaction_methods ON bu_prefills.`method_id` = bu_transaction_methods.`method_id`
                                                    ORDER BY 
                                                        bu_entities.`entity_description` ASC
                                                ");
                                                $stmt->execute();

                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                            ?>
                                            <tr>
                                                <!-- <td><?php //echo ((!empty($row->notes)) ? '<i class="fa-solid fa-book"></i> ' : "") . $counter; ?></td> -->
                                                <td <?php echo ((!empty($row->notes)) ? 'class="has-note right details-control" data-note="' . nl2br($row->notes) . '"'  : "") . '>' . $counter; ?></td>
                                                <!-- <td><?php //echo $row->name; ?></td> -->
                                                <td><?php echo $row->entity_description; ?></td>
                                                <td><?php echo $row->trading_name . ' ' . $row->_account_name . ' - ' . $row->account_number . ' ['. $row->account_id_alpha . ']' . ($row->status === 'Closed' ? ' CLOSED' : ''); ?></td>
                                                <!-- <td><?php //echo $fmt_currency->formatCurrency($row->amount, "GBP"); ?></td> -->
                                                <td><?php echo $row->type_description; ?></td>
                                                <td><?php echo $row->sub_type_description; ?></td>
                                                <td><?php echo $row->method_description; ?></td>
                                                <td style="text-align: center">
                                                    <a class="btn btn-success btn-sm view-record" href="#" data-bs-toggle="modal" data-bs-target="#update-prefill-modal" data-mysql-table="bu_prefills" data-record-id="<?php echo $row->id; ?>">
                                                        <i class="fa fa-edit"></i>
                                                    </a>

                                                    <a data-mysql-table="bu_prefills" data-record-id="<?php echo $row->id; ?>" data-record-type="pre-fill" data-record-identifier="<?php echo $row->entity_description; ?>"  class="btn btn-danger btn-sm delete-record" href="#">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php 
                                                    $counter++;
                                                } // while
                                                
                                                $stmt = null;
                                            ?>
                                        <tfoot>
                                            <tr>
                                            </tr>
                                        </tfoot>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <a class="btn btn-success" href="bu_add_prefill.php">Add Pre-fill</a>
                                </div>
                            </div>  <!-- /.card -->
                        </div>  <!-- /.col -->
                    </div>  <!-- /.row -->
                </section>  <!-- /.content -->
            </div>  <!-- /.dummy -->
        <!-- Common Footer -->
            <?php include("partials/footer.php"); ?>
        </div>  <!-- ./wrapper -->
    <!-- Update Prefill Modal -->
        <div class="modal fade" id="update-prefill-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">   <!-- `.modal-dialog-centered` to centre on screen -->
                <div class="modal-content"  style="position: relative;">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">View | Update Pre-fill</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <!-- Inject the update bank form -->
                        <?php include("forms/form_update_prefill.php"); ?>
                    </div>
                    <div class="modal-footer justify-content-between">
                    <!-- Update-form's submit button -->
                        <button type="submit" form="update-prefill" name="update-prefill-submit" id="update-prefill-submit" class="btn btn-success">Update</button>
                    <!-- Update-modal's close button -->
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    <!-- Common Scripts -->
        <?php include("partials/scripts.php"); ?>
    <!-- DataTable Table -->
        <script>
            var prefills = new DataTable('#prefills', {
                stateSave: false,
                select: false,
                pageLength: 25,
                lengthMenu: [
                    25, 
                    50, 
                    100, 
                    {
                        label: 'All',
                        value: -1 
                    }
                ],
                fixedHeader: {
                    header: true,
                    footer: false
                    },
                columns: [
                    {   // Column Index 0
                        name: 'counter',
                        className: 'counter', 
                        searchable: false, 
                        width: '100px'
                    },
                    /*
                    {   // Column Index 1
                        name: 'prefill_name',
                        className: 'name', 
                        width: '500px'
                    },
                    */
                    {   // Column Index 1
                        name: 'entity',
                        className: 'entity', 
                        width: '500px'
                    }, 
                    {
                        name: 'account_details',
                        className: 'account-name', 
                        width: '300px'
                    }, 
                    {
                        name: 'type',
                        className: 'transaction-type', 
                        width: '175px'
                    },
                    {
                        name: 'sub_type',
                        className: 'transaction-sub-type', 
                        width: '175px'
                    }, 
                    {
                        name: 'method',
                        className: 'transaction-method', 
                        width: '175px'
                    },
                    {   
                        name: 'actions',
                        className: 'actions', 
                        width: '95px',
                        searchable: false, 
                        orderable: false
                    } 
                ],
            // Callbacks
                initComplete: function () {
                    CreateFilterDropdowns (this.api().columns(['account_details:name','type:name','sub_type:name','method:name']))                   
                },
                footerCallback: function (row, data, start, end, display) {
                },
                drawCallback: function (settings) {
                },
            });
        </script>
    <!-- AJAX Update -->
        <!-- <script src="ajax/bu_ajax_update_prefill.js"></script> -->
        <script src="ajax/bu_ajax_update_form.js"></script>
    <!-- Ajax Delete -->
        <script src="ajax/bu_ajax_delete.js"></script>
    <!-- Page Script -->
        <script>
            $(function() {
                // Something here
            });
        </script>
    </body>
</html>