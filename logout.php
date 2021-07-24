<?php
// Required functions
define('IN_PONEPASTE', 1);
require_once('includes/common.php');
require_once('includes/functions.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $current_user === null) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    die();
}

/* Destroy remember token */
$current_user->destroySession($conn, $_COOKIE[User::REMEMBER_TOKEN_COOKIE]);
unset($_COOKIE[User::REMEMBER_TOKEN_COOKIE]);
setcookie(User::REMEMBER_TOKEN_COOKIE, null, time() - 3600);

/* Destroy PHP session */
unset($_SESSION['user_id']);
session_destroy();

header('Location: ' . $_SERVER['HTTP_REFERER']);
