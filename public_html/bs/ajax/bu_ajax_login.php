<?php

    // Simulate an error to test error responses. Comment-out when finished testing. 
    //header("HTTP/1.0 500 Internal Server Error");   // This is handled by the fail() method of the AJAX request (bu_ajax_login.js)

    include_once('../conf/pdoconfig.php');

    $stmt = $pdo->prepare("
        CALL 
            bu_password(?);
    ");
    $stmt->execute(
        [
            $_POST['email']
        ]
    );

    $bu_user = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = null;

    // The values echoed below (1 or 0) are passed to the 'dataReturnedByServer' argument of the function defined in the done() method of the AJAX request (bu_ajax_login.js)
    if ($bu_user) {   // User's login email is correct (record found in bu_admin table). Do not use '$stmt->rowCount() != 0'. See https://stackoverflow.com/a/16776839
        
        if ($bu_user['locked'] != 0) {                                      // User's account is not locked

            //if ($bu_user['md5_password'] === sha1(md5($_POST['password']))) {   

            if ( password_verify($_POST['password'], $bu_user['password_hash']) ) { // User's password is correct

                // Reset login attempts to '0' as user may have had 1 or 2 previous failed login attempts
                $stmt = $pdo->prepare("
                    UPDATE
                        bu_admin
                    SET
                        login_attempts = ?
                    WHERE
                        admin_id = ?;
                ");
                $stmt->execute(
                    [
                        0,
                        $bu_user['admin_id']
                    ]
                );

                $stmt = null;

                session_start();
                $_SESSION['admin_id'] = $bu_user['admin_id'];
                $_SESSION['email'] = $bu_user['email'];

                echo json_encode(array(
                    'success' => 1  // True
                ));
            } else {                                                        // User's password is incorrect
                // Log failed login attempts
                $stmt = $pdo->prepare("
                    UPDATE
                        bu_admin
                    SET
                        login_attempts = ?,
                        locked = IF(login_attempts = 3, 0, locked)          -- The condition is evaluated after `login_attempts` is updated.
                    WHERE
                        admin_id = ?;
                ");
                $stmt->execute(
                    [
                        $bu_user['login_attempts'] + 1,
                        $bu_user['admin_id']
                    ]
                );

                $stmt = null;

                echo json_encode(array(
                    'success' => 0, // False
                    'message' => ($bu_user['login_attempts'] + 1 < 3) ? 'Invalid login credentials.' : 'Too many failed login attempts. Account has been locked.'
                ));
            }   // Password
            
        

        } else {                                                            // User's account is locked
            echo json_encode(array(
                'success' => 0,  // False
                'message' => 'Your account is locked.'
            ));

        }  // Locked

    } else {                                                                // User's login email isn't correct
        echo json_encode(array(
            'success' => 0,  // False
            'message' => 'Your email address was not recognised.'
        ));
    }   // eMail

    $stmt = null;

?>
    