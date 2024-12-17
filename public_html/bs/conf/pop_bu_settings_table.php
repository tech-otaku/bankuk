<?php

// This file is executed by public_html/bs/partials/head.php

// Get the current accounting period data based upon today's date
    $stmt = $pdo->prepare("
        CALL 
            bu_accounting_periods_current(?);
    ");
    $stmt->execute(
        [
            date('Y-m-d')   // Today's date as YYYY-MM-DD
        ]
    );

    $bu_accounting_period_current = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = null;

// Get the id of the only, single settings record (should be '1')
    $stmt = $pdo->prepare("
        SELECT 
            id
        FROM 
            bu_settings
        ORDER BY 
            id DESC
        LIMIT 1;
        
    ");        
    $stmt->execute();

    $id = $stmt->fetchColumn();
    $stmt = null;

// Update the current accounting period data in the bu_settings table 
    $stmt = $pdo->prepare("
        UPDATE 
            bu_settings
        SET 
            current_period = ?,
            current_start = ?,
            current_end = ?
        WHERE
            id = ?
    ");        
    $stmt->execute(
        [ 
            $bu_accounting_period_current['period'],
            $bu_accounting_period_current['start'],
            $bu_accounting_period_current['end'],
            $id
        ]
    );

    $stmt = null;






