<?php

    // Simulate an error to test error responses. Comment-out when finished testing. 
    //header("HTTP/1.0 500 Internal Server Error");   // This is handled by the fail() method of the AJAX request (bu_ajax_delete.js)

    include_once('../conf/pdoconfig.php');

    //var_dump($_POST);

// Get the regular debit record based on the record id passed from the client (ajax_due_date.js)
    
    $stmt = $pdo->prepare("
        SELECT *
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

    $next_due = new DateTime($bu_regular_debit->next);
    $next_due->modify('+1 month');

    //echo $bu_regular_debit->next;
    

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
    
    //echo $bu_accounting_period->period;

    // Add the regular debit record to the transactions table
    $stmt = $pdo->prepare("
        INSERT INTO 
            bu_transactions (
                account_id, 
                account_id_alpha, 
                amount, 
                type, 
                sub_type, 
                party_id, 
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
            $bu_regular_debit->party_id, 
            $bu_regular_debit->next, 
            $bu_accounting_period->period, 
            $bu_regular_debit->notes
        ]
    );

    $stmt = $pdo->prepare("
        SELECT 
            party 
        FROM 
            bu_parties
        WHERE 
            party_id = ?;
    ");        
    $stmt->execute(
        [
            $bu_regular_debit->party_id
        ]
    );

    $bu_party = $stmt->fetch(PDO::FETCH_OBJ);   
    $stmt = null;
    

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
            $next_due->format('Y-m-d'), 
            intval($_POST['record-id'])
        ]
    );

    //echo $stmt->rowCount();

    if ($stmt->rowCount() != 0) {   // $stmt->rowCount() should only be used for DELETE, INSERT or UPDATE statements. 
    // Success

        echo json_encode(array(
            'success' => 1,  // True
            'message' => 'Transaction added for <span class="record-updated">' . $bu_party->party . '</span> effective <span class="record-updated">' . $bu_regular_debit->next . '</span>',
            'party' => $bu_party->party,
            "current" => $bu_regular_debit->next,
            "next" => $next_due->format('Y-m-d'),
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

    
    /*
    // The server response returned to the client (ajax-due-date.js)
    echo json_encode(array(
        "current" => $bu_regular_debit->next,
        "next" => $next_due->format('Y-m-d'),
        "period" => $bu_accounting_period->period
    ));
    */
    
?>
    