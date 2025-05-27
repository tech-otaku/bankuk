<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    include('conf/pop_bu_monthly_spend_table.php');         // Populate the bu_monthly_spend table. The bu_reconcilliation table is dependent upon values in this table.
    include('conf/pop_bu_reconcilliation_table.php');       // Populate the bu_reconcilliation table.
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Reconcilliation";
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
                                    <h3 class="card-title"></h3>
                                </div>
                                <div class="card-body">
                                    <table id="reconcilliation" class="table table-hover table-bordered table-striped bu-data-table">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" class="text-left">#</th>
                                                <th rowspan="2" class="text-left">Period</th>
                                                <th rowspan="2" class="text-left">Period End</th>
                                                <th rowspan="2" class="text-left">Opening<br />Balance</th>
                                                <th rowspan="2" class="text-left">Income</th>
                                                <th rowspan="2" class="text-left">Monthly<br />Spend</th>
                                                <th colspan="2" class="text-center">Interest [B]</th>
                                                <th rowspan="2" class="text-center">Cashback [C]</th>
                                                <th colspan="2" class="text-center">Transfers [D]</th>
                                                <th colspan="2" class="text-center">Excluded From [E]</th>
                                                <th rowspan="2" class="text-left">Closing<br />Balance</th>
                                                <th colspan="3" class="text-center">Savings</th>
                                            </tr>
                                            <tr>
                                                <th class="text-left">Taxable</th>
                                                <th class="text-left">Tax-Free</th>
                                                <th class="text-left">Out</th>
                                                <th class="text-left">In</th>
                                                <th class="text-left">Spend</th>
                                                <th class="text-left">Income</th>
                                                <th class="text-left">Including B, C, D and E</th>
                                                <th class="text-left">Excluding B, C, D and E </th>
                                                <th class="text-left">From Monthly Spend</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $counter = 1;

                                                $stmt = $pdo->prepare("
                                                    SELECT
                                                        bu_reconcilliation.`period`,
                                                        bu_reconcilliation.`end`,
                                                        bu_reconcilliation.`opening`,
                                                        bu_reconcilliation.`income`,
                                                        bu_reconcilliation.`monthly_spend`,
                                                        bu_reconcilliation.`taxable_interest`,
                                                        bu_reconcilliation.`tax_free_interest`,
                                                        bu_reconcilliation.`cashback`,
                                                        bu_reconcilliation.`transfers_to`,
                                                        bu_reconcilliation.`transfers_from`,
                                                        bu_reconcilliation.`excluded_spend`,
                                                        bu_reconcilliation.`excluded_income`,
                                                        bu_reconcilliation.`closing`,
                                                        bu_reconcilliation.`savings_actual`,
                                                        bu_reconcilliation.`savings`,
                                                        bu_monthly_spend.`id` AS _monthly_spend_record_id,
                                                        bu_monthly_spend.`salary`,
                                                        bu_monthly_spend.`pension`,
                                                        bu_monthly_spend.`cash`,
                                                        bu_monthly_spend.`utilities`,
                                                        bu_monthly_spend.`commute`,
                                                        bu_monthly_spend.`cards`,
                                                        bu_monthly_spend.`supermarket`,
                                                        bu_monthly_spend.`other`,
                                                        bu_monthly_spend.`rent`,
                                                        bu_monthly_spend.`charities`
                                                    FROM
                                                        bu_reconcilliation
                                                    LEFT JOIN 
                                                        bu_monthly_spend ON bu_reconcilliation.`period` = bu_monthly_spend.`period`
                                                    ORDER BY 
                                                        bu_reconcilliation.`period` DESC;
                                                ");
                                                $stmt->execute(); 

                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                            ?>
                                            
                                            <tr>
                                                <td><?php echo $counter; ?></td>
                                                <td><?php echo $row->period; ?></td>
                                                <td><?php echo $row->end; ?></td>
                                                <td><?php echo $row->opening; ?></td>
                                                <td data-filter-url-param="filter-col-5=<?php echo rawurlencode('Income'); ?>&filter-col-10=<?php echo $row->period; ?>"><?php echo $row->income; ?></td>
                                                <?php //TableCellLinks($fmt_currency, $data = array('amount' => $row->income, 'filter' => 'filter-col-5=' . rawurlencode('Income') . '&filter-col-10=' . $row->period));?>
                                                <td><a class="no-link-color" href="#" data-bs-toggle="modal" data-bs-target="#view-monthly-spend-modal" data-mysql-table="bu_monthly_spend" data-record-id="<?php echo $row->_monthly_spend_record_id; ?>"><?php echo $fmt_currency->formatCurrency($row->monthly_spend, "GBP"); ?></a></td>
                                                <td data-filter-url-param="filter-col-5=<?php echo rawurlencode('Taxable Interest'); ?>&filter-col-10=<?php echo $row->period; ?>"><?php echo $row->taxable_interest; ?></td>
                                                <?php //TableCellLinks($fmt_currency, $data = array('amount' => $row->taxable_interest, 'filter' => 'filter-col-5=' . rawurlencode('Taxable Interest') . '&filter-col-10=' . $row->period));?>
                                                <td data-filter-url-param="filter-col-5=<?php echo rawurlencode('Non-taxable Interest'); ?>&filter-col-10=<?php echo $row->period; ?>"><?php echo $row->tax_free_interest; ?></td>
                                                <?php //TableCellLinks($fmt_currency, $data = array('amount' => $row->tax_free_interest, 'filter' => 'filter-col-5=' . rawurlencode('Non-taxable Interest') . '&filter-col-10=' . $row->period));?>
                                                <td data-filter-url-param="dt-search-0=<?php echo rawurlencode('cashback'); ?>&filter-col-10=<?php echo $row->period; ?>"><?php echo $row->cashback; ?></td>
                                                <?php //TableCellLinks($fmt_currency, $data = array('amount' => $row->cashback, 'filter' => 'dt-search-0=' . rawurlencode('cashback') . '&filter-col-10=' . $row->period));?>
                                                <td data-filter-url-param="filter-col-5=<?php echo rawurlencode('Transfer'); ?>&filter-col-10=<?php echo $row->period; ?>"><?php echo $row->transfers_to; ?></td>
                                                <?php //TableCellLinks($fmt_currency, $data = array('amount' => $row->transfers_to, 'filter' => 'filter-col-5=' . rawurlencode('Transfer') . '&filter-col-10=' . $row->period));?>
                                                <td data-filter-url-param="filter-col-5=<?php echo rawurlencode('Transfer'); ?>&filter-col-10=<?php echo $row->period; ?>"><?php echo $row->transfers_from; ?></td>
                                                <?php //TableCellLinks($fmt_currency, $data = array('amount' => $row->transfers_from, 'filter' => 'filter-col-5=' . rawurlencode('Transfer') . '&filter-col-10=' . $row->period));?>
                                                <td data-filter-url-param="filter-col-5=<?php echo rawurlencode('Excluded'); ?>&filter-col-10=<?php echo $row->period; ?>"><?php echo $row->excluded_spend; ?></td>
                                                <?php //TableCellLinks($fmt_currency, $data = array('amount' => $row->excluded_spend, 'filter' => 'filter-col-5=' . rawurlencode('Excluded') . '&filter-col-10=' . $row->period));?>
                                                <td data-filter-url-param="filter-col-5=<?php echo rawurlencode('Excluded'); ?>&filter-col-10=<?php echo $row->period; ?>"><?php echo $row->excluded_income; ?></td>
                                                <?php //TableCellLinks($fmt_currency, $data = array('amount' => $row->excluded_income, 'filter' => 'filter-col-5=' . rawurlencode('Excluded') . '&filter-col-10=' . $row->period));?>
                                                <td><?php echo $row->closing; ?></td>
                                                <td><?php echo $row->savings_actual; ?></td>
                                                <td><?php echo $row->savings; ?></td>

                                                <?php

                                                    $monthly_spend_savings = 
                                                        $row->salary +
                                                        $row->pension +
                                                        $row->cash +
                                                        $row->utilities +
                                                        $row->commute +
                                                        $row->cards +
                                                        $row->supermarket +
                                                        $row->other +
                                                        $row->rent +
                                                        $row->charities
                                                    ;
/*
                                                    $remaining = 
                                                        $row->_salary +
                                                        $row->_pension +
                                                        $total_spend;
                                                        */
                                                ?>
                                                
                                                <td><?php echo $monthly_spend_savings; ?></td>
                                                <!--
                                                <td><?php //echo $fmt_currency->formatCurrency($remaining, "GBP"); ?></td>
                                                -->
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
    <!-- Update Transaction Modal -->
        <div class="modal fade" id="view-monthly-spend-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">   <!-- `.modal-dialog-centered` to centre on screen -->
                <div class="modal-content"  style="position: relative;">
                    <div class="modal-header">
                        <h4 class="modal-title" id="staticBackdropLabel"><!-- jQuery populated --></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <!-- Inject the update transaction form -->
                        <?php //include("forms/form_update_transaction.php"); ?>
                        <div class="card-body">
                            <div id="view-monthly-spend" class="container w-75" style="border: 1px solid #6c757d;">
                                <h4 id="caption"></h4>
                                <div class="row bg-light">
                                    <div class="col">
                                        Salary
                                    </div>
                                    <div id="salary" class="col text-end" >
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        Pension
                                    </div>
                                    <div id="pension" class="col text-end">
                                    </div>
                                </div>
                                <div class="row bg-light">
                                    <div class="col">
                                        Cash
                                    </div>
                                    <div id="cash" class="col text-end">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        Utilities
                                    </div>
                                    <div id="utilities" class="col text-end">
                                    </div>
                                </div>
                                <div class="row bg-light">
                                    <div class="col">
                                        Commute
                                    </div>
                                    <div id="commute" class="col text-end">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        Cards
                                    </div>
                                    <div id="cards" class="col text-end">
                                    </div>
                                </div>
                                <div class="row bg-light">
                                    <div class="col">
                                        Supermarket
                                    </div>
                                    <div id="supermarket" class="col text-end">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        Other
                                    </div>
                                    <div id="other" class="col text-end">
                                    </div>
                                </div>
                                <div class="row bg-light">
                                    <div class="col">
                                        Rent
                                    </div>
                                    <div id="rent" class="col text-end">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        Charities
                                    </div>
                                    <div id="charities" class="col text-end">
                                    </div>
                                </div>
                                <div class="row bg-secondary">
                                    &nbsp;
                                </div>
                                <div class="row">
                                    <div class="col">
                                        Total Spend
                                    </div>
                                    <div id="total_spend" class="col text-end">
                                    </div>
                                </div>
                                <div class="row bg-light">
                                    <div class="col">
                                        Remaining
                                    </div>
                                    <div id="remaining" class="col text-end">
                                    </div>
                                </div>
                            </div>
                        <!--       
                        <div class="card-footer">
                            
                        </div>
                                            -->

                    </div>
                    <div class="modal-footer">
                    <!-- Update-modal's close button -->
                        <button type="button" class="btn btn-secondary float-end" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!--
        <style>
            
            table#view-monthly-spend {
                table-layout: fixed;
                width: 100%;
                border-collapse: collapse;
                border: 3px solid purple;
            }
            table#view-monthly-spend thead th:nth-child(1) {
                width: 30%;
            }

            table#view-monthly-spend thead th:nth-child(2) {
                width: 20%;
            }

            table#view-monthly-spend thead th:nth-child(3) {
                width: 15%;
            }

            table#view-monthly-spend thead th:nth-child(4) {
                width: 35%;
            }

            table#view-monthly-spend th,
            table#view-monthly-spend td {
                padding: 20px;
            }
        </style>
        -->
    <!-- Common Scripts -->
        <?php include("partials/scripts.php"); ?>
    <!-- DataTable Table -->
        <script>
            var reconcilliation = new DataTable('#reconcilliation', {

                drawCallback: function (settings) {
//                    customClass('account-code', 'account-code-')
                    //customClass('currency', 'debit')
                    //customClass('transaction-date', '')
                },
                order: [
                    [1, 'desc']
                ],
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
                columns: [
                    { name: 'counter' }, 
                    { name: 'period' },
                    {
                        name: 'end', 
                        type: 'date', 
                        render: DataTable.render.datetime('ddd DD/MM/YYYY'),  // requires moment.js
                        createdCell: function (td, cellData, rowData, row, col) {
                            $(td).addClass(Chronology(cellData));
                        }
                    },  
                    { 
                        name: 'opening',
                        render: DataTable.render.number(',', '.', '2', '£'),
                        createdCell: function (td, cellData, rowData, row, col) {
                            if (cellData < 0) {
                                $(td).addClass('debit');
                            }
                        }
                    },
                    { name: 'income' },
                    { name: 'monthly_spend' },
                    { name: 'taxable_interest' },
                    { name: 'tax_free_interest' },
                    { name: 'cashback' },
                    { name: 'transfers_to' },
                    { name: 'transfers_from' },
                    { name: 'excluded_spend' },
                    { name: 'excluded_income' },
                    { 
                        name: 'closing',
                        render: DataTable.render.number(',', '.', '2', '£'),
                        createdCell: function (td, cellData, rowData, row, col) {
                            if (cellData < 0) {
                                $(td).addClass('debit');
                            }
                        }
                    },
                    { 
                        name: 'savings_actual',
                        render: DataTable.render.number(',', '.', '2', '£'),
                        createdCell: function (td, cellData, rowData, row, col) {
                            if (cellData < 0) {
                                $(td).addClass('debit');
                            }
                        }
                    },
                    { 
                        name: 'savings',
                        render: DataTable.render.number(',', '.', '2', '£'),
                        createdCell: function (td, cellData, rowData, row, col) {
                            if (cellData < 0) {
                                $(td).addClass('debit');
                            }
                        }
                    },
                    { 
                        name: 'monthly_spend_savings',
                        render: DataTable.render.number(',', '.', '2', '£'),
                        createdCell: function (td, cellData, rowData, row, col) {
                            if (cellData < 0) {
                                $(td).addClass('debit');
                            }
                        }
                    },
                ],
                columnDefs: [
                    {
                        targets: [ 
                            'income:name',
                            'taxable_interest:name',
                            'tax_free_interest:name',
                            'cashback:name',
                            'transfers_to:name',
                            'transfers_from:name',
                            'excluded_spend:name',
                            'excluded_income:name'
                        ],
                        createdCell: function (td, cellData, rowData, row, col) {
                            var amount = DataTable.render.number(',', '.', '2', '£').display(cellData)  // See https://datatables.net/examples/basic_init/data_rendering.html
                
                            if (intVal(cellData) !== 0) {
                                var filter_url_param = $(td).attr("data-filter-url-param");
                                $(td).html(`<a class="no-link-color" href="bu_manage_transactions.php?${filter_url_param}">${amount}</a>`);
                            } else {
                                $(td).text(amount)
                            }
                           
                            if (intVal(cellData) < 0) {
                                $(td).addClass('debit');
                            }
                        }
                    }
                ]
            });
        </script>
    <!-- DataTable -->
     <!--
        <script>
            new DataTable('#view-monthly-spend', {
                ordering: false,
                layout: {
                    topStart: null,
                    topEnd: null,
                    bottomStart: null,
                    bottomEnd: null
                },
                columns: [
                    {
                        name:'header', 
                        orderable:false
                    },
                    {
                        name:'value', 
                        orderable:false,
                        render: DataTable.render.number(',', '.', '2', '£'),
                        createdCell: function (td, cellData, rowData, row, col) {
                            if (cellData < 0) {
                                $(td).addClass('debit');
                            }
                        }
                    }
                ],
                drawCallback ()
            });
        </script>
            -->
    <!-- Ajax Delete -->
        <script src="ajax/bu_ajax_view_monthly_spend.js"></script>
    <!-- Page Script -->
        <script>
            $(function() {
                // Highlight column under mouse pointer
                $('#reconcilliation tbody').on('mouseenter', 'td', function () {
                    var colIdx = reconcilliation.cell(this).index().column;
 
                    $(reconcilliation.cells().nodes()).removeClass('highlight');
                    $(reconcilliation.column(colIdx).nodes()).addClass('highlight');
                });
            });
        </script>
    </body>
</html>