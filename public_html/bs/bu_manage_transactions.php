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
                                <div class="card-header">
                                    <a class="btn btn-success" href="bu_add_transaction.php">Add Transaction</a>
                                    <button type="button" id="clear-filters" class="btn btn-warning btn-sm float-end d-none">Clear Filters</a>  <!-- `d-none` Bootstrap class initially hides the button -->
                                </div>
                                
                                <div class="card-body pt-0">
                                    <h2 class="loading text-center"><i class="fa fa-spinner fa-spin" style="font-size:36px; margin-top:25px;"></i></i></h2>     <!-- DOM element is removed at the end of the `initComplete` callback once the table is ready -->
                                    <table id="transactions" class="table table-bordered bu-data-table bu-table-striped bu-table-hover" style="display:none">     <!-- Initially hidden, the table is displayed at the end of the `initComplete` callback once it's ready -->
                                        <thead>
                                            <tr>
                                                <th class="text-left">#</th>
                                                <th>Account ID Alpha</th>
                                                <th>Account Details</th>
                                                <th>Amount</th>

                                                <th>Entity</th>
                                                <th>Type</th>
                                                <th>Sub-type</th>
                                                <th>Method</th>
                                                
                                                <th>Date</th>
                                                <th>Tax Year</th>
                                                <th>Period</th>
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
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <?php
                                                $counter = 1;

                                                // This stored procedure uses a WHERE clause to select rows whose `id` column is equal to a specific value. This value is passed as a parameter to the procedure: '1' or '%' = ALL
                                                $stmt = $pdo->prepare("
                                                    CALL 
                                                        bu_transactions_get_transactions(?);
                                                ");
                                                $stmt->execute(
                                                    [
                                                        '%'     // WHERE `id` LIKE '%', selects ALL records
                                                    ]
                                                );

                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                            ?>
                                            <tr>
                                                <!-- <td><?php //echo ((!empty($row->notes)) ? '<i class="fa-solid fa-book"></i> ' : "") . $counter; ?></td> -->
                                                <!--  
                                                <td <?php //echo ((!empty($row->notes)) ? 'class="has-note" data-counter="' . $counter .'" data-note="' . nl2br($row->notes) .'" data-entity-description="' . $row->entity_description . '" data-amount="'  . $fmt_currency->formatCurrency($row->amount, "GBP") . '" data-date="'  . $fmt_date->format(strtotime($row->date)) . '"'  : "") . '>' . $counter; ?></td>
                                                -->
                                                <td <?php echo ((!empty($row->notes)) ? 'class="has-note right details-control" data-note="' . nl2br($row->notes) . '"'  : "") . '>' . $counter; ?></td>
                                                <td><?php echo $row->account_id_alpha; ?></td>
                                                <td><?php echo $row->trading_name . ' ' . $row->name . ' - ' . $row->account_number . ' ['. $row->account_id_alpha . ']' . ($row->status === 'Closed' ? ' CLOSED' : ''); ?></td>
                                                <!-- <td><?php //echo $fmt_currency->formatCurrency($row->amount, "GBP"); ?></td> -->
                                                <td><?php echo $row->amount; ?></td>

                                                <td><?php echo $row->entity_description; ?></td>
                                                <td><?php echo $row->type_description; ?></td>
                                                <td><?php echo $row->sub_type_description; ?></td>
                                                <td><?php echo $row->method_description; ?></td>
                                                
                                                <td><?php echo $row->transaction_date; ?></td>
                                                <td><?php echo $row->tax_year; ?></td>
                                                <td><?php echo $row->period; ?></td>
                                                <td style="text-align: center">
                                                    
                                                    <a class="btn btn-success btn-sm view-record" href="#" data-bs-toggle="modal" data-bs-target="#update-transaction-modal" data-mysql-table="bu_transactions" data-record-id="<?php echo $row->id; ?>">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                
                                                    <a data-mysql-table="bu_transactions" data-record-id="<?php echo $row->id; ?>" data-record-type="transaction" data-record-identifier="<?php echo $row->entity_description; ?>"  class="btn btn-danger btn-sm delete-record" href="#">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php 
                                                    $counter++;
                                                } // while
                                                
                                                $stmt = null;
                                            ?>
                                            <!--
                                            <tfoot>
                                                <tr>
                                                </tr>
                                            </tfoot>
                                            -->
                                        </tbody>
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
                        <h5 class="modal-title" id="staticBackdropLabel"><!-- jQuery populated --></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <!-- Inject the update transaction form -->
                        <?php include("forms/form_update_transaction.php"); ?>
                    </div>
                    <div class="modal-footer justify-content-between">
                    <!-- Update-form's submit button -->
                        <button type="submit" form="update-transaction" name="update-transaction-submit" id="update-transaction-submit" class="btn btn-success">Update</button>
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
            $(function() {
                var transactions = new DataTable('#transactions', {
                    layout: {
                        topStart: {
                            buttons: [
                                {
                                    extend: 'colvis',
                                    columns: ':not(.exclude-from-column-visibility)',
                                    popoverTitle: 'Column visibility selector'
                                }
                            ]
                        }
                    },
                    autoWidth: false,
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
                        {  
                            name: 'counter',
                            className: 'counter exclude-from-column-visibility', 
                            searchable: false, 
                            width: '75px'
                        }, 
                        {   // Column Index 1
                            name: 'account_id_alpha',
                            className: 'account-code exclude-from-column-visibility', 
                            width: '120px',
                            //visible: false  // Hidden by default when the page loads, but can be made visible by clicking the 'Column visibilty' button.
                        }, 
                        {
                            name: 'account_details',
                            className: 'account-name', 
                            width: '300px'
                        }, 
                        {
                            name: 'amount',
                            className: 'transaction-amount exclude-from-column-visibility',
                            width: '125px',
                            //type: 'num', 
                            render: DataTable.render.number(',', '.', '2', '£'), 
                            createdCell: function (td, cellData, rowData, row, col) {
                                if (cellData < 0) {
                                    $(td).addClass('debit');
                                }
                            }
                        },
                        {
                            name: 'entity',
                            className: 'entity exclude-from-column-visibility', 
                            width: '500px'
                        }, 
                        {
                            name: 'type',
                            className: 'transaction-type exclude-from-column-visibility', 
                            width: '175px'
                        }, 
                        {
                            name: 'sub_type',
                            className: 'transaction-subtype', 
                            width: '175px'
                        },
                        {
                            name: 'method',
                            className: 'transaction-method', 
                            width: '175px'
                        },
                        {
                            name: 'date',
                            className: 'transaction-date exclude-from-column-visibility',
                            width: '150px',
                            type: 'date',  
                            render: DataTable.render.datetime('ddd DD/MM/YYYY'),   // requires moment.js
                            createdCell: function (td, cellData, rowData, row, col) {
                                //console.log($(td).attr('data-order'))
                                $(td).addClass(Chronology(cellData));
                            },
                            orderable: true
                        },
                        {
                            name: 'tax_year',
                            className: 'tax-year', 
                            width: '120px', 
                            type: 'num'
                        },
                        {
                            name: 'period',
                            className: 'period', 
                            width: '120px', 
                            type: 'num'
                        },
                        {   
                            name: 'actions',
                            className: 'actions exclude-from-column-visibility', 
                            width: '95px',
                            searchable: false, 
                            orderable: false
                        } 
                    ],
                    order: [
                        [ 8, 'desc' ], 
                        [ 0, 'asc' ]
                    ],
                // Callbacks
                    initComplete: function () {
                        //console.log(this)

                        CreateFilterDropdowns (this.api().columns(['account_id_alpha:name','account_details:name','entity:name','type:name','sub_type:name','method:name']))
                        CreateFilterDropdownsIntegerSort (this.api().columns(['amount:name','tax_year:name','period:name']))

                        /**
                         * When the URL contains a filter parameter, all filters should be cleared before applying the filter contained in the URL
                         * This happens after any filters are restored from the save 'state' object.
                         */

                        const urlParams = new URLSearchParams(window.location.search);

                        if ( Array.from(urlParams).length !== 0 ) {     // URL contains filter paramater(s); 'bu_manage_transactions.php?filter-col-5=Non-taxable%20Interest&filter-col-10=99'

                            $(this).DataTable().search('').columns().search('').draw()  // Reset all current search parameters

                            
                        // In the URL, each search parameter is separated by '&' and defined as a key/value pair.  
                            for (const [key, value] of urlParams.entries()) {
                            // Iterate over each of the search parameters in the URL.
                            
                                $(`#${key}`).val(value)     // e.g. $('#filter-col-10').val('99')

                                if (key === 'dt-search-0') {
                                // Search Box
                                    $(`#${key}`).trigger('keyup')
                                } else {
                                // Select Dropdowns
                                    $(`#${key}`).trigger('change')
                                }

                            }
                            
                        }

                    // Page Loading 
                        $('h2.loading').remove();   // Remove the 'Loading' message now the table's ready 
                        $(this).show()              // The table is initially hidden (style="display:none"), once it's ready, show it. 
                            
                    },                    
                    footerCallback: function (row, data, start, end, display) {
                        //console.log('DataTables footerCallback Fired')
                        var api = this.api();
                
                        var currentPeriod = <?php echo $bu_settings['current_period']; ?> 
                        //console.log(currentPeriod)
                
                        // Total over all pages
                        total = api
                            .column(
                            // columnSelector parameter (optional?)    
                                'amount:name' /*3*/, 
                            // // modifier parameter (optional)
                                {
                                    search: 'applied'
                                }
                            )
                            .data()
                            .reduce(function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);

                    
                        // See https://datatables.net/forums/discussion/65346/how-to-sum-values-%E2%80%8B%E2%80%8Bfrom-one-column-based-on-a-value-from-another-column

                        // Sum the amount only for transactions whose effective date is today or earlier
                        //console.clear()
                        todayTotal = api
                            .rows( 
                            // See https://datatables.net/reference/type/row-selector#Function for using a function as a row selector
                                function ( idx, data, node ) {
                                    return Chronology(data['8']) !== 'future' ? true : false;    // data['8'] is the transaction's effective date. Chronology returns, 'past', 'today' or 'future'.
                                }, 
                            // See https://datatables.net/reference/type/selector-modifier#search for using search as a selector modifier
                                {
                                    search: 'applied'
                                } 
                            )
                            .data()
                            .pluck(3 /*'amount:name'*/)
                            .reduce(
                            // callback function parameter
                                function (a, b,) {
                                    return intVal(a) + intVal(b);
                                },
                            // initial value parameter (optional)
                                0
                            );    

                        //console.log("Today " + todayTotal)

                        periodTotal = api
                            .rows( 
                            // rowSelector parameter
                                function ( idx, data, node ) {                             
                                    return data['10'] <= currentPeriod ? true : false;    // data['10'] is the transaction's period
                                }, 
                            // modifier parameter (optional)
                                {
                                    search: 'applied'
                                } 
                            )
                            .data()
                            .pluck(3 /*'amount:name'*/)
                            .reduce(
                            // callback function parameter
                                function (a, b,) {
                                    return intVal(a) + intVal(b);
                                },
                            // initial value parameter (optional)
                                0
                            ); 
                            
                            //console.log("Period " + periodTotal)

                
                        // Total over this page
                        pageTotal = api
                            .column('amount:name' /*3*/, { page: 'current' })
                            .data()
                            .reduce(function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);

                    // Display total over all pages

                        /**
                         Totals that are expected to be zero are ocassionaly less than zero e.g. -1.5279510989785194e-10 which is eqaul to -1.5279510989785194 x 0.0000000001 = -0.00000000015279510989785194. These numbers 
                        are so small, that when formatted with the Intl.NumberFormat object they are shown as 0 (-0, if `signDisplay` is not `exceptZero` or `negative`). However, testing if these totals are 
                        less than zero, will return `true`.
                        */ 

                        total = parseFloat(new Intl.NumberFormat(   
                            'en-GB', 
                            {
                                useGrouping: false,     // Do not show thousands separator
                                signDisplay: 'negative' // Sign display for negative numbers only, excluding negative zero.
                            }
                        ).format(total))    // Will return 0 (correctly) if total is a negative number that's so incredibly small e.g. -1.5279510989785194e-10 (-0.00000000015279510989785194)

                        if (total < 0) {
                            $('span.all-total').addClass('debit')
                        } else {
                            $('span.all-total').removeClass('debit')
                        }
                        
                        $('span.all-total').html(new Intl.NumberFormat(
                            'en-GB', 
                            {
                                style: 'currency', 
                                currency: 'GBP', 
                                currencyDisplay: 'symbol', 
                                signDisplay: 'negative' 
                            }
                        ).format(total));

                    // Display total upto and including today
                        todayTotal = parseFloat(new Intl.NumberFormat(
                            'en-GB', 
                            {
                                useGrouping: false,     // Do not show thousands separator
                                signDisplay: 'negative' // Sign display for negative numbers only, excluding negative zero.
                            }
                        ).format(todayTotal))

                        if (todayTotal < 0) {
                            $('span.today-total').addClass('debit')
                        } else {
                            $('span.today-total').removeClass('debit')
                        }

                        $('span.today-total').html(new Intl.NumberFormat(
                            'en-GB', 
                            {
                                style: 'currency', 
                                currency: 'GBP', 
                                currencyDisplay: 'symbol', 
                                signDisplay: 'negative' 
                            }
                        ).format(todayTotal));

                        
                    // Display total upto and including the period-end

                        periodTotal = parseFloat(new Intl.NumberFormat(
                            'en-GB', 
                            {
                                useGrouping: false,     // Do not show thousands separator
                                signDisplay: 'negative' // Sign display for negative numbers only, excluding negative zero.
                            }
                        ).format(periodTotal))

                        if (periodTotal < 0) {
                            $('span.period-total').addClass('debit')
                        } else {
                            $('span.period-total').removeClass('debit')
                        }

                        $('span.period-total').html(new Intl.NumberFormat(
                            'en-GB', 
                            {
                                style: 'currency', 
                                currency: 'GBP', 
                                currencyDisplay: 'symbol', 
                                signDisplay: 'negative' 
                            }
                        ).format(periodTotal));

                    },
                    drawCallback: function (settings) {
                        //console.log('DataTables drawCallback Fired')
                        //customClass('account-code', 'account-code-')
                        //customClass('currency', 'debit')
                        //customClass('transaction-date', '')
                    },
                    createdRow: function (row, data, dataIndex) {
                        //console.log(data[1].toLowerCase())
                        //console.log('DataTables createdRow Fired')
                        // data[1] contains the alpha account code A, B, ..., K
                        $(row).addClass('account-code-' + data[1].toLowerCase());   // 'account-code-a' where data[1] = 'A', for example
                    }
                });

                function RestoreSearchFilter(api, dropdown) {
                    //console.log(this.state.loaded())
                    var state = api.state.loaded();
                    if (state) {
                        var val = state.columns[api.index()];
                        dropdown.val(val.search.search);
                    }

                }

            }); // Document ready
        </script>
    <!-- AJAX Update -->
        <!-- <script src="ajax/bu_ajax_update_transaction.js"></script> -->
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