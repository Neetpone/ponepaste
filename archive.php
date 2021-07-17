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

// UTF-8
header('Content-Type: text/html; charset=utf-8');

$date = date('jS F Y');

// Temp count for untagged pastes
$total_untagged = intval($conn->query("SELECT COUNT(*) from pastes WHERE tagsys IS NULL")->fetch(PDO::FETCH_NUM)[0]);

updatePageViews($conn);


$p_title = $lang['archive']; // "Pastes Archive";

// Theme
require_once('theme/' . $default_theme . '/header.php');
require_once('theme/' . $default_theme . '/archive.php');
require_once('theme/' . $default_theme . '/footer.php');
