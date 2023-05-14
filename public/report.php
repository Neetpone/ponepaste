<?php
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/../includes/common.php');

use PonePaste\Models\Paste;

$error = null;

if ($current_user === null) {
    header("Location: /login");
    die();
}

$paste = Paste::find((int) $_REQUEST['id']);

if (!$paste) {
    header('HTTP/1.1 404 Not Found');
    $error = 'Not found';
    goto done;
}

if (!can('view', $paste)) {
    $error = 'This is a private paste. Why are you attempting to report it?';
    goto done;
}

/* $password_ok_pastes is an array of IDs of pastes for which a correct password has already been entered this session. */
if (isset($_SESSION['password_ok'])) {
    $password_ok_pastes = json_decode($_SESSION['password_ok']);
} else {
    $password_ok_pastes = [];
}

$password_required = $paste->password !== null && $paste->password !== 'NONE';
if ($password_required && !in_array($paste->id, $password_ok_pastes)) {
    $error = 'This is a passworded paste, but you have not entered the password for it.';
    goto done;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken()) {
        $error = 'Invalid CSRF token (do you have cookies enabled?)';
        goto done;
    }

    // TODO: Check if the paste has already been reported.

    if (empty($_POST['reason'])) {
        $error = 'You must provide a report reason.';
        goto done;
    }

    if ($paste->reports->where('open', true)->isNotEmpty()) {
        $error = 'This paste has already been reported.';
        goto done;
    }

    $paste->reports()->create([
        'user_id' => $current_user->id,
        'reason' => $_POST['reason'],
        'open' => true
    ]);

    flashSuccess('Paste successfully reported.');
    header('Location: ' . urlForPaste($paste));
    die();
}

$csrf_token = setupCsrfToken();


$page_template = 'report';
$page_title = 'Report Paste';

done:
if ($error) {
    $page_title = 'Error';
    $page_template = 'errors';
}

require_once(__DIR__ . '/../theme/' . $default_theme . '/common.php');
