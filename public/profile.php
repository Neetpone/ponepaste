<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/../includes/common.php');

use PonePaste\Models\Paste;

if ($current_user === null) {
    header("Location: /login");
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
    } else if (isset($_POST['cpassword']) && !empty($_POST['old_password']) && !empty($_POST['password'])) {
        if (pp_password_verify($_POST['old_password'], $user_password)) {
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
require_once(__DIR__ . '/../theme/' . $default_theme . '/common.php');

