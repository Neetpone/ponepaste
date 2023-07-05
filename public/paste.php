<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/../includes/common.php');

use Highlight\Highlighter;
use PonePaste\Models\Paste;
use PonePaste\Models\User;
use PonePaste\Pastedown;

function isRequesterLikelyBot() : bool {
    return str_contains(strtolower($_SERVER['HTTP_USER_AGENT']), 'bot');
}

function rawView($content, $p_code) : void {
    if ($p_code) {
        header('Content-Type: text/plain');
        echo $content;
    } else {
        header('HTTP/1.1 404 Not Found');
    }
}

function getUserRecommended(User $user) {
    return Paste::where('visible', Paste::VISIBILITY_PUBLIC)
                ->where('user_id', $user->id)
                ->orderBy('id')->limit(5)
                ->get();
    /*$query = $conn->prepare(
        "SELECT pastes.id AS id, users.username AS member, title, visible
            FROM pastes
            INNER JOIN users ON pastes.user_id = users.id
            WHERE pastes.visible = '0' AND users.id = ?
            ORDER BY id DESC
            LIMIT 0, 5");
    $query->execute([$user_id]);
    return $query->fetchAll();*/
}

updatePageViews();

// This is used in the theme files.
$totalpastes = Paste::count();

$paste = Paste::with('user')->find((int) trim($_REQUEST['id']));
$is_private = false;
$error = null;

if (!$paste) {
    header('HTTP/1.1 404 Not Found');
    $error = 'Not found';
    goto Not_Valid_Paste;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken()) {
        $error = 'Invalid CSRF token (do you have cookies enabled?)';
        goto Not_Valid_Paste;
    }
}

/* $password_ok_pastes is an array of IDs of pastes for which a correct password has already been entered this session. */
if (isset($_SESSION['password_ok'])) {
    $password_ok_pastes = json_decode($_SESSION['password_ok']);
} else {
    $password_ok_pastes = [];
}

$paste_owner_id = $paste->user->id;
$paste_title = $paste->title;
$paste_code = $paste->code;
$using_highlighter = $paste_code !== 'pastedown';
$fav_count = $paste->favouriters()->count();

$p_content = $paste->content;
$p_password = $paste->password;
$paste_is_favourited = $current_user !== null && $current_user->favourites->where('id', $paste->id)->count() === 1;

$is_private = $paste->visible === Paste::VISIBILITY_PRIVATE;

if (!can('view', $paste)) {
    if ($paste->is_hidden) {
        $error = 'This paste has been removed by the moderation team.';
    } else {
        $error = 'This is a private paste. If you created this paste, please log in to view it.';
    }

    goto Not_Valid_Paste;
}

/* Paste deletion */
if (isset($_POST['delete'])) {
    if (!can('delete', $paste)) {
        $error = 'You cannot delete someone else\'s paste!';
        goto Not_Valid_Paste;
    }
//
//    $paste->delete();
//    flashSuccess('Paste deleted.');
    flashError('Paste deletion is currently disabled.');
    header('Location: ' . urlForMember($current_user));
    die();
}

if (isset($_POST['hide'])) {
    if (!can('hide', $paste)) {
        $error = 'You do not have permission to hide this paste.';
        goto Not_Valid_Paste;
    }

    $is_hidden = !$paste->is_hidden;

    if ($is_hidden) {
        $paste->reports()->update(['open' => false]);
    }

    $paste->is_hidden = $is_hidden;
    $paste->save();
    $redis->del('ajax_pastes'); /* Expire from Redis so it doesn't show up anymore */
    flashSuccess('Paste ' . ($is_hidden ? 'hidden' : 'unhidden') . '.');
    header('Location: ' . urlForPaste($paste));
    die();
}

if (isset($_POST['blank'])) {
    if (!can('blank', $paste)) {
        $error = 'You do not have permission to blank this paste.';
        goto Not_Valid_Paste;
    }

    $paste->content = '';
    $paste->title = 'Removed by moderator';
    $paste->tags()->detach();

    $paste->save();
    $redis->del('ajax_pastes'); /* Expire from Redis so it doesn't show up anymore */
    flashSuccess('Paste contents blanked.');
    header('Location: ' . urlForPaste($paste));
    die();
}

/* Verify paste password */
$password_required = $p_password !== null && $p_password !== 'NONE';
$password_valid = true;

if ($password_required && !in_array($paste->id, $password_ok_pastes)) {
    if (empty($_POST['mypass'])) {
        $password_valid = false;
        $error = 'This paste is password protected.';
        goto Not_Valid_Paste;
    } elseif (!pp_password_verify($_POST['mypass'], $p_password)) {
        $password_valid = false;
        $error = 'The provided password is incorrect.';
        goto Not_Valid_Paste;
    }

    $password_ok_pastes[] = $paste->id;
    $_SESSION['password_ok'] = json_encode($password_ok_pastes);
}

if (PP_MOD_REWRITE) {
    $p_download = "download/$paste->id";
    $p_raw = "raw/$paste->id";
    $p_embed = "embed/$paste->id";
} else {
    $p_download = "paste.php?download&id=$paste->id";
    $p_raw = "paste.php?raw&id=$paste->id";
    $p_embed = "paste.php?embed&id=$paste->id";
}

/* Expiry */
if (!empty($paste->expiry) && $paste->expiry !== 'NULL') {
    if ($paste->expiry === 'SELF') {
        $paste->delete();
        flashWarning('This paste has self-destructed - if you close this window, you will no longer be able to view it!');
    } else if (time() > (int) $paste->expiry) {
        $paste->delete();
        $error = 'This paste has expired.';
        goto Not_Valid_Paste;
    }
}

/* handle favouriting */
if (isset($_POST['fave']) && $current_user) {
    if ($paste_is_favourited) {
        $current_user->favourites()->detach($paste);
    } else {
        $current_user->favourites()->attach($paste);
    }

    $paste_is_favourited = !$paste_is_favourited;
}

if ($paste->encrypt) {
    $p_content = openssl_decrypt($p_content, PP_ENCRYPTION_ALGO, PP_ENCRYPTION_KEY);
}

$op_content = trim(htmlspecialchars_decode($p_content));

// Download the paste
if (isset($_GET['download'])) {
    $filename = pp_filename_escape($paste->title . '_' . $paste->user->username, '.txt');
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo $op_content;
    exit();
}

// Raw view
if (isset($_GET['raw'])) {
    rawView($op_content, $paste_code);
    exit();
}

// Preprocess
$highlight = [];
$prefix_size = strlen('!highlight!');
$lines = explode("\n", $p_content);
$p_content = "";

foreach ($lines as $idx => $line) {
    if (substr($line, 0, $prefix_size) == '!highlight!') {
        $highlight[] = $idx + 1;
        $line = substr($line, $prefix_size);
    }
    $p_content .= $line . "\n";
}

$p_content = rtrim($p_content);

// Apply syntax highlight
$p_content = htmlspecialchars_decode($p_content);

if ($paste_code === "pastedown" || $paste_code === 'pastedown_old') {
    $parsedown = new Pastedown();
    $parsedown->setSafeMode(true);
    $p_content = $parsedown->text($p_content);
} else {
    Highlighter::registerLanguage('green', __DIR__ . '/../config/green.lang.json');
    Highlighter::registerLanguage('plaintext', __DIR__ . '/../vendor/scrivo/highlight.php/Highlight/languages/plaintext.json');
    $hl = new Highlighter(false);
    $highlighted = $hl->highlight($paste_code == 'text' ? 'plaintext' : $paste_code, $p_content)->value;
    $lines = HighlightUtilities\splitCodeIntoArray($highlighted);
}

// Embed view after highlighting is applied so that $p_code is syntax highlighted as it should be.
if (isset($_GET['embed'])) {
    embedView($paste->id, $paste->title, $p_content, $site_name);
    exit();
}

// View counter
if (!isRequesterLikelyBot() && @$_SESSION['not_unique'] !== $paste->id) {
    $_SESSION['not_unique'] = $paste->id;
    $paste->views += 1;
    $paste->save();
}

$page_title = $paste->title;
$page_template = 'view';
$recommended_pastes = getUserRecommended($paste->user);

/* We arrive at this GOTO from various errors */
Not_Valid_Paste:

if ($error) {
    $page_title = 'Error';
    $page_template = 'errors';
}

$csrf_token = setupCsrfToken();
require_once(__DIR__ . '/../theme/' . $default_theme . '/common.php');
