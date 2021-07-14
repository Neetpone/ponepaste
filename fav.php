<?php
define('IN_PONEPASTE', 1);
require_once('includes/common.php');
require_once('includes/functions.php');

// UTF-8
header('Content-Type: text/html; charset=utf-8');

$date = date('jS F Y');
$ip = $_SERVER['REMOTE_ADDR'];

if (isset($_POST['fid']) && isset($_SESSION['token'])) {
    $f_user = htmlspecialchars($_SESSION['username']);
    $f_pasteid = Trim(htmlspecialchars($_POST['fid']));
    $f_pasteid = preg_replace('/[^0-9]/', '', $f_pasteid);
    $f_pasteid = (int)filter_var($f_pasteid, FILTER_SANITIZE_NUMBER_INT);
    $f_time = gmmktime(date("H"), date("i"), date("s"), date("n"), date("j"), date("Y"));


    $query = $conn->prepare('SELECT 1 FROM pins WHERE f_paste = ? AND m_fav = ?');
    $query->execute([$f_pasteid, $f_user]);

    if ($query->fetch()) { /* Already favorited */
        $query = $conn->prepare('DELETE FROM pins WHERE f_paste = ? AND m_fav = ?');
    } else {
        $query = $conn->prepare('INSERT INTO pins (m_fav, f_paste, f_time) VALUES (?, ?, NOW())');
    }

    $query->execute([$f_pasteid, $f_user]);

    $error = 'Paste has been favorited.';
}

// Theme
require_once('theme/' . $default_theme . '/header.php');
require_once('theme/' . $default_theme . '/report.php');
