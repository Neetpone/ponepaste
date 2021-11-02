<?php
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/../includes/common.php');

use PonePaste\Models\User;
use PonePaste\Models\AdminLog;

function updateAdminHistory(User $admin, int $action) {
    $log = new AdminLog([
        'user_id' => $admin->id,
        'action' => $action,
        'ip' => $_SERVER['REMOTE_ADDR']
    ]);

    $log->save();
}

if ($current_user === null || !$current_user->admin) {
    header('Location: ..');
    die();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (password_verify($_POST['password'], $current_user->admin_password_hash)) {
        updateAdminHistory($current_user, AdminLog::ACTION_LOGIN);
        $_SESSION['admin_login'] = true;
        header("Location: dashboard.php");
        exit();
    } else {
        updateAdminHistory($current_user, AdminLog::ACTION_FAIL_LOGIN);
        $msg = '<div class="paste-alert alert6" style="text-align:center;">
						Wrong Password
					</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PonePaste - Authenticate</title>
    <link href="css/paste.css" rel="stylesheet">
    <style>
        body {
            background: #F5F5F5;
        }
    </style>
</head>
<body>
<div class="login-form">
    <?php
    if (isset($msg)) {
        echo $msg;
    }
    ?>
    <form action="." method="post">
        <div class="top">
            <h1>PonePaste Admin Authentication</h1>
        </div>
        <div class="form-area">
            <div class="group">
                <input type="text" class="form-control" id="username" name="username" disabled="disabled" value="<?= pp_html_escape($current_user->username); ?>">
                <i class="fa fa-user"></i>
            </div>
            <div class="group">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password"
                       value="">
                <i class="fa fa-key"></i>
            </div>
            <button type="submit" class="btn btn-default btn-block">Authenticate</button>
        </div>
    </form>
</div>
</body>
</html>