<?php
 
/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simple to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See https://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - https://datatables.net/license_mit
 */
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */
 
// DB table to use
$table = 'manage_transactions_view';
 
// Table's primary key
$primaryKey = 'id';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier - in this case object
// parameter names
$columns = array(
    array(
        'db' => 'id',
        'dt' => 'DT_RowId',
        'formatter' => function( $d, $row ) {
            // Technically a DOM id cannot start with an integer, so we prefix
            // a string. This can also be useful if you have multiple tables
            // to ensure that the id is unique with a different prefix
            return 'row_'.$d;
        }
    ),

    /**
     * The value for the `dt` parameter can be different to the value of the corresponding `db` parameter, but 
     * the value for the `dt` parameter MUST be the same as the value of the `data` property in the table initialisation script defined in ss_manage_transactions.php
     */ 
    array( 'db' => 'id',                    'dt' => 'id' ),
    array( 'db' => 'account_id_alpha',      'dt' => 'account_id_alpha' ),
    array( 'db' => 'trading_name',          'dt' => 'trading_name' ),
    array( 'db' => 'name',                  'dt' => 'name' ),
    array( 'db' => 'sort_code',             'dt' => 'sort_code' ),
    array( 'db' => 'account_number',        'dt' => 'account_number' ),
    array( 'db' => 'status',                'dt' => 'status' ),
    array( 'db' => 'amount',                'dt' => 'amount' ),
    array( 'db' => '_type',                 'dt' => '_type' ),
    array( 'db' => '_subtype',              'dt' => '_subtype' ),
    array( 'db' => 'entity_description',    'dt' => 'entity_description' ),
    array( 'db' => 'date',                  'dt' => 'date' ),
    array( 'db' => 'tax_year',              'dt' => 'tax_year' ),
    array( 'db' => 'period',                'dt' => 'period' ),
    array( 'db' => 'notes',                 'dt' => 'notes' )
);
 
$sql_details = array(
    'user' => 'root',
    'pass' => 'root',
    'db'   => 'bankuk',
    'host' => 'localhost'
);
 
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
 
require( 'classes/ssp.class.php' );
 
echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);