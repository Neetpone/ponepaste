<?php
define('IN_PONEPASTE', 1);
require_once('includes/common.php');
require_once('includes/functions.php');
require_once('includes/passwords.php');

use PonePaste\Helpers\SessionHelper;
use PonePaste\Models\User;
use PonePaste\Models\UserSession;

// Current Date & User IP
$date = date('jS F Y');
$ip = $_SERVER['REMOTE_ADDR'];


// Check if already logged in
if ($current_user !== null) {
    header("Location: ./");
    die();
}

updatePageViews($conn);

if (isset($_POST['forgot'])) {
    if (!empty($_POST['username']) && !empty($_POST['recovery_code'])) {
        $username = trim($_POST['username']);
        $recovery_code = trim($_POST['recovery_code']);

        $user = User::select('id', 'recovery_code_hash')
                    ->where('username', $username);
        /* see justification below for error-suppression operator */
        if (pp_password_verify($_POST['recovery_code'], @$user->recovery_code_hash)) {
            $new_password = pp_random_password();
            $new_password_hash = pp_password_hash($new_password);

            $recovery_code = pp_random_token();
            $new_recovery_code_hash = pp_password_hash($recovery_code);

            $user->password = $new_password_hash;
            $user->recovery_code_hash = $new_recovery_code_hash;

            $user->save();

            $success = 'Your password has been changed. A new recovery code has also been generated. Please note the recovery code and then sign in with the new password.';
        } else {
            $error = 'Incorrect username or recovery code.';
        }
    } else {
        $error = 'All fields must be filled out.';
    }
} elseif (isset($_POST['signin'])) { // Login process
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $remember_me = (bool) $_POST['remember_me'];
        $username = trim($_POST['username']);
        $user = User::select('id', 'password', 'banned')
                    ->where('username', $username)
                    ->first();

        $needs_rehash = false;

        /* This is designed to be a constant time lookup, hence the warning suppression operator so that
         * we always call pp_password_verify, even if the user is null.
         */
        if (pp_password_verify($_POST['password'], @$user->password, $needs_rehash)) {
            if ($needs_rehash) {
                $user->password = pp_password_hash($_POST['password']);
                $user->save();
            }

            if ($user->banned) {
                // User is banned
                $error = 'You are banned.';
            } else {
                // Login successful
                $_SESSION['user_id'] = (string) $user->id;

                if ($remember_me) {
                    $remember_token = pp_random_token();
                    $expire_at = (new DateTime())->add(new DateInterval('P1Y'));

                    $session = new UserSession([
                        'user_id' => $user->id,
                        'token' => $remember_token,
                        'expire_at' => $expire_at
                    ]);
                    $session->save();

                    setcookie(SessionHelper::REMEMBER_TOKEN_COOKIE, $remember_token, [
                        'expires' => (int) $expire_at->format('U'),
                        'secure' => !empty($_SERVER['HTTPS']), /* Local dev environment is non-HTTPS */
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);
                }

                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }
        } else {
            // Username not found or password incorrect.
            $error = 'Incorrect username or password.';
        }
    } else {
        $error = 'All fields must be filled out.';
    }
} elseif (isset($_POST['signup'])) { // Registration process
    $username = trim($_POST['username']);
    $password = pp_password_hash($_POST['password']);

    if (empty($_POST['password']) || empty($_POST['username'])) {
        $error = 'All fields must be filled out.';
    } elseif (strlen($username) > 25) {
        $error = 'Username too long.'; // "Username already taken.";
    } elseif (preg_match('/[^A-Za-z0-9._\\-$]/', $username)) {
        $error = 'Username is invalid - please use A-Za-z0-9, periods, hyphens, and underscores only.';
    } else {
        if (User::where('username', $username)->first()) {
            $error = 'That username has already been taken.';
        } else {
            /* this is displayed to the user in the template, hence the variable rather than inlining */
            $recovery_code = pp_random_token();

            $user = new User([
                'username' => $username,
                'password' => $password,
                'recovery_code_hash' => pp_password_hash($recovery_code),
                'date' => $date,
                'ip' => $ip
            ]);
            $user->save();

            $success = 'Your account was successfully registered.';
        }
    }
}
// Theme
$page_template = 'login';
$page_title = 'Login / Register';
require_once('theme/' . $default_theme . '/common.php');


