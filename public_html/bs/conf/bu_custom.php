<?php

    $publicHolidays = [
        // 2024
        // https://www.officesimplify.com/bank-holidays-uk-2024
            '2024-01-01', '2024-03-29', '2024-04-01', '2024-05-06', '2024-05-27', '2024-08-26', '2024-12-25', '2024-12-26', 
        // 2025
        // https://www.officesimplify.com/bank-holidays-uk-2025
            '2025-01-01', '2025-04-18', '2025-04-21', '2025-05-05', '2025-05-26', '2025-08-25', '2025-12-25', '2025-12-26',
        // 2026
        // https://www.officesimplify.com/bank-holidays-uk-2026
            '2026-01-01', '2026-04-03', '2026-04-06', '2026-05-04', '2026-05-25', '2026-08-31', '2026-12-25', '2026-12-28',
        // 2027
        // https://www.officesimplify.com/bank-holidays-uk-2027
            '2027-01-01', '2027-03-26', '2027-03-29', '2027-05-03', '2027-05-31', '2027-08-30', '2027-12-27', '2027-12-28',
        // 2028
        // https://www.officesimplify.com/bank-holidays-uk-2028
            '2028-01-03', '2028-04-14', '2028-04-17', '2028-05-01', '2028-05-29', '2028-08-28', '2028-12-25', '2028-12-26',
        // 2029
        // https://www.officesimplify.com/bank-holidays-uk-2029
            '2029-01-01', '2029-03-30', '2029-04-02', '2029-05-07', '2029-05-28', '2029-08-27', '2029-12-25', '2029-12-26',
        // 2030
        // https://www.officesimplify.com/bank-holidays-uk-2030
            '2030-01-01', '2030-04-19', '2030-04-22', '2030-05-06', '2030-05-27', '2030-08-26', '2030-12-25', '2030-12-26',
        // 2031
        // https://www.officesimplify.com/bank-holidays-uk-2031
            '2031-01-01', '2031-04-11', '2031-04-14', '2031-05-05', '2031-05-26', '2031-08-25', '2031-12-25', '2031-12-26',
        // 2032
        // https://www.officesimplify.com/bank-holidays-uk-2032
            '2032-01-01', '2032-03-26', '2032-03-29', '2032-05-03', '2032-05-31', '2032-08-30', '2032-12-27', '2032-12-28',
        // 2033
        // https://www.officesimplify.com/bank-holidays-uk-2033
            '2033-01-03', '2033-04-15', '2033-04-18', '2033-05-02', '2033-05-30', '2033-08-29', '2033-12-26', '2033-12-27',
        // 2034
        // https://www.officesimplify.com/bank-holidays-uk-2034
            '2034-01-02', '2034-04-07', '2034-04-10', '2034-05-01', '2034-05-29', '2034-08-28', '2034-12-25', '2034-12-26',
        // 2035
        // https://www.officesimplify.com/bank-holidays-uk-2035
            '2035-01-01', '2035-03-23', '2035-03-26', '2035-05-07', '2035-05-28', '2035-08-27', '2035-12-25', '2035-12-26',
    ];

    $fmt_currency = new NumberFormatter( 'en_GB', NumberFormatter::CURRENCY );      // Example usage: echo $fmt_currency->formatCurrency($row->amount, "GBP");
    
    $fmt_date = new IntlDateFormatter( "en-GB" ,IntlDateFormatter::FULL, IntlDateFormatter::FULL,'Europe/London',IntlDateFormatter::GREGORIAN , "EEE dd/MM/yyyy");
