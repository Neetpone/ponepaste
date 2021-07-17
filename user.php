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

$date = date('jS F Y');
$ip = $_SERVER['REMOTE_ADDR'];

// If username defined in URL, then check if it's exists in database. If invalid, redirect to main site.
$user_username = trim($_SESSION['username']);

if (empty($_GET['user'])) {
    // No username provided
    header("Location: ../error.php");
    die();
}

$profile_username = trim($_GET['user']);

$query = $conn->prepare('SELECT id, date, badge FROM users WHERE username = ?');
$query->execute([$profile_username]);
$profile_info = $query->fetch();

if (!$profile_info) {
    // Invalid username
    header("Location: ../error.php");
    die();
}

$p_title = $profile_username . $lang['user_public_pastes']; // "Username's Public Pastes"

// Favorite Counts
$query = $conn->prepare(
    'SELECT COUNT(*) AS total_favs FROM pins INNER JOIN pastes ON pastes.id = pins.paste_id WHERE pins.paste_id = ?'
);
$query->execute([$profile_info['id']]);
$total_pfav = intval($query->fetch(PDO::FETCH_NUM)[0]);


$query = $conn->prepare(
    'SELECT COUNT(*) AS total_favs
            FROM pins INNER JOIN users ON users.id = pins.user_id
            WHERE users.username = ?'
);
$query->execute([$profile_username]);
$total_yfav = intval($query->fetch(PDO::FETCH_NUM)[0]);

// Badges
$profile_badge = match ($profile_info['badge']) {
    1 => '<img src = "/img/badges/donate.png" title="[Donated] Donated to Ponepaste" style="margin:5px">',
    2 => '<img src = "/img/badges/spoon.png" title="[TheWoodenSpoon] You had one job" style="margin:5px">',
    3 => '<img src = "/img/badges/abadge.png" title="[>AFuckingBadge] Won a PasteJam Competition" style="margin:5px">',
    default => '',
};

$query = $conn->prepare('SELECT COUNT(*) FROM pastes WHERE user_id = ?');
$query->execute([$profile_info['id']]);
$profile_total_pastes = intval($query->fetch(PDO::FETCH_NUM)[0]);


$query = $conn->prepare('SELECT COUNT(*) FROM pastes WHERE user_id = ? AND visible = 0');
$query->execute([$profile_info['id']]);
$profile_total_public = intval($query->fetch(PDO::FETCH_NUM)[0]);

$query = $conn->prepare('SELECT COUNT(*) FROM pastes WHERE user_id = ? AND visible = 1');
$query->execute([$profile_info['id']]);
$profile_total_unlisted = intval($query->fetch(PDO::FETCH_NUM)[0]);

$query = $conn->prepare('SELECT COUNT(*) FROM pastes WHERE user_id = ? AND visible = 2');
$query->execute([$profile_info['id']]);
$profile_total_private = intval($query->fetch(PDO::FETCH_NUM)[0]);

$query = $conn->prepare('SELECT SUM(views) FROM pastes WHERE user_id = ?');
$query->execute([$profile_info['id']]);
$profile_total_paste_views = intval($query->fetch(PDO::FETCH_NUM)[0]);

$profile_join_date = $profile_info['date'];

$profile_pastes = getUserPastes($conn, $profile_info['id']);


updatePageViews($conn);

if (isset($_GET['del'])) {
    if ($_SESSION['token']) { // Prevent unauthorized deletes
        $paste_id = intval(trim($_GET['id']));

        $query = $conn->prepare('SELECT user_id FROM pastes WHERE id = ?');
        $query->execute([$paste_id]);
        $result = $query->fetch();

        if (empty($result) || $result['user_id'] !== $profile_info['id']) {
            $error = $lang['delete_error_invalid']; // Does not exist or not paste owner
        } else {
            $query = $conn->prepare('DELETE FROM pastes WHERE id = ?');
            $query->execute([$paste_id]);
            $success = $lang['pastedeleted']; // "Paste deleted successfully."
        }
    } else {
        $error = $lang['not_logged_in']; // Must be logged in to do that
    }
}

// Theme
require_once('theme/' . $default_theme . '/header.php');
require_once('theme/' . $default_theme . '/user_profile.php');
require_once('theme/' . $default_theme . '/footer.php');
