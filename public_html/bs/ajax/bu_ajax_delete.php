<?php

    // Simulate an error to test error responses. Comment-out when finished testing. 
    //header("HTTP/1.0 500 Internal Server Error");   // This is handled by the fail() method of the AJAX request (bu_ajax_delete.js)

    include_once('../conf/pdoconfig.php');

    // Whitelist of allowed values for the value of $_POST['mysql-table']
    $whitelist = [
        "bu_accounting_periods",
        "bu_accounts",
        "bu_banks",
        "bu_monthly_spend",
        "bu_parties",
        "bu_prefills",
        "bu_reconcilliation",
        "bu_regular_debits",
        "bu_regular_debit_types",
        "bu_transactions",
        "bu_transaction_types"
    ];

    if (array_search($_POST['mysql-table'], $whitelist) !== false) {    // Only execute code if the value of $_POST['mysql-table'] is in the whitelist

        $table = $_POST['mysql-table'];
        //echo $table;

        /*
        $stmt = $mysqli->prepare("
            SELECT 
                *
            FROM 
                $table
            WHERE 
                id = ?;
        ");

        $stmt->execute(
            [ 
                intval($_POST['record-id'])
            ]
        );
        $result = $stmt->get_result();
        $stmt->close();

        $row = $result->fetch_object();

        //var_dump($row);
        */

        $stmt = $pdo->prepare("
            DELETE 
            FROM 
                $table 
            WHERE 
                id = ?;
        ");

        $stmt->execute(
            [
                intval($_POST['record-id'])
            ]
        );

        if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record ID <span class="text-grey">' . $_POST['record-id'] . '</span> Deleted'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for ID <span class="text-grey">' . $_POST['period'] . '</span> NOT Deleted'

                ));
            }

            // Close connections 
            $stmt = null; 
            $pdo = null;

        /*
        if ($stmt) {
            $info = "Transaction Deleted";
        } else {
            $err = "Try Again Later";
        }
        */

       // echo 'Record for ID <span class="text-grey">' . $_POST['record-id'] . '</span> Deleted';

    }

    //echo 'Record for ID <span class="text-grey">' . $_POST['record-id'] . '</span> Deleted';
    
?>
    