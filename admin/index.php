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
define('IN_PONEPASTE', 1);
require_once(__DIR__  . '/../includes/common.php');

$row = $conn->querySelectOne('SELECT user, pass FROM admin LIMIT 1');
$adminid = $row['user'];
$password = $row['pass'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($adminid === trim($_POST['username']) && password_verify($_POST['password'], $password)) {
        $_SESSION['login'] = true;
        header("Location: dashboard.php");
        exit();
    } else {
        $msg = '<div class="paste-alert alert6" style="text-align:center;">
						Wrong User/Password
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
    <title>Paste - Login</title>
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
            <h1>Paste</h1>
        </div>
        <div class="form-area">
            <div class="group">
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="">
                <i class="fa fa-user"></i>
            </div>
            <div class="group">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password"
                       value="">
                <i class="fa fa-key"></i>
            </div>
            <!-- Not yet implemented
              <div class="checkbox checkbox-primary">
                      <input id="rememberme" type="checkbox" checked="">
                      <label for="rememberme"> Remember Me</label>
              </div>
            -->
            <button type="submit" class="btn btn-default btn-block">LOGIN</button>
        </div>
    </form>
</div>
</body>
</html>