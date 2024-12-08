<?php
// Global PDO exception handler to avoid using explicit individual try/catch blocks
set_exception_handler(function($e) {
    error_log($e->getMessage());
    exit($e->getMessage());
});

$mysql_credentials = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/../mysql-credentials.ini');    // file containing mysql connection information

$dsn = "mysql:host=" . $mysql_credentials['host'] . ";dbname=" . $mysql_credentials['database']. ";charset=" . $mysql_credentials['charset'];
$opt = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    //PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_FOUND_ROWS => true  // See https://stackoverflow.com/a/11820939/2518495
];

// Connect to database
$pdo = new PDO(
    $dsn, 
    $mysql_credentials['username'], 
    $mysql_credentials['password'], 
    $opt
);

