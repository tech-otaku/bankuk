<?php

    // Simulate an error to test error responses. Comment-out when finished testing. 
    //header("HTTP/1.0 500 Internal Server Error");   // This is handled by the fail() method of the AJAX request (bu_ajax_update.js)

    include_once('../conf/pdoconfig.php');

    switch ($_POST['form-id']) {

    // UPDATE ACCOUNTING PERIOD RECORD
        case 'update-accounting-period':

            // See https://akrabat.com/turn-warnings-into-an-exception/
            set_error_handler(function ($severity, $message, $file, $line) {
                throw new \ErrorException($message, $severity, $severity, $file, $line);
            });

            try {

                //throw new Exception('Wrong',404);

                $stmt = $pdo->prepare("
                    UPDATE
                        bu_accounting_periods
                    SET
                        bu_accounting_periods.`period_start` = ?, 
                        bu_accounting_periods.`period_end` = ?,
                        bu_accounting_periods.`period` = ?
                    WHERE
                        bu_accounting_periods.`id` = ?;
                ");        
                $stmt->execute(
                    [ 
                        $_POST['period-start'],
                        $_POST['period-end'],
                        $_POST['period'],
                        intval($_POST['record-id'])
                    ]
                );

                // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Accounting Period <span class="text-grey">' . $_POST['period'] . '</span> Updated'
                ));

            } catch (Exception $e) {

                // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'An Error Occured: ' . $e->getMessage()
                ));

            }
            
            restore_error_handler();
            /*
            $stmt = $pdo->prepare("
            UPDATE
                bu_accounting_periods
            SET
                bu_accounting_periods.`period_start` = ?, 
                bu_accounting_periods.`period_end` = ?,
                bu_accounting_periods.`period` = ?
            WHERE
                bu_accounting_periods.`id` = ?;
            ");        
            $stmt->execute(
                [ 
                    $_POST['period-start'],
                    $_POST['period-end'],
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
                */

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
                    bu_accounts.`bank_id` = ?,
                    bu_accounts.`name` = ?,
                    bu_accounts.`sort_code` = ?,
                    bu_accounts.`account_number` = ?,
                    bu_accounts.`status` = ?,
                    bu_accounts.`notes` = ?
                WHERE 
                    bu_accounts.`id` = ?;
            ");        
            $stmt->execute(
                [ 
                    $_POST['bank-id'],
                    $_POST['account-name'],
                    $_POST['sort-code'],
                    $_POST['account-number'],
                    $_POST['status'],
                    $_POST['notes'],
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
                    bu_banks.`legal_name` = ?, 
                    bu_banks.`trading_name` = ? 
                WHERE
                    bu_banks.id = ?;
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


    // UPDATE ENTITY RECORD
        case 'update-entity':

            $stmt = $pdo->prepare("
                UPDATE 
                    bu_entities 
                SET 
                    bu_entities.`entity_description` = ?  
                WHERE 
                    bu_entities.`id` = ?;
            ");        
            $stmt->execute(
                [ 
                    $_POST['entity-description'], 
                    intval($_POST['record-id'])
                ]
            );

            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Entity ID <span class="text-grey">' . $_POST['entity-id'] . '</span> Updated'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Entity ID <span class="text-grey">' . $_POST['entity-id'] . '</span> NOT Updated'

                ));
            }

            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;


    // UPDATE PRE-FILL RECORD
        case 'update-prefill':

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
                    bu_prefills 
                SET 
                    bu_prefills.`name` = ?,
                    bu_prefills.`account_id` = ?, 
                    bu_prefills.`account_id_alpha` = ?, 
                    bu_prefills.`type_id` = ?, 
                    bu_prefills.`sub_type_id` = ?, 
                    bu_prefills.`entity_id` = ?,
                    bu_prefills.`method_id` = ?,
                    bu_prefills.`notes` = ?
                WHERE 
                    bu_prefills.`id` = ?;
            ");        
            $stmt->execute(
                [
                    $_POST['prefill-name'],
                    $bu_account['account_id'],
                    $_POST['account-id-alpha'], 
                    $_POST['type-id'], 
                    (isset($_POST['sub-type-id']) && trim($_POST['sub-type-id']) != '' ? $_POST['sub-type-id'] : ''), 
                    $_POST['entity-id'],
                    $_POST['method-id'],
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
            

    // UPDATE REGULAR DEBIT RECORD
        case 'update-regular-debit':

        // Get the period from bu_accounting_periods based on the value of $bu_regular_debit->next
            if ($_POST['last'] != "1970-01-01") {
                $stmt = $pdo->prepare("
                    SELECT 
                        bu_accounting_periods.`period` 
                    FROM 
                        bu_accounting_periods
                    WHERE 
                        bu_accounting_periods.`period_start` <= ? AND bu_accounting_periods.`period_end` >= ?;
                ");

                $stmt->execute(
                    [
                        $_POST['last'], 
                        $_POST['last']
                    ]
                );

                $bu_accounting_period = $stmt->fetch(PDO::FETCH_OBJ);   
                $stmt = null;
            } else {
                $bu_accounting_period = (object) array("period"=>0);
            }

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
                    bu_regular_debits.`account_id` = ?,
                    bu_regular_debits.`account_id_alpha` = ?,
                    bu_regular_debits.`amount` = ?,
                    bu_regular_debits.`type_id` =? , 
                    bu_regular_debits.`sub_type_id` = ?,
                    bu_regular_debits.`method_id` = ?,
                    bu_regular_debits.`entity_id` = ?,
                    bu_regular_debits.`day` = ?,
                    bu_regular_debits.`period` = ?,
                    bu_regular_debits.`last` = ?,
                    bu_regular_debits.`next` = ?, 
                    bu_regular_debits.`notes` = ? 
                WHERE 
                    bu_regular_debits.`id` = ?;
            ");        
            $stmt->execute(
                [
                    $bu_account['account_id'],
                    $_POST['account-id-alpha'], 
                    $_POST['amount'],
                    $_POST['type-id'], 
                    (isset($_POST['sub-type-id']) && trim($_POST['sub-type-id']) != '' ? $_POST['sub-type-id'] : ''),
                    $_POST['method-id'],
                    $_POST['entity-id'], 
                    $_POST['day'],
                    $bu_accounting_period->period,
                    $_POST['last'],
                    $_POST['next'],
                    $_POST['notes'],
                    intval($_POST['record-id'])
                ]
            );

            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements.
                $stmt = $pdo->prepare("
                    SELECT
                        * 
                    FROM 
                        bu_regular_debits
                    WHERE 
                        bu_regular_debits.`id` = ?;
                ");

                $stmt->execute(
                    [
                        intval($_POST['record-id'])
                    ]
                );

                $row = $stmt->fetch(PDO::FETCH_OBJ);   
                $stmt = null;

            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'data' => json_encode($row),    // The record's data after being updated
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
                    bu_regular_debit_types.`description` = ?
                WHERE 
                    bu_regular_debit_types.`id` = ?;
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
                    bu_settings.`reconcilliation_first_period` = ?,
                    bu_settings.`reconcilliation_opening_balance` = ?,
                    bu_settings.`monthly_spend_first_period` = ?,
                    bu_settings.`monthly_spend_opening_balance` = ?,
                    bu_settings.`first_tax_year_start` = ?,
                    bu_settings.`number_of_tax_years` = ?
                WHERE 
                    bu_settings.`id` = ?;
            ");        
            $stmt->execute(
                [ 
                    $_POST['reconcilliation-first'],
                    $_POST['reconcilliation-opening-balance'],
                    $_POST['monthly-spend-first'],
                    $_POST['monthly-spend-opening-balance'],
                    $_POST['first-tax-year-start'],
                    $_POST['number-of-tax-years'],
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

// UPDATE TAX-YEAR RECORD
        case 'update-tax-year':

            $stmt = $pdo->prepare("
            UPDATE
                bu_tax_years
            SET
                bu_tax_years.`tax_year_start` = ?, 
                bu_tax_years.`tax_year_end` = ?,
                bu_tax_years.`tax_year` = ?
            WHERE
                bu_tax_years.`id` = ?;
            ");        
            $stmt->execute(
                [ 
                    $_POST['tax-year-start'],
                    $_POST['tax-year-end'],
                    $_POST['tax-year'],
                    intval($_POST['record-id'])
                ]
            );

            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Tax Year <span class="text-grey">' . $_POST['tax-year'] . '</span> Updated'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Accounting Period <span class="text-grey">' . $_POST['tax-year'] . '</span> NOT Updated'

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
                    $_POST['transaction-date']
                ]
            );
            
            $bu_accounting_period = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt = null;

            // Get tax year from bu_tax_years based on the value of $_POST['date']
            $stmt = $pdo->prepare("
                CALL 
                    bu_tax_years_current(?);
            ");
            $stmt->execute(
                [
                    $_POST['transaction-date']
                ]
            );

            $bu_tax_year = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt = null;
        
            $stmt = $pdo->prepare("
                UPDATE 
                    bu_transactions 
                SET 
                    bu_transactions.`account_id` = ?, 
                    bu_transactions.`account_id_alpha` = ?, 
                    bu_transactions.`amount` = ?, 
                    bu_transactions.`type_id` = ?, 
                    bu_transactions.`sub_type_id` = ?,
                    bu_transactions.`method_id` = ?,
                    bu_transactions.`entity_id` = ?, 
                    bu_transactions.`transaction_date` = ?,
                    bu_transactions.`tax_year` = ?,
                    bu_transactions.`period` = ?, 
                    bu_transactions.`notes` =?  
                WHERE 
                    bu_transactions.`id` = ?;
            ");        
            $stmt->execute(
                [
                    $bu_account['account_id'],
                    $_POST['account-id-alpha'], 
                    $_POST['amount'],
                    $_POST['type-id'], 
                    (isset($_POST['sub-type-id']) && trim($_POST['sub-type-id']) != '' ? $_POST['sub-type-id'] : ''), 
                    $_POST['method-id'],
                    $_POST['entity-id'], 
                    $_POST['transaction-date'],
                    $bu_tax_year['tax_year'],
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

    // UPDATE TRANSACTION SUB-TYPE RECORD
        case 'update-transaction-method':

            $stmt = $pdo->prepare("
                UPDATE 
                    bu_transaction_methods
                SET 
                    bu_transaction_methods.`method_description` = ?
                WHERE 
                    bu_transaction_methods.`id` = ?;
            ");        
            $stmt->execute(
                [ 
                    $_POST['method-description'],
                    intval($_POST['record-id'])
                ]
            );
            
            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Transaction Method <span class="text-grey">' . $_POST['method-id'] . '</span> Updated'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Transaction Method <span class="text-grey">' . $_POST['method-id'] . '</span> NOT Updated'

                ));
            }

            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;


    // UPDATE TRANSACTION SUB-TYPE RECORD
        case 'update-transaction-sub-type':

            $stmt = $pdo->prepare("
                UPDATE 
                    bu_transaction_sub_types
                SET 
                    bu_transaction_sub_types.`sub_type_description` = ?
                WHERE 
                    bu_transaction_sub_types.`id` = ?;
            ");        
            $stmt->execute(
                [ 
                    $_POST['sub-type-description'],
                    intval($_POST['record-id'])
                ]
            );
            
            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Transaction Sub-Type <span class="text-grey">' . $_POST['sub-type-id'] . '</span> Updated'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Transaction Sub-Type <span class="text-grey">' . $_POST['sub-type-id'] . '</span> NOT Updated'

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
                    bu_transaction_types.`type_description` = ?
                WHERE 
                    bu_transaction_types.`id` = ?;
            ");        
            $stmt->execute(
                [ 
                    $_POST['type-description'],
                    intval($_POST['record-id'])
                ]
            );
            
            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Transaction Type <span class="text-grey">' . $_POST['type-id'] . '</span> Updated'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Transaction Type <span class="text-grey">' . $_POST['type-id'] . '</span> NOT Updated'

                ));
            }

            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;

    }   // switch
    
?>
    