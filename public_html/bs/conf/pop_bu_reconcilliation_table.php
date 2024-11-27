<?php

// Get settings data
    $stmt = $pdo->prepare("
        CALL 
            bu_settings_get_settings();
    ");
    $stmt->execute();
    
    $bu_settings = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = null;

// Delete all records in the bu_reconcilliation table
    $stmt = $pdo->prepare("
        TRUNCATE TABLE 
            bu_reconcilliation;
    ");
    $stmt->execute();
    $stmt = null;

   
// Populate the bu_reconcilliation table with summed values from the bu_transactions table
    $stmt = $pdo->prepare("
        INSERT INTO 
            bu_reconcilliation (
                income,
                taxable_interest,
                tax_free_interest,
                cashback,
                transfers_to,
                transfers_from,
                excluded_spend,
                excluded_income,
                period,
                `end`
            )
        SELECT
            SUM(IF(`type` IN ('A' , 'B'), amount, 0)),
            SUM(IF(`type` = 'C', amount, 0)),
            SUM(IF(`type` = 'D', amount, 0)),
            SUM(IF(`type` = 'E', amount, 0)),
            SUM(IF(`type` = 'T' AND amount < 0, amount, 0)),
            SUM(IF(`type` = 'T' AND amount >= 0,amount,0)),
            SUM(IF(`type` = 'X' AND amount < 0, amount, 0)),
            SUM(IF(`type` = 'X' AND amount >= 0,amount,0)),
            ap1.period,
            ap1.`end`
        FROM
            bu_transactions AS t1
        LEFT JOIN 
            bu_accounting_periods AS ap1 ON t1.period = ap1.period
        WHERE 
            ap1.period >= ? AND ap1.period <= ?
        GROUP BY
            ap1.period, ap1.`end`;
        ");
    $stmt->execute(
        [
            $bu_settings['reconcilliation_first_period'],     // Default is 87
            $bu_settings['current_period']
        ]
    );
    $stmt = null;


// Update the records in the bu_reconcilliation table with values from the bu_monthly_spend table
    $stmt = $pdo->prepare("
        UPDATE 
            bu_reconcilliation AS r1
        LEFT JOIN 
            bu_monthly_spend AS ms1 ON ms1.period = r1.period
        SET monthly_spend = 
            ms1.cash + 
            ms1.utilities + 
            ms1.commute + 
            ms1.cards + 
            ms1.supermarket + 
            ms1.other + 
            ms1.rent + 
            ms1.charities;"
    );
    $stmt->execute();
    $stmt = null;


// Set variables used to update the opening and closing balances in the bu_reconcilliation table
    $stmt = $pdo->prepare("
        SELECT 
            ? 
        INTO 
            @opening;
    ");
    $stmt->execute(
        [
            $bu_settings['reconcilliation_opening_balance']     // Default is 76985.75
        ]
    );
    $stmt = null;

    $stmt = $pdo->prepare("
        SELECT 
            @opening 
        INTO 
            @closing;
    ");
    $stmt->execute();
    $stmt = null;    

// Update the opening and closing balances for records in the bu_reconcilliation table 
    $stmt = $pdo->prepare("
        UPDATE
            bu_reconcilliation
        SET `opening` = IF(id = 1,@opening,@closing), 
            `closing` = @closing := @closing + 
                `income` + 
                `monthly_spend` +
                `taxable_interest` + 
                `tax_free_interest` + 
                `cashback` + 
                `transfers_to` +
                `transfers_from` +
                `excluded_spend` +
                `excluded_income`,
            `savings_actual` = `closing` - `opening`,
            `savings` = `savings_actual` + 
                (`taxable_interest` * -1) + 
                (`tax_free_interest` * -1) + 
                (`cashback` * -1) + 
                (`transfers_to` * -1) + 
                (`transfers_from` * -1) + 
                (`excluded_spend` * -1) + 
                (`excluded_income` * -1);
    ");
    $stmt->execute();
    $stmt = null;