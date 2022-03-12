<?php
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/common.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paste - Users</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="css/paste.css" rel="stylesheet" type="text/css"/>
    <link href="css/datatables.min.css" rel="stylesheet" type="text/css"/>
</head>
<body>

<div id="top" class="clearfix">
    <!-- Start App Logo -->
    <div class="applogo">
        <a href="../" class="logo">Paste</a>
    </div>
    <!-- End App Logo -->

    <!-- Start Top Right -->
    <ul class="top-right">
        <li class="dropdown link">
            <a href="#" data-toggle="dropdown" class="dropdown-toggle profilebox"><b>Admin</b><span
                        class="caret"></span></a>
            <ul class="dropdown-menu dropdown-menu-list dropdown-menu-right">
                <li><a href="admin.php">Settings</a></li>
                <li><a href="?logout">Logout</a></li>
            </ul>
        </li>
    </ul>
    <!-- End Top Right -->
</div>
<!-- END TOP -->

<div class="content">
    <!-- START CONTAINER -->
    <div class="container-widget">
        <?php include 'menu.php'; ?>
        <!-- End Menu -->

        <?php
        if (isset($_GET['delete'])) {
            $user_id = htmlentities(Trim($_GET['delete']));
            $query = "DELETE FROM users WHERE id=$user_id";
            $result = mysqli_query($con, $query);
            if (mysqli_errno($con)) {
                $msg = '<div class="paste-alert alert6" style="text-align: center;">
				 ' . mysqli_error($con) . '
				 </div>';
            } else {
                $msg = '<div class="paste-alert alert3" style="text-align: center;">
					 User deleted
					 </div>';
            }
        }

        if (isset($_GET['ban'])) {
            $ban_id = htmlentities(Trim($_GET['ban']));
            $query = "UPDATE users SET verified='2' WHERE id='$ban_id'";
            $result = mysqli_query($con, $query);
            if (mysqli_errno($con)) {
                $msg = '<div class="paste-alert alert6" style="text-align: center;">
				 ' . mysqli_error($con) . '
				 </div>';
            } else {
                $msg = '<div class="paste-alert alert3" style="text-align: center;">
					 User banned
					 </div>';
            }
        }

        if (isset($_GET['unban'])) {
            $ban_id = htmlentities(Trim($_GET['unban']));
            $query = "UPDATE users SET verified='1' WHERE id='$ban_id'";
            $result = mysqli_query($con, $query);
            if (mysqli_errno($con)) {
                $msg = '<div class="paste-alert alert6" style="text-align: center;">
				 ' . mysqli_error($con) . '
				 </div>';
            } else {
                $msg = '<div class="paste-alert alert3" style="text-align: center;">
					 User unbanned
					 </div>';
            }
        }
        ?>

        <!-- Start Users -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-widget">
                    <?php
                    if (isset($_GET['details'])) {
                        $user = User::find($_GET['details']);
                        $user_date = $row['date'];

                        if ($user->banned) {
                            $user_verified = 'Banned';
                        } elseif ($user->verified) {
                            $user_verified = 'Verified';
                        } else {
                            $user_verified = 'Unverified';
                        }

                        ?>
                        <div class="panel-body">
                            <div class="panel-title">
                                <?= pp_html_escape($user->username) . ' Details'; ?>
                            </div>

                            <table class="table table-striped table-bordered">
                                <tbody>
                                <tr>
                                    <td> Username</td>
                                    <td><?= pp_html_escape($user->username) ?> </td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td><?= $user_verified ?></td>
                                </tr>

                                <tr>
                                    <td> User IP</td>
                                    <td><?= $user->ip ?> </td>
                                </tr>

                                <tr>
                                    <td>Date Registered</td>
                                    <td><?php echo $user_date; ?> </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>

                        <div class="panel-body">
                            <div class="panel-title">
                                Manage Users
                            </div>

                            <?php if (isset($msg)) echo $msg; ?>

                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered"
                                   id="usersTable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Date Registered</th>
                                    <th>Ban User</th>
                                    <th>Profile</th>
                                    <th>Delete</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <!-- End Admin Settings -->
    </div>
    <!-- END CONTAINER -->

    <!-- Start Footer -->
    <div class="row footer">
        <div class="col-md-6 text-left">
            <a href="https://github.com/jordansamuel/PASTE" target="_blank">Updates</a> &mdash; <a
                    href="https://github.com/jordansamuel/PASTE/issues" target="_blank">Bugs</a>
        </div>
        <div class="col-md-6 text-right">
            Powered by <a href="https://phpaste.sourceforge.io" target="_blank">Paste</a>
        </div>
    </div>
    <!-- End Footer -->
</div>
<!-- End content -->

<script type="text/javascript" language="javascript" class="init">
    $(document).ready(function () {
        $('#usersTable').dataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "ajax_users.php",
            "order": [[0, "desc"]]
        });
    });
</script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
</body>
</html>