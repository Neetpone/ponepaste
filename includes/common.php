<?php
if (!defined('IN_PONEPASTE')) {
    die('This file may not be accessed directly.');
}
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/functions.php');
require_once(__DIR__ . '/DatabaseHandle.class.php');

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use PonePaste\Helpers\SessionHelper;
use PonePaste\Models\Paste;
use PonePaste\Models\User;

/* View functions */
function urlForPage($page = '') : string {
    if (!PP_MOD_REWRITE) {
        $page .= '.php';
    }

    return (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/' . $page;
}

function urlForPaste(Paste $paste) : string {
    if (PP_MOD_REWRITE) {
        return "/{$paste->id}";
    }

    return "/paste.php?id={$paste->id}";
}

function urlForMember(User $user) : string {
    if (PP_MOD_REWRITE) {
        return '/user/' . urlencode($user->username);
    }

    return '/user.php?name=' . urlencode($user->username);
}

function optionsForSelect(array $displays, array $values, string $currentSelection = null) : string {
    $size = count($displays);

    if (count($values) !== $size) {
        throw new Exception('Option names and option values must be the same count');
    }

    $html = '';

    for ($i = 0; $i < $size; $i++) {
        $html .= '<option value="' . pp_html_escape($values[$i]) . '"';

        if ($currentSelection === $values[$i]) {
            $html .= ' selected="selected"';
        }

        $html .= '>' . pp_html_escape($displays[$i]) . '</option>';
    }

    return $html;
}

function flash(string $level, string $message) {
    if (!isset($_SESSION['flashes'])) {
        $_SESSION['flashes'] = [
            'success' => [],
            'warning' => []
        ];
    }

    if (!array_key_exists($level, $_SESSION['flashes'])) {
        throw new Exception('Invalid flash level ' . $level);
    }

    array_push($_SESSION['flashes'][$level], $message);
}


function flashError(string $message) {
    flash('error', $message);
}

function flashSuccess(string $message) {
    flash('success', $message);
}

function getFlashes() {
    if (!isset($_SESSION['flashes'])) {
        return ['success' => [], 'error' => []];
    }

    $flashes = $_SESSION['flashes'];

    unset($_SESSION['flashes']);

    return $flashes;
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
$capsule = new Capsule();

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $db_host,
    'database' => $db_schema,
    'username' => $db_user,
    'password' => $db_pass ,
    'charset' => 'utf8mb4',
    'prefix' => ''
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();


// Setup site info
$site_info = getSiteInfo();
$global_site_info = $site_info['site_info'];
$row = $site_info['site_info'];
$title = Trim($row['title']);
$baseurl = Trim($row['baseurl']);
$site_name = Trim($row['site_name']);
$email = Trim($row['email']);
$additional_scripts = Trim($row['additional_scripts']);

// Setup theme
$default_theme = 'bulma';

// Site permissions
$site_permissions = $site_info['permissions'];

$site_is_private = false;
$site_disable_guests = false;

if ($site_permissions) {
    $site_is_private = (bool) $site_permissions['private'];
    $site_disable_guests = (bool) $site_permissions['disable_guest'];
}

// CAPTCHA configuration
$captcha_config = $site_info['captcha'];
$captcha_enabled = (bool) $captcha_config['enabled'];

// Check if IP is banned
$ip = $_SERVER['REMOTE_ADDR'];
if ($conn->query('SELECT 1 FROM ban_user WHERE ip = ?', [$ip])->fetch()) {
    die('You have been banned.');
}

$site_ads = getSiteAds($conn);
$total_pastes = getSiteTotalPastes($conn);
$total_page_views = getSiteTotalviews($conn);
$total_unique_views = getSiteTotal_unique_views($conn);

$current_user = SessionHelper::currentUser();

/* Security headers */
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');

//ob_start();
