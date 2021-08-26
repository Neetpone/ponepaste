<?php
define('IN_PONEPASTE', 1);
require_once('includes/common.php');

$date = date('jS F Y');

// Temp count for untagged pastes
$total_untagged = intval($conn->query("SELECT COUNT(*) from pastes WHERE tagsys IS NULL")->fetch(PDO::FETCH_NUM)[0]);

updatePageViews($conn);

// Theme
$page_template = 'archive';
$page_title = 'Pastes Archive';
require_once('theme/' . $default_theme . '/common.php');
