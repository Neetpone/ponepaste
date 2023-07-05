<?php
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/common.php');

use PonePaste\Models\User;

if (empty($_POST['user_id'])) {
    echo "Error: No User ID specified.";
    die();
}

$user = User::find((int) $_POST['user_id']);

if (!$user) {
    echo "Error: User not found.";
    die();
}

if (!verifyCsrfToken()) {
    flashError('Invalid CSRF token (do you have cookies enabled?)');
}

$can_administrate = can('administrate', $user);

if (!$can_administrate) {
    flashError('Error: You do not have permission to administrate this user.');
} else {
    if (isset($_POST['reset_password'])) {
        $new_password = pp_random_password();
        $user->password = pp_password_hash($new_password);
        $user->save();

        flashSuccess('Password reset to ' . $new_password);
    } elseif (isset($_POST['change_role'])) {
        if ($user->role === User::ROLE_MODERATOR) {
            $user->role = 0;
        } elseif ($user->role === 0) {
            $user->role = User::ROLE_MODERATOR;
        }

        $user->save();
        flashSuccess('Role changed.');
    }
}

header('Location: ' . urlForMember($user));
