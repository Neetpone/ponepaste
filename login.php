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

// Required functions
define('IN_PONEPASTE', 1);
require_once('includes/common.php');
require_once('includes/functions.php');

// Current Date & User IP
$date = date('jS F Y');
$ip = $_SERVER['REMOTE_ADDR'];



// Check if already logged in
if (isset($_SESSION['token'])) {
    header("Location: ./");
}

// Page title
$p_title = $lang['login/register']; // "Login/Register";

updatePageViews($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if logged in
    if (isset($_SESSION['token'])) {
        header("Location: ./");
        exit;
    }

    // Login process
    if (isset($_POST['signin'])) {
        if (!empty($_POST['username']) && !empty($_POST['password'])) {
            $username = trim($_POST['username']);
            $query = $conn->prepare("SELECT id, password, banned, verified FROM users WHERE username = ?");
            $query->execute([$username]);
            $row = $query->fetch();
            if ($row && password_verify($_POST['password'], $row['password'])) {
                // Username found
                $db_oauth_uid = $row['oauth_uid'];
                $db_ip = $row['ip'];
                $db_id = $row['id'];

                if ($row['banned']) {
                    // User is banned
                    $error = $lang['banned'];
                } if ($row['verified']) {
                    // Login successful
                    $_SESSION['token'] = md5($db_id . $username);
                    $_SESSION['oauth_uid'] = $db_oauth_uid;
                    $_SESSION['username'] = $username;

                    header('Location: ' . $_SERVER['HTTP_REFERER']);
                    exit();
                } else {
                    // Account not verified
                    $error = $lang['notverified'];
                }
            } else {
                // Username not found or password incorrect.
                $error = $lang['incorrect'];
            }
        } else {
            $error = $lang['missingfields']; // "All fields must be filled out.";
        }
    }
}
// Register process
if (isset($_POST['signup'])) {
    $username = htmlentities(trim($_POST['username'], ENT_QUOTES));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $chara_max = 25;   //characters for max input

    if (empty($_POST['password']) || empty($_POST['username'])) {
        $error = $lang['missingfields']; // "All fields must be filled out";
    } elseif (strlen($username) > $chara_max) {
        $error = $lang['maxnamelimit']; // "Username already taken.";
    } elseif (!isValidUsername($username)) {
        $error = $lang['usrinvalid']; // "Username not valid. Usernames can't contain special characters.";
    } else {
        $query = $conn->prepare('SELECT 1 FROM users WHERE username = ?');
        $query->execute([$username]);

        if ($query->fetch()) {
            $error = $lang['userexists']; // "Username already taken.";
        } else {
            $query = $conn->prepare(
                "INSERT INTO users (username, password, picture, date, ip, badge) VALUES (?, ?, 'NONE', ?, ?, '0')"
            );
            $query->execute([$username, $password, $date, $ip]);

            $success = $lang['registered']; // "Your account was successfully registered.";
        }
    }
}
// Theme
require_once('theme/' . $default_theme . '/header.php');
require_once('theme/' . $default_theme . '/login.php');
require_once('theme/' . $default_theme . '/footer.php');
