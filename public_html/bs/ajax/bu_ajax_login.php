<?php

    // Simulate an error to test error responses. Comment-out when finished testing. 
    //header("HTTP/1.0 500 Internal Server Error");   // This is handled by the fail() method of the AJAX request (bu_ajax_login.js)

    include_once('../conf/pdoconfig.php');

    $stmt = $pdo->prepare("
        CALL 
            bu_password(?,?);
    ");
    $stmt->execute(
        [
            $_POST['email'],
            sha1(md5($_POST['password']))
        ]
    );

    /*
    $stmt = $pdo->prepare("
        SELECT 
            email, 
            password, 
            admin_id  
        FROM 
            bu_admin 
        WHERE 
            email = ? AND password = ?;
    ");
    $stmt->execute(
        [
            $_POST['email'],
            sha1(md5($_POST['password']))
        ]
    );
    */

    $bu_user = $stmt->fetch(PDO::FETCH_ASSOC);

    // The values echoed below (1 or 0) are passed to the 'dataReturnedByServer' argument of the function defined in the done() method of the AJAX request (bu_ajax_login.js)
    if ($bu_user) {   // Do not use '$stmt->rowCount() != 0'. See https://stackoverflow.com/a/16776839
        // User's login credentials are correct
        session_start();
        $_SESSION['admin_id'] = $bu_user['admin_id'];
        echo json_encode(array(
            'success' => 1  // True
        ));
        //echo 1; // True
    } else {
        // User's login credentials are incorrect
        echo json_encode(array(
            'success' => 0  // True
        ));
        //echo 0; // False
    }

    $stmt = null;

?>
    