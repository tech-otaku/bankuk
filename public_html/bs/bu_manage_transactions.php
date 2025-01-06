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
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="bu_dashboard.php">Dashboard</a></li>
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
                                <div class="card-header p-6">
                                    <!-- <h3 id="filter-total" class="card-title"><span class="all-total currency"></span></h3> -->
                                </div>
                                
                                <div class="card-body">
                                    <table id="transactions" class="table table-hover table-bordered table-striped bu-data-table">
                                        <thead>
                                            <tr>
                                                <!--
                                                    <th>#</th>
                                                    <th>Account Type</th>
                                                    <th>Account Name</th>
                                                    <th>Amount</th>
                                                    <th>Type</th>
                                                    <th>Sub-type</th>
                                                    <th>Party</th>
                                                    <th>Date</th>
                                                    <th>Period</th>
                                                    <th>Action</th>
                                                    -->
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
                                        </thead>
                                        <tbody>
                                            <?php
                                                $counter = 1;

                                                $stmt = $pdo->prepare("
                                                    SELECT 
                                                        t1.id,
                                                        t1.account_id_alpha,
                                                        b1.trading_name,
                                                        a1.name,
                                                        a1.sort_code,
                                                        a1.account_number,
                                                        a1.status,
                                                        t1.amount,
                                                        tt1.description AS _type,
                                                        tt2.description AS _subtype,
                                                        p1.party,
                                                        t1.date,
                                                        t1.period,
                                                        t1.notes
                                                    FROM
                                                        bu_transactions AS t1
                                                    LEFT JOIN
                                                        bu_accounts AS a1 ON t1.account_id_alpha = a1.account_id_alpha
                                                    LEFT JOIN
                                                        bu_banks AS b1 ON a1.bank_id = b1.bank_id
                                                    LEFT JOIN
                                                        bu_parties AS p1 ON t1.party_id = p1.party_id
                                                    LEFT JOIN
                                                        bu_transaction_types AS tt1 ON t1.type = tt1.type
                                                    LEFT JOIN
                                                        bu_transaction_types AS tt2 ON t1.sub_type = tt2.type
                                                    ORDER BY 
                                                        t1.date DESC , t1.id DESC
                                                ");
                                                $stmt->execute();

                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                            ?>
                                            <tr>
                                                <!-- <td><?php //echo ((!empty($row->notes)) ? '<i class="fa-solid fa-book"></i> ' : "") . $counter; ?></td> -->
                                                <td <?php echo ((!empty($row->notes)) ? 'class="has-note" data-counter="' . $counter .'" data-note="' . $row->notes .'" data-party="' . $row->party . '" data-amount="'  . $fmt_currency->formatCurrency($row->amount, "GBP") . '" data-date="'  . $fmt_date->format(strtotime($row->date)) . '"'  : "") . '>' . $counter; ?></td>
                                                <td><?php echo $row->account_id_alpha; ?></td>
                                                <td><?php echo $row->trading_name . ' ' . $row->name . ' - ' . $row->account_number . ' ['. $row->account_id_alpha . ']' . ($row->status === 'Closed' ? ' CLOSED' : ''); ?></td>
                                                <!-- <td><?php //echo $fmt_currency->formatCurrency($row->amount, "GBP"); ?></td> -->
                                                <td><?php echo $row->amount; ?></td>
                                                <td><?php echo $row->_type; ?></td>
                                                <td><?php echo $row->_subtype; ?></td>
                                                <td><?php echo $row->party; ?></td>
                                                <td><?php echo $row->date; ?></td>
                                                <td><?php echo $row->period; ?></td>
                                                <td>
                                                    <a class="btn btn-success btn-sm" href="bu_view_transaction.php?id=<?php echo $row->id; ?>">
                                                        <i class="fa fa-edit"></i>
                                                        <!-- Edit -->
                                                    </a>
                                                    <a data-mysql-table="bu_transactions" data-record-id="<?php echo $row->id; ?>" class="btn btn-danger btn-sm delete-record" href="#">
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
    <!-- Common Scripts -->
        <?php include("partials/scripts.php"); ?>
    <!-- DataTable Table -->
        <script>
            var transactions = new DataTable('#transactions', {
                stateSave: false,
                select: true,
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
                        className: 'counter', 
                        searchable: false, 
                        width: '60px'
                    }, 
                    {   // Column Index 1
                        name: 'account',
                        className: 'account-code', 
                        width: '120px'
                    }, 
                    {
                        className: 'account-name', 
                        width: '300px'
                    }, 
                    {
                        /*className: 'transaction-amount currency',*/ 
                        width: '125px',
                        type: 'num', 
                        render: DataTable.render.number(',', '.', '2', '£'), 
                        createdCell: function (td, cellData, rowData, row, col) {
                            if (cellData < 0) {
                                $(td).addClass('debit');
                            }
                        }
                    }, 
                    {
                        className: 'transaction-type', 
                        width: '175px'
                    }, 
                    {
                        className: 'transaction-subtype', 
                        width: '175px'
                    }, 
                    {
                        className: 'party', 
                        width: '500px'
                    }, 
                    {
                        className: 'transaction-date',
                        width: '175px',
                        type: 'date',  
                        render: DataTable.render.datetime('ddd DD/MM/YYYY'),   // requires moment.js
                        createdCell: function (td, cellData, rowData, row, col) {
                            $(td).addClass(chronology(cellData));
                        },
                        orderable: true
                    },  
                    {
                        className: 'period', 
                        width: '120px', 
                        type: 'num'
                    },
                    {   className: 'actions', 
                        width: '100px',
                        searchable: false, 
                        orderable: false
                    } 
                ],
            // Callbacks
                initComplete: function () {
                    console.log('DataTables initComplete Fired')
                    this.api()
                        .columns([1,2,3,4,5,6,8],)
                        .every(function () {
                            var column = this;
            
                            // Create select element and listener
                            var select = $('<select id="filter-col-' + column.index() +'"><option value="">Show all</option></select>')
                                .appendTo($(column.header()))
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

                            if (column.index() === 3 || column.index() === 8) {     // The 'Amount' and 'Period' columns

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

                            // When a column filter is applied - say Party = BT - and stateSave is true, if the page is reloaded the filter is still in effect, but the filter dropdown now displays 'Show all' and not 'BT'.
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
                footerCallback: function (row, data, start, end, display) {
                    console.log('DataTables footerCallback Fired')
                    var api = this.api();
            
                    

                    // Check if the transaction date is today or earlier
                    var todayOrEarlier = function (d) {
                        let todaysDate = new Date();
                        todaysDate.setHours(0,0,0,0);
                        let transactionDate = new Date(d);
                        
                        return transactionDate.getTime() <= todaysDate.getTime() ? true : false;    // Return 'true' if the transaction date is today or earlier, otherwise false
                    }

                    var currentPeriod = <?php echo $bu_settings['current_period']; ?> 
                    console.log(currentPeriod)
            
                    // Total over all pages
                    total = api
                        .column(
                        // columnSelector parameter (optional?)    
                            3, 
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
                    todayTotal = api
                        .rows( 
                        // rowSelector parameter
                            function ( idx, data, node ) {                             
                                return todayOrEarlier(data['7']) ? true : false;    // data['7'] is the transaction's effective date
                            }, 
                        // modifier parameter (optional)
                            {
                                search: 'applied'
                            } 
                        )
                        .data()
                        .pluck(3)
                        .reduce(
                        // callback function parameter
                            function (a, b,) {
                                return intVal(a) + intVal(b);
                            },
                        // initial value parameter (optional)
                            0
                        );    

                    console.log("Today " + todayTotal)

                    periodTotal = api
                        .rows( 
                        // rowSelector parameter
                            function ( idx, data, node ) {                             
                                return data['8'] <= currentPeriod ? true : false;    // data['8'] is the transaction's period
                            }, 
                        // modifier parameter (optional)
                            {
                                search: 'applied'
                            } 
                        )
                        .data()
                        .pluck(3)
                        .reduce(
                        // callback function parameter
                            function (a, b,) {
                                return intVal(a) + intVal(b);
                            },
                        // initial value parameter (optional)
                            0
                        ); 
                        
                        console.log("Period " + periodTotal)

            
                    // Total over this page
                    pageTotal = api
                        .column(3, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    
                    $('span.all-total').html(new Intl.NumberFormat(
                        'en-GB', 
                        {
                            style: 'currency', 
                            currency: 'GBP', 
                            currencyDisplay: 'symbol', 
                            signDisplay: 'negative' 
                        }
                    ).format(total));

                    if (total < 0) {
                        $('span.all-total').addClass('debit')
                    } else {
                        $('span.all-total').removeClass('debit')
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

                    if (todayTotal < 0) {
                        $('span.today-total').addClass('debit')
                    } else {
                        $('span.today-total').removeClass('debit')
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

                    if (periodTotal < 0) {
                        $('span.period-total').addClass('debit')
                    } else {
                        $('span.period-total').removeClass('debit')
                    }

                },
                drawCallback: function (settings) {
                    console.log('DataTables drawCallback Fired')
                    //customClass('account-code', 'account-code-')
                    //customClass('currency', 'debit')
                    //customClass('transaction-date', '')
                },
                createdRow: function (row, data, dataIndex) {
                    console.log('DataTables createdRow Fired')
                    // data[1] contains the alpha account code A, B, ..., K
                    $(row).addClass('account-code-' + data[1].toLowerCase());   // 'account-code-a' where data[1] = 'A', for example
                }
            });
            /* Plugin API method to determine is a column is sortable */
            $.fn.dataTable.Api.register('column().searchable()', function() {
            var ctx = this.context[0];
            return ctx.aoColumns[this[0]].bSearchable;
            });

            function createDropdowns(api) {
                api.columns([1,2,3,4,5,6,8],).every(function () {
                    //if (this.searchable()) {
                        var that = this;
                        var column = this;

                        // Only create if not there or blank
                        var selected = $('thead tr:eq(1) td:eq(' + column + ') select').val();
                        if (selected === undefined || selected === '') {
                            // Create the `select` element
                            
                            $('thead tr:eq(0) td')
                                .eq(column)
                                .empty();
                                var select = $('<select id="filter-col-' + column.index() +'"><option value="">Show all</option></select>')
                                .appendTo($(column.header()))
                                .on('change', function () {
                                    that
                                        .search(    
                                            $(this).val(), 
                                            {exact: true}
                                        )
                                        .draw();
                                    createDropdowns(api);
                                });

                                $( select ).click( function(e) {
                                    e.stopPropagation();
                                });

                            api
                                .cells(null, column, {
                                    search: 'applied'
                                })
                                .data()
                                .sort()
                                .unique()
                                .each(function (d) {
                                    select.append($('<option>' + d + '</option>'));
                                });
                        //}
                    }
                });
            }
            $(function() {
            });
        </script>
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