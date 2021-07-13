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

// Current Date & User IP
$date = date('jS F Y');
$ip = $_SERVER['REMOTE_ADDR'];

// Mail
$mail_type = "1";

// Check if already logged in
if (isset($_SESSION['token'])) {
    header("Location: ./");
}

$admin_mail = $email;
$admin_name = $site_name;

// Email information

$email_info_rows = $conn->query("SELECT * FROM mail LIMIT 1");
while ($row = $email_info_rows->fetch()) {
    $verification = Trim($row['verification']);
    $smtp_host = Trim($row['smtp_host']);
    $smtp_user = Trim($row['smtp_username']);
    $smtp_pass = Trim($row['smtp_password']);
    $smtp_port = Trim($row['smtp_port']);
    $smtp_protocol = Trim($row['protocol']);
    $smtp_auth = Trim($row['auth']);
    $smtp_sec = Trim($row['socket']);
}
$mail_type = $smtp_protocol;

// Page title
$p_title = $lang['login/register']; // "Login/Register";

updatePageViews($conn);

if (isset($_GET['resend'])) {
    if (isset($_POST['email'])) {
        $email = htmlentities(trim($_POST['email']));
        $statement = $conn->prepare("SELECT * FROM users WHERE email_id = ?");
        $statement->execute([$email]);
        if ($statement->fetchColumn() > 0) {
            // Username found
            foreach ($statement as $index => $row) {
                $username = $row['username'];
                $db_email_id = $row['email_id'];
                $db_platform = $row['platform'];
                $db_password = Trim($row['password']);
                $db_verified = $row['verified'];
                $db_picture = $row['picture'];
                $db_date = $row['date'];
                $db_ip = $row['ip'];
                $db_id = $row['id'];
            }
            if ($db_verified == '0') {
                $protocol = paste_protocol();
                $verify_url = $protocol . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/verify.php?username=$username&code=" . Md5('4et4$55765' . $db_email_id . 'd94ereg');
                $sent_mail = $email;
                $subject = $lang['mail_acc_con']; // "$site_name Account Confirmation";
                $body = "
          Hello $db_full_name, Please verify your account by clicking the link below.<br /><br />

          <a href='$verify_url' target='_self'>$verify_url</a>  <br /> <br />

          After confirming your account you can log in using your username: <b>$username</b> and the password you used when signing up.
          ";

                if ($mail_type == '1') {
                    default_mail($admin_mail, $admin_name, $sent_mail, $subject, $body);
                } else {
                    smtp_mail($smtp_host, $smtp_port, $smtp_auth, $smtp_user, $smtp_pass, $smtp_sec, $admin_mail, $admin_name, $sent_mail, $subject, $body);
                }
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
    if (isset($_POST['email'])) {
        $email = htmlentities(trim($_POST['email']));
        $query = "SELECT * FROM users WHERE email_id='$email'";
        $result = mysqli_query($con, $query);
        if (mysqli_num_rows($result) > 0) {
            // Username found
            while ($row = mysqli_fetch_array($result)) {
                $username = $row['username'];
                $db_email_id = $row['email_id'];
                $db_platform = $row['platform'];
                $db_password = Trim($row['password']);
                $db_verified = $row['verified'];
                $db_picture = $row['picture'];
                $db_date = $row['date'];
                $db_ip = $row['ip'];
                $db_id = $row['id'];
            }
            $new_pass = uniqid(rand(), true);
            $new_pass_hash = password_hash($new_pass, PASSWORD_DEFAULT);

            $query = "UPDATE users SET password='$new_pass_hash' WHERE username='$username'";
            mysqli_query($con, $query);
            if (mysqli_error($con)) {
                $error = "Unable to access database.";
            } else {
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
    } else {
        // Login process
        if (isset($_POST['signin'])) {
            $username = htmlentities(trim($_POST['username']));
            $password = $_POST['password'];
            if ($username != null && $password != null) {
                $query = $conn->prepare("SELECT * FROM users WHERE username = ?");
                $query->execute([$username]);
                if ($row = $query->fetch()) {
                    // Username found
                    $db_oauth_uid = $row['oauth_uid'];
                    $db_email_id = $row['email_id'];
                    $db_full_name = $row['full_name'];
                    $db_platform = $row['platform'];
                    $db_password = $row['password'];
                    $db_verified = $row['verified'];
                    $db_picture = $row['picture'];
                    $db_date = $row['date'];
                    $db_ip = $row['ip'];
                    $db_id = $row['id'];

                    if (password_verify($password, $db_password)) {
                        if ($db_verified == "1") {
                            // Login successful
                            $_SESSION['token'] = Md5($db_id . $username);
                            $_SESSION['oauth_uid'] = $db_oauth_uid;
                            $_SESSION['username'] = $username;

                            header('Location: ' . $_SERVER['HTTP_REFERER']);

                        } elseif ($db_verified == "2") {
                            // User is banned
                            $error = $lang['banned'];
                        } else {
                            // Account not verified
                            $error = $lang['notverified'];
                        }
                    } else {
                        // Password wrong
                        $error = $lang['incorrect'];

                    }
                } else {
                    // Username not found
                    $error = $lang['incorrect'];
                }
            } else {
                $error = $lang['missingfields']; //"All fields must be filled out.";
            }
        }

    }

}
// Register process
if (isset($_POST['signup'])) {
    $username = htmlentities(trim($_POST['username'], ENT_QUOTES));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = htmlentities(trim($_POST['email'], ENT_QUOTES));
    $chara_max = 25;   //characters for max input

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = $lang['email_invalid']; // "Your email address seems to be invalid.";
    } else {
        if (strlen($username) > $chara_max) {
            $error = $lang['maxnamelimit']; // "Username already taken.";
        } else {
            if ($username != null && $password != null && $email != null) {
                $res = isValidUsername($username);
                if ($res) {
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

                            if (!$verification_needed) {
                                $success = $lang['registered']; // "Your account was successfully registered.";
                            } else {
                                $success = $lang['registered']; // "Your account was successfully registered.";
                                $protocol = paste_protocol();
                                $verify_url = $protocol . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/verify.php?username=$username&code=" . Md5('4et4$55765' . $email . 'd94ereg');
                                $sent_mail = $email;
                                $subject = $lang['mail_acc_con']; // "$site_name Account Confirmation";
                                $body = "
			  Hello $username, Your $site_name account has been created. Please verify your account by clicking the link below.<br /><br />

			  <a href='$verify_url' target='_self'>$verify_url</a>  <br /> <br />

			  After confirming your account you can log in using your username: <b>$username</b> and the password you used when signing up.
			  ";
                                if ($mail_type == '1') {
                                    default_mail($admin_mail, $admin_name, $sent_mail, $subject, $body);
                                } else {
                                    smtp_mail($smtp_host, $smtp_port, $smtp_auth, $smtp_user, $smtp_pass, $smtp_sec, $admin_mail, $admin_name, $sent_mail, $subject, $body);
                                }
                            }

                        }

                    }
                } else {
                    $error = $lang['usrinvalid']; // "Username not valid. Usernames can't contain special characters.";
                }
            } else {
                $error = $lang['missingfields']; // "All fields must be filled out";
            }
        }
    }
}

// Theme
require_once('theme/' . $default_theme . '/header.php');
require_once('theme/' . $default_theme . '/login.php');
require_once('theme/' . $default_theme . '/footer.php');
