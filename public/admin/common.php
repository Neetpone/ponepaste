<?php
if (!defined('IN_PONEPASTE')) {
    die('This file may not be accessed directly.');
}

require_once('../../includes/common.php');

use PonePaste\Models\User;

if ($current_user === null || $current_user->role < User::ROLE_MODERATOR) {
    header('Location: ..');
    die();
}

if (!isset($_SESSION['admin_login'])) {
    // this is a hack, paste_id is set when POSTing to admin/paste_action.php, which we can only arrive at from a paste page
    if (isset($_POST['paste_id'])) {
        flashError('You must authenticate to perform that action.');
        $_SESSION['redirect_back'] = urlForPaste($_POST['paste_id']);
    } elseif (isset($_POST['user_id'])) {
        flashError('You must authenticate to perform that action.');
        $_SESSION['redirect_back'] = urlForMember($_POST['user_id']);
    }

    header('Location: .');
    exit();
}

if (isset($_GET['logout'])) {
    if (isset($_SESSION['login']))
        unset($_SESSION['login']);

    session_destroy();
    header("Location: .");
    exit();
}

function checkAdminAccess(int $role) {
    global $current_user;

    if ($current_user === null || $current_user->role < $role) {
        flashError('You do not have access to this page.');
        header('Location: /admin/');
        die();
    }
}

$flashes = getFlashes();
