<?php

use PonePaste\Models\User;

define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/common.php');

checkAdminAccess(User::ROLE_ADMIN);

list($per_page, $current_page) = pp_setup_pagination();

$total_users = User::count();
$all_users = User::limit($per_page)->offset($current_page * $per_page)->get();
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
                                    <th>Username</th>
                                    <th>Date Registered</th>
                                    <th>Ban User</th>
                                    <th>Profile</th>
                                    <th>Delete</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($all_users as $user): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= urlForMember($user); ?>"><?= pp_html_escape($user->username); ?></a>
                                            </td>
                                            <td><?= pp_html_escape($user->created_at); ?> </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?= paginate($current_page, $per_page, $total_users); ?>
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
    </div>
    <!-- End Footer -->
</div>
<!-- End content -->

</body>
</html>