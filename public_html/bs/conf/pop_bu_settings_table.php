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
            bu_settings.id
        FROM 
            bu_settings
        ORDER BY 
            bu_settings.id DESC
        LIMIT 1;
        
    ");        
    $stmt->execute();

    $id = $stmt->fetchColumn();
    $stmt = null;

    $taxYears = TaxYears($pdo);

// Update the current accounting period data in the bu_settings table 
    $stmt = $pdo->prepare("
        UPDATE 
            bu_settings
        SET 
            bu_settings.`current_period` = ?,
            bu_settings.`current_start` = ?,
            bu_settings.`current_end` = ?,
            bu_settings.`first_tax_year_start` = ?,
            bu_settings.`number_of_tax_years` = ?
        WHERE
            bu_settings.`id` = ?
    ");        
    $stmt->execute(
        [ 
            $bu_accounting_period_current['period'],
            $bu_accounting_period_current['period_start'],
            $bu_accounting_period_current['period_end'],
            $taxYears['earliest'],
            $taxYears['number'],
            $id
        ]
    );

    $stmt = null;

//








