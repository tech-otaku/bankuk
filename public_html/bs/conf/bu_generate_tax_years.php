<?php

// Get settings data
    $stmt = $pdo->prepare("
        CALL 
            bu_settings_get_settings();
    ");
    $stmt->execute();

    $bu_settings = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = null;
    
// Delete all records in the bu_monthly_spend table
    $stmt = $pdo->prepare("
        TRUNCATE TABLE 
            bu_monthly_spend;
    ");
    $stmt->execute();
    $stmt = null;    

// Populate the bu_monthly_spend table with summed values from the bu_transactions table
    $stmt = $pdo->prepare("
        INSERT INTO 
            bu_monthly_spend(
                salary,
                pension,
                cash,
                utilities,
                commute,
                cards,
                supermarket,
                other,
                rent,
                charities,
                period,
                `end`
            )
        SELECT
            SUM(IF(`type` = 'A', amount, 0)),
            SUM(IF(`type` = 'B', amount, 0)),
            SUM(IF(`type` = '1', amount, 0)),
            SUM(IF(`type` = '2', amount, 0)),
            SUM(IF(`type` = '3', amount, 0)),
            SUM(IF(`type` = '4', amount, 0)),
            SUM(IF(`type` = '5', amount, 0)),
            SUM(IF(`type` = '6', amount, 0)),
            SUM(IF(`type` = '7', amount, 0)),
            SUM(IF(`type` = '8', amount, 0)),
            bu_accounting_periods.`period`,
            bu_accounting_periods.`end`
        FROM
            bu_transactions AS t1
        LEFT JOIN 
            bu_accounting_periods AS ap1 ON ap1.period = t1.period
        WHERE 
            ap1.period >= ? AND ap1.period <= ?
        GROUP BY
            ap1.period, ap1.`end`;
    ");
    $stmt->execute(
        [
            $bu_settings['monthly_spend_first_period'],     // Default is 11
            $bu_settings['current_period']
        ]
    );
    $stmt = null;