<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Manage Transactions";
    
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
    <!-- Column Visibility Plugin -->
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.2/css/buttons.dataTables.css">
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
                                <h1 id="filter-total" class="card-title">
                                     
                                    <span class="text-grey" style="font-size: .75em;">Today</span>
                                    &nbsp;
                                    <span class="today-total currency">
                                        <!-- Populated with JavaScript variable 'todayTotal' by DataTables footerCallback event -->
                                    </span>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <span class="text-grey" style="font-size: .75em;">Period End [<?php echo $bu_settings['current_period']; ?>]</span>
                                    &nbsp;
                                    <span class="period-total currency">
                                        <!-- Populated with JavaScript variable 'periodTotal' by DataTables footerCallback event -->
                                    </span>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <span class="text-grey" style="font-size: .75em;">All</span>
                                    &nbsp;
                                    <span class="all-total currency">
                                        <!-- Populated with JavaScript variable 'total' by DataTables footerCallback event -->
                                    </span>
                                </h1>
                            </div>
                            <div class="col">
                                <ol class="breadcrumb float-sm-end">
                                    <li class="breadcrumb-item"><a class="text-decoration-none" href="bu_dashboard.php">Dashboard</a></li>
                                    <!-- <li class="breadcrumb-item"><a href="bu_manage_transactions.php">Transactions</a></li> -->
                                    <li class="breadcrumb-item active"><?php echo $page_name; ?></li>
                                </ol>
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
                                <div class="card-header">
                                    <a class="btn btn-success" href="bu_add_transaction.php">Add Transaction</a>
                                </div>
                                
                                <div class="card-body" style="padding-top: 0px;">


                                    <table id="transactions" class="table table-hover table-bordered table-striped bu-data-table">
                                        <thead>
                                            <tr>
                                                <th>Counter</th>
                                                <th rowspan="2">Account ID</th>
                                                <th>Account name</th>
                                                <th>Amount</th>
                                                <th>Type</th>
                                                <th>Sub-type</th>
                                                <th>Entity</th>
                                                <th>Date</th>
                                                <th>Tax Year</th>
                                                <th>Period</th>
                                                <th style="text-align: center">Actions</th>
                                            </tr>
                                            <tr>
                                                <th>Counter</th>
                                                
                                                <th>Account name</th>
                                                <th>Amount</th>
                                                <th>Type</th>
                                                <th>Sub-type</th>
                                                <th>Entity</th>
                                                <th>Date</th>
                                                <th>Tax Year</th>
                                                <th>Period</th>
                                                <th style="text-align: center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
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
                                            </tr>
                                        </tfoot>
                                        
                                    </table>

                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <a class="btn btn-success" href="bu_add_transaction.php">Add Transaction</a>
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
        <div class="modal fade" id="update-transaction-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">   <!-- `.modal-dialog-centered` to centre on screen -->
                <div class="modal-content"  style="position: relative;">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">View | Update Transaction</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <!-- Inject the update transaction form -->
                        <?php include("forms/form_update_transaction.php"); ?>
                    </div>
                    <div class="modal-footer justify-content-between">
                    <!-- Update-form's submit button -->
                        <button type="submit" form="update-transaction" name="update-transaction-submit" id="update-transaction-submit" class="btn btn-success disabled">Update</button>
                    <!-- Update-modal's close button -->
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    <!-- Common Scripts -->
        <?php include("partials/scripts.php"); ?>
    <!-- Column Visibility Plugin -->
        <script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.js"></script>
        <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.dataTables.js"></script>
        <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.colVis.min.js"></script>
    <!-- DataTable Table -->
        <script>
            new DataTable('#transactions', {
                select: true,
                order: [],  // See https://stackoverflow.com/a/51076425
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
                /*
                ajax: DataTable.pipeline({
                    url: 'ss_get_data.php',
                    type: 'GET',
                    pages: 5 // number of pages to cache
                   
                }),
                */
                ajax: 'ss_get_data.php',
                columns: [
                    /**
                     * The value for the `data` property parameter MUST be the same as the value of the 
                     * corresponding `dt` parameter defined in ss_get_data.php
                     */
                    { data: null },
                    { data: 'account_id_alpha' },
                    { 
                        data: 'trading_name',
                        render: function ( data, type, row ) {
                            console.log(row)
                            return row['trading_name'] + ' ' + row['name'] + ' - ' + row['account_number'] + ' [' + row['account_id_alpha'] + ' ]'
                        }
                     },
                    // { data: 'name' },
                    // { data: 'sort_code' },
                    // { data: 'account_number' },
                    // { data: 'status' },
                    { 
                        data: 'amount',
                        render: DataTable.render.number(',', '.', '2', '£'), 
                        createdCell: function (td, cellData, rowData, row, col) {
                            if (cellData < 0) {
                                $(td).addClass('debit');
                            }
                        }
                    },
                    { data: '_type' },
                    { data: '_subtype' },
                    { data: 'entity_description' },
                    { 
                        data: 'date',
                        className: 'transaction-date',
                        render: DataTable.render.datetime('ddd DD/MM/YYYY'),   // requires moment.js
                        createdCell: function (td, cellData, rowData, row, col) {
                            $(td).addClass(Chronology(cellData));
                        }
                    },
                    { data: 'tax_year' },
                    { data: 'period' },
                    { data: null }
                ],
                processing: true,
                serverSide: true,
                initComplete: function () {
                    //console.log('DataTables initComplete Fired')
                    this.api()
                        .columns([1],)
                        .every(function () {
                            var column = this;
            
                            // Create select element and listener
                            var select = $('<select id="filter-col-' + column.index() +'"><option value="">Show all</option></select>')
                                .appendTo($(column.footer()))
                                .on('change', function () {
                                    column
                                        .search(    
                                            $(this).val(), 
                                            {exact: true}
                                        )
                                        .draw();
                                });
            
                                $( select ).click( function(e) {
                                    e.stopPropagation();
                                });

                            if (column.index() === 3 || column.index() === 8 || column.index() === 9) {     // The 'Amount', 'Tax Year' and 'Period' columns

                                //    For some columns containing numeric data, the options in the select dropdown are sorted as strings 1, 10, 11, 12, 2, 20 etc.
                                //    The arrow function '(a, b) => a - b' passed to sort() enures they are sorted as numbers - 1, 2, 10, 11, 12, 20 etc
            
                                column
                                    .data()
                                    .unique()
                                    .sort(
                                        function (a, b) {
                                            return a - b          // See https://stackoverflow.com/a/68980030/2518495 re sorting on integers
                                        }
                                    )    
                                    .each(function (d, j) {
                                        select.append(
                                            '<option value="' + d + '">' + d + '</option>'
                                        );
                                    });
                            } else {
                                column
                                    .data()
                                    .unique()
                                    .sort() 
                                    .each(function (d, j) {
                                        select.append(
                                            '<option value="' + d + '">' + d + '</option>'
                                        );
                                    });

                            }

                            // When a column filter is applied - say Entity = BT - and stateSave is true, if the page is reloaded the filter is still in effect, but the filter dropdown now displays 'Show all' and not 'BT'.
                            // To overcome this, the correct dropdown filter value is restored from the saved 'state' object. See https://stackoverflow.com/a/49878256
                            // See https://datatables.net/reference/api/state() for the structure of the 'state' object 
                            var state = this.state.loaded();
                            if (state) {
                                var val = state.columns[this.index()];
                                select.val(val.search.search);
                            }

                        }); //every

                        // When the URL contains a filter parameter, all filters should be cleared before applying the filter contained in the URL 
                        // This happens after any filters are restored from the save 'state' object.

                        const urlParams = new URLSearchParams(window.location.search);

                        if (urlParams.has('filter')) {                                          // URL contains filter paramater; 'bu_manage_transactions.php?filter=filter-col-6&value=BT'

                            $('select').each(function() {                                       // Effective for all select elements on the page
                                // $(this) now refers to one specific <select> element
                                $(this).prop("selectedIndex", 0).val();                         // Set the option of the select element to its first (0) option which is 'Show all' 
                                $(this).trigger('change');                                      // Update the display based on the filter condition; 'Show all'
                            });

                            $("#" + urlParams.get('filter')).val(urlParams.get('value'));       // Set the appropriate filter to the value included in the URL
                            $("#" + urlParams.get('filter')).trigger("change");                 // Update the display based on the new filter condition

                        }
                        
                },
                createdRow: function (row, data, dataIndex) {
                    $(row).addClass('account-code-' + data['account_id_alpha'].toLowerCase());   // 'account-code-a' where `data['account_id_alpha']` = 'A', for example
                }
            });
        </script>
    <!-- Ajax Modal -->
        <!-- <script src="ajax/bu_ajax_transaction_modal.js"></script> -->
    <!-- AJAX Update -->
        <script src="ajax/bu_ajax_update_transaction.js"></script>
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