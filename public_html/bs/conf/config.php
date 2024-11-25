<?php

    $mysql_credentials = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/../mysql-credentials.ini');    // file containing mysql connection information

    $mysqli=new mysqli(
        $mysql_credentials['host'],
        $mysql_credentials['username'],
        $mysql_credentials['password'], 
        $mysql_credentials['database']
    );
