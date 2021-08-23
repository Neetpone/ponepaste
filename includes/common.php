<?php
if (!defined('IN_PONEPASTE')) {
    die('This file may not be accessed directly.');
}
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/functions.php');
require_once(__DIR__ . '/DatabaseHandle.class.php');
require_once(__DIR__ . '/User.class.php');
require_once(__DIR__ . '/ViewBag.class.php');

/* View functions */
function urlForPaste($paste_id) : string {
    if (PP_MOD_REWRITE) {
        return "/${paste_id}";
    }

    return "/paste.php?id=${paste_id}";
}

function urlForMember(string $member_name) : string {
    if (PP_MOD_REWRITE) {
        return '/user/' . urlencode($member_name);
    }

    return '/user.php?name=' . urlencode($member_name);
}

/* Database functions */
function getSiteInfo() : array {
    return require(__DIR__ . '/../config/site.php');
}

function getSiteAds(DatabaseHandle $conn) : array|bool {
    return $conn->query('SELECT text_ads, ads_1, ads_2 FROM ads LIMIT 1')->fetch();
}

function getSiteTotalPastes(DatabaseHandle $conn) : int {
    return intval($conn->query('SELECT COUNT(*) FROM pastes')->fetch(PDO::FETCH_NUM)[0]);
}

function getSiteTotalviews(DatabaseHandle $conn) : int {
    return intval($conn->query('SELECT tpage FROM page_view ORDER BY id DESC LIMIT 1')->fetch(PDO::FETCH_NUM)[0]);
}

function getSiteTotal_unique_views(DatabaseHandle $conn) : int {
    return intval($conn->query('SELECT tvisit FROM page_view ORDER BY id DESC LIMIT 1')->fetch(PDO::FETCH_NUM)[0]);
}

/**
 * Specialization of `htmlentities()` that avoids double escaping and uses UTF-8.
 *
 * @param string $unescaped String to escape
 * @return string HTML-escaped string
 */
function pp_html_escape(string $unescaped) : string {
    return htmlspecialchars($unescaped, ENT_QUOTES, 'UTF-8', false);
}

function updatePageViews(DatabaseHandle $conn) : void {
    $ip = $_SERVER['REMOTE_ADDR'];
    $date = date('jS F Y');
    $data_ip = file_get_contents('tmp/temp.tdata');

    $last_page_view = $conn->query('SELECT * FROM page_view ORDER BY id DESC LIMIT 1')->fetch();
    $last_date = $last_page_view['date'];

    if ($last_date == $date) {
        $last_tpage = intval($last_page_view['tpage']) + 1;

        if (str_contains($data_ip, $ip)) {
            // IP already exists, Update view count
            $statement = $conn->prepare("UPDATE page_view SET tpage = ? WHERE id = ?");
            $statement->execute([$last_tpage, $last_page_view['id']]);
        } else {
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

$conn = new DatabaseHandle("mysql:host=$db_host;dbname=$db_schema;charset=utf8mb4", $db_user, $db_pass);

// Setup site info
$site_info = getSiteInfo();
$global_site_info = $site_info['site_info'];
$row = $site_info['site_info'];
$title = Trim($row['title']);
$baseurl = Trim($row['baseurl']);
$site_name = Trim($row['site_name']);
$email = Trim($row['email']);
$ga = Trim($row['google_analytics']);
$additional_scripts = Trim($row['additional_scripts']);


// Setup theme and language
$lang_and_theme = $site_info['interface'];
$default_lang = $lang_and_theme['language'];
$default_theme = $lang_and_theme['theme'];

// Site permissions
$site_permissions = $site_info['permissions'];

if ($site_permissions) {
    $site_is_private = (bool) $site_permissions['private'];
    $site_disable_guest = (bool) $site_permissions['disable_guest'];
} else {
    $site_is_private = false;
    $site_disable_guest = false;
}

// CAPTCHA configuration
$captcha_config = $site_info['captcha'];
$captcha_enabled = (bool) $captcha_config['enabled'];

// Prevent a potential LFI (you never know :p)
$lang_file = "${default_lang}.php";
if (in_array($lang_file, scandir(__DIR__ . '/langs/'))) {
    require_once(__DIR__ . "/langs/${lang_file}");
}

// Check if IP is banned
$ip = $_SERVER['REMOTE_ADDR'];
if ($conn->query('SELECT 1 FROM ban_user WHERE ip = ?', [$ip])->fetch()) {
    die($lang['banned']); // "You have been banned from " . $site_name;
}

$site_ads = getSiteAds($conn);
$total_pastes = getSiteTotalPastes($conn);
$total_page_views = getSiteTotalviews($conn);
$total_unique_views = getSiteTotal_unique_views($conn);

$current_user = User::current($conn);

/* Security headers */
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');

//ob_start();
