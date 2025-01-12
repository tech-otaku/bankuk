<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/checklogin.php');
    include('conf/bu_custom.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    $page_name = "Manage Entities";
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
                                    <table id="entities" class="table table-hover table-bordered table-striped bu-data-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Entity ID</th>
                                                <th>Entity</th>
                                                <th>Used</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $counter = 1;

                                                $stmt = $pdo->prepare("
                                                    SELECT 
                                                        e1.id,
                                                        e1.entity_id,
                                                        e1.entity_name,
                                                        COUNT(t1.entity_id) AS _used
                                                    FROM
                                                        bu_entities AS e1
                                                    LEFT JOIN
                                                        bu_transactions AS t1 ON e1.entity_id = t1.entity_id
                                                    GROUP BY 
                                                        e1.id
                                                    ORDER BY 
                                                        e1.entity_name ASC;
                                                ");
                                                $stmt->execute(); 
                                                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                                            ?>
                                            <tr>
                                                <td><?php echo $counter; ?></td>
                                                <td><?php echo $row->entity_id; ?></td>
                                                <td><?php echo $row->entity_name; ?></td>
                                                <td>
                                                    <?php if ($row->_used != 0) { ?>
                                                        <a href="bu_manage_transactions.php?filter=filter-col-6&value=<?php echo rawurlencode($row->entity_name); ?>"><?php echo $row->_used; ?></a>
                                                    <?php } else { 
                                                        echo $row->_used;
                                                    } ?>
                                                </td>
                                                <td>
                                                    <a class="btn btn-success btn-sm" href="bu_view_entity.php?id=<?php echo $row->id; ?>&used=<?php echo $row->_used; ?>&record=transaction">
                                                        <i class="fa fa-edit"></i>
                                                        <!-- Edit -->
                                                    </a>
                                                    <a data-mysql-table="bu_entities" data-record-id="<?php echo $row->id; ?>" class="btn btn-danger btn-sm delete-record<?php echo ($row->_used != 0 ? ' disabled' : ''); ?>" href="#">
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
                                    </table>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <a class="btn btn-success" href="bu_add_entity.php">Add Entity</a>
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
            var entities = new DataTable('#entities', {
                
                pageLength: 25,
                lengthMenu: [
                    25,
                    50,
                    100, 
                    {label: 'All', value: -1 }
                ],
                
                columns: [
                    {className: 'counter'},
                    {className: 'code'}, 
                    {className: 'description'},
                    {className: 'used'},
                    {className: 'actions', orderable: false}
                ]
                
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