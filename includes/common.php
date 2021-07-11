<?php
if (!defined('IN_PONEPASTE')) {
    die('This file may not be accessed directly.');
}

require_once('config.php');
require_once('includes/functions.php');


function getSiteInfo(PDO $conn) : array {
    return $conn->query('SELECT * FROM site_info LIMIT 1')->fetch();
}

function getSiteLangAndTheme(PDO $conn) : array {
    return $conn->query('SELECT lang, theme FROM interface LIMIT 1')->fetch();
}

function getSitePermissions(PDO $conn) : array {
    return $conn->query('SELECT * FROM site_permissions LIMIT 1')->fetch();
}

function getSiteAds(PDO $conn) : array | bool {
    return $conn->query('SELECT text_ads, ads_1, ads_2 FROM ads LIMIT 1')->fetch();
}

function getSiteTotalPastes(PDO $conn) : int {
    return intval($conn->query('SELECT COUNT(*) FROM pastes')->fetch(PDO::FETCH_NUM)[0]);
}

function updatePageViews(PDO $conn) : void {
    $ip = $_SERVER['REMOTE_ADDR'];
    $date    = date('jS F Y');
    $data_ip = file_get_contents('tmp/temp.tdata');

    $last_page_view = $conn->query('SELECT * FROM page_view ORDER BY id DESC LIMIT 1')->fetch();
    $last_date = $last_page_view['date'];

    if ($last_date == $date) {
        if (str_contains($data_ip, $ip)) {
            $last_tpage = intval($last_page_view['tpage']) + 1;

            // IP already exists, Update view count
            $statement = $conn->prepare("UPDATE page_view SET tpage = ? WHERE id = ?");
            $statement->execute([$last_tpage, $last_page_view['id']]);
        } else {
            $last_tpage  = intval($last_page_view['tpage']) + 1;
            $last_tvisit = intval($last_page_view['tvisit']) + 1;

            // Update both tpage and tvisit.
            $statement = $conn->prepare("UPDATE page_view SET tpage = ?,tvisit = ? WHERE id = ?");
            $statement->execute([$last_tpage, $last_tvisit, $last_page_view['id']]);
            file_put_contents('tmp/temp.tdata', $data_ip . "\r\n" . $ip);
        }
    } else {
        // Delete the file and clear data_ip
        unlink("tmp/temp.tdata");

        // New date is created
        $statement = $conn->prepare("INSERT INTO page_view (date, tpage, tvisit) VALUES (?, '1', '1')");
        $statement->execute([$date]);

        // Update the IP
        file_put_contents('tmp/temp.tdata', $ip);
    }
}

session_start();

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

// site permissions
$site_permissions = getSitePermissions($conn);

if ($site_permissions) {
    $siteprivate = $site_permissions['siteprivate'];
} else {
    $siteprivate = 'off';
}

$privatesite = $siteprivate;


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
    unset($_SESSION['pic']);
    session_destroy();
}

$site_ads = getSiteAds($conn);
