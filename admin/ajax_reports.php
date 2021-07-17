<?php
session_start();

if (isset($_SESSION['login'])) {
// Do nothing	
} else {
    header("Location: .");
    exit();
}

if (isset($_GET['logout'])) {
    if (isset($_SESSION['login']))
        unset($_SESSION['login']);

    session_destroy();
    header("Location: .");
    exit();
}

require_once('../includes/config.php');
// DB table to use
$table = 'user_reports';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array('db' => 'id', 'dt' => 0),
    array('db' => 'm_report', 'dt' => 1),
    array('db' => 'p_report', 'dt' => 2),
    array('db' => 't_report', 'dt' => 3)
);

$columns2 = array(
    array('db' => 'id', 'dt' => 0),
    array('db' => 'm_report', 'dt' => 1),
    array('db' => 'p_report', 'dt' => 2),
    array('db' => 't_report', 'dt' => 3),
    array('db' => 'details', 'dt' => 4),
    array('db' => 'view', 'dt' => 5),
    array('db' => 'delete', 'dt' => 6)
);


// SQL server connection information
$sql_details = array(
    'user' => $dbuser,
    'pass' => $dbpassword,
    'db' => $dbname,
    'host' => $dbhost
);


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require('ssp.reports.php');

echo json_encode(
    SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $columns2)
);
?>