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
                        bu_accounting_periods.`period_start`, 
                        bu_accounting_periods.`period_end`,
                        bu_accounting_periods.`period`
                    ) 
                 VALUES (?, ?, ?);
            ");
             $stmt->execute(
                 [
                     $_POST['period-start'],
                     $_POST['period-end'],
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
                        bu_accounts.`account_id`, 
                        bu_accounts.`account_id_alpha`, 
                        bu_accounts.`bank_id`, 
                        bu_accounts.`name`, 
                        bu_accounts.`sort_code`, 
                        bu_accounts.`account_number`, 
                        bu_accounts.`status`,
                        bu_accounts.`notes`
                    ) 
                VALUES 
                    (?, ?, ?, ?, ?, ?, ?, ?);
            ");
            $stmt->execute(
                [
                    $_POST['account-id'],
                    $_POST['account-id-alpha'],
                    $_POST['bank-id'],
                    $_POST['account-name'],
                    $_POST['sort-code'],
                    $_POST['account-number'],
                    $_POST['status'],
                    $_POST['notes']
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
    
    // ADD BANK RECORD
        case 'add-bank':

            $stmt = $pdo->prepare("
                INSERT INTO 
                    bu_banks (
                        bu_banks.`bank_id`, 
                        bu_banks.`legal_name`, 
                        bu_banks.`trading_name`
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
    
    // ADD ENTITY RECORD
        case 'add-entity':

            $stmt = $pdo->prepare("
                INSERT INTO
                    bu_entities (
                        bu_entities.`entity_id`, 
                        bu_entities.`entity_description`
                    )
                VALUES (?, ?);
            ");        
            $stmt->execute(
                [
                    $_POST['entity-id'],
                    $_POST['entity-description']
                ]
            );
        
            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Entity ID <span class="text-grey">' . $_POST['entity-id'] . '</span> Added'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Entity ID <span class="text-grey">' . $_POST['entity-id'] . '</span> NOT Added'

                ));
            }

            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;

    // ADD PRE-FILL RECORD
        case 'add-prefill':

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
                INSERT INTO 
                    bu_prefills (
                        bu_prefills.`account_id`, 
                        bu_prefills.`account_id_alpha`, 
                        bu_prefills.`type_id`, 
                        bu_prefills.`sub_type_id`, 
                        bu_prefills.`entity_id`,
                        bu_prefills.`method_id`,
                        bu_prefills.`notes`
                    ) 
                VALUES (?,?,?,?,?,?,?);
            ");

            $stmt->execute(
                [
                    $bu_account['account_id'],
                    $_POST['account-id-alpha'],  
                    $_POST['type-id'], 
                    (isset($_POST['sub-type-id']) ? $_POST['sub-type-id'] : ''), 
                    $_POST['entity-id'],
                    $_POST['method-id'],
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
        
            $bu_account = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt = null;
    
            $stmt = $pdo->prepare("
                INSERT INTO 
                    bu_regular_debits (
                        bu_regular_debits.`account_id`, 
                        bu_regular_debits.`account_id_alpha`, 
                        bu_regular_debits.`amount`, 
                        bu_regular_debits.`type_id`, 
                        bu_regular_debits.`sub_type_id`,
                        bu_regular_debits.`method_id`,
                        bu_regular_debits.`entity_id`,
                        bu_regular_debits.`day`,
                        bu_regular_debits.`period`,
                        bu_regular_debits.`last`,
                        bu_regular_debits.`next`,
                        bu_regular_debits.`notes`
                    ) 
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?);
            ");
            $stmt->execute(
                [
                    $bu_account['account_id'],
                    $_POST['account-id-alpha'],  
                    $_POST['amount'],
                    $_POST['type-id'], 
                    (isset($_POST['sub-type-id']) ? $_POST['sub-type-id'] : ''),
                    $_POST['method-id'],
                    $_POST['entity-id'],
                    $_POST['day'], 
                    $_POST['period'],
                    $_POST['last'],
                    $_POST['next'],
                    $_POST['notes']            
                ]
            );
            //$stmt = null;

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
                        bu_regular_debit_types.`type`, 
                        bu_regular_debit_types.`description`
                    ) 
                 VALUES (?, ?);
            ");
             //bind paramaters
             //$rc = $stmt->bind_param('s', $entity);
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

    // ADD TAX YEAR RECORD
        case 'add-tax-year':
            $stmt = $pdo->prepare("
                INSERT INTO 
                    bu_tax_years (
                        bu_tax_years.`tax_year_start`, 
                        bu_tax_years.`tax_year_end`,
                        bu_tax_years.`tax_year`
                    ) 
                VALUES (?, ?, ?);
            ");
            $stmt->execute(
                [
                    $_POST['tax-year-start'],
                    $_POST['tax-year-end'],
                    $_POST['tax-year'],
                ]
            );
                        
            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Tax Year <span class="text-grey">' . $_POST['tax-year'] . '</span> Added'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Tax Year <span class="text-grey">' . $_POST['tax-year'] . '</span> NOT Added'

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
                INSERT INTO 
                    bu_transactions (
                        bu_transactions.`account_id`, 
                        bu_transactions.`account_id_alpha`, 
                        bu_transactions.`amount`, 
                        bu_transactions.`type_id`, 
                        bu_transactions.`sub_type_id`,
                        bu_transactions.`method_id`,
                        bu_transactions.`entity_id`, 
                        bu_transactions.`transaction_date`,
                        bu_transactions.`tax_year`,
                        bu_transactions.`period`, 
                        bu_transactions.`notes`
                    ) 
                VALUES (?,?,?,?,?,?,?,?,?,?,?);
            ");

            $stmt->execute(
                [
                    $bu_account['account_id'],
                    $_POST['account-id-alpha'],  
                    $_POST['amount'],
                    $_POST['type-id'], 
                    (isset($_POST['sub-type-id']) ? $_POST['sub-type-id'] : ''),
                    $_POST['method-id'],
                    $_POST['entity-id'], 
                    $_POST['transaction-date'], 
                    $bu_tax_year['tax_year'],
                    $bu_accounting_period['period'], 
                    $_POST['notes']            
                ]
            );

            // Get the id of the record just added to bu_regular_debits
            $id = $pdo->lastInsertId();

            if (isset($_POST['create-prefill'])) {
                $stmt = $pdo->prepare("
                INSERT INTO 
                    bu_prefills (
                        bu_prefills.`account_id`, 
                        bu_prefills.`account_id_alpha`, 
                        bu_prefills.`type_id`, 
                        bu_prefills.`sub_type_id`,
                        bu_prefills.`method_id`,
                        bu_prefills.`entity_id`,
                        bu_prefills.`notes`
                    ) 
                VALUES (?,?,?,?,?,?,?);
            ");

            $stmt->execute(
                [
                    $bu_account['account_id'],
                    $_POST['account-id-alpha'],  
                    $_POST['type-id'], 
                    (isset($_POST['sub-type-id']) ? $_POST['sub-type-id'] : ''),
                    $_POST['method-id'],
                    $_POST['entity-id'],
                    $_POST['notes'],
                ]
            );

            }
        
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

    // ADD TRANSACTION METHOD RECORD
        case 'add-transaction-method':
            $stmt = $pdo->prepare("
                INSERT INTO 
                    bu_transaction_methods (
                        bu_transaction_methods.`method_id`, 
                        bu_transaction_methods.`method_description`
                    ) 
                VALUES (?, ?);
            ");
            $stmt->execute(
                [
                    $_POST['method-id'],
                    $_POST['method-description']
                ]
            );
                        
            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Transaction Method <span class="text-grey">' . $_POST['method-description'] . '</span> Added'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Transaction Method <span class="text-grey">' . $_POST['method-description'] . '</span> NOT Added'

                ));
            }

            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;


    // ADD TRANSACTION SUB-TYPE RECORD
        case 'add-transaction-sub-type':

            $stmt = $pdo->prepare("
                INSERT INTO 
                    bu_transaction_sub_types (
                        bu_transaction_sub_types.`sub_type_id`, 
                        bu_transaction_sub_types.`sub_type_description`
                    ) 
                VALUES (?, ?);
            ");
            $stmt->execute(
                [
                    $_POST['sub-type-id'],
                    $_POST['sub-type-description']
                ]
            );
            
            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Transaction Sub-Type <span class="text-grey">' . $_POST['sub-type-id'] . '</span> Added'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Transaction Sub-Type <span class="text-grey">' . $_POST['sub-type-id'] . '</span> NOT Added'

                ));
            }

            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;

    
    // ADD TRANSACTION TYPE RECORD
        case 'add-transaction-type':

            $stmt = $pdo->prepare("
                INSERT INTO 
                     bu_transaction_types (
                        bu_transaction_types.`type_id`, 
                        bu_transaction_types.`type_description`
                    ) 
                 VALUES (?, ?);
            ");
            $stmt->execute(
                [
                    $_POST['type-id'],
                    $_POST['type-description']
                ]
            );
            
            if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
            // Success
                echo json_encode(array(
                    'success' => 1,  // True
                    'message' => 'Record for Transaction Type <span class="text-grey">' . $_POST['type-id'] . '</span> Added'
                ));
            } else { 
            // Failure
                echo json_encode(array(
                    'success' => 0,  // False
                    'message' => 'Record for Transaction Type <span class="text-grey">' . $_POST['type-id'] . '</span> NOT Added'

                ));
            }

            // Close connections 
            $stmt = null; 
            $pdo = null;

            break;

    }   // switch

?>
    