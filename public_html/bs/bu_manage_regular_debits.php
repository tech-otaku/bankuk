<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Manage Regular Debits";

    function prepareDate($day) {

        $date = new DateTimeImmutable();
        $current_year = $date->format('Y');
        $current_month = $date->format('m');
        $new_date = new DateTime($current_year.'-'.$current_month.'-'.$day);
        //echo $new_date->format('Y-m-d');
        if ($new_date < $date) {
            $new_date->modify('+1 month');
        }
        
        return $new_date->format('Y-m-d');

        /*
        $today = date('Y-m-d');
        $current_month = $date
        echo date('Y-m');
        */
    }
    
    // NOTE: $pdo is an instance of a pdo() object declared in conf/pdoconfig.php
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
                            <div class="card">
                                <div class="card-header p-6">
                                <a class="btn btn-success" href="bu_add_regular_debit.php">Add Regular Debit</a>
                                <button type="button" id="clear-filters" class="btn btn-warning btn-sm float-end d-none">Clear Filters</a>  <!-- `d-none` Bootstrap class initially hides the button -->
                                </div>
                                <div class="card-body">
                                    <table id="regular-debits" class="table table-hover table-bordered table-striped bu-data-table">
                                    <thead>
                                            <tr>
                                                <th class="text-left">#</th>
                                                <th></th>
                                                <th>Account ID Alpha</th>
                                                <th>Account Details</th>
                                                <th>Amount</th>
                                                <th>Type</th>
                                                <th>Sub-type</th>
                                                <th>Method</th>
                                                <th>Entity</th>
                                                <th>Day</th>
                                                <th>Period</th>
                                                <th>Last</th>
                                                <th>Next</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tfoot class="place-below-table-header">    <!-- The `place-below-table-header` class uses `display: table-header-group` to place the footer immediately below the table header before the table body -->
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
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
                                                    bu_regular_debits.`id`,
                                                    bu_regular_debits.`account_id`,
                                                    bu_regular_debits.`account_id_alpha`,
                                                    bu_banks.`trading_name`,
                                                    bu_accounts.`name`,
                                                    bu_accounts.`sort_code`,
                                                    bu_accounts.`account_number`,
                                                    bu_accounts.`status`,
                                                    bu_regular_debits.`amount`,
                                                    bu_transaction_types.`type_description`,
                                                    bu_transaction_sub_types.`sub_type_description`,
                                                    bu_transaction_methods.`method_description`,
                                                    bu_entities.`entity_description`,
                                                    bu_regular_debits.`day`,
                                                    bu_regular_debits.`period`,
                                                    bu_regular_debits.`notes`,
                                                    bu_regular_debits.`regular_debit_type`,
                                                    bu_regular_debit_types.`description`,
                                                    bu_regular_debits.`last`,
                                                    bu_regular_debits.`next`
                                                FROM
                                                    bu_regular_debits
                                                LEFT JOIN
                                                    bu_accounts ON bu_regular_debits.`account_id_alpha` = bu_accounts.`account_id_alpha`
                                                LEFT JOIN
                                                    bu_banks ON bu_accounts.`bank_id` = bu_banks.`bank_id`
                                                LEFT JOIN
                                                    bu_entities ON bu_regular_debits.`entity_id` = bu_entities.`entity_id`
                                                LEFT JOIN
                                                    bu_transaction_types ON bu_regular_debits.`type_id` = bu_transaction_types.`type_id`
                                                LEFT JOIN
                                                    bu_transaction_sub_types ON bu_regular_debits.`sub_type_id` = bu_transaction_sub_types.`sub_type_id`
                                                LEFT JOIN
                                                    bu_transaction_methods ON bu_regular_debits.`method_id` = bu_transaction_methods.`method_id`
                                                LEFT JOIN
                                                    bu_regular_debit_types ON bu_regular_debits.`regular_debit_type` = bu_regular_debit_types.`type`
                                                ORDER BY 
                                                    bu_regular_debits.`day` DESC;
                                                ");
                                                $stmt->execute();

                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                            ?>
                                            <tr>
                                                <td <?php echo ((!empty($row->notes)) ? 'class="has-note right details-control" data-counter="' . $counter .'" data-note="' . nl2br($row->notes) . '"'  : "") . '>' . $counter; ?></td>
                                                <!-- <td><?php //echo $row->id; ?></td> -->
                                                <td style="text-align: center">
                                                    <a data-id="<?php echo $row->id; ?>" class="btn btn-primary btn-sm add-current-due-date" href="#">
                                                        <i class="fa fa-plus"></i>
                                                    </a>
                                                </td>
                                                <td><?php echo $row->account_id_alpha; ?></td>
                                                <td><?php echo $row->trading_name . ' ' . $row->name . ' - ' . $row->account_number . ' ['. $row->account_id_alpha . ']' . ($row->status === 'Closed' ? ' CLOSED' : ''); ?></td>
                                                <!-- <td><?php //echo $row->trading_name . ' ' . $row->name; ?></td> -->
                                                <td><?php echo $row->amount; ?></td>
                                                <td><?php echo $row->type_description; ?></td>
                                                <td><?php echo $row->sub_type_description; ?></td>
                                                <td><?php echo $row->method_description; ?></td>
                                                <td><?php echo $row->entity_description; ?></td>
                                                <!-- <td><?php //echo $row->description; ?></td> -->
                                                <td><?php echo $row->day; ?></td>
                                                <td><?php echo $row->period; ?></td>
                                                <td><?php echo $row->last; ?></td>  <!-- The display format is defined using a DataTable render -->
                                                <td><?php echo $row->next; ?></td>  <!-- The display format is defined using a DataTable render -->
                                                <!-- <td><?php //echo date_format(date_create($row->date), 'D d/m/Y'); ?></td> -->
                                                <!-- <td><?php //echo $row->period; ?></td> -->
                                                <td style="text-align: center">

                                                    <a class="btn btn-success btn-sm view-record" href="#" data-bs-toggle="modal" data-bs-target="#update-regular-debit-modal" data-mysql-table="bu_regular_debits" data-record-id="<?php echo $row->id; ?>">
                                                        <i class="fa fa-edit"></i>
                                                    </a>

                                                    <a data-mysql-table="bu_regular_debits" data-record-id="<?php echo $row->id; ?>" data-record-type="regular debit" data-record-identifier="<?php echo $row->entity_description; ?>" class="btn btn-danger btn-sm delete-record" href="#">
                                                        <i class="fa fa-trash"></i>
                                                        <!-- Delete -->
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php 
                                                    $counter++;
                                                } // while
                                                
                                                $stmt = null;
                                            ?>  
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <a class="btn btn-success" href="bu_add_regular_debit.php">Add Regular Debit</a>
                                </div>
                            </div>  <!-- /.card -->
                        </div>  <!-- /.col -->
                    </div>  <!-- /.row -->
                </section>  <!-- /.content -->
            </div>  <!-- /.dummy -->
        <!-- Common Footer -->
            <?php include("partials/footer.php"); ?>
        </div>  <!-- ./wrapper -->
    <!-- Update Transaction Modal -->
        <div class="modal fade" id="update-regular-debit-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">   <!-- `.modal-dialog-centered` to centre on screen -->
                <div class="modal-content"  style="position: relative;">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">View | Update Regular Debit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <!-- Inject the update transaction form -->
                        <?php include("forms/form_update_regular_debit.php"); ?>
                    </div>
                    <div class="modal-footer justify-content-between">
                    <!-- Update-form's submit button -->
                        <button type="submit" form="update-regular-debit" name="update-regular-debit-submit" id="update-regular-debit-submit" class="btn btn-success">Update</button>
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
            //DataTable.datetime('ddd DD MM YYYY');

            var regular_debits = new DataTable('#regular-debits', {
                layout: {
                    topStart: null,
                    topEnd: null,
                    //bottomEnd: null
                },
                order: [[11, 'desc']],
                pageLength: 25,
                lengthMenu: [25, 50, 100, { label: 'All', value: -1 }],
                columns: [ 
                    {name: 'counter', className: 'counter', searchable: false, width: '100px'/*, width: '1%'*/},
                    /* {className: 'id', searchable: false, width: '60px'}, */
                    {name: 'add', className: 'add', searchable: false, width: '50px'/*, width: '1%'*/}, 
                    {name: 'account_id_alpha', className: 'account-code', width: '70px'/*, width: '5%'*/}, 
                    {name: 'account_details', className: 'account-name', width: '300px'/*, width: '15%'*/}, 
                    {
                        /*className: 'transaction-amount currency',*/ 
                        name: 'amount', 
                        width: '75px',
                        type: 'num', 
                        render: DataTable.render.number(',', '.', '2', '£'), 
                        createdCell: function (td, cellData, rowData, row, col) {
                            if (cellData < 0) {
                                $(td).addClass('debit');
                            }
                        }
                    }, 
                    {name: 'type', className: 'transaction-type', width: '100px'/*, width: '5%'*/}, 
                    {name: 'sub_type', className: 'transaction-subtype', width: '100px'/*, width: '5%'*/}, 
                    {name: 'method', className: 'transaction-method', width: '100px'/*, width: '5%'*/}, 
                    {name: 'entity', className: 'entity', width: '200px'/*width: '15%'*/}, 
                    //{className: 'regular-debit-type', width: '250px'/*width: '15%'*/}, 
                    {name: 'day', className: 'transaction-day', type: 'num', width: '35px'/*, width: '7%'*/, orderable: false},
                    {name: 'period', className: 'transaction-period', type: 'num', width: '35px'/*, width: '7%'*/, orderable: false},
                    {
                        name: 'current', 
                        className: 'transaction-date current-due-date', 
                        type: 'date', 
                        width: '150px', 
                        render: DataTable.render.datetime('ddd DD/MM/YYYY'),       // requires moment.js
                        createdCell: function (td, cellData, rowData, row, col) {
                            $(td).addClass(Chronology(cellData));
                        }
                    },
                    {
                        name: 'next', 
                        className: 'transaction-date next-due-date', 
                        type: 'date', 
                        width: '120px', 
                        render: DataTable.render.datetime('ddd DD/MM/YYYY'),       // requires moment.js
                        createdCell: function (td, cellData, rowData, row, col) {
                            $(td).addClass(Chronology(cellData));
                        }
                    }, 
                    {name: 'actions', className: 'actions', searchable: false, width: '95px'/*, width: '7%'*/, orderable: false} 
                ],

            // Callbacks
                initComplete: function () {
                    CreateFilterDropdowns (this.api().columns(['account_id_alpha:name','account_details:name','type:name','sub_type:name','method:name','entity:name']))
                    CreateFilterDropdownsIntegerSort (this.api().columns(['amount:name','day:name','period:name']))
                },
                footerCallback: function (row, data, start, end, display) {
                },
                drawCallback: function (settings) {
                },
            });
            $(function() {
                /*
                var query = getUrlVars()['search'];
                //console.log(query)
                //console.log(decodeURIComponent(query));
                if (query) {
                    transactions.search(decodeURIComponent(query)).draw();
                }
                */
            });
        </script>
    <!-- AJAX Update -->
        <!-- <script src="ajax/bu_ajax_update_regular_debit.js"></script> -->
        <script src="ajax/bu_ajax_update_form.js"></script>
    <!-- AJAX Add Last Date -->
        <script src="ajax/bu_ajax_due_date.js"></script>
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