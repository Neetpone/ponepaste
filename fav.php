<?php
define('IN_PONEPASTE', 1);
require_once('includes/common.php');
require_once('includes/functions.php');

if ($current_user && !empty($_POST['fid'])) {
    $paste_id = intval($_POST['fid']);
    $query = $conn->prepare('SELECT 1 FROM pins WHERE paste_id = ? AND user_id = ?');
    $query->execute([$paste_id, $current_user->user_id]);

    if ($query->fetch()) { /* Already favorited */
        $query = $conn->prepare('DELETE FROM pins WHERE paste_id = ? AND user_id = ?');
    } else {
        $query = $conn->prepare('INSERT INTO pins (paste_id, user_id, f_time) VALUES (?, ?, NOW())');
    }

    $query->execute([$paste_id, $current_user->user_id]);
    $error = 'Paste has been favorited.';
}

// Theme
$page_template = 'report';
require_once('theme/' . $default_theme . '/common.php');
