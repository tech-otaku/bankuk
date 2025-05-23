<?php

    // Simulate an error to test error responses. Comment-out when finished testing. 
    //header("HTTP/1.0 500 Internal Server Error");   // This is handled by the fail() method of the AJAX request (bu_ajax_update.js)

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
    if ($bu_user) {   // Record for email found in bu_admin table. Do not use '$stmt->rowCount() != 0'. See https://stackoverflow.com/a/16776839
        
        if ( password_verify($_POST['current-password'], $bu_user['password_hash']) ) {     // User's current password is correct

            if ($_POST['new-password'] === $_POST['new-password-confirm']) {                // New password entries match

                // Update password
                $stmt = $pdo->prepare("
                    UPDATE
                        bu_admin
                    SET
                        bu_admin.`password_hash` = ?
                    WHERE
                        bu_admin.`admin_id` = ?;
                ");
                $stmt->execute(
                    [
                        password_hash($_POST['new-password'], PASSWORD_DEFAULT),
                        $bu_user['admin_id']
                    ]
                );

                $stmt = null;
                

                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Password updated.'
                ));
            } else {
                echo json_encode(array(
                    'success' => 0, // False
                    'message' => 'Passwords do not match'
                ));

            }   // New password matches

        } else {                                                                            // User's current password is incorrect
            echo json_encode(array(
                'success' => 0, // False
                'message' => 'Current password is incorrect'
            ));
        }   // Current password

    } else {                                                                                // User's login email isn't correct
        echo json_encode(array(
            'success' => 0,  // False
            'message' => 'Your email address was not recognised.'
        ));
    }   // eMail

    $stmt = null;
    
?>
    