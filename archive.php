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

require_once('config.php');
require_once('includes/functions.php');

// UTF-8
header('Content-Type: text/html; charset=utf-8');

$date    = date('jS F Y');
$ip      = $_SERVER['REMOTE_ADDR'];
$data_ip = file_get_contents('tmp/temp.tdata');
$conn = new PDO(
    "mysql:host=$db_host;dbname=$db_schema;charset=utf8",
    $db_user,
    $db_pass,
    $db_opts
);

// Get site info
$site_info_rows = $conn->query('SELECT * FROM site_info');
while ($row = $site_info_rows->fetch()) {
    $title				= Trim($row['title']);
    $des				= Trim($row['des']);
    $baseurl    		= Trim($row['baseurl']);
    $keyword			= Trim($row['keyword']);
    $site_name			= Trim($row['site_name']);
    $email				= Trim($row['email']);
    $twit				= Trim($row['twit']);
    $face				= Trim($row['face']);
    $gplus				= Trim($row['gplus']);
    $ga					= Trim($row['ga']);
    $additional_scripts	= Trim($row['additional_scripts']);
}


//Temp count for untagged pastes
$untagged = $conn->query("SELECT COUNT(id) from pastes WHERE tagsys is null");
    while ($row = $untagged->fetch()) {
    $total_untagged = $row['COUNT(id)'];
}


// Set theme and language
$site_theme_rows = $conn->query('SELECT * FROM interface WHERE id="1"');
while ($row = $site_theme_rows->fetch()) {
    $default_lang  = Trim($row['lang']);
    $default_theme = Trim($row['theme']);
}
require_once("langs/$default_lang");

$p_title = $lang['archive']; // "Pastes Archive";

// Check if IP is banned
if ( is_banned($conn, $ip) ) die($lang['banned']); // "You have been banned from ".$site_name;

// Logout
if (isset($_GET['logout'])) {
	header('Location: ' . $_SERVER['HTTP_REFERER']);
    unset($_SESSION['token']);
    unset($_SESSION['oauth_uid']);
    unset($_SESSION['username']);
    session_destroy();
}

// Page views 
$site_view_rows = $conn->query("SELECT @last_id := MAX(id) FROM page_view");
while ($row = $site_view_rows->fetch()) {
    $last_id = $row['@last_id := MAX(id)'];
}

$site_view_last = $conn->query("SELECT * FROM page_view WHERE id = ? ");
$site_view_last->execute([$last_id]);      
while ($row = $site_view_last->fetch()) {
    $last_date = $row['date'];
}

if ($last_date == $date) {
    if (str_contains($data_ip, $ip)) {
        $statement = $conn->prepare("SELECT * FROM page_view WHERE id = ?");
        $statement->execute([$last_id]);        
        while ($row = $statement->fetch()) {
            $last_tpage = Trim($row['tpage']);
        }
        $last_tpage = $last_tpage + 1;
        
        // IP already exists, Update view count
        $statement = $conn->prepare("UPDATE page_view SET tpage=? WHERE id= ?");
        $statement->execute([$last_tpage,$last_id]);  
    } else {
        $statement = $conn->prepare("SELECT * FROM page_view WHERE id = ?");
        $statement->execute([$last_id]);  
        while ($row = $statement->fetch()) {
            $last_tpage  = Trim($row['tpage']);
            $last_tvisit = Trim($row['tvisit']);
        }
        $last_tpage  = $last_tpage + 1;
        $last_tvisit = $last_tvisit + 1;
      
        // Update both tpage and tvisit.
        $statement = $conn->prepare("UPDATE page_view SET tpage=?,tvisit=? WHERE id = ?");
        $statement->execute([$last_tpage,$last_tvisit,$last_id]); 
        file_put_contents('tmp/temp.tdata', $data_ip . "\r\n" . $ip);
    }
} else {
    // Delete the file and clear data_ip
    unlink("tmp/temp.tdata");
    $data_ip = "";
    
    // New date is created
    $statement = $conn->prepare("INSERT INTO page_view (date,tpage,tvisit) VALUES (?,'1','1')");
    $statement->execute([$date]); 
    // Update the IP
    file_put_contents('tmp/temp.tdata', $data_ip . "\r\n" . $ip);
    
}
// Ads
$site_ads_rows = $conn->query('SELECT * FROM ads WHERE id="1"');
while ($row = $site_ads_rows->fetch()) {
    $text_ads = Trim($row['text_ads']);
    $ads_1    = Trim($row['ads_1']);
    $ads_2    = Trim($row['ads_2']);
}
// Theme
require_once('theme/' . $default_theme . '/header.php');
require_once('theme/' . $default_theme . '/archive.php');
require_once('theme/' . $default_theme . '/footer.php');
?>