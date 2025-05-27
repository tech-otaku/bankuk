<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    include('conf/pop_bu_monthly_spend_table.php');     // Populate the bu_monthly_spend table.
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Monthly Spend";

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
                                    <table id="monthly-spend" class="table table-hover table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Period</th>
                                                <th>End</th>
                                                <th>Salary</th>
                                                <th>Pension</th>
                                                <th>Cash</th>
                                                <th>Utilities</th>
                                                <th>Commute</th>
                                                <th>Cards</th>
                                                <th>Supermarket</th>
                                                <th>Other</th>
                                                <th>Rent</th>
                                                <th>Charities</th>
                                                <th>TOTAL SPEND</th>
                                                <th>REMAINING</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $counter = 1;

                                                $stmt = $pdo->prepare("
                                                    SELECT 
                                                        * 
                                                    FROM 
                                                        bu_monthly_spend
                                                    ORDER BY 
                                                        bu_monthly_spend.`period` DESC;
                                                    ");
                                                $stmt->execute(); 

                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                            ?>
                                            <tr>
                                                <td><?php echo $counter; ?></td>
                                                <td><?php echo $row->period; ?></td>
                                                <td><?php echo $row->end; ?></td>
                                                <td data-filter-url-param="filter-col-6=<?php echo rawurlencode('Salary'); ?>&filter-col-10=<?php echo $row->period; ?>"><?php echo $row->salary; ?></td>
                                                <td data-filter-url-param="filter-col-6=<?php echo rawurlencode('Pension'); ?>&filter-col-10=<?php echo $row->period; ?>"><?php echo $row->pension; ?></td>
                                                <td data-filter-url-param="filter-col-5=<?php echo rawurlencode('ATM'); ?>&filter-col-10=<?php echo $row->period; ?>"><?php echo $row->cash; ?></td>
                                                <td data-filter-url-param="filter-col-5=<?php echo rawurlencode('Utility'); ?>&filter-col-10=<?php echo $row->period; ?>"><?php echo $row->utilities; ?></td>
                                                <td data-filter-url-param="filter-col-5=<?php echo rawurlencode('Commute'); ?>&filter-col-10=<?php echo $row->period; ?>"><?php echo $row->commute; ?></td>
                                                <td data-filter-url-param="filter-col-5=<?php echo rawurlencode('Card'); ?>&filter-col-10=<?php echo $row->period; ?>"><?php echo $row->cards; ?></td>
                                                <td data-filter-url-param="filter-col-5=<?php echo rawurlencode('Supermarket'); ?>&filter-col-10=<?php echo $row->period; ?>"><?php echo $row->supermarket; ?></td>
                                                <td data-filter-url-param="filter-col-5=<?php echo rawurlencode('Other'); ?>&filter-col-10=<?php echo $row->period; ?>"><?php echo $row->other; ?></td>
                                                <td data-filter-url-param="filter-col-5=<?php echo rawurlencode('Rent'); ?>&filter-col-10=<?php echo $row->period; ?>"><?php echo $row->rent; ?></td>
                                                <td data-filter-url-param="filter-col-5=<?php echo rawurlencode('Charity'); ?>&filter-col-10=<?php echo $row->period; ?>"><?php echo $row->charities; ?></td>

                                                <?php
                                                    $total_spend = 
                                                        $row->cash +
                                                        $row->utilities +
                                                        $row->commute +
                                                        $row->cards +
                                                        $row->supermarket +
                                                        $row->other +
                                                        $row->rent +
                                                        $row->charities
                                                    ;

                                                    $remaining = 
                                                        $row->salary +
                                                        $row->pension +
                                                        $total_spend;
                                                ?>

                                                <td><?php echo $total_spend; ?></td>
                                                <td><?php echo $remaining; ?></td>
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
            var monthly_spend = new DataTable('#monthly-spend', {
                layout: {
                    topStart: {
                        buttons: ['colvis']
                    }
                },
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
                    { name: 'salary' },
                    { name: 'pension' },
                    { name: 'cash' },
                    { name: 'utilities' },
                    { name: 'commute' },
                    { name: 'cards' },
                    { name: 'supermarket' },
                    { name: 'other' },
                    { name: 'rent' },
                    { name: 'charities' },
                    {
                        name: 'total_spend',
                        className: 'total-spend',
                        render: DataTable.render.number(',', '.', '2', '£'),            
                        createdCell: function (td, cellData, rowData, row, col) {
                            if (cellData < 0) {
                                $(td).addClass('debit');
                            }
                        }
                    },
                    {
                        name: 'remaining',
                        className: 'remaining',
                        render: DataTable.render.number(',', '.', '2', '£'), 
                        createdCell: function (td, cellData, rowData, row, col) {
                            if (cellData < 0) {
                                $(td).addClass('debit');
                            }
                        }
                    }
                ],
                columnDefs: [
                    {
                        targets: [ 
                            'salary:name',
                            'pension:name',
                            'cash:name',
                            'utilities:name',
                            'commute:name',
                            'cards:name',
                            'supermarket:name',
                            'other:name',
                            'rent:name',
                            'charities:name',
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
                ],
            // Callbacks
                drawCallback: function (settings) {
                    //customClass('account-code', 'account-code-')
                    //customClass('currency', 'debit')
                    //customClass('transaction-date', '')
                },
            });
        </script>
    <!-- Page Script -->
        <script>
            $(function() {
                // Highlight column under mouse pointer
                $('#monthly-spend tbody').on('mouseenter', 'td', function () {
                    var colIdx = monthly_spend.cell(this).index().column;
 
                    $(monthly_spend.cells().nodes()).removeClass('highlight');
                    $(monthly_spend.column(colIdx).nodes()).addClass('highlight');
                });
            });
        </script>
    </body>
</html>