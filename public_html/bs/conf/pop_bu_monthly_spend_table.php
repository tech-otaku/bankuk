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
                bu_monthly_spend.`salary`,
                bu_monthly_spend.`pension`,
                bu_monthly_spend.`cash`,
                bu_monthly_spend.`utilities`,
                bu_monthly_spend.`commute`,
                bu_monthly_spend.`cards`,
                bu_monthly_spend.`supermarket`,
                bu_monthly_spend.`other`,
                bu_monthly_spend.`rent`,
                bu_monthly_spend.`charities`,
                bu_monthly_spend.`period`,
                bu_monthly_spend.`end`
            )
        SELECT
            -- SUM(IF(bu_transactions.`type_id` = 'A', bu_transactions.`amount`, 0)),
            SUM(IF(bu_transactions.`sub_type_id` = 'A', bu_transactions.`amount`, 0)),
            -- SUM(IF(bu_transactions.`type_id` = 'B', bu_transactions.`amount`, 0)),
            SUM(IF(bu_transactions.`sub_type_id` = 'B', bu_transactions.`amount`, 0)),
            SUM(IF(bu_transactions.`type_id` = '1', bu_transactions.`amount`, 0)),
            SUM(IF(bu_transactions.`type_id` = '2', bu_transactions.`amount`, 0)),
            SUM(IF(bu_transactions.`type_id` = '3', bu_transactions.`amount`, 0)),
            SUM(IF(bu_transactions.`type_id` = '4', bu_transactions.`amount`, 0)),
            SUM(IF(bu_transactions.`type_id` = '5', bu_transactions.`amount`, 0)),
            SUM(IF(bu_transactions.`type_id` = '6', bu_transactions.`amount`, 0)),
            SUM(IF(bu_transactions.`type_id` = '7', bu_transactions.`amount`, 0)),
            SUM(IF(bu_transactions.`type_id` = '8', bu_transactions.`amount`, 0)),
            bu_accounting_periods.`period`,
            bu_accounting_periods.`period_end`
        FROM
            bu_transactions
        LEFT JOIN 
            bu_accounting_periods ON bu_accounting_periods.`period` = bu_transactions.`period`
        WHERE 
            bu_accounting_periods.`period` >= ? AND bu_accounting_periods.`period` <= ?
        GROUP BY
            bu_accounting_periods.`period`, bu_accounting_periods.`period_end`;
    ");
    $stmt->execute(
        [
            $bu_settings['monthly_spend_first_period'],     // Default is 11
            $bu_settings['current_period']
        ]
    );
    $stmt = null;

    // Populate the `total_spend` and `remaining` columns in the bu_monthly_spend table 
        $stmt = $pdo->prepare("
            UPDATE 
                bu_monthly_spend 
            SET 
                bu_monthly_spend.`total_spend` = 
                    bu_monthly_spend.`cash` + 
                    bu_monthly_spend.`utilities` + 
                    bu_monthly_spend.`commute` + 
                    bu_monthly_spend.`cards` + 
                    bu_monthly_spend.`supermarket` + 
                    bu_monthly_spend.`other` +
                    bu_monthly_spend.`rent` + 
                    bu_monthly_spend.`charities`,
                bu_monthly_spend.`remaining` = 
                    bu_monthly_spend.`salary` + 
                    bu_monthly_spend.`pension` + 
                    bu_monthly_spend.`total_spend`;
        ");
        $stmt->execute();
        $stmt = null;