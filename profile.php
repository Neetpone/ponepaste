<?php
define('IN_PONEPASTE', 1);
require_once('includes/common.php');
require_once('includes/functions.php');
require_once('includes/passwords.php');

use PonePaste\Models\Paste;

// Check if already logged in
if ($current_user === null) {
    header("Location: ./login.php");
    die();
}

$user_username = $current_user->username;
$row = $query->fetch();
$user_id = $current_user->id;
$user_date = $current_user->date;
$user_ip = $current_user->ip;
$user_password = $current_user->password;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cpassword'])) {
        $user_new_full = trim(htmlspecialchars($_POST['full']));
        $user_old_pass = $_POST['old_password'];
        if (pp_password_verify($user_old_pass, $user_password)) {
            $user_new_cpass = pp_password_hash($_POST['password']);

            $conn->prepare('UPDATE users SET password = ? WHERE id = ?')
                ->execute([$user_new_cpass, $user_id]);

            $success = 'Your profile has been updated.';
        } else {
            $error = 'Your old password is incorrect.';
        }
    } else {
        $error = 'All fields must be filled out.';
    }
}

updatePageViews($conn);

$total_user_pastes = Paste::where('user_id', $current_user->user_id)->count();

// Theme
$page_template = 'profile';
$page_title = 'My Profile';
require_once('theme/' . $default_theme . '/common.php');

