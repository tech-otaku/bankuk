<?php

    /***
     * 1. Create a new transaction record in the bu_transactions table using data from the relevant regular debit record. The transaction
     *    record's effective `date` uses the current value of the regular debit record's `next` date column.
     * 2. Replace the value of the regular debit record's `last` date column with the current value of its `next` date column.
     * 3. Re-calculate the value of the regular debit record's `next` date column for the next accounting period based upon its `day` column making adjustments if the date is a weekend or public holiday.
    */ 
   
    // Simulate an error to test error responses. Comment-out when finished testing. 
    //header("HTTP/1.0 500 Internal Server Error");   // This is handled by the fail() method of the AJAX request (bu_ajax_delete.js)

    include_once('../conf/pdoconfig.php');
    include_once('../conf/bu_custom.php');

// Get the regular debit record based on the record id passed from the client (ajax_due_date.js)
    
    $stmt = $pdo->prepare("
        SELECT 
            *
        FROM 
            bu_regular_debits
        WHERE
            id = ?;
    ");

    $stmt->execute(
        [
            intval($_POST['record-id']),
        ]
    );

    $bu_regular_debit = $stmt->fetch(PDO::FETCH_OBJ);   
    $stmt = null;

// Get the period from bu_accounting_periods based on the value of $bu_regular_debit->next
    $stmt = $pdo->prepare("
        SELECT 
            period 
        FROM 
            bu_accounting_periods 
        WHERE 
            start <= ? AND end >= ?;
    ");

    $stmt->execute(
        [
            $bu_regular_debit->next, 
            $bu_regular_debit->next
        ]
    );

    $bu_accounting_period = $stmt->fetch(PDO::FETCH_OBJ);   
    $stmt = null;

    if ($bu_accounting_period) {   // Record found in bu_accounting_periods using $bu_regular_debit->next
    
        // 1. Add the regular debit record to the transactions table
        $stmt = $pdo->prepare("
            INSERT INTO 
                bu_transactions (
                    account_id, 
                    account_id_alpha, 
                    amount, 
                    type, 
                    sub_type, 
                    entity_id, 
                    date, 
                    period, 
                    notes
                )  
            VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, ?);
        "); 

        $stmt->execute(
            [
                $bu_regular_debit->account_id,
                $bu_regular_debit->account_id_alpha, 
                $bu_regular_debit->amount,
                $bu_regular_debit->type, 
                (isset($bu_regular_debit->sub_type) ? $bu_regular_debit->sub_type : ''),
                //'P5283',
                $bu_regular_debit->entity_id, 
                $bu_regular_debit->next, 
                $bu_accounting_period->period, 
                $bu_regular_debit->notes
            ]
        );

        $stmt = $pdo->prepare("
            SELECT 
                entity_description 
            FROM 
                bu_entities
            WHERE 
                entity_id = ?;
        ");

        $stmt->execute(
            [
                $bu_regular_debit->entity_id
            ]
        );

        $bu_entity = $stmt->fetch(PDO::FETCH_OBJ);   
        $stmt = null;

    // 3. Re-calculate bu_regular_debits.next for next month

        $next_recalculated = new DateTime($bu_regular_debit->next);     

        if ($bu_regular_debit->regular_debit_type != 8) {
            $next_recalculated->modify($bu_regular_debit->day - $next_recalculated->format('d') . ' day');
        } else {
            if ($next_recalculated->format('d') > $bu_regular_debit->day) {
                $next_recalculated->modify($bu_regular_debit->day - $next_recalculated->format('d') . ' day +1 month');
            }
        }   // regular_debit_type != 8

        $next_recalculated->modify('+1 month');

        if ($bu_regular_debit->regular_debit_type != 3 && $bu_regular_debit->regular_debit_type != 5) {  // Statement Debit Card Payment (3) or Scheduled Deduction (5) i.e. Account Maintenance Fee. Don't adjust date.
            $excluded = true;   // Assume date is Saturday, Sunday or Public Holiday
            while ($excluded) {
                if ($next_recalculated->format('N') >= 6 || in_array($next_recalculated->format('Y-m-d'), $publicHolidays)) {     // Saturday (6), Sunday (7) or Public Holiday 
                    if ($bu_regular_debit->regular_debit_type == 7 || $bu_regular_debit->regular_debit_type == 8) {     // Salary or Pension
                        $next_recalculated->modify('-1 day');   // Go back 1 day
                    } else {
                        $next_recalculated->modify('+1 day');   // Go forward 1 day
                    }   // Salary or Pension
                } else {
                    $excluded = false;
                } // Saturday (6), Sunday (7) or Public Holiday
            }   // while 
        }   // // Statement Debit Card Payment (3) or Scheduled Deduction (5)


        $stmt = $pdo->prepare("
            UPDATE 
                bu_regular_debits 
            SET 
                last = ?, 
                next = ?  
            WHERE 
                id = ?;
        ");        
        $stmt->execute(
            [
                $bu_regular_debit->next,
                $next_recalculated->format('Y-m-d'), 
                intval($_POST['record-id'])
            ]
        );

        if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
        // Success
            echo json_encode(array(
                'success' => 1,  // True
                'message' => 'Transaction added for <span class="text-grey">' . $bu_entity->entity_description . '</span> effective <span class="text-grey">' . $fmt_date->format(strtotime($bu_regular_debit->next)) . '</span> period <span class="text-grey">' . $bu_accounting_period->period . '</span>',
                'entity' => $bu_entity->entity_description,
                "current" => $bu_regular_debit->next,
                "next" => $next_recalculated->format('Y-m-d'),
                "period" => $bu_accounting_period->period
            ));
        } else { 
        // Failure
            echo json_encode(array(
                'success' => 0,  // True
                'message' => 'Something went wrong!'

            ));
        }

        // Close connections 
        $stmt = null; 
        $pdo = null;


    } else { 
    // Failure
        echo json_encode(array(
            'success' => 0,  // False
            'message' => 'No <span class="text-grey">Accounting Period</span> record for transactions with an effective date of <span class="text-grey">' . $fmt_date->format(strtotime($bu_regular_debit->next)) . '</span> exists. Please add one.'
        ));

        // Close connections 
        $stmt = null; 
        $pdo = null;
    }
 
?>
    