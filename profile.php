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


$p_title = $lang['myprofile']; //"My Profile";


// Check if already logged in
if (!isset($_SESSION['token'])) {
    header("Location: ./login.php");
}
$user_username = htmlentities(trim($_SESSION['username']));

$query = $conn->prepare('SELECT * FROM users WHERE username = ?');
$query->execute([$user_username]);
$row = $query->fetch();
$user_id = $row['id'];
$user_full_name = $row['full_name'];
$user_platform = Trim($row['platform']);
$user_date = $row['date'];
$user_ip = $row['ip'];
$user_password = $row['password'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cpassword'])) {
        $user_new_full = trim(htmlspecialchars($_POST['full']));
        $user_old_pass = $_POST['old_password'];
        if (pp_password_verify($user_old_pass, $user_password)) {
            $user_new_cpass = pp_password_hash($_POST['password']);

            $conn->prepare('UPDATE users SET full_name = ?, password = ? WHERE username = ?')
                ->execute([$user_new_full, $user_new_cpass, $user_username]);

            $success = $lang['profileupdated']; //"  Your profile information is updated ";
        } else {
            $error = $lang['oldpasswrong']; // "  Your old password is wrong.";
        }
    } else {
        $error = $lang['error']; //"Something went wrong.";
    }
}

updatePageViews($conn);

$total_pastes = getTotalPastes($conn, $user_username);

// Theme
require_once('theme/' . $default_theme . '/header.php');
require_once('theme/' . $default_theme . '/profile.php');
require_once('theme/' . $default_theme . '/footer.php');
