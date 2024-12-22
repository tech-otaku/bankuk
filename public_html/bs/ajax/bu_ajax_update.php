<?php

    // Simulate an error to test error responses. Comment-out when finished testing. 
    //header("HTTP/1.0 500 Internal Server Error");   // This is handled by the fail() method of the AJAX request (bu_ajax_update.js)

    include_once('../conf/pdoconfig.php');

    switch ($_POST['form-id']) {

    // UPDATE ACCOUNTING PERIOD RECORD
        case 'update-accounting-period':

            $stmt = $pdo->prepare("
            UPDATE
                bu_accounting_periods 
            SET
                `start` = ?, 
                `end` = ?,
                period = ?
            WHERE
                id = ?;
            ");        
            $stmt->execute(
                [ 
                    $_POST['start'],
                    $_POST['end'],
                    $_POST['period'],
                    intval($_POST['record-id'])
                ]
            );

            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Accounting Period <span class="text-grey">' . $_POST['period'] . '</span> Updated'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Accounting Period <span class="text-grey">' . $_POST['period'] . '</span> NOT Updated'

                ));
            }

            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;

    // UPDATE ACCOUNT RECORD
        case 'update-account':

            $stmt = $pdo->prepare("
                UPDATE 
                    bu_accounts
                SET 
                    bank_id = ?,
                    `name` = ?,
                    sort_code = ?,
                    account_number = ?,
                    `status` = ?
                WHERE 
                    id = ?;
            ");        
            $stmt->execute(
                [ 
                    $_POST['bank-id'],
                    $_POST['account-name'],
                    $_POST['sort-code'],
                    $_POST['account-number'],
                    $_POST['status'],
                    intval($_POST['record-id'])
                ]
            );
            
            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Account ID <span class="text-grey">' . $_POST['account-id'] . '</span> Updated'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Account ID <span class="text-grey">' . $_POST['account-id'] . '</span> NOT Updated'

                ));
            }
            
            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;

    // UPDATE BANK RECORD
        case 'update-bank':

            $stmt = $pdo->prepare("
                UPDATE
                    bu_banks 
                SET
                    legal_name = ?, 
                    trading_name = ? 
                WHERE
                    id = ?;
            ");        
            $stmt->execute(
                [ 
                    $_POST['legal-name'],
                    $_POST['trading-name'],
                    intval($_POST['record-id'])
                ]
            );

            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Bank ID <span class="text-grey">' . $_POST['bank-id'] . '</span> Updated'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Bank ID <span class="text-grey">' . $_POST['bank-id'] . '</span> NOT Updated'

                ));
            }
            
            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;

    // UPDATE ADMIN PASSWORD

        case 'update-password':

            // $_POST['current-password'],
            // $_POST['new-password'],
            // $_POST['new-password-confirm'],

            $stmt = $pdo->prepare("
            UPDATE
                bu_accounting_periods 
            SET
                `start` = ?, 
                `end` = ?,
                period = ?
            WHERE
                id = ?;
            ");        
            $stmt->execute(
                [ 
                    $_POST['start'],
                    $_POST['end'],
                    $_POST['period'],
                    intval($_POST['record-id'])
                ]
            );

            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Accounting Period <span class="text-grey">' . $_POST['period'] . '</span> Updated'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Accounting Period <span class="text-grey">' . $_POST['period'] . '</span> NOT Updated'

                ));
            }

            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;

    // UPDATE PARTY RECORD
        case 'update-party':

            $stmt = $pdo->prepare("
                UPDATE 
                    bu_parties 
                SET 
                    party = ?  
                WHERE 
                    id = ?;
            ");        
            $stmt->execute(
                [ 
                    $_POST['party'], 
                    intval($_POST['record-id'])
                ]
            );

            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Party ID <span class="text-grey">' . $_POST['party-id'] . '</span> Updated'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Party ID <span class="text-grey">' . $_POST['party-id'] . '</span> NOT Updated'

                ));
            }

            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;
            

    // UPDATE REGULAR DEBIT RECORD
        case 'update-regular-debit':

        // Get account_id from bu_accounts based on the value of $_POST['account-id-alpha']
            $stmt = $pdo->prepare("
                CALL 
                    bu_accounts_get_data(?);
            ");
            $stmt->execute(
                [
                    $_POST['account-id-alpha']
                ]
            );
            
            $bu_account = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt = null;

            $stmt = $pdo->prepare("
                UPDATE 
                    bu_regular_debits 
                SET 
                    account_id = ?,
                    account_id_alpha = ?,
                    amount = ?,
                    `type` =? , 
                    sub_type = ?,
                    regular_debit_type = ?,
                    party_id = ?,
                    `day` = ?,
                    `last` = ?,
                    `next` = ?, 
                    notes = ? 
                WHERE 
                    id = ?;
            ");        
            $stmt->execute(
                [
                    $bu_account['account_id'],
                    $_POST['account-id-alpha'], 
                    $_POST['amount'],
                    $_POST['type'], 
                    (isset($_POST['sub-type']) ? $_POST['sub-type'] : ''),
                    $_POST['regular-debit-type'], 
                    $_POST['party-id'], 
                    $_POST['day'],
                    $_POST['last'],
                    $_POST['next'],
                    $_POST['notes'],
                    intval($_POST['record-id'])
                ]
            );

            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for ID <span class="text-grey">' . $_POST['record-id'] . '</span> Updated'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for ID <span class="text-grey">' . $_POST['record-id'] . '</span> NOT Updated'

                ));
            }

            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;

    // UPDATE REGULAR DEBIT TYPE RECORD
        case 'update-regular-debit-type':
            $stmt = $pdo->prepare("
                UPDATE 
                    bu_regular_debit_types
                SET 
                    `description` = ?
                WHERE 
                    id = ?;
            ");        
            $stmt->execute(
                [ 
                    $_POST['description'],
                    intval($_POST['record-id'])
                ]
            );
            
            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements.
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Regular Debit Type <span class="text-grey">' . $_POST['type'] . '</span> Updated'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Regular Debit Type <span class="text-grey">' . $_POST['type'] . '</span> NOT Updated'
                ));
            }

            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;

    // UPDATE SETTINGS
        case 'update-settings':

            $stmt = $pdo->prepare("
                UPDATE 
                    bu_settings
                SET 
                    reconcilliation_first_period = ?,
                    reconcilliation_opening_balance = ?,
                    monthly_spend_first_period = ?,
                    monthly_spend_opening_balance = ?
                WHERE 
                    id = ?;
            ");        
            $stmt->execute(
                [ 
                    $_POST['reconcilliation-first'],
                    $_POST['reconcilliation-opening-balance'],
                    $_POST['monthly-spend-first'],
                    $_POST['monthly-spend-opening-balance'],
                    intval($_POST['record-id'])
                ]
            );

            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for ID <span class="text-grey">' . $_POST['record-id'] . '</span> Updated'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for ID <span class="text-grey">' . $_POST['record-id'] . '</span> NOT Updated'

                ));
            }

            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;

    // UPDATE TRANSACTION RECORD
        case 'update-transaction':

        // Get account_id from bu_accounts based on the value of $_POST['account-id-alpha']
            $stmt = $pdo->prepare("
                CALL 
                    bu_accounts_get_data(?);
            ");
            $stmt->execute(
                [
                    $_POST['account-id-alpha']
                ]
            );
            
            $bu_account = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt = null;

        // Get period from bu_accounting_periods based on the value of $_POST['date']
            $stmt = $pdo->prepare("
                CALL 
                    bu_accounting_periods_current(?);
            ");
            $stmt->execute(
                [
                    $_POST['date']
                ]
            );
            
            $bu_accounting_period = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt = null;
        
            $stmt = $pdo->prepare("
                UPDATE 
                    bu_transactions 
                SET 
                    account_id = ?, 
                    account_id_alpha = ?, 
                    amount = ?, 
                    `type` = ?, 
                    sub_type = ?, 
                    party_id = ?, 
                    `date` = ?, 
                    period = ?, 
                    notes =?  
                WHERE 
                    id = ?;
            ");        
            $stmt->execute(
                [
                    $bu_account['account_id'],
                    $_POST['account-id-alpha'], 
                    $_POST['amount'],
                    $_POST['type'], 
                    (isset($_POST['sub-type']) ? $_POST['sub-type'] : ''), 
                    $_POST['party-id'], 
                    $_POST['date'], 
                    $bu_accounting_period['period'], 
                    $_POST['notes'],
                    intval($_POST['record-id'])
                ]
            );

            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for ID <span class="text-grey">' . $_POST['record-id'] . '</span> Updated'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for ID <span class="text-grey">' . $_POST['record-id'] . '</span> NOT Updated'

                ));
            }

            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;

    // UPDATE TRANSACTION TYPE RECORD
        case 'update-transaction-type':

            $stmt = $pdo->prepare("
                UPDATE 
                    bu_transaction_types
                SET 
                    `description` = ?
                WHERE 
                    id = ?;
            ");        
            $stmt->execute(
                [ 
                    $_POST['description'],
                    intval($_POST['record-id'])
                ]
            );
            
            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Transaction Type <span class="text-grey">' . $_POST['type'] . '</span> Updated'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Transaction Type <span class="text-grey">' . $_POST['type'] . '</span> NOT Updated'

                ));
            }

            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;

    }   // switch
    
?>
    