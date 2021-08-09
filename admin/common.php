<?php
if (!defined('IN_PONEPASTE')) {
    die('This file may not be accessed directly.');
}
require_once('../includes/common.php');

function updateAdminHistory($conn) {
    $last_date = null;
    $last_ip = null;
    $ip = $_SERVER['REMOTE_ADDR'];
    $date = date('jS F Y');

    $query = $conn->query('SELECT ip, last_date FROM admin_history ORDER BY ID DESC LIMIT 1');

    if ($row = $query->fetch()) {
        $last_date = $row['last_date'];
        $last_ip = $row['ip'];
    }

    if ($last_ip !== $ip || $last_date !== $date) {
        $conn->prepare('INSERT INTO admin_history (ip, last_date) VALUES (?, ?)')->execute([$date, $ip]);
    }
}

if (!isset($_SESSION['login'])) {
    header('Location: .');
    exit();
}

if (isset($_GET['logout'])) {
    if (isset($_SESSION['login']))
        unset($_SESSION['login']);

    session_destroy();
    header("Location: .");
    exit();
}

