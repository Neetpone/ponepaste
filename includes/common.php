<?php
if (!defined('IN_PONEPASTE')) {
    die('This file may not be accessed directly.');
}
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/functions.php');
require_once(__DIR__ . '/passwords.php');
require_once(__DIR__ . '/captcha.php');

use Illuminate\Database\Capsule\Manager as Capsule;
use PonePaste\Helpers\SessionHelper;
use PonePaste\Models\IPBan;
use PonePaste\Models\PageView;
use PonePaste\Models\Paste;
use PonePaste\Models\User;
use PonePaste\Helpers\AbilityHelper;

/* View functions */
function javascriptIncludeTag(string $name) : string {
    if (PP_DEBUG) {
        return "<script src=\"/assets/bundle/{$name}.js\"></script>";
    }

    return "<script src=\"/assets/bundle/{$name}.min.js\"></script>";
}

function urlForPage($page = '') : string {
    if (!PP_MOD_REWRITE) {
        $page .= '.php';
    }

    return (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/' . $page;
}

function urlForPaste(int | Paste $paste) : string {
    if (!is_int($paste)) {
        $paste = $paste->id;
    }

    if (PP_MOD_REWRITE) {
        return "/{$paste}";
    }

    return "/paste.php?id={$paste}";
}

function urlForReport(Paste $paste) : string {
    if (PP_MOD_REWRITE) {
        return "/{$paste->id}/report";
    }

    return "/report.php?id={$paste->id}";
}

function urlForMember(int | User $user) : string {
    if (is_int($user)) {
        $user = User::find($user);
    }

    if (PP_MOD_REWRITE) {
        return '/user/' . urlencode($user->username);
    }

    return '/user.php?name=' . urlencode($user->username);
}

/**
 * @throws Exception if the names and values aren't the same length
 */
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

/**
 * @throws Exception if the flash level is invalid
 */
function flash(string $level, string $message) : void {
    if (!isset($_SESSION['flashes'])) {
        $_SESSION['flashes'] = [
            'success' => [],
            'warning' => [],
            'error' => []
        ];
    }

    if (!array_key_exists($level, $_SESSION['flashes'])) {
        throw new Exception('Invalid flash level ' . $level);
    }

    $_SESSION['flashes'][$level][] = $message;
}


function flashError(string $message) : void {
    flash('error', $message);
}

function flashWarning(string $message) : void {
    flash('warning', $message);
}

function flashSuccess(string $message) : void {
    flash('success', $message);
}

function getFlashes() {
    if (!isset($_SESSION['flashes'])) {
        return ['success' => [], 'warning' => [], 'error' => []];
    }

    $flashes = $_SESSION['flashes'];

    unset($_SESSION['flashes']);

    return $flashes;
}

function outputFlashes($flashes) : void {
    function __outputFlash($level, $flash) : void {
        echo '<div class="notification is-' . $level . ' flash">
                <i class="fa fa-exclamation-circle" aria-hidden="true"></i>'
            . pp_html_escape($flash) .
            '</div>';
    }

    foreach ($flashes['success'] as $flash) {
        __outputFlash('info', $flash);
    }

    foreach ($flashes['warning'] as $flash) {
        __outputFlash('warning', $flash);
    }

    foreach ($flashes['error'] as $flash) {
        __outputFlash('danger', $flash);
    }
}

/* Database functions */
function getSiteInfo() : array {
    return require(__DIR__ . '/../config/site.php');
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

/* I think there is one row for each day, and in that row, tpage = non-unique, tvisit = unique page views for that day */
function updatePageViews() : void {
    global $redis;

    $ip = $_SERVER['REMOTE_ADDR'];
    $date = date('jS F Y');

    $last_page_view = PageView::orderBy('id', 'desc')->limit(1)->first();

    if ($last_page_view && $last_page_view->date == $date) {
        if (!$redis->sIsMember('page_view_ips', $ip)) {
            $last_page_view->tvisit++;
            $redis->sAdd('page_view_ips', $ip);
        }

        $last_page_view->tpage++;
        $last_page_view->save();
    } else {
        $redis->del('page_view_ips');

        // New date is created
        $new_page_view = new PageView(['date' => $date]);
        $new_page_view->save();

        $redis->sAdd('page_view_ips', $ip);
    }
}

function setupCsrfToken() : string {
    if (isset($_SESSION[SessionHelper::CSRF_TOKEN_KEY])) {
        return $_SESSION[SessionHelper::CSRF_TOKEN_KEY];
    }

    $token = pp_random_token();
    $_SESSION[SessionHelper::CSRF_TOKEN_KEY] = $token;

    return $token;
}

function verifyCsrfToken($token = null) : bool {
    if ($token === null) {
        $token = $_POST[SessionHelper::CSRF_TOKEN_KEY];
    }

    if (empty($token) || empty($_SESSION[SessionHelper::CSRF_TOKEN_KEY])) {
        return false;
    }

    return hash_equals($_SESSION[SessionHelper::CSRF_TOKEN_KEY], $token);
}

session_set_cookie_params(86400);
session_start();

/* Set up the database and Eloquent ORM */
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

// Check if IP is banned
$ip = $_SERVER['REMOTE_ADDR'];
if (IPBan::where('ip', $ip)->first()) {
    die('You have been banned.');
}

/* Set up Redis */
$redis = new Redis();
$redis->pconnect(PP_REDIS_HOST);

// Setup site info
$site_info = getSiteInfo();
$global_site_info = $site_info['site_info'];
$row = $site_info['site_info'];
$site_name = trim($row['site_name']);
$email = trim($row['email']);

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
$captcha_enabled = (bool) $site_info['captcha']['enabled'];

$total_pastes = Paste::count();
$total_page_views = PageView::select('tpage')->orderBy('id', 'desc')->first()->tpage;
$total_unique_views = PageView::select('tvisit')->orderBy('id', 'desc')->first()->tvisit;

$current_user = SessionHelper::currentUser();
$start = microtime(true);

function can(string $action, mixed $subject) : bool {
    global $current_user;
    static $current_ability = null;

    if ($current_ability === null) {
        $current_ability = new AbilityHelper($current_user);
    }

    return $current_ability->can($action, $subject);
}

$script_bundles = [];

/* Security headers */
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header("Content-Security-Policy: default-src 'self' data: 'unsafe-inline'; img-src 'self' data: " . implode(' ', $site_info['allowed_image_hosts']));
