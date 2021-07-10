<?php
if (!defined('IN_PONEPASTE')) {
    die();
}

require_once('config.php');
require_once('includes/functions.php');


function getSiteInfo($conn) {
    return $conn->query('SELECT * FROM site_info LIMIT 1')->fetch();
}

function getSiteLangAndTheme($conn) {
    return $conn->query('SELECT lang, theme FROM interface LIMIT 1')->fetch();
}

$conn = new PDO(
    "mysql:host=$db_host;dbname=$db_schema;charset=utf8",
    $db_user,
    $db_pass,
    $db_opts
);

// Setup site info
$row = getSiteInfo($conn);
$title				= Trim($row['title']);
$des				= Trim($row['des']);
$baseurl    		= Trim($row['baseurl']);
$keyword			= Trim($row['keyword']);
$site_name			= Trim($row['site_name']);
$email				= Trim($row['email']);
$twit				= Trim($row['twit']);
$face				= Trim($row['face']);
$gplus				= Trim($row['gplus']);
$ga					= Trim($row['ga']);
$additional_scripts	= Trim($row['additional_scripts']);


// Setup theme and language

$lang_and_theme = getSiteLangAndTheme($conn);

if ($lang_and_theme) {
    $default_lang = $lang_and_theme['lang'];
    $default_theme = $lang_and_theme['theme'];
} else {
    $default_lang = 'en.php';
    $default_theme = 'bulma';
}

// Prevent a potential LFI (you never know :p)
if (in_array($default_lang, scandir('langs/'))) {
    require_once("langs/$default_lang");
}

// Check if IP is banned
$ip      = $_SERVER['REMOTE_ADDR'];
if ( is_banned($conn, $ip) ) die($lang['banned']); // "You have been banned from ".$site_name;

// Logout
if (isset($_GET['logout'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    unset($_SESSION['token']);
    unset($_SESSION['oauth_uid']);
    unset($_SESSION['username']);
    session_destroy();
}
