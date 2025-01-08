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
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="bu_dashboard.php">Dashboard</a></li>
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
                                    <h3 id="filter-total" class="card-title"><span class="ftotal currency"></span></h3>
                                </div>
                                <div class="card-body">
                                    <table id="regular-debits" class="table table-hover table-bordered table-striped bu-data-table">
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
                                                <th></th>
                                                <th>Day</th>
                                                <th>Last</th>
                                                <th>Next</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php


                                                $counter = 1;

                                                $stmt = $pdo->prepare("
                                                SELECT 
                                                    rd1.id,
                                                    rd1.account_id,
                                                    rd1.account_id_alpha,
                                                    b1.trading_name,
                                                    a1.name,
                                                    rd1.amount,
                                                    tt1.description AS _type,
                                                    tt2.description AS _subtype,
                                                    p1.party,
                                                    rd1.day,
                                                    -- rd1.period,
                                                    rd1.notes,
                                                    rd1.regular_debit_type,
                                                    rdt1.description,
                                                    rd1.last,
                                                    rd1.next
                                                FROM
                                                    bu_regular_debits AS rd1
                                                LEFT JOIN
                                                    bu_accounts AS a1 ON rd1.account_id_alpha = a1.account_id_alpha
                                                LEFT JOIN
                                                    bu_banks AS b1 ON a1.bank_id = b1.bank_id
                                                LEFT JOIN
                                                    bu_parties AS p1 ON rd1.party_id = p1.party_id
                                                LEFT JOIN
                                                    bu_transaction_types AS tt1 ON rd1.type = tt1.type
                                                LEFT JOIN
                                                    bu_transaction_types AS tt2  ON rd1.sub_type = tt2.type
                                                LEFT JOIN
                                                    bu_regular_debit_types rdt1 ON rd1.regular_debit_type = rdt1.type
                                                ORDER BY 
                                                    rd1.day DESC;
                                                ");
                                                $stmt->execute();

                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                            ?>
                                            <tr class="regular-debit">
                                                <td <?php echo ((!empty($row->notes)) ? 'class="has-note" data-counter="' . $counter . '" data-note="' . $row->notes .'" data-party="' . $row->party . '" data-amount="'  . $fmt_currency->formatCurrency($row->amount, "GBP") . '" data-date="'  . $fmt_date->format(strtotime($row->last)) . '"'  : "") . '>' . $counter; ?></td>
                                                <td><?php echo $row->id; ?></td>
                                                <td>
                                                    <a data-id="<?php echo $row->id; ?>" class="btn btn-primary btn-sm add-current-due-date" href="#">
                                                        <i class="fa fa-plus"></i>
                                                    </a>
                                                </td>
                                                <td><?php echo $row->account_id_alpha; ?></td>
                                                <td><?php echo $row->trading_name . ' ' . $row->name; ?></td>
                                                <td><?php echo $row->amount; ?></td>
                                                <td><?php echo $row->_type; ?></td>
                                                <td><?php echo $row->_subtype; ?></td>
                                                <td><?php echo $row->party; ?></td>
                                                <td><?php echo $row->description; ?></td>
                                                <td><?php echo $row->day; ?></td>
                                                <td><?php echo $row->last; ?></td>  <!-- The display format is defined using a DataTable render -->
                                                <td><?php echo $row->next; ?></td>  <!-- The display format is defined using a DataTable render -->
                                                <!-- <td><?php //echo date_format(date_create($row->date), 'D d/m/Y'); ?></td> -->
                                                <!-- <td><?php //echo $row->period; ?></td> -->
                                                <td>
                                                    <a class="btn btn-success btn-sm" href="bu_view_regular_debit.php?id=<?php echo $row->id; ?>">
                                                        <i class="fa fa-edit"></i>
                                                        <!-- Edit -->
                                                    </a>
                                                    <a data-mysql-table="bu_regular_debits" data-record-id="<?php echo $row->id; ?>" class="btn btn-danger btn-sm delete-record" href="#">
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
                                        <tfoot>
                                            <tr>
                                            </tr>
                                        </tfoot>
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
    <!-- Common Scripts -->
        <?php include("partials/scripts.php"); ?>
    <!-- DataTable Table -->
        <script>
            //DataTable.datetime('ddd DD MM YYYY');

            var regular_debits = new DataTable('#regular-debits', {
            
                //search('Aldi').draw();
                
                /*
                initComplete: function () {
                    this.api()
                        .columns([1,2,4,5,6,7,8],)
                        .every(function () {
                            var column = this;
            
                            // Create select element and listener
                            var select = $('<select><option value="">Show all</option></select>')
                                .appendTo($(column.header()))
                                .on('change', function () {
                                    column
                                        .search($(this).val(), {exact: true})
                                        .draw();
                                });
            
                                $( select ).click( function(e) {
                                    e.stopPropagation();
                                });
            
                            // Add list of options
                            column
                                .data()
                                .unique()
                                .sort()
                                .each(function (d, j) {
                                    select.append(
                                        '<option value="' + d + '">' + d + '</option>'
                                    );
                                });
                        });
                },
                */
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
            
                    // Remove the formatting to get integer data for summation
                    var intVal = function (i) {
                        //console.log(i);
                        return typeof i === 'string'
                            //? i.replace(/[\$,]/g, '') * 1
                            ? i.replace(/[^0-9.-]+/g, '') * 1
                            : typeof i === 'number'
                            ? i
                            : 0;
                    };
            
                    // Total over all pages
                    total = api
                        .column(5, {search: 'applied'})
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
            
                    // Total over this page
                    pageTotal = api
                        .column(5, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    
                    $('span.ftotal').html(new Intl.NumberFormat(
                        'en-GB', 
                        {
                            style: 'currency', 
                            currency: 'GBP', 
                            currencyDisplay: 'symbol', 
                            signDisplay: 'negative' 
                        }
                    ).format(total));

                    customClass('currency', 'debit')
                },
                drawCallback: function (settings) {
                    customClass('transaction-date', '')
                },
                layout: {
                    topStart: null,
                    topEnd: null,
                    //bottomEnd: null
                },
                order: [[11, 'desc']],
                pageLength: 25,
                lengthMenu: [25, 50, 100, { label: 'All', value: -1 }],
                columns: [ 
                    {className: 'counter', searchable: false, width: '60px'/*, width: '1%'*/},
                    {className: 'id', searchable: false, width: '60px'/*, width: '1%'*/},
                    {className: 'add', searchable: false, width: '100px'/*, width: '1%'*/}, 
                    {className: 'account-code', width: '75px'/*, width: '5%'*/}, 
                    {className: 'account-name', width: '300px'/*, width: '15%'*/}, 
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
                    {className: 'transaction-type', width: '100px'/*, width: '5%'*/}, 
                    {className: 'transaction-subtype', width: '100px'/*, width: '5%'*/}, 
                    {className: 'party', width: '200px'/*width: '15%'*/}, 
                    {className: 'regular-debit-type', width: '250px'/*width: '15%'*/}, 
                    {className: 'transaction-day', type: 'num', width: '75px'/*, width: '7%'*/, orderable: false},
                    {className: 'transaction-date current-due-date', type: 'date', width: '150px', render: DataTable.render.datetime('ddd DD/MM/YYYY')/*, width: '5%'*/},   // requires moment.js
                    {className: 'transaction-date next-due-date', type: 'date', width: '120px', render: DataTable.render.datetime('ddd DD/MM/YYYY')/*, width: '5%'*/},      // requires moment.js
                    {className: 'actions', searchable: false, width: '150px'/*, width: '7%'*/, orderable: false} 
                ]
            });
            $(function() {
                var query = getUrlVars()['search'];
                console.log(query)
                console.log(decodeURIComponent(query));
                if (query) {
                    transactions.search(decodeURIComponent(query)).draw();
                }
                
            });
        </script>
    <!-- AJAX Add Last Date -->
        <script src="ajax/ajax_due_date.js"></script>
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