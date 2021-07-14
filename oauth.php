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

// Current date & user IP
$date = date('jS F Y');
$ip = $_SERVER['REMOTE_ADDR'];

// Page title
$p_title = $lang['login/register']; // "Login/Register";

if (isset($_GET['new_user'])) {
    $new_user = 1;
}

$username = $_SESSION['username'];

// POST Handler
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['user_change'])) {
        $new_username = htmlentities(Trim($_POST['new_username']));
        if ($new_username == "" || $new_username == null) {
            $error = $lang['usernotvalid']; //"Username not vaild";
        } else {
            $res = isValidUsername($new_username);
            if ($res) {
                $query = "SELECT * FROM users WHERE username='$new_username'";
                $result = mysqli_query($con, $query);
                if (mysqli_num_rows($result) > 0) {
                    $error = $lang['userexists']; //"Username already taken";
                } else {
                    $client_id = Trim($_SESSION['oauth_uid']);
                    $query = "UPDATE users SET username='$new_username' WHERE oauth_uid='$client_id'";
                    mysqli_query($con, $query);
                    if (mysqli_error($con)) {
                        $error = $lang['databaseerror']; // "Unable to access database.";
                    } else {
                        $success = $lang['userchanged']; //"Username changed successfully";
                        unset($_SESSION['username']);
                        $_SESSION['username'] = $new_username;
                    }
                }
            } else {
                $error = $lang['usernotvalid']; //"Username not vaild";
                $username = Trim($_SESSION['username']);
                goto OutPut;
            }
        }
    }
}

OutPut:
// Theme
require_once('theme/' . $default_theme . '/header.php');
require_once('theme/' . $default_theme . '/oauth.php');
require_once('theme/' . $default_theme . '/footer.php');
