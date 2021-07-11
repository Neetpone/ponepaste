<?php
/*
 * Paste <https://github.com/jordansamuel/PASTE>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License in GPL.txt for more details.
 */
define('IN_PONEPASTE', 1);
require_once('includes/common.php');
require_once('includes/functions.php');

// UTF-8
header('Content-Type: text/html; charset=utf-8');

$date    = date('jS F Y');
$ip      = $_SERVER['REMOTE_ADDR'];

updatePageViews($conn);

if (isset($_GET['page'])) {
    $page_name = htmlspecialchars(trim($_GET['page']));

    $query = $conn->prepare('SELECT page_title, page_content, last_date FROM pages WHERE page_name = ?');
    $query->execute([$page_name]);
    if ($row = $query->fetch()) {
        $page_title   = $row['page_title'];
        $page_content = $row['page_content'];
        $last_date    = $row['last_date'];
        $stats        = "OK";
        $p_title      = $page_title;
    }
}
// Theme
require_once('theme/' . $default_theme . '/header.php');
require_once('theme/' . $default_theme . '/pages.php');
require_once('theme/' . $default_theme . '/footer.php');

