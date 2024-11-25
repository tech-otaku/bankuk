<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    echo $_SESSION['admin_id'];
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Summary";
    
    // NOTE: $pdo is an instance of a pdo() object declared in conf/pdoconfig.php

// Get the current accounting period based upon today's date
    $stmt = $pdo->prepare("
        CALL 
            bu_accounting_periods_current(?);
    ");
    $stmt->execute(
        [
            date('Y-m-d')   // Today's date as YYYY-MM-DD
        ]
    );

    $bu_accounting_period = $stmt->fetch(PDO::FETCH_ASSOC);
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
            <div class="content-wrapper ">   <!-- Temporarily .dummy -->
                <!-- Content Header with logged in user details (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1><?php echo $page_name; ?></h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
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
                                    <h2 id="filter-total" class="card-title"><?php echo date('l jS F Y', strtotime($bu_accounting_period['start'])) . ' to ' . date('l jS F Y', strtotime($bu_accounting_period['end'])) . ' [Period ' . $bu_accounting_period['period'] .']'; ?></h2>
                                </div>
                                <div class="card-body">
                                    <table id="summary" class="table table-hover table-bordered table-striped">
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
                                                <th>A/C Code</th>
                                                <th>A/C Name</th>
                                                <th>Today [A]</th>
                                                <th>Period End [B]</th>
                                                <th>Difference [A -B]</th>
                                                <th>Today</th>
                                                <th>G</th>
                                                <th>H</th>
                                                <th>I</th>
                                                <th>J</th>
                                                <th>K</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php

                                                $counter = 1;

                                                $stmt = $pdo->prepare("
                                                    CREATE TEMPORARY TABLE 
                                                        temp 
                                                    SELECT
                                                         * 
                                                    FROM 
                                                        bu_transactions 
                                                    WHERE 
                                                        `date` <= (
                                                            SELECT 
                                                                `end`
                                                            FROM 
                                                                bu_accounting_periods 
                                                            WHERE 
                                                                CURDATE() >= `start` AND CURDATE() <= `end`
                                                        );
                                                ");
                                                $stmt->execute();

                                                $stmt = $pdo->prepare("
                                                    SELECT 
                                                        a1.account_id_alpha, 
                                                        b1.trading_name,
                                                        a1.`name`,
                                                        a1.sort_code,
                                                        a1.account_number,
                                                        a1.`status`,
                                                        SUM(IF(tp1.`date` <= CURDATE(),tp1.amount,0)) AS totalA, 
                                                        SUM(IF(tp1.amount != 0,tp1.amount,0)) AS totalB, 
                                                        COUNT(IF(tp1.`date` = CURDATE(),tp1.amount,NULL)) AS totalC,
                                                        SUM(IF(tp1.`date` = CURDATE(),tp1.amount,0)) AS totalD,
                                                        COUNT(IF(tp1.`date` = CURDATE() + INTERVAL 1 DAY,tp1.amount,NULL)) AS totalE,
                                                        SUM(IF(tp1.`date` = CURDATE() + INTERVAL 1 DAY,tp1.amount,0)) AS totalF,
                                                        COUNT(IF(tp1.`date` >= CURDATE() + INTERVAL 2 DAY AND tp1.`date` <= CURDATE() + INTERVAL 6 DAY,tp1.amount,NULL)) AS totalG,
                                                        SUM(IF(tp1.`date` >= CURDATE() + INTERVAL 2 DAY AND tp1.`date` <= CURDATE() + INTERVAL 6 DAY ,tp1.amount,0)) AS totalH 
                                                    FROM 
                                                        bu_accounts AS a1 
                                                    LEFT JOIN 
                                                        temp AS tp1 ON a1.account_id_alpha = tp1.account_id_alpha 
                                                    LEFT JOIN 
                                                        bu_banks b1 ON a1.bank_id = b1.bank_id 
                                                    GROUP BY a1.account_id_alpha, 
                                                        b1.trading_name,
                                                        a1.`name`, 
                                                        a1.sort_code, 
                                                        a1.account_number, 
                                                        a1.`status`;
                                                ");
                                                $stmt->execute();

                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                            ?>
                                            <tr style="font-size: 1.25em !important;">
                                                <td><?php echo $row->account_id_alpha; ?></td>
                                                <td><?php echo $row->trading_name . ' ' . $row->name . ' <span class="account-number">' . $row->sort_code . ' ' . $row->account_number .'</span>' . ((strtolower($row->status) === 'closed') ? ' [<span class="account-closed">CLOSED</span>]' : ''); ?></td>
                                                <!-- <td><?php //echo $row->_name; ?></td> -->
                                                <td><?php echo $fmt_currency->formatCurrency($row->totalA, "GBP"); ?></td>
                                                <td><?php echo $fmt_currency->formatCurrency($row->totalB, "GBP"); ?></td>
                                                <td><?php echo $fmt_currency->formatCurrency($row->totalB - $row->totalA, "GBP"); ?></td>
                                                <td><?php echo $row->totalC; ?></td>
                                                <td><?php echo $fmt_currency->formatCurrency($row->totalD, "GBP"); ?></td>
                                                <td><?php echo $row->totalE; ?></td>
                                                <td><?php echo $fmt_currency->formatCurrency($row->totalF, "GBP"); ?></td>
                                                <td><?php echo $row->totalG; ?></td>
                                                <td><?php echo $fmt_currency->formatCurrency($row->totalH, "GBP"); ?></td>
                                            </tr>
                                            <?php 
                                                    $counter++;
                                                } // while
                                                
                                                $stmt = null;
                                            ?>    
                                        <tfoot>
                                            <tr style="font-size: 1.5em !important; font-weight: 400 !important;">
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
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
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
            var summary = new DataTable('#summary', {
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
            
                    // Remove the formatting to get integer data for summation
                    var intVal = function (i) {
                        return typeof i === 'string'
                            //? i.replace(/[\$,]/g, '') * 1
                            ? i.replace(/[^0-9.-]+/g, '') * 1
                            : typeof i === 'number'
                            ? i
                            : 0;
                    };
            
                    // Total over all pages
                    totalA = api
                        .column(2)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    totalB = api
                        .column(3)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    totalC = api
                        .column(4)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    totalD = api
                        .column(5)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    
                    totalE = api
                        .column(6)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    totalF = api
                        .column(7)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    totalG = api
                        .column(8)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    totalH = api
                        .column(9)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    totalI = api
                        .column(10)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
            
                    
                    
                    // Update footer
                    
                    
                    $( api.column( 2 ).footer() ).html(new Intl.NumberFormat('en-GB', { style: 'currency', currency: 'GBP', currencyDisplay: 'symbol' }).format(totalA));
                    $( api.column( 3 ).footer() ).html(new Intl.NumberFormat('en-GB', { style: 'currency', currency: 'GBP', currencyDisplay: 'symbol' }).format(totalB));
                    $( api.column( 4 ).footer() ).html(new Intl.NumberFormat('en-GB', { style: 'currency', currency: 'GBP', currencyDisplay: 'symbol' }).format(totalC));
                    $( api.column( 5 ).footer() ).html(new Intl.NumberFormat('en-GB').format(totalD));
                    $( api.column( 6 ).footer() ).html(new Intl.NumberFormat('en-GB', { style: 'currency', currency: 'GBP', currencyDisplay: 'symbol' }).format(totalE));
                    $( api.column( 7 ).footer() ).html(new Intl.NumberFormat('en-GB').format(totalF));
                    $( api.column( 8 ).footer() ).html(new Intl.NumberFormat('en-GB', { style: 'currency', currency: 'GBP', currencyDisplay: 'symbol' }).format(totalG));
                    $( api.column( 9 ).footer() ).html(new Intl.NumberFormat('en-GB').format(totalH));
                    $( api.column( 10 ).footer() ).html(new Intl.NumberFormat('en-GB', { style: 'currency', currency: 'GBP', currencyDisplay: 'symbol' }).format(totalI));
                    
                    
                    //console.log($( api.column( 9 ).footer().text ))
                    //customClass('next-5-days', 'debit')
        
                },
                
    
                drawCallback: function (settings) {
                    customClass('account-code', 'account-code-')
                    customClass('currency', 'debit')
                    //customClass('transaction-date', '')
                },
                pageLength: -1,
                //lengthMenu: [{ label: 'All', value: -1 }],
                
                columns: [
                    {className: 'account-code'},
                    null,
                    {className: 'currency', orderable: false},
                    {className: 'currency', orderable: false},
                    {className: 'currency', orderable: false},
                    null,
                    {className: 'currency', orderable: false},
                    null,
                    {className: 'currency', orderable: false},
                    null,
                    {className: 'currency', orderable: false}
                ],
                
                layout: {
                    topStart: null,
                    topEnd: null,
                    bottomEnd: null
                },
                /*
                columns: [ 
                    {className: 'counter', searchable: false}, 
                    {className: 'account-code'}, 
                    {className: 'account-name'}, 
                    {className: 'transaction-amount', searchable: false}, 
                    {className: 'transaction-type'}, 
                    {className: 'transaction-subtype'}, 
                    {className: 'party'}, 
                    {className: 'transaction-date'}, 
                    {className: 'period'}, 
                    {className: 'actions', searchable: false}
                ]
                */
            });
        </script>
    <!-- Page Script -->
        <script>
            $(function() {
                // Highlight column under mouse pointer
                $('#summary tbody').on('mouseenter', 'td', function () {
                    var colIdx = summary.cell(this).index().column;
 
                    $(summary.cells().nodes()).removeClass('highlight');
                    $(summary.column(colIdx).nodes()).addClass('highlight');
                });
            });
        </script>
    </body>
</html>