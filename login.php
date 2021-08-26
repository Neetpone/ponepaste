<?php
define('IN_PONEPASTE', 1);
require_once('includes/common.php');
require_once('includes/functions.php');
require_once('includes/passwords.php');

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

        $query = $conn->query("SELECT id, recovery_code_hash FROM users WHERE username = ?", [$username]);
        $row = $query->fetch();

        if ($row && pp_password_verify($_POST['recovery_code'], $row['recovery_code_hash'])) {
            $new_password = pp_random_password();
            $new_password_hash = pp_password_hash($new_password);

            $recovery_code = pp_random_token();
            $new_recovery_code_hash = pp_password_hash($recovery_code);

            $conn->prepare('UPDATE users SET password = ?, recovery_code_hash = ? WHERE id = ?')
                ->execute([$new_password_hash, $new_recovery_code_hash, $row['id']]);

            $success = 'Your password has been changed. A new recovery code has also been generated. Please note the recovery code and then sign in with the new password.';
        } else {
            $error = 'Incorrect username or password.';
        }
    } else {
        $error = 'All fields must be filled out.';
    }
} elseif (isset($_POST['signin'])) { // Login process
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $remember_me = (bool) $_POST['remember_me'];
        $username = trim($_POST['username']);
        $row = $conn->query("SELECT id, password, banned FROM users WHERE username = ?", [$username])
            ->fetch();

        $needs_rehash = false;

        /* This is designed to be a constant time lookup, hence the warning suppression operator so that
         * we always call pp_password_verify, even if row is null.
         */
        if (pp_password_verify($_POST['password'], @$row['password'], $needs_rehash)) {
            $user_id = $row['id'];

            if ($needs_rehash) {
                $new_password_hash = pp_password_hash($_POST['password']);

                $conn->query('UPDATE users SET password = ? WHERE id = ?',
                    [$new_password_hash, $user_id]);
            }

            if ($row['banned']) {
                // User is banned
                $error = 'You are banned.';
            } else {
                // Login successful
                $_SESSION['user_id'] = (string) $user_id;

                if ($remember_me) {
                    $remember_token = pp_random_token();
                    $expire_at = (new DateTime())->add(new DateInterval('P1Y'));

                    $conn->query('INSERT INTO user_sessions (user_id, token, expire_at) VALUES (?, ?, FROM_UNIXTIME(?))', [$user_id, $remember_token, $expire_at->format('U')]);

                    setcookie(User::REMEMBER_TOKEN_COOKIE, $remember_token, [
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
    $username = htmlentities(trim($_POST['username'], ENT_QUOTES));
    $password = pp_password_hash($_POST['password']);

    if (empty($_POST['password']) || empty($_POST['username'])) {
        $error = 'All fields must be filled out.';
    } elseif (strlen($username) > 25) {
        $error = 'Username too long.'; // "Username already taken.";
    } elseif (preg_match('/[^A-Za-z0-9._\\-$]/', $username)) {
        $error = 'Username is invalid - please use A-Za-z0-9, periods, hyphens, and underscores only.';
    } else {
        if ($conn->querySelectOne('SELECT 1 FROM users WHERE username = ?', [$username])) {
            $error = 'That username has already been taken.';
        } else {
            $recovery_code = pp_random_token();
            $recovery_code_hash = pp_password_hash($recovery_code);
            $conn->query(
                "INSERT INTO users (username, password, recovery_code_hash, picture, date, ip, badge) VALUES (?, ?, ?, 'NONE', ?, ?, '0')",
                [$username, $password, $recovery_code_hash, $date, $ip]
            );

            $success = 'Your account was successfully registered.';
        }
    }
}
// Theme
$page_template = 'login';
$page_title = 'Login / Register';
require_once('theme/' . $default_theme . '/common.php');


