<?php

    // Simulate an error to test error responses. Comment-out when finished testing. 
    //header("HTTP/1.0 403 Forbidden");

    
    include_once('../conf/pdoconfig.php');

    $defaults = parse_ini_file('../defaults.ini');   // Get settings' defaults

    $stmt = $pdo->prepare("
        UPDATE
            bu_settings
        SET  
            bu_settings.`reconcilliation_first_period` = ?,
            bu_settings.`reconcilliation_opening_balance` = ?,
            bu_settings.`monthly_spend_first_period` = ?,
            bu_settings.`monthly_spend_opening_balance` = ?
        WHERE 
            bu_settings.`id` = ?;
    ");
    $stmt->execute(
        [
            $defaults['reconcilliation_first_period'],
            $defaults['reconcilliation_opening_balance'],
            $defaults['monthly_spend_first_period'],
            $defaults['monthly_spend_opening_balance'],
            intval($_POST['record-id'])
        ]
    );

    if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
    // Success
        echo json_encode(array(
            'success' => 1,  // True
            'message' => 'Settings have been reset to their defaults',
            'reconcilliation_first' => $defaults['reconcilliation_first_period'],
            'reconcilliation_opening_balance' => $defaults['reconcilliation_opening_balance'],
            'monthly_spend_first' => $defaults['monthly_spend_first_period'],
            'monthly_spend_opening_balance' => $defaults['monthly_spend_opening_balance']
        ));
    } else { 
    // Failure
        echo json_encode(array(
            'success' => 0,  // False
            'message' => 'Settings have NOT been reset to their defaults'
        ));
    }
    
    // Close connections 
    $stmt = null; 
    $pdo = null;

?>
    