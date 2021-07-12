<?php
define('IN_PONEPASTE', 1);
require_once('includes/common.php');
require_once('includes/functions.php');

// UTF-8
header('Content-Type: text/html; charset=utf-8');

$p_title = $lang['archive']; // "Pastes Archive";

// Theme
require_once('theme/' . $default_theme . '/header.php');
require_once('theme/' . $default_theme . '/rules.php');
require_once('theme/' . $default_theme . '/footer.php');
