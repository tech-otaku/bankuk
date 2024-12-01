
<?php

// Accounts Dropdown
    $pdo->query("
        DROP PROCEDURE IF EXISTS 
            bu_accounts_dropdown
    ");

    $pdo->query("
        CREATE PROCEDURE
            bu_accounts_dropdown(
                IN _status VARCHAR(6)
            ) 
        BEGIN 
            SELECT 
                a1.account_id_alpha,
                CONCAT(b1.trading_name,' ',a1.`name`) AS _name, 
                a1.account_number, 
                a1.`status` 
            FROM 
                bu_accounts AS a1
            LEFT JOIN 
                bu_banks AS b1 ON a1.bank_id = b1.bank_id
            WHERE
                LOWER(a1.`status`) LIKE LOWER(_status)
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
            bu_accounts_get_data(
                IN _account_id_alpha VARCHAR(1)
            ) 
        BEGIN 
            SELECT 
                account_id
            FROM 
                bu_accounts
            WHERE
                LOWER(account_id_alpha) LIKE LOWER(_account_id_alpha); 
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
                bank_id, 
                legal_name 
            FROM 
                bu_banks;
        END;
    ");


// Parties Dropdown
    $pdo->query("
        DROP PROCEDURE IF EXISTS 
            bu_parties_dropdown
    ");

    $pdo->query("
        CREATE PROCEDURE 
            bu_parties_dropdown() 
        BEGIN 
            SELECT 
                party_id, 
                party
            FROM 
                bu_parties 
            ORDER BY 
                party ASC;
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
                `type`, 
                `description` 
            FROM 
                bu_regular_debit_types 
            ORDER BY 
                `description` ASC;
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
                `type`, 
                `description` 
            FROM 
                bu_transaction_types 
            ORDER BY 
                `description` ASC;
        END;
    ");

// Current Accounting Period
    $pdo->query("
        DROP PROCEDURE IF EXISTS 
            bu_accounting_periods_current
    ");


    $pdo->query("
        CREATE PROCEDURE 
            bu_accounting_periods_current(
                IN _date DATE
            ) 
        BEGIN 
            SELECT 
                * 
            FROM 
                bu_accounting_periods 
            WHERE 
                `start` <= _date AND `end` >= _date;
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
                IN _email VARCHAR(200),
                IN _password VARCHAR(200)
            ) 
        BEGIN 
            SELECT 
                email, 
                password, 
                admin_id  
            FROM 
                bu_admin 
            WHERE 
                email = _email AND password = _password;
        END;
    ");