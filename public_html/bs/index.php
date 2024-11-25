<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>
    <!-- Common Head -->
        <?php include("partials/head.php"); ?>
    </head>
    <body class="hold-transition login-page">
        <div class="login-box">
            <div class="login-logo">
                <p><?php //echo $auth->sys_name; ?></p>
            </div>  <!-- /.login-logo -->
            <div class="card">
                <div class="card-body login-card-body">
                    <form id="login" method="post">
                        <div class="input-group mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <button type="submit" name="login-submit" class="btn btn-success btn-block">Login</button>
                            </div>    <!-- /.col -->
                        </div>  <!-- /.row -->
                    </form>
                </div>    <!-- /.login-card-body -->
            </div>
        </div>    <!-- /.login-box -->
    <!-- Common Scripts -->
        <?php include("partials/scripts.php"); ?>
    <!-- Ajax Login -->
        <script src="ajax/bu_ajax_login.js"></script>
    </body>
</html>