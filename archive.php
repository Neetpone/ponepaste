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

// Page views
$last_page_view = $conn->query('SELECT * FROM page_view ORDER BY id DESC LIMIT 1')->fetch();
$last_date = $last_page_view['date'];

if ($last_date == $date) {
    if (str_contains($data_ip, $ip)) {
        $last_tpage = intval($last_page_view['tpage']) + 1;

        // IP already exists, Update view count
        $statement = $conn->prepare("UPDATE page_view SET tpage = ? WHERE id = ?");
        $statement->execute([$last_tpage, $last_page_view['id']]);
    } else {
        $last_tpage  = intval($last_page_view['tpage']) + 1;
        $last_tvisit = intval($last_page_view['tvisit']) + 1;

        // Update both tpage and tvisit.
        $statement = $conn->prepare("UPDATE page_view SET tpage = ?,tvisit = ? WHERE id = ?");
        $statement->execute([$last_tpage, $last_tvisit, $last_page_view['id']]);
        file_put_contents('tmp/temp.tdata', $data_ip . "\r\n" . $ip);
    }
} else {
    // Delete the file and clear data_ip
    unlink("tmp/temp.tdata");

    // New date is created
    $statement = $conn->prepare("INSERT INTO page_view (date, tpage, tvisit) VALUES (?, '1', '1')");
    $statement->execute([$date]);

    // Update the IP
    file_put_contents('tmp/temp.tdata', $ip);
}

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