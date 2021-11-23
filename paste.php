<?php
define('IN_PONEPASTE', 1);
require_once('includes/common.php');
require_once('includes/functions.php');
require_once('includes/passwords.php');

use Highlight\Highlighter;
use PonePaste\Models\Paste;
use PonePaste\Models\User;

function isRequesterLikelyBot() : bool {
    return str_contains(strtolower($_SERVER['HTTP_USER_AGENT']), 'bot');
}

function rawView($content, $p_code) {
    if ($p_code) {
        header('Content-Type: text/plain');
        echo $content;
    } else {
        header('HTTP/1.1 404 Not Found');
    }
}

function getUserRecommended(User $user) {
    return Paste::where('visible', '0')
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

$paste_id = intval(trim($_REQUEST['id']));

updatePageViews($conn);

// This is used in the theme files.
$totalpastes = Paste::count();

$paste = Paste::find($paste_id);

$notfound = null;
$is_private = false;

if (!$paste) {
    header('HTTP/1.1 404 Not Found');
    $notfound = 'Not found';
    goto Not_Valid_Paste;
}

$paste_owner_id = $paste->user->id;
$paste_title = $paste->title;
$paste_code = $paste->code;
$using_highlighter = $paste_code !== 'pastedown';
$fav_count = $paste->favouriters()->count();


/*$paste = [
    'title' => $paste_title,
    'created_at' => $paste->created_at->format('jS F Y h:i:s A'),
    'updated_at' => $paste->created_at->format('jS F Y h:i:s A'),
    'user_id' => $paste_owner_id,
    'views' => $row['views'],
    'code' => $paste_code,
    'tags' => getPasteTags($conn, $paste_id)
];*/

//$p_member = $row['member'];
$p_content = $paste->content;
$p_visible = $paste->visible;
$p_expiry = $paste->expiry;
$p_password = $paste->password;
$p_encrypt = (bool) $paste->encrypt;
$paste_is_favourited = $current_user !== null && $current_user->favourites->where('paste_id', $paste->id)->count() === 1;


$is_private = $p_visible === '2';

if ($is_private && (!$current_user || $current_user->id !== $paste_owner_id)) {
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

/* handle favouriting */
if (isset($_POST['fave'])) {
    if ($paste_is_favourited) {
        $current_user->favourites()->detach($paste->id);
    } else {
        $current_user->favourites()->attach($paste->id);
    }
}

if ($p_encrypt == 1) {
    $p_content = openssl_decrypt($p_content, PP_ENCRYPTION_ALGO, PP_ENCRYPTION_KEY);
}

$op_content = trim(htmlspecialchars_decode($p_content));

// Download the paste
if (isset($_GET['download'])) {
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="' . $paste->id . '_' . pp_html_escape($paste->title) . '_' . pp_html_escape($paste->user->username) . '.txt"');
    echo $op_content;
    exit();
}

// Raw view
if (isset($_GET['raw'])) {
    rawView($op_content, $paste_code);
    exit();
}

// Deletion
if (isset($_POST['delete'])) {
    if (!$current_user || ($paste_owner_id !== $current_user->id)) {
        flashError('You must be logged in and own this paste to delete it.');
    } else {
        $paste->delete();
        flashSuccess('Paste deleted.');
        header('Location: ' . urlForMember($current_user->username));
        die();
    }
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
if (!isRequesterLikelyBot() && @$_SESSION['not_unique'] !== $paste_id) {
    $_SESSION['not_unique'] = $paste_id;
    $paste->views += 1;
    $paste->save();
}

$page_template = 'view';
$recommended_pastes = getUserRecommended($paste->user);

Not_Valid_Paste:

if ($is_private || $notfound || !$password_valid) {
    // FIXME
    // Display errors
    $page_template = 'errors';
}
require_once('theme/' . $default_theme . '/common.php');

