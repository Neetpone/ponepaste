<?php
define('IN_PONEPASTE', 1);
require_once('includes/common.php');
require_once('includes/functions.php');

use PonePaste\Models\Page;

updatePageViews();

$page_title = 'Page not found';

if (isset($_GET['page'])) {
    $page = Page::select('page_title', 'page_content', 'last_date')
        ->where('page_name', $_GET['page'])
        ->first();

    if (isset($page)) {
        $page_title = $page->page_title;
    }
}

$page_template = 'pages';
require_once('theme/' . $default_theme . '/common.php');

