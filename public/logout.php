<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/../includes/common.php');

use PonePaste\Helpers\SessionHelper;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $current_user === null) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    die();
}

/* Destroy remember token */
SessionHelper::destroySession();

/* Destroy PHP session */
unset($_SESSION['user_id']);
session_destroy();
flashSuccess('You have been logged out.');

header('Location: ' . $_SERVER['HTTP_REFERER']);
