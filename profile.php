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
require_once('includes/passwords.php');

// UTF-8
header('Content-Type: text/html; charset=utf-8');

$date = date('jS F Y');
$ip = $_SERVER['REMOTE_ADDR'];


$p_title = $lang['myprofile']; //"My Profile";

// Check if already logged in
if ($current_user === null) {
    header("Location: ./login.php");
    die();
}

$user_username = $current_user->username;

$query = $conn->query('SELECT * FROM users WHERE id = ?', [$current_user->user_id]);
$row = $query->fetch();
$user_id = $row['id'];
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

            $conn->prepare('UPDATE users SET password = ? WHERE id = ?')
                ->execute([$user_new_cpass, $user_id]);

            $success = $lang['profileupdated']; //"  Your profile information is updated ";
        } else {
            $error = $lang['oldpasswrong']; // "  Your old password is wrong.";
        }
    } else {
        $error = $lang['error']; //"Something went wrong.";
    }
}

updatePageViews($conn);

$total_user_pastes = getTotalPastes($conn, $current_user->user_id);

// Theme
$page_template = 'profile';
require_once('theme/' . $default_theme . '/common.php');

