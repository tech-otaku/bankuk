<?php

$fmt_currency = new NumberFormatter( 'en_GB', NumberFormatter::CURRENCY );      // Example usage: echo $fmt_currency->formatCurrency($row->amount, "GBP");
$fmt_date = new IntlDateFormatter( "en-GB" ,IntlDateFormatter::FULL, IntlDateFormatter::FULL,'Europe/London',IntlDateFormatter::GREGORIAN , "EEE dd/MM/yyyy");
