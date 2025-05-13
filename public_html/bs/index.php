<?php
    session_start();
    include('conf/pdoconfig.php');
    include('conf/bu_custom.php');
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

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <input type="email" name="email" id="email" class="form-control" placeholder="Email">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <input type="password" name="password" id="password" class="form-control" placeholder="password">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <button type="submit" name="login-submit" id="login-submit" class="btn btn-success btn-block">Login</button>
                            </div>
                        </div>

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