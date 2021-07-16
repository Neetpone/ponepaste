<?php
header('Content-Type: application/json');

require_once('config.php');
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
    array('db' => 'date', 'dt' => 5),
    array('db' => 'now_time', 'dt' => 6),
);

$columns2 = array(
    array('db' => 'id', 'dt' => "ID"),
    array('db' => 'title', 'dt' => "Title"),
    array('db' => 'member', 'dt' => "Author"),
    array('db' => 'tagsys', 'dt' => "Tags"),
    array('db' => 'date', 'dt' => "Post Date"),
    array('db' => 'now_time', 'dt' => "Modified Date"),
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

require('public.pastes.php');

echo json_encode(
    SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $columns2),
    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
);
