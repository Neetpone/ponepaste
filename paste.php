<?php
define('IN_PONEPASTE', 1);
require_once('includes/common.php');
require_once('includes/functions.php');
require_once('includes/Tag.class.php');
require_once('includes/passwords.php');

use Highlight\Highlighter;

function rawView($content, $p_code) {
    if ($p_code) {
        header('Content-Type: text/plain');
        echo $content;
    } else {
        header('HTTP/1.1 404 Not Found');
    }
}

$paste_id = intval(trim($_REQUEST['id']));

updatePageViews($conn);

// This is used in the theme files.
$totalpastes = getSiteTotalPastes($conn);

// Get paste favorite count
$fav_count = $conn->querySelectOne('SELECT COUNT(*) FROM pins WHERE paste_id = ?', [$paste_id], PDO::FETCH_NUM)[0];

// Get paste info
$row = $conn->querySelectOne(
    'SELECT title, content, visible, code, expiry, pastes.password AS password, created_at, updated_at, encrypt, views, users.username AS member, users.id AS user_id
        FROM pastes
        INNER JOIN users ON users.id = pastes.user_id
        WHERE pastes.id = ?', [$paste_id]);


$notfound = null;
$is_private = false;

if ($row === null) {
    header('HTTP/1.1 404 Not Found');
    $notfound = 'Not found';
    goto Not_Valid_Paste;
}

$paste_owner_id = (int) $row['user_id'];
$paste_title = $row['title'];
$paste_code = $row['code'];
$using_highlighter = $paste_code !== 'pastedown';

$paste = [
    'title' => $paste_title,
    'created_at' => (new DateTime($row['created_at']))->format('jS F Y h:i:s A'),
    'updated_at' => (new DateTime($row['updated_at']))->format('jS F Y h:i:s A'),
    'user_id' => $paste_owner_id,
    'member' => $row['member'],
    'views' => $row['views'],
    'code' => $paste_code,
    'tags' => getPasteTags($conn, $paste_id)
];

$p_member = $row['member'];
$p_content = $row['content'];
$p_visible = $row['visible'];
$p_expiry = $row['expiry'];
$p_password = $row['password'];
$p_encrypt = (bool) $row['encrypt'];


$is_private = $row['visible'] === '2';

if ($is_private && (!$current_user || $current_user->user_id !== $paste_owner_id)) {
    $notfound = 'This is a private paste. If you created this paste, please log in to view it.';
    goto Not_Valid_Paste;
}

/* Verify paste password */
$password_required = $p_password !== null && $p_password !== 'NONE';
$password_valid = true;
$password_candidate = '';

if ($password_required) {
    if (!empty($_POST['mypass'])) {
        $password_candidate = $_POST['mypass'];
    } elseif (!empty($_GET['password'])) {
        $password_candidate = @base64_decode($_GET['password']);
    }

    if (empty($password_candidate)) {
        $password_valid = false;
        $error = 'This paste is password protected.';
        goto Not_Valid_Paste;
    } elseif (!pp_password_verify($password_candidate, $p_password)) {
        $password_valid = false;
        $error = 'The provided password is incorrect.';
        goto Not_Valid_Paste;
    }
}

if (!empty($p_expiry) && $p_expiry !== 'SELF') {
    $input_time = $p_expiry;
    $current_time = mktime(date("H"), date("i"), date("s"), date("n"), date("j"), date("Y"));
    if ($input_time < $current_time) {
        $notfound = 'This paste has expired.';
        goto Not_Valid_Paste;
    }
}

if ($p_encrypt == 1) {
    $p_content = openssl_decrypt($p_content, PP_ENCRYPTION_ALGO, PP_ENCRYPTION_KEY);
}

$op_content = trim(htmlspecialchars_decode($p_content));

// Download the paste
if (isset($_GET['download'])) {
    doDownload($paste_id, $paste_title, $p_member, $op_content, $paste_code);
    exit();
}

// Raw view
if (isset($_GET['raw'])) {
    rawView($op_content, $paste_code);
    exit();
}

// Preprocess
$highlight = array();
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
if ($paste_code === "pastedown") {
    $parsedown = new Parsedown();
    $parsedown->setSafeMode(true);
    $p_content = $parsedown->text($p_content);
} else {
    Highlighter::registerLanguage('green', 'config/green.lang.json');
    $hl = new Highlighter();
    $highlighted = $hl->highlight($paste_code == 'text' ? 'plaintext' : $paste_code, $p_content)->value;
    $lines = HighlightUtilities\splitCodeIntoArray($highlighted);
}

// Embed view after highlighting is applied so that $p_code is syntax highlighted as it should be.
if (isset($_GET['embed'])) {
    embedView($paste_id, $paste_title, $p_content, $paste_code, $title, $baseurl, $lang);
    exit();
}

if ($password_required && $password_valid) {
    /* base64 here means that the password is exposed in the URL, technically - how to handle this better? */
    $p_download = "paste.php?download&id=$paste_id&password=" . base64_encode($password_candidate);
    $p_raw = "paste.php?raw&id=$paste_id&password=" . base64_encode($password_candidate);
    $p_embed = "paste.php?embed&id=$paste_id&password=" . base64_encode($password_candidate);
} else {
    // Set download URL
    if (PP_MOD_REWRITE) {
        $p_download = "download/$paste_id";
        $p_raw = "raw/$paste_id";
        $p_embed = "embed/$paste_id";
    } else {
        $p_download = "paste.php?download&id=$paste_id";
        $p_raw = "paste.php?raw&id=$paste_id";
        $p_embed = "paste.php?embed&id=$paste_id";
    }
}

// View counter
if (@$_SESSION['not_unique'] !== $paste_id) {
    $_SESSION['not_unique'] = $paste_id;
    $conn->query("UPDATE pastes SET views = (views + 1) where id = ?", [$paste_id]);
}

$page_template = 'view';

Not_Valid_Paste:

if ($is_private || $notfound || !$password_valid) {
    // FIXME
    // Display errors
    $page_template = 'errors';
}

require_once('theme/' . $default_theme . '/common.php');

