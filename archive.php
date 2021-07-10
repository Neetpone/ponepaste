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
session_start();

define('IN_PONEPASTE', 1);
require_once('includes/common.php');
require_once('config.php');

// UTF-8
header('Content-Type: text/html; charset=utf-8');

$date    = date('jS F Y');
$data_ip = file_get_contents('tmp/temp.tdata');

// Temp count for untagged pastes
$total_untagged = intval($conn->query("SELECT COUNT(*) from pastes WHERE tagsys IS NULL")->fetch(PDO::FETCH_NUM)[0]);

updatePageViews($conn);

// Ads
$site_ads_rows = $conn->query('SELECT * FROM ads WHERE id = 1');
while ($row = $site_ads_rows->fetch()) {
    $text_ads = Trim($row['text_ads']);
    $ads_1    = Trim($row['ads_1']);
    $ads_2    = Trim($row['ads_2']);
}

$p_title = $lang['archive']; // "Pastes Archive";

// Theme
require_once('theme/' . $default_theme . '/header.php');
require_once('theme/' . $default_theme . '/archive.php');
require_once('theme/' . $default_theme . '/footer.php');
?>