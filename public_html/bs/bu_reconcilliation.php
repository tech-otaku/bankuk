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
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><?php echo $page_name; ?></li>
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
                                    <h3 class="card-title"></h3>
                                </div>
                                <div class="card-body">
                                    <table id="reconcilliation" class="table table-hover table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Period</th>
                                                <th>End</th>
                                                <th>OPENING</th>
                                                <th>Income</th>
                                                <th>Monthly Spend</th>
                                                <th>Taxable Interest</th>
                                                <th>Tax-Free Interest</th>
                                                <th>Cashback</th>
                                                <th>Transfers To</th>
                                                <th>Transfers From</th>
                                                <th>Exclude from Spend</th>
                                                <th>Excluded from Income</th>
                                                <th>CLOSING</th>
                                                <th>SAVINGS #1</th>
                                                <th>SAVINGS #2</th>
                                                <th>SAVINGS #3</th>
                                                <!--
                                                <th>TOTAL SPEND</th>
                                                <th>REMAINING</th>
                                                -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $counter = 1;

                                                $stmt = $pdo->prepare("
                                                    SELECT 
                                                        r1.period,
                                                        r1.`end`,
                                                        r1.opening,
                                                        r1.income,
                                                        r1.monthly_spend,
                                                        r1.taxable_interest,
                                                        r1.tax_free_interest,
                                                        r1.cashback,
                                                        r1.transfers_to,
                                                        r1.transfers_from,
                                                        r1.excluded_spend,
                                                        r1.excluded_income,
                                                        r1.closing,
                                                        r1.savings_actual,
                                                        r1.savings,
                                                        ms1.salary,
                                                        ms1.pension,
                                                        ms1.cash,
                                                        ms1.utilities,
                                                        ms1.commute,
                                                        ms1.cards,
                                                        ms1.supermarket,
                                                        ms1.other,
                                                        ms1.rent,
                                                        ms1.charities
                                                    FROM
                                                        bu_reconcilliation AS r1
                                                    LEFT JOIN 
                                                        bu_monthly_spend AS ms1 ON r1.period = ms1.period
                                                    ORDER BY 
                                                        r1.period DESC;
                                                ");
                                                $stmt->execute(); 

                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                            ?>
                                            <tr>
                                                <td><?php echo $counter; ?></td>
                                                <td><?php echo $row->period; ?></td>
                                                <td><?php echo $row->end; ?></td>
                                                <td><?php echo $fmt_currency->formatCurrency($row->opening, "GBP"); ?></td>
                                                <td><?php echo $fmt_currency->formatCurrency($row->income, "GBP"); ?></td>
                                                <td><?php echo $fmt_currency->formatCurrency($row->monthly_spend, "GBP"); ?></td>
                                                <td><?php echo $fmt_currency->formatCurrency($row->taxable_interest, "GBP"); ?></td>
                                                <td><?php echo $fmt_currency->formatCurrency($row->tax_free_interest, "GBP"); ?></td>
                                                <td><?php echo $fmt_currency->formatCurrency($row->cashback, "GBP"); ?></td>
                                                <td><?php echo $fmt_currency->formatCurrency($row->transfers_to, "GBP"); ?></td>
                                                <td><?php echo $fmt_currency->formatCurrency($row->transfers_from, "GBP"); ?></td>
                                                <td><?php echo $fmt_currency->formatCurrency($row->excluded_spend, "GBP"); ?></td>
                                                <td><?php echo $fmt_currency->formatCurrency($row->excluded_income, "GBP"); ?></td>
                                                <td><?php echo $fmt_currency->formatCurrency($row->closing, "GBP"); ?></td>
                                                <td><?php echo $fmt_currency->formatCurrency($row->savings_actual, "GBP"); ?></td>
                                                <td><?php echo $fmt_currency->formatCurrency($row->savings, "GBP"); ?></td>

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
                                                
                                                <td><?php echo $fmt_currency->formatCurrency($monthly_spend_savings, "GBP"); ?></td>
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
    <!-- Common Scripts -->
        <?php include("partials/scripts.php"); ?>
    <!-- DataTable Table -->
        <script>
            var reconcilliation = new DataTable('#reconcilliation', {

                drawCallback: function (settings) {
//                    customClass('account-code', 'account-code-')
                    customClass('currency', 'debit')
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
                    {
                        className: 'counter'}, 
                    {
                        className: 'period'},
                    {
                        className: 'end', 
                        type: 'date', 
                        render: DataTable.render.datetime('ddd DD/MM/YYYY') // requires moment.js
                    },  
                    {
                        className: 'opening'
                    },
                    {
                        className: 'income currency'
                    },
                    {
                        className: 'monthly-spend currency'
                    },
                    {
                        className: 'taxable-interest currency'
                    },
                    {
                        className: 'tax-free-interest currency'
                    },
                    {
                        className: 'cashback currency'
                    },
                    {
                        className: 'transfers-to currency'
                    },
                    {
                        className: 'transfers-from currency'
                    },
                    {
                        className: 'excluded-spend currency'
                    },
                    {
                        className: 'excluded-income currency'
                    },
                    {
                        className: 'closing currency'
                    },
                    {
                        className: 'savings-1 currency'
                    },
                    {
                        className: 'savings-2 currency'
                    },
                    {
                        className: 'savings-3 currency'
                    }
                ]
            });
        </script>
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