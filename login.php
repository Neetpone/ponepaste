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
require_once('includes/passwords.php');

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

if (isset($_POST['forgot'])) {
    if (!empty($_POST['username']) && !empty($_POST['recovery_code'])) {
        $username = trim($_POST['username']);
        $recovery_code = trim($_POST['recovery_code']);

        $query = $conn->prepare("SELECT id, recovery_code_hash FROM users WHERE username = ?");
        $query->execute([$username]);
        $row = $query->fetch();
        if ($row && pp_password_verify($_POST['recovery_code'], $row['recovery_code_hash'])) {
            $new_password = md5(random_bytes(64));
            $new_password_hash = pp_password_hash($new_password);

            $recovery_code = hash('SHA512', random_bytes(64));
            $new_recovery_code_hash = pp_password_hash($recovery_code);

            $conn->prepare('UPDATE users SET password = ?, recovery_code_hash = ? WHERE id = ?')
                ->execute([$new_password_hash, $new_recovery_code_hash, $row['id']]);

            $success = 'Your password has been changed. A new recovery code has also been generated. Please note the recovery code and then sign in with the new password.';
        } else {
            $error = $lang['incorrect'];
        }
    } else {
        $error = $lang['missingfields']; // "All fields must be filled out";
    }
} else if (isset($_POST['signin'])) { // Login process
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = trim($_POST['username']);
        $query = $conn->prepare("SELECT id, password, banned FROM users WHERE username = ?");
        $query->execute([$username]);
        $row = $query->fetch();
        $needs_rehash = false;
        if ($row && pp_password_verify($_POST['password'], $row['password'], $needs_rehash)) {
            // Username found
            $db_ip = $row['ip'];
            $db_id = $row['id'];

            if ($needs_rehash) {
                $new_password_hash = pp_password_hash($_POST['password']);

                $conn->prepare('UPDATE users SET password = ? WHERE id = ?')
                    ->execute([$new_password_hash, $row['id']]);
            }

            if ($row['banned']) {
                // User is banned
                $error = $lang['banned'];
            } else {
                // Login successful
                $_SESSION['token'] = md5($db_id . $username);
                $_SESSION['username'] = $username;

                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }
        } else {
            // Username not found or password incorrect.
            $error = $lang['incorrect'];
        }
    } else {
        $error = $lang['missingfields']; // "All fields must be filled out.";
    }
} else if (isset($_POST['signup'])) { // Registration process
    $username = htmlentities(trim($_POST['username'], ENT_QUOTES));
    $password = pp_password_hash($_POST['password']);
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
            $recovery_code = hash('SHA512', random_bytes('64'));
            $recovery_code_hash = pp_password_hash($recovery_code);
            $query = $conn->prepare(
                "INSERT INTO users (username, password, recovery_code_hash, picture, date, ip, badge) VALUES (?, ?, ?, 'NONE', ?, ?, '0')"
            );
            $query->execute([$username, $password, $recovery_code_hash, $date, $ip]);

            $success = $lang['registered']; // "Your account was successfully registered.";
        }
    }
}
// Theme
require_once('theme/' . $default_theme . '/header.php');
require_once('theme/' . $default_theme . '/login.php');
require_once('theme/' . $default_theme . '/footer.php');
