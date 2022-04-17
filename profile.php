<?php
require_once('includes/common.php');
require_once('includes/functions.php');
require_once('includes/passwords.php');

use PonePaste\Models\Paste;

if ($current_user === null) {
    header("Location: ./login.php");
    die();
}

$user_username = $current_user->username;
$user_id = $current_user->id;
$user_date = $current_user->date;
$user_ip = $current_user->ip;
$user_password = $current_user->password;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyCsrfToken()) {
        $error = 'Invalid CSRF token (do you have cookies enabled?)';
    } else if (isset($_POST['cpassword'])) {
        $user_old_pass = $_POST['old_password'];
        if (pp_password_verify($user_old_pass, $user_password)) {
            $user_new_cpass = pp_password_hash($_POST['password']);

            $current_user->password = $user_new_cpass;
            $current_user->save();

            $success = 'Your profile has been updated.';
        } else {
            $error = 'Your old password is incorrect.';
        }
    } else {
        $error = 'All fields must be filled out.';
    }
}

updatePageViews();

$total_user_pastes = Paste::where('user_id', $current_user->id)->count();
$csrf_token = setupCsrfToken();

$page_template = 'profile';
$page_title = 'My Profile';
require_once('theme/' . $default_theme . '/common.php');

