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
                bu_reconcilliation.`income`,
                bu_reconcilliation.`taxable_interest`,
                bu_reconcilliation.`tax_free_interest`,
                bu_reconcilliation.`cashback`,
                bu_reconcilliation.`transfers_to`,
                bu_reconcilliation.`transfers_from`,
                bu_reconcilliation.`excluded_spend`,
                bu_reconcilliation.`excluded_income`,
                bu_reconcilliation.`period`,
                bu_reconcilliation.`end`
            )
        SELECT
            SUM(IF(bu_transactions.`type_id` IN ('A' , 'B'), bu_transactions.`amount`, 0)),
            SUM(IF(bu_transactions.`type_id` = 'C', bu_transactions.`amount`, 0)),
            SUM(IF(bu_transactions.`type_id` = 'D', bu_transactions.`amount`, 0)),
            SUM(IF(bu_transactions.`type_id` = 'E', bu_transactions.`amount`, 0)),
            SUM(IF(bu_transactions.`type_id` = 'T' AND bu_transactions.`amount` < 0, amount, 0)),
            SUM(IF(bu_transactions.`type_id` = 'T' AND bu_transactions.`amount` >= 0, bu_transactions.`amount`,0)),
            SUM(IF(bu_transactions.`type_id` = 'X' AND bu_transactions.`amount` < 0, bu_transactions.`amount`, 0)),
            SUM(IF(bu_transactions.`type_id` = 'X' AND bu_transactions.`amount` >= 0, bu_transactions.`amount`,0)),
            bu_accounting_periods.`period`,
            bu_accounting_periods.`period_end`
        FROM
            bu_transactions
        LEFT JOIN 
            bu_accounting_periods ON bu_transactions.`period` = bu_accounting_periods.`period`
        WHERE 
            bu_accounting_periods.`period` >= ? AND bu_accounting_periods.`period` <= ?
        GROUP BY
            bu_accounting_periods.`period`, bu_accounting_periods.`period_end`;
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
            bu_reconcilliation
        LEFT JOIN 
            bu_monthly_spend ON bu_monthly_spend.`period` = bu_reconcilliation.`period`
        SET monthly_spend = 
            bu_monthly_spend.`cash` + 
            bu_monthly_spend.`utilities` + 
            bu_monthly_spend.`commute` + 
            bu_monthly_spend.`cards` + 
            bu_monthly_spend.`supermarket` + 
            bu_monthly_spend.`other` + 
            bu_monthly_spend.`rent` + 
            bu_monthly_spend.`charities`;"
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
                bu_reconcilliation.`income` + 
                bu_reconcilliation.`monthly_spend` +
                bu_reconcilliation.`taxable_interest` + 
                bu_reconcilliation.`tax_free_interest` + 
                bu_reconcilliation.`cashback` + 
                bu_reconcilliation.`transfers_to` +
                bu_reconcilliation.`transfers_from` +
                bu_reconcilliation.`excluded_spend` +
                bu_reconcilliation.`excluded_income`,
            bu_reconcilliation.`savings_actual` = `closing` - `opening`,
            bu_reconcilliation.`savings` = bu_reconcilliation.`savings_actual` + 
                (bu_reconcilliation.`taxable_interest` * -1) + 
                (bu_reconcilliation.`tax_free_interest` * -1) + 
                (bu_reconcilliation.`cashback` * -1) + 
                (bu_reconcilliation.`transfers_to` * -1) + 
                (bu_reconcilliation.`transfers_from` * -1) + 
                (bu_reconcilliation.`excluded_spend` * -1) + 
                (bu_reconcilliation.`excluded_income` * -1);
    ");
    $stmt->execute();
    $stmt = null;