<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Summary";
    
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
            <div class="content-wrapper">   <!-- Temporarily .dummy -->
                <!-- Content Header with logged in user details (Page header) -->
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
                                <div class="card-header p-6" style="position: relative;">
                                            <div class="float-start" style="position: absolute; top: 30%; font-size: 1.5em;">
                                                <?php echo date('l jS F Y', strtotime($bu_settings['current_start'])) . ' to ' . date('l jS F Y', strtotime($bu_settings['current_end'])); ?>
                                            </div>
                                            <div class="float-end" style="font-size: 3em; font-weight: 700;">
                                                <?php echo $bu_settings['current_period']; ?>
                                            </div>
                                </div>
                                <div class="card-body">
                                    <?php //echo TaxYears($pdo); ?>
                                    <?php

                                        $testDate = '2025-03-22';

                                        define('TOMORROW', 1);      // Today + 1 day
                                        define('NEXT_DAYS' ,5);
                                        define('NEXT_START', TOMORROW + 1);    // Today + 2 days
                                        define('NEXT_END', NEXT_START + NEXT_DAYS - 1);      // Today + 6 days


                                        $today = new DateTime();
                                        //$today = new DateTime($testDate);
                                        $today->settime(0,0);
                                       

                                        $periodEnd = new DateTime($bu_settings['current_end']);
                                        $periodEnd->settime(0,0);
                                        //echo 'Period End: ' . $periodEnd->format('d/m/y'). '<br />';

                                        $tomorrow = new DateTime();     // Today's date
                                        //$tomorrow = new DateTime($testDate);
                                        $tomorrow->settime(0,0);
                                        $tomorrow->modify('+' . TOMORROW . ' days');
                                        //echo 'Tomorrow: ' . $tomorrow->format('d/m/y'). '<br />';
                                
                                        $nextStart = new DateTime();    // Today's date
                                        //$nextStart = new DateTime($testDate);
                                        $nextStart->settime(0,0);
                                        $nextStart->modify('+' . NEXT_START . ' days');

                                        $nextEnd = new DateTime();      // Today's date
                                        //$nextEnd = new DateTime($testDate);
                                        $nextEnd->settime(0,0);
                                        $nextEnd->modify('+' . NEXT_END . ' days');
                                        //echo 'Next End: ' . $nextEnd->format('d/m/y') . '<br />';

                                        //echo ($nextEnd->diff($periodEnd))->format('%R%a') + 1;
                                        $nextDays = ($nextEnd->diff($periodEnd))->format('%R%a');
                                        //echo 'Days to Period End: ' . $nextDays . '<br />';

                                        $d = min(NEXT_DAYS, (NEXT_DAYS + $nextDays));
                                        //echo 'Min ' . NEXT_DAYS . ' and (' . NEXT_DAYS . ' + ' . $nextDays . '): ' .  $d . '<br />';
                                        if ($nextDays <= 0) {
                                            $d = 5 + $nextDays;
                                            $nextEnd->modify($nextDays . ' days');
                                            //echo 'Adjusted Next End: ' . $nextEnd->format('D d/m/y');
                                            //echo "Period End is on or before Next End";
                                        }


                                    ?>
                                    <!--
                                    <div class="row">
                                        <div class="col-sm-9">
                                            <input class="form-check-input" type="checkbox" value="" name="hide-closed" id="hide-closed" checked>
                                            <label class="form-check-label" for="hide-closedl">Hide closed accounts</label>
                                        </div>
                                    </div>
                                    -->
                                    <div class="row">
                                        <div class="col-sm-1">Show Accounts:</div>
                                        <div class="col-sm-4">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="account-status" id="account-status-all" value="all">
                                                <label class="form-check-label" for="inlineRadio1">All</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="account-status" id="account-status-open" value="open" checked>
                                                <label class="form-check-label" for="inlineRadio2">Open</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="account-status" id="account-status-closed" value="closed">
                                                <label class="form-check-label" for="inlineRadio3">Closed</label>
                                            </div>
                                        </div>
                                    </div>
                                    <table id="summary" class="table bu-table-hover table-bordered bu-table-striped">
                                        <thead>
                                            <tr>                                    <!-- Row 1 -->
                                                <th rowspan="2" style="vertical-align: middle;">A/C Code</th>           <!-- Column 1 -->
                                                <th rowspan="2" style="vertical-align: middle;">A/C Name</th>           <!-- Column 1 -->
                                                <th rowspan="2" style="vertical-align: middle; text-align: center;">Upto and including<br /><?php echo $today->format('l jS F Y'); ?><br />[<span class="text-grey">A</span>]</th>                      <!-- Column 3 -->
                                                <th rowspan="2" style="vertical-align: middle; text-align: center;">By Period Ending<br /><?php echo $periodEnd->format('l jS F Y'); ?><br />[<span class="text-grey">B</span>]</th>                 <!-- Column 4 -->
                                                <th rowspan="2" style="vertical-align: middle; text-align: center;">Difference<br />[<span class="text-grey">A</span> - <span class="text-grey">B</span>]</th>              <!-- Column 5 -->
                                                <th colspan="2" style="text-align: center; border-left: 2px solid #aaaaaa; border-top: 2px solid #aaaaaa; border-right: 2px solid #aaaaaa;" >Today</th>              <!-- Columns 6 & 7 -->
                                                <th colspan="2" style="text-align: center; border-top: 2px solid #aaaaaa; border-right: 2px solid #aaaaaa;">
                                                    <?php 
                                                        if (($tomorrow->diff($periodEnd))->format('%R%a') >= 0) {
                                                            echo 'Tomorrow ';
                                                        }
                                                    ?>
                                                </th>           <!-- Columns 8 & 9 -->
                                                <th colspan="2" style="text-align: center; border-top: 2px solid #aaaaaa; border-right: 2px solid #aaaaaa;">
                                                    <?php 
                                                        if ($d > 1) {
                                                            echo 'Next ' . $d . ' Days';
                                                        } else if ($d == 1) {
                                                            echo 'Next Day';
                                                        }
                                                    ?>
                                                </th>        <!-- Columns 10 & 11 -->
                                                
                                            </tr>
                                            <tr>                                    <!-- Row 1 -->
                                                
                                                
                                                
                                                
                                                <th colspan="2" style="vertical-align: middle; text-align: center; border-left: 2px solid #aaaaaa; border-right: 2px solid #aaaaaa;"><?php echo $today->format('D d/m/y'); ?></th>
                                                
                                                <th colspan="2" style="vertical-align: middle; text-align: center; border-right: 2px solid #aaaaaa;"><?php 
                                                        if (($tomorrow->diff($periodEnd))->format('%R%a') >= 0) {
                                                            echo $tomorrow->format('D d/m/y');
                                                        }
                                                    ?>
                                                </th>
                                                
                                                <th colspan="2" style="vertical-align: middle; text-align: center; border-right: 2px solid #aaaaaa;">
                                                    <?php 
                                                        if ($d > 1) {
                                                            echo 'From ' . $nextStart->format('D d/m/y') . '<br />to ' .  $nextEnd->format('D d/m/y');
                                                        } else if ($d == 1) {
                                                            echo $nextEnd->format('D d/m/y');
                                                        }
                                                    ?>
                                                </th>
                                                
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
                                                        bu_transactions.`transaction_date` <= (
                                                            SELECT 
                                                                bu_accounting_periods.`period_end`
                                                            FROM 
                                                                bu_accounting_periods
                                                            WHERE 
                                                                CURDATE() >= bu_accounting_periods.`period_start` AND CURDATE() <= bu_accounting_periods.`period_end`
                                                        );
                                                ");
                                                $stmt->execute();

                                                $stmt = $pdo->prepare("
                                                    SELECT 
                                                        bu_accounts.`account_id_alpha`, 
                                                        bu_banks.`trading_name`,
                                                        bu_accounts.`name`,
                                                        bu_accounts.`sort_code`,
                                                        bu_accounts.`account_number`,
                                                        bu_accounts.`status`,
                                                        SUM(IF(temp.`transaction_date` <= CURDATE(),temp.`amount`,0)) AS totalA, 
                                                        SUM(IF(temp.`amount` != 0,temp.`amount`,0)) AS totalB, 
                                                        COUNT(IF(temp.`transaction_date` = CURDATE() AND temp.amount != 0,temp.`amount`,NULL)) AS totalC,
                                                        SUM(IF(temp.`transaction_date` = CURDATE(),temp.`amount`,0)) AS totalD,
                                                        COUNT(IF(temp.`transaction_date` = CURDATE() + INTERVAL 1 DAY AND temp.amount != 0,temp.`amount`,NULL)) AS totalE,
                                                        SUM(IF(temp.`transaction_date` = CURDATE() + INTERVAL 1 DAY,temp.`amount`,0)) AS totalF,
                                                        COUNT(IF(temp.`transaction_date` >= CURDATE() + INTERVAL 2 DAY AND temp.`transaction_date` <= CURDATE() + INTERVAL 6 DAY AND temp.amount != 0,temp.`amount`,NULL)) AS totalG,
                                                        SUM(IF(temp.`transaction_date` >= CURDATE() + INTERVAL 2 DAY AND temp.`transaction_date` <= CURDATE() + INTERVAL 6 DAY,temp.`amount`,0)) AS totalH 
                                                    FROM 
                                                        bu_accounts
                                                    LEFT JOIN 
                                                        temp ON bu_accounts.`account_id_alpha` = temp.`account_id_alpha` 
                                                    LEFT JOIN 
                                                        bu_banks ON bu_accounts.`bank_id` = bu_banks.`bank_id` 
                                                    GROUP BY bu_accounts.`account_id_alpha`, 
                                                        bu_banks.`trading_name`,
                                                        bu_accounts.`name`, 
                                                        bu_accounts.`sort_code`, 
                                                        bu_accounts.`account_number`, 
                                                        bu_accounts.`status`;
                                                ");
                                                $stmt->execute();

                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                            ?>
                                            <tr data-status="<?php echo strtolower($row->status);?>"style="font-size: 1.25em !important;">
                                                <td><?php echo $row->account_id_alpha; ?></td>
                                                <td><?php echo $row->trading_name . ' ' . $row->name . ' <span class="account-number">' . $row->sort_code . ' ' . $row->account_number .'</span>' . ((strtolower($row->status) === 'closed') ? ' [<span class="account-closed">CLOSED</span>]' : ''); ?></td>
                                                <!-- <td><?php //echo $row->_name; ?></td> -->
                                                <td><?php echo $fmt_currency->formatCurrency($row->totalA, "GBP"); ?></td>
                                                <td><?php echo $fmt_currency->formatCurrency($row->totalB, "GBP"); ?></td>
                                                <td><?php echo $fmt_currency->formatCurrency($row->totalB - $row->totalA, "GBP"); ?></td>
                                                <td style="border-left: 2px solid #aaaaaa;"><?php echo $row->totalC; ?></td>
                                                <td style="border-right: 2px solid #aaaaaa;"><?php echo $fmt_currency->formatCurrency($row->totalD, "GBP"); ?></td>
                                                <td><?php echo $row->totalE; ?></td>
                                                <td style="border-right: 2px solid #aaaaaa;"><?php echo $fmt_currency->formatCurrency($row->totalF, "GBP"); ?></td>
                                                <td><?php echo $row->totalG; ?></td>
                                                <td style="border-right: 2px solid #aaaaaa;"><?php echo $fmt_currency->formatCurrency($row->totalH, "GBP"); ?></td>
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
                                                <th style="border-bottom: 2px solid #aaaaaa !important; border-left: 2px solid #aaaaaa;"></th>
                                                <th style="border-right: 2px solid #aaaaaa; border-bottom: 2px solid #aaaaaa !important;""></th>
                                                <th style="border-bottom: 2px solid #aaaaaa !important;">></th>
                                                <th style="border-right: 2px solid #aaaaaa; border-bottom: 2px solid #aaaaaa !important;""></th>
                                                <th style="border-bottom: 2px solid #aaaaaa !important;"></th>
                                                <th style="border-right: 2px solid #aaaaaa; border-bottom: 2px solid #aaaaaa !important;"></th>
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
            $.fn.dataTable.ext.search.push(
                // See https://live.datatables.net/nivenixu/1/edit
                function( settings, searchData, index, rowData, counter ) {
                    console.log(settings)

                    var option = $('input[name="account-status"]').filter(":checked").val();
                    //console.log(option)

                    //var api = new $.fn.dataTable.Api( settings ); // Get API instance for table
                    var api = api = $('#summary').dataTable().api(); // Get API instance for table
                    //console.log(api)
                    var node = api.row(index).node();

                    switch (option) {   // Will be one of 'all', 'open' or 'closed'
                        case 'all':
                            return true; // Include all account records
                            break;
                        default:
                            if ($(node).data('status') === option) {
                                return true;    // Include account records whose data-status attribute value equals the value of the selected radio button
                            } else {
                                return false;
                            }
                            break;
                    }

                    return true;
                }
            );

            var summary = new DataTable('#summary', {
                select: {
                    items: 'cell',
                    style: 'os'
                },
                footerCallback: function (row, data, start, end, display) {
                    console.log('Fired')
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
                        .column(
                            2, 
                            {
                                search: 'applied'
                            }
                        )
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    totalB = api
                        .column(3, 
                            {
                                search: 'applied'
                            }
                        )
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    totalC = api
                        .column(4, 
                            {
                                search: 'applied'
                            }
                        )
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    totalD = api
                        .column(5, 
                            {
                                search: 'applied'
                            }
                        )
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    
                    totalE = api
                        .column(6, 
                            {
                                search: 'applied'
                            }
                        )
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    totalF = api
                        .column(7, 
                            {
                                search: 'applied'
                            }
                        )
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    totalG = api
                        .column(8, 
                            {
                                search: 'applied'
                            }
                        )
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    totalH = api
                        .column(9, 
                            {
                                search: 'applied'
                            }
                        )
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    totalI = api
                        .column(10, 
                            {
                                search: 'applied'
                            }
                        )
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
                    {className: 'entity'}, 
                    {className: 'transaction-date'}, 
                    {className: 'period'}, 
                    {className: 'actions', searchable: false}
                ]
                */
            });

            $('input[name="account-status"]').on('change', function () {
                summary.draw();
            });
        </script>
    <!-- Page Script -->
        <script>
            $(function() {
                // Highlight column under mouse pointer
                /*
                $('#summary tbody').on('mouseenter', 'td', function () {
                    var colIdx = summary.cell(this).index().column;
 
                    $(summary.cells().nodes()).removeClass('highlight');
                    $(summary.column(colIdx).nodes()).addClass('highlight');
                });
                */
            });
        </script>
    </body>
</html>