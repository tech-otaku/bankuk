
<?php

// Transactions
    $pdo->query("
        DROP PROCEDURE IF EXISTS 
            bu_transactions_get_transactions
    ");

    $pdo->query("
        CREATE PROCEDURE
            bu_transactions_get_transactions (
                IN _record TEXT
            ) 
        BEGIN
            SELECT 
                bu_transactions.`id`,
                bu_transactions.`account_id_alpha`,
                bu_banks.`trading_name`,
                bu_accounts.`name`,
                bu_accounts.`sort_code`,
                bu_accounts.`account_number`,
                bu_accounts.`status`,
                bu_transactions.`amount`,
                bu_transaction_types.`type_description`,
                bu_transaction_sub_types.`sub_type_description`,
                bu_transaction_methods.`method_description`,
                bu_entities.`entity_description`,
                bu_transactions.`transaction_date`,
                bu_transactions.`period`,
                bu_transactions.`tax_year`,
                bu_transactions.`notes`
            FROM
                bu_transactions
            LEFT JOIN
                bu_accounts ON bu_transactions.`account_id_alpha` = bu_accounts.`account_id_alpha`
            LEFT JOIN
                bu_entities ON bu_transactions.`entity_id` = bu_entities.`entity_id`
            LEFT JOIN
                bu_transaction_types ON bu_transactions.`type_id` = bu_transaction_types.`type_id`
            LEFT JOIN
                bu_transaction_sub_types ON bu_transactions.`sub_type_id` = bu_transaction_sub_types.`sub_type_id`
            LEFT JOIN
                bu_transaction_methods ON bu_transactions.`method_id` = bu_transaction_methods.`method_id`
            LEFT JOIN
                bu_banks ON bu_accounts.`bank_id` = bu_banks.`bank_id`
            WHERE 
                bu_transactions.`id` LIKE _record       /* ALL records are returned if `_record` is '%' */
            ORDER BY 
                bu_transactions.`transaction_date` DESC, 
                bu_transactions.`id` DESC;
        END;
    ");


// Accounts Dropdown
    $pdo->query("
        DROP PROCEDURE IF EXISTS 
            bu_accounts_dropdown
    ");

    $pdo->query("
        CREATE PROCEDURE
            bu_accounts_dropdown (
                IN _status VARCHAR(6)
            ) 
        BEGIN 
            SELECT 
                bu_accounts.`account_id_alpha`,
                CONCAT(bu_banks.`trading_name`,' ',bu_accounts.`name`) AS _name, 
                bu_accounts.`account_number`, 
                bu_accounts.`status` 
            FROM 
                bu_accounts
            LEFT JOIN 
                bu_banks ON bu_accounts.`bank_id` = bu_banks.`bank_id`
            WHERE
                LOWER(bu_accounts.`status`) LIKE LOWER(_status)
            ORDER BY 
                _name; 
        END;
    ");


// Get account_id from bu_accounts
    $pdo->query("
        DROP PROCEDURE IF EXISTS 
            bu_accounts_get_data
    ");

    $pdo->query("
        CREATE PROCEDURE
            bu_accounts_get_data (
                IN _account_id_alpha VARCHAR(1)
            ) 
        BEGIN 
            SELECT 
                bu_accounts.`account_id`
            FROM 
                bu_accounts
            WHERE
                LOWER(bu_accounts.`account_id_alpha`) LIKE LOWER(_account_id_alpha); 
        END;
    ");


// Banks Dropdown
    $pdo->query("
        DROP PROCEDURE IF EXISTS 
            bu_banks_dropdown
    ");

    $pdo->query("
        CREATE PROCEDURE 
            bu_banks_dropdown() 
        BEGIN 
            SELECT 
                bu_banks.`bank_id`, 
                bu_banks.`legal_name`,
                bu_banks.`trading_name` 
            FROM 
                bu_banks
            ORDER BY bu_banks.`legal_name` ASC;
        END;
    ");


// Entities Dropdown
    $pdo->query("
        DROP PROCEDURE IF EXISTS 
            bu_entities_dropdown
    ");

    $pdo->query("
        CREATE PROCEDURE 
            bu_entities_dropdown() 
        BEGIN 
            SELECT 
                bu_entities.`entity_id`, 
                bu_entities.`entity_description`
            FROM 
                bu_entities
            ORDER BY 
                bu_entities.`entity_description` ASC;
        END;
    ");

// Regular Debit Types Dropdown
    $pdo->query("
        DROP PROCEDURE IF EXISTS 
            bu_regular_debit_types_dropdown
    ");

    $pdo->query("
        CREATE PROCEDURE 
            bu_regular_debit_types_dropdown() 
        BEGIN 
            SELECT 
                bu_regular_debit_types.`type`, 
                bu_regular_debit_types.`description` 
            FROM 
                bu_regular_debit_types
            ORDER BY 
                bu_regular_debit_types.`description` ASC;
        END;
    ");

// Transaction Method Dropdown
    $pdo->query("
        DROP PROCEDURE IF EXISTS 
            bu_transaction_methods_dropdown
        ");

        $pdo->query("
        CREATE PROCEDURE 
            bu_transaction_methods_dropdown() 
        BEGIN 
            SELECT 
                bu_transaction_methods.`method_id`, 
                bu_transaction_methods.`method_description` 
            FROM 
                bu_transaction_methods
            ORDER BY 
                bu_transaction_methods.`method_description` ASC;
        END;
    ");


// Transaction Sub-Types Dropdown
    $pdo->query("
        DROP PROCEDURE IF EXISTS 
            bu_transaction_sub_types_dropdown
        ");

        $pdo->query("
        CREATE PROCEDURE 
            bu_transaction_sub_types_dropdown() 
        BEGIN 
            SELECT 
                bu_transaction_sub_types.`sub_type_id`, 
                bu_transaction_sub_types.`sub_type_description` 
            FROM 
                bu_transaction_sub_types
            ORDER BY 
                bu_transaction_sub_types.`sub_type_description` ASC;
        END;
    ");



// Transaction Types Dropdown
    $pdo->query("
        DROP PROCEDURE IF EXISTS 
            bu_transaction_types_dropdown
    ");


    $pdo->query("
        CREATE PROCEDURE 
            bu_transaction_types_dropdown() 
        BEGIN 
            SELECT 
                bu_transaction_types.`type_id`, 
                bu_transaction_types.`type_description` 
            FROM 
                bu_transaction_types
            ORDER BY 
                bu_transaction_types.`type_description` ASC;
        END;
    ");

// Current Accounting Period
    $pdo->query("
        DROP PROCEDURE IF EXISTS 
            bu_accounting_periods_current
    ");


    $pdo->query("
        CREATE PROCEDURE 
            bu_accounting_periods_current (
                IN _date DATE
            ) 
        BEGIN 
            SELECT 
                * 
            FROM 
                bu_accounting_periods
            WHERE 
                bu_accounting_periods.`period_start` <= _date AND bu_accounting_periods.`period_end` >= _date;
        END;
    ");

// Current Tax Year
    $pdo->query("
        DROP PROCEDURE IF EXISTS 
            bu_tax_years_current
    ");


    $pdo->query("
        CREATE PROCEDURE 
            bu_tax_years_current(
                IN _date DATE
            ) 
        BEGIN 
            SELECT 
                * 
            FROM 
                bu_tax_years
            WHERE 
                bu_tax_years.`tax_year_start` <= _date AND bu_tax_years.`tax_year_end` >= _date;
        END;
    ");

// Settings
    $pdo->query("
        DROP PROCEDURE IF EXISTS 
            bu_settings_get_settings
    ");


    $pdo->query("
        CREATE PROCEDURE 
            bu_settings_get_settings() 
        BEGIN 
            SELECT 
                *
            FROM 
                bu_settings;
        END;
    ");

// Password
    $pdo->query("
        DROP PROCEDURE IF EXISTS 
            bu_password
        ");


    $pdo->query("
        CREATE PROCEDURE 
            bu_password(
                IN _email VARCHAR(200)
            ) 
        BEGIN 
            SELECT 
                bu_admin.`email`,
                bu_admin.`md5_password`, 
                bu_admin.`password_hash`, 
                bu_admin.`admin_id`,
                bu_admin.`locked`,
                bu_admin.`login_attempts`  
            FROM 
                bu_admin
            WHERE 
                bu_admin.`email` = _email;
        END;
    ");