<?php
if (!defined('IN_PONEPASTE')) {
    die('This file may not be accessed directly.');
}

require_once('../../includes/common.php');

use PonePaste\Models\AdminLog;
use PonePaste\Models\User;

function updateAdminHistory(User $admin, int $action) : void {
    $log = new AdminLog([
        'user_id' => $admin->id,
        'action' => $action,
        'ip' => $_SERVER['REMOTE_ADDR']
    ]);

    $log->save();
}

if ($current_user === null || $current_user->role < User::ROLE_MODERATOR) {
    header('Location: ..');
    die();
}

if (!isset($_SESSION['admin_login'])) {
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
