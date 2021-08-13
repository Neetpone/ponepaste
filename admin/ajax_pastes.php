<?php
define('IN_PONEPASTE', 1);

require_once(__DIR__ . '/common.php');


// DB table to use
$table = 'pastes';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array('db' => 'id', 'dt' => 0),
    array('db' => 'member', 'dt' => 1),
    array('db' => 'ip', 'dt' => 2),
    array('db' => 'visible', 'dt' => 3)
);

$columns2 = array(
    array('db' => 'id', 'dt' => 0),
    array('db' => 'member', 'dt' => 1),
    array('db' => 'ip', 'dt' => 2),
    array('db' => 'visible', 'dt' => 3),
    array('db' => 'details', 'dt' => 4),
    array('db' => 'view', 'dt' => 5),
    array('db' => 'delete', 'dt' => 6)
);


// SQL server connection information
$sql_details = array(
    'user' => $db_user,
    'pass' => $db_pass,
    'db' => $db_schema,
    'host' => $db_host
);


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require('ssp.pastes.php');

echo json_encode(
    SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $columns2)
);
