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
require_once('mail/mail.php');


function sendVerificationEmail($email_address, $username, $full_name) {
    global $lang;
    global $email;
    global $site_name;

    $mail_type = "1";

    $protocol = paste_protocol();
    $verify_url = $protocol . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/verify.php?username=${username}&code=" . md5('4et4$55765' . $email_address . 'd94ereg');
    $subject = $lang['mail_acc_con']; // "$site_name Account Confirmation";
    $body = "
          Hello ${full_name}, Please verify your account by clicking the link below.<br /><br />

          <a href='$verify_url' target='_self'>$verify_url</a>  <br /> <br />

          After confirming your account you can log in using your username: <b>$username</b> and the password you used when signing up.
          ";

    if ($mail_type == '1') {
        default_mail($email, $site_name, $email_address, $subject, $body);
    } else {
        $email_info = getSiteInfo()['mail'];
        smtp_mail(
            $email_info['smtp_host'], $email_info['smtp_port'],
            $email_info['auth'], $email_info['smtp_username'], $email_info['smtp_password'], $email_info['socket'],
            $email, $site_name, $email_address, $subject, $body
        );
    }
}

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

if (isset($_GET['resend'])) {
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        $statement = $conn->prepare("SELECT username, verified FROM users WHERE email_id = ?");
        $statement->execute([$email]);
        if ($row = $statement->fetch()) {
            $username = $row['username'];
            $verified = (bool) $row['verified'];

            if (!$verified) {
                sendVerificationEmail($email, $username, $username);
                $success = $lang['mail_suc']; // "Verification code successfully sent to your email.";
            } else {
                $error = $lang['email_ver']; //"Email already verified.";
            }
        } else {
            $error = $lang['email_not']; // "Email not found.";
        }
    }
}

if (isset($_GET['forgot'])) {
    if (!empty($_POST['email'])) {
        $query = $conn->prepare('SELECT id, username FROM users WHERE email_id = ?');
        $query->execute([trim($_POST['email'])]);

        if ($row = $query->fetch()) {
            $username = $row['username'];

            $new_pass = uniqid(rand(), true);
            $new_pass_hash = password_hash($new_pass, PASSWORD_DEFAULT);

            $conn->prepare('UPDATE users SET password = ? WHERE id = ?')
                ->execute([$new_pass_hash, $row['id']]);

            $success = $lang['pass_change']; //"Password changed successfully and sent to your email address.";
            $sent_mail = $email;
            $subject = "$site_name Password Reset";
            $body = "<br />
          Hello $username , <br /><br />
          
          Your password has been reset: $new_pass  <br /> <br />
          
          You can now login and change your password. <br />
          ";
            if ($mail_type == '1') {
                default_mail($admin_mail, $admin_name, $sent_mail, $subject, $body);
            } else {
                smtp_mail($smtp_host, $smtp_port, $smtp_auth, $smtp_user, $smtp_pass, $smtp_sec, $admin_mail, $admin_name, $sent_mail, $subject, $body);
            }

        } else {
            $error = $lang['email_not']; //"Email not found";
        }
    }
}

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

    if (empty($_POST['email']) || empty($_POST['password']) || empty($_POST['username'])) {
        $error = $lang['missingfields']; // "All fields must be filled out";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error = $lang['email_invalid']; // "Your email address seems to be invalid.";
    } elseif (strlen($username) > $chara_max) {
        $error = $lang['maxnamelimit']; // "Username already taken.";
    } elseif (!isValidUsername($username)) {
        $error = $lang['usrinvalid']; // "Username not valid. Usernames can't contain special characters.";
    } else {
        $email = trim($_POST['email']);
        $query = $conn->prepare('SELECT 1 FROM users WHERE username = ?');
        $query->execute([$username]);
        if ($query->fetch()) {
            $error = $lang['userexists']; // "Username already taken.";
        } else {
            $query = $conn->prepare("SELECT 1 FROM users WHERE email_id = ?");
            $query->execute([$email]);

            if ($query->fetch()) {
                $error = $lang['emailexists']; // "Email already registered.";
            } else {
                $verification_needed = $verification !== 'disabled';

                $query = $conn->prepare(
                    "INSERT INTO users (oauth_uid, username, email_id, platform, password, verified, picture, date, ip, badge) VALUES ('0', ?, ?, 'Direct', ?, ?, 'NONE', ?, ?, '0')"
                );
                $query->execute([$username, $email, $password, $verification_needed ? 0 : 1, $date, $ip]);

                if ($verification_needed) {
                    sendVerificationEmail($email, $username, $username);;
                }

                $success = $lang['registered']; // "Your account was successfully registered.";
            }

        }
    }
}
// Theme
require_once('theme/' . $default_theme . '/header.php');
require_once('theme/' . $default_theme . '/login.php');
require_once('theme/' . $default_theme . '/footer.php');
