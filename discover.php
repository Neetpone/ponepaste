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


function transformPasteRow(array $row) : array {
    global $conn;

    return [
        'id' => $row['id'],
        'title' => $row['title'],
        'member' => $row['member'],
        'time' => $row['created_at'],
        'time_update' => $row['updated_at'],
        'friendly_update_time' => friendlyDateDifference(new DateTime($row['updated_at']), new DateTime()),
        'friendly_time' => friendlyDateDifference(new DateTime($row['created_at']), new DateTime()),
        'tags' => getPasteTags($conn, $row['id'])
    ];
}

$popular_pastes = array_map('transformPasteRow', getpopular($conn, 10));
$monthly_popular_pastes = array_map('transformPasteRow', monthpop($conn, 10));
$recent_pastes = array_map('transformPasteRow', getRecent($conn, 10));
$updated_pastes = array_map('transformPasteRow', recentupdate($conn, 10));
$random_pastes = array_map('transformPasteRow', getrandom($conn, 10));

// Theme
$p_title = $lang['archive']; // "Pastes Archive";
require_once('theme/' . $default_theme . '/header.php');
require_once('theme/' . $default_theme . '/discover.php');
require_once('theme/' . $default_theme . '/footer.php');
