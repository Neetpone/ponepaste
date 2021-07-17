<?php
require_once('../includes/config.php');
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
    array('db' => 'title', 'dt' => 1),
    array('db' => 'member', 'dt' => 2),
    array('db' => 'tagsys', 'dt' => 3),
    array('db' => 'visible', 'dt' => 4),
);

$columns2 = array(
    array('db' => 'title', 'dt' => 0),
    array('db' => 'member', 'dt' => 1),
    array('db' => 'tagsys', 'dt' => 2),
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
?>