<?php /** @noinspection PhpDefineCanBeReplacedWithConstInspection */
define('IN_PONEPASTE', 1);

const ROUTER_VALID_PAGES = [
    'archive', 'discover', 'event', 'index', 'login',
    'logout', 'pages', 'paste', 'profile', 'user'
];

if (empty($_GET['route'])) {
    die('No route specified.');
}

if (!in_array($_GET['route'], ROUTER_VALID_PAGES)) {
    die('Invalid route specified.');
}

/* This is safe, because this is whitelisted to the options above. */
require_once($_GET['route'] . '.php');
