<?php

    // Simulate an error to test error responses. Comment-out when finished testing. 
    //header("HTTP/1.0 500 Internal Server Error");   // This is handled by the fail() method of the AJAX request (bu_ajax_add.js)

    include_once('../conf/pdoconfig.php');

    switch ($_POST['form-id']) {

    // ADD ACCOUNTING PERIOD RECORD
        case 'add-accounting-period':
            $stmt = $pdo->prepare("
                INSERT INTO 
                     bu_accounting_periods (
                        start, 
                        `end`,
                        period
                    ) 
                 VALUES (?, ?, ?);
            ");
             $stmt->execute(
                 [
                     $_POST['start'],
                     $_POST['end'],
                     $_POST['period'],
                 ]
             );
                        
            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Accounting Period <span class="text-grey">' . $_POST['period'] . '</span> Added'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Accounting Period <span class="text-grey">' . $_POST['period'] . '</span> NOT Added'

                ));
            }

            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;
    
    // ADD ACCOUNT RECORD
        case 'add-account':

            $stmt = $pdo->prepare("
                INSERT INTO 
                    bu_accounts (
                        account_id, 
                        account_id_alpha, 
                        bank_id, 
                        `name`, 
                        sort_code, 
                        account_number, 
                        `status`
                    ) 
                VALUES 
                    (?, ?, ?, ?, ?, ?, ?);
            ");
            $stmt->execute(
                [
                    $_POST['account-id'],
                    $_POST['account-id-alpha'],
                    $_POST['bank-id'],
                    $_POST['account-name'],
                    $_POST['sort-code'],
                    $_POST['account-number'],
                    $_POST['status']
                ]
            );
                        
            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Account ID <span class="text-grey">' . $_POST['account-id'] . '</span> Added'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Account ID <span class="text-grey">' . $_POST['account-id'] . '</span> NOT Added'

                ));
            }
            
            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;
    
    // UPDATE BANK RECORD
        case 'add-bank':

            $stmt = $pdo->prepare("
                INSERT INTO 
                    bu_banks (
                        bank_id, 
                        legal_name, 
                        trading_name
                    ) 
                 VALUES 
                    (?, ?, ?);
            ");
             $stmt->execute(
                [
                    $_POST['bank-id'],
                    $_POST['legal-name'],
                    $_POST['trading-name'],
                ]
             );
                        
            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Bank ID <span class="text-grey">' . $_POST['bank-id'] . '</span> Added'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Bank ID <span class="text-grey">' . $_POST['bank-id'] . '</span> NOT Added'

                ));
            }
            
            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;
    
    // ADD PARTY RECORD
        case 'add-party':

            $stmt = $pdo->prepare("
                INSERT INTO
                    bu_parties (
                        party_id, 
                        party
                    )
                VALUES (?, ?);
            ");        
            $stmt->execute(
                [
                    $_POST['party-id'],
                    $_POST['party']
                ]
            );
        
            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Party ID <span class="text-grey">' . $_POST['party-id'] . '</span> Added'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Party ID <span class="text-grey">' . $_POST['party-id'] . '</span> NOT Added'

                ));
            }

            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;
    
    // ADD REGULAR DEBIT RECORD
        case 'add-regular-debit':

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
        
            $bu_account = $stmt->fetch(PDO::ASSOC);
            $stmt = null;
    
            $stmt = $pdo->prepare("
                INSERT INTO 
                    bu_regular_debits (
                        account_id, 
                        account_id_alpha, 
                        amount, 
                        `type`, 
                        sub_type, 
                        party_id, 
                        regular_debit_type, 
                        `day`, 
                        notes
                    ) 
                VALUES (?,?,?,?,?,?,?,?,?);
            ");
            $stmt->execute(
                [
                    $bu_account['account_id'],
                    $_POST['account-id-alpha'],  
                    $_POST['amount'],
                    $_POST['type'], 
                    (isset($_POST['sub-type']) ? $_POST['sub-type'] : ''), 
                    $_POST['party-id'],
                    $_POST['regular-debit-type'],  
                    $_POST['day'], 
                    $_POST['notes']            
                ]
            );
            $stmt = null;

            // Get the id of the record just added to bu_regular_debits
            $id = $pdo->lastInsertId();
            
            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for ID <span class="text-grey">' . $id . '</span> Added'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for ID <span class="text-grey">' . $id . '</span> NOT Added'

                ));
            }

            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;

    // ADD REGULAR DEBIT TYPE RECORD
        case 'add-regular-debit-type':
            $stmt = $pdo->prepare("
                INSERT INTO 
                     bu_regular_debit_types (
                        `type`, 
                        `description`
                    ) 
                 VALUES (?, ?);
            ");
             //bind paramaters
             //$rc = $stmt->bind_param('s', $party);
             $stmt->execute(
                 [
                     $_POST['type'],
                     $_POST['description']
                 ]
             );
            
             if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements.
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Regular Debit Type <span class="text-grey">' . $_POST['type'] . '</span> Added'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Regular Debit Type <span class="text-grey">' . $_POST['type'] . '</span> NOT Added'
                ));
            }

            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;

    // ADD TRANSACTION RECORD
        case 'add-transaction':

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
                INSERT INTO 
                    bu_transactions (
                        account_id, 
                        account_id_alpha, 
                        amount, 
                        `type`, 
                        sub_type, 
                        party_id, 
                        `date`, 
                        period, 
                        notes
                    ) 
                VALUES (?,?,?,?,?,?,?,?,?);
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
                    $_POST['notes']            
                ]
            );
            
            // Get the id of the record just added to bu_regular_debits
            $id = $pdo->lastInsertId();
        
            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for ID <span class="text-grey">' . $id . '</span> Added'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for ID <span class="text-grey">' . $id . '</span> NOT Added'

                ));
            }

            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;
    
    // UPDATE TRANSACTION TYPE RECORD
        case 'add-transaction-type':

            $stmt = $pdo->prepare("
                INSERT INTO 
                     bu_transaction_types (
                        `type`, 
                        `description`
                    ) 
                 VALUES (?, ?);
            ");
            $stmt->execute(
                [
                    $_POST['type'],
                    $_POST['description']
                ]
            );
            
            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Transaction Type <span class="text-grey">' . $_POST['type'] . '</span> Added'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Transaction Type <span class="text-grey">' . $_POST['type'] . '</span> NOT Added'

                ));
            }

            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;

    }   // switch

?>
    