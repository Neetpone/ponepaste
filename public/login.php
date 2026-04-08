<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/../includes/common.php');
require_once(__DIR__ . '/../includes/captcha.php');

use PonePaste\Helpers\SessionHelper;
use PonePaste\Models\User;
use PonePaste\Models\UserSession;

// Check if already logged in
if ($current_user !== null) {
    header("Location: ./");
    die();
}

updatePageViews();

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

            $recovery_code = pp_random_friendly_token();
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

        /* This is designed to be a constant time comparison for the password hash, whether the user is found or not.
         * Assumptions: pp_password_verify() is itself constant time.
         * - Both paths: we set $dummy = password_hash(''); this takes n time.
         * - User not found path: we set $password_ok to a constant and call pp_password_verify($input, $dummy); this takes m time, for a total of n + m time.
         * - User found path: we set $password_ok to a constant call pp_password_verify($input, $user->password); this takes m time, for a total of n + m time.
         * In reality, a timing attack to a PHP server on the Internet is highly unlikely,
         * but this is funny and I implemented it badly several years ago for some reason, so I'm going to keep coming back to it.
         */
        $password_ok = false;
        $needs_rehash = false;
        $dummy_hash = pp_password_hash('');
        if (!$user) {
            $password_ok = false;
            pp_password_verify($_POST['password'], $dummy_hash);
        } else if (pp_password_verify($_POST['password'], $user->password, $needs_rehash)) {
            $password_ok = true;
            if ($needs_rehash) {
                $user->password = pp_password_hash($_POST['password']);
                $user->save();
            }

            if ($user->banned) {
                $error = 'You are banned.';
            } else {
                // Login successful - regenerate session ID to prevent session fixation
                session_regenerate_id(true);
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
                        'secure' => pp_is_https(), /* Local dev environment is non-HTTPS */
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);
                }

                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }
        }

        if (!$password_ok) {
            // Username not found or password incorrect.
            $error = 'Incorrect username or password.';
        }
    } else {
        $error = 'All fields must be filled out.';
    }
} elseif (isset($_POST['signup'])) { // Registration process
    $username = trim($_POST['username']);
    $password = pp_password_hash($_POST['password']);

    if ($captcha_enabled && !checkCaptcha($_POST['captcha_token'], trim($_POST['captcha_answer']))) {
        $error = 'Incorrect CAPTCHA.';
    } elseif (empty($_POST['password']) || empty($_POST['username'])) {
        $error = 'All fields must be filled out.';
    } elseif (strlen($_POST['password']) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif (strlen($username) > 25) {
        $error = 'Username too long.';
    } elseif (!preg_match('/^[A-Za-z0-9._\\-]+$/', $username)) {
        $error = 'Username is invalid - please use A-Za-z0-9, periods, hyphens, and underscores only.';
    } elseif (User::where('username', $username)->first()) {
        $error = 'That username has already been taken.';
    } else {
        /* this is displayed to the user in the template, hence the variable rather than inlining */
        $recovery_code = pp_random_friendly_token();

        $user = new User([
            'username' => $username,
            'password' => $password,
            'recovery_code_hash' => pp_password_hash($recovery_code),
            'ip' => $ip
        ]);
        $user->save();

        $success = 'Your account was successfully registered.';
    }
}

$page_template = 'login';
$page_title = 'Login / Register';

require_once(__DIR__ . '/../theme/' . $default_theme . '/common.php');

