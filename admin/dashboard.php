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

define('IN_ADMIN', 1);
require_once('common.php');

$today_users_count = 0;
$today_pastes_count = 0;

require_once('../includes/functions.php');

updateAdminHistory($conn);

$query = $conn->query("SELECT @last_id := MAX(id) FROM page_view");
$row = $query->fetch(PDO::FETCH_NUM);
$page_last_id = intval($row[0]);


$query = $conn->prepare('SELECT tpage, tvisit FROM page_view WHERE id = ?');
$query->execute([$page_last_id]);

while ($row = $query->fetch()) {
    $today_page = $row['tpage'];
    $today_visit = $row['tvisit'];
}

$admin_email = getSiteInfo()['site_info']['email'];
$c_date = date('jS F Y');

/* Number of users today */
$query = $conn->prepare('SELECT COUNT(*) FROM users WHERE `date` = ?');
$query->execute([$c_date]);
$today_users_count = intval($query->fetch(PDO::FETCH_NUM)[0]);

/* Number of pastes today */
$query = $conn->prepare('SELECT COUNT(*) FROM pastes where s_date = ?');
$query->execute([$c_date]);
$today_pastes_count = intval($query->fetch(PDO::FETCH_NUM)[0]);

for ($loop = 0; $loop <= 6; $loop++) {
    $myid = $page_last_id - $loop;
    $query = $conn->prepare("SELECT date, tpage, tvisit FROM page_view WHERE id = ?");
    $query->execute([$myid]);

    while ($row = $query->fetch()) {
        $sdate = $row['date'];
        $sdate = str_replace(date('Y'), '', $sdate);
        $sdate = str_replace('January', 'Jan', $sdate);
        $sdate = str_replace('February', 'Feb', $sdate);
        $sdate = str_replace('March', 'Mar', $sdate);
        $sdate = str_replace('April', 'Apr', $sdate);
        $sdate = str_replace('August', 'Aug', $sdate);
        $sdate = str_replace('September', 'Sep', $sdate);
        $sdate = str_replace('October', 'Oct', $sdate);
        $sdate = str_replace('November', 'Nov', $sdate);
        $sdate = str_replace('December', 'Dec', $sdate);

        $ldate[$loop] = $sdate;
        $tpage[$loop] = $row['tpage'];
        $tvisit[$loop] = $row['tvisit'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ponepaste - Dashboard</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="css/paste.css" rel="stylesheet" type="text/css"/>
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
        <!-- Start Menu -->
        <?php include 'menu.php'; ?>
        <!-- End Menu -->

        <!-- Start Stats -->
        <div class="row">
            <div class="col-md-12">
                <ul class="panel topstats clearfix">
                    <li class="col-xs-6 col-lg-3">
                        <span class="title"><i class="fa fa-eye"></i> Views</span>
                        <h3><?php echo $today_page; ?></h3>
                        <span class="diff">Today</span>
                    </li>
                    <li class="col-xs-6 col-lg-3">
                        <span class="title"><i class="fa fa-clipboard"></i> Pastes</span>
                        <h3><?php echo $today_pastes_count; ?></h3>
                        <span class="diff">Today</span>
                    </li>
                    <li class="col-xs-6 col-lg-3">
                        <span class="title"><i class="fa fa-users"></i> Users</span>
                        <h3><?php echo $today_users_count; ?></h3>
                        <span class="diff">Today</span>
                    </li>
                    <li class="col-xs-6 col-lg-3">
                        <span class="title"><i class="fa fa-users"></i> Unique Views</span>
                        <h3><?php echo $today_visit; ?></h3>
                        <span class="diff">Today</span>
                    </li>
                </ul>
            </div>
        </div>
        <!-- End Stats -->

        <div class="row">
            <!-- Start Recent -->
            <div class="col-md-12 col-lg-6">
                <div class="panel panel-widget">
                    <div class="panel-title">
                        Recent Pastes
                    </div>

                    <div class="panel-body table-responsive">

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <td>ID</td>
                                <td>Username</td>
                                <td>Date</td>
                                <td>IP</td>
                                <td>Views</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $res = getRecentadmin($conn, 7);
                            foreach ($res as $row) {
                                $title = Trim($row['title']);
                                $p_id = Trim($row['id']);
                                $p_date = Trim($row['s_date']);
                                $p_ip = Trim($row['ip']);
                                $p_member = Trim($row['member']);
                                $p_view = Trim($row['views']);
                                $p_time = Trim($row['now_time']);
                                $nowtime1 = time();
                                $oldtime1 = $p_time;
                                $p_time = conTime($nowtime1 - $oldtime1);
                                $title = truncate($title, 5, 30);
                                echo "
										  <tr>
											<td>$p_id</td>
											<td>$p_member</td>
											<td>$p_date</td>
											<td><span class='label label-default'>$p_ip</span></td>
											<td>$p_view</td>
										  </tr> ";
                            }
                            ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <!-- End Recent -->

            <!-- Start Recent Users -->
            <div class="col-md-12 col-lg-6">
                <div class="panel panel-widget">
                    <div class="panel-title">
                        Recent Users
                    </div>

                    <div class="panel-body table-responsive">

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <td>ID</td>
                                <td>Username</td>
                                <td>Date</td>
                                <td>IP</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $most_recent_users = $conn->query('SELECT id, username, date, ip FROM users ORDER BY id DESC LIMIT 7')->fetchAll();
                            $last_id = intval(
                                $conn->query('SELECT MAX(id) FROM users')->fetch(PDO::FETCH_NUM)[0]
                            );

                            foreach ($most_recent_users as $user) {
                                echo "
										  <tr>
											<td>${user['id']}</td>
											<td>${user['username']}</td>
											<td>${user['date']}</td>
											<td><span class='label label-default'>${user['ip']}</span></td>
										  </tr> ";
                            }

                            ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <!-- End Recent Users -->
        </div>

        <div class="row">
            <!-- Start Admin History -->
            <div class="col-md-12 col-lg-6">
                <div class="panel panel-widget">
                    <div class="panel-title">
                        Admin History
                    </div>

                    <div class="panel-body table-responsive">

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <td>ID</td>
                                <td>Last Login Date</td>
                                <td>IP</td>
                                <td>ID</td>
                                <td>Last Login Date</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $res = getreports($conn, 7);
                            foreach ($res as $row) {
                                $r_paste = Trim($row['p_report']);
                                $r_id = Trim($row['id']);
                                $r_date = Trim($row['t_report']);
                                $m_report = Trim($row['m_report']);
                                $r_reason = Trim($row['rep_reason']);
                                echo '
										  <tr>
											<td>' . $r_id . '</td>
											<td>' . $r_paste . '</td>
											<td>' . $m_report . '</td>
											<td>' . $r_date . '</td>
											<td>' . $r_reason . '</td>
										  </tr> ';
                            }
                            ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <!-- End Admin History -->

            <div class="col-md-12 col-lg-6">
                <div class="panel panel-widget">
                    <div class="panel-title">
                    </div>
                    <p style="height: auto;">
                        <?php
                        $latestversion = file_get_contents('https://raw.githubusercontent.com/jordansamuel/PASTE/releases/version');
                        echo "Latest version: " . $latestversion . "&mdash; Installed version: " . $currentversion;
                        if ($currentversion == $latestversion) {
                            echo '<br />You have the latest version';
                        } else {
                            echo '<br />Your Paste installation is outdated. Get the latest version from <a href="https://sourceforge.net/projects/phpaste/files/latest/download">SourceForge</a>';
                        }
                        ?>

                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- END CONTAINER -->

    <!-- Start Footer -->
    <div class="row footer">
        <div class="col-md-6 text-left">
            <a href="https://github.com/jordansamuel/PASTE" target="_blank">Updates</a> &mdash; <a
                    href="https://github.com/jordansamuel/PASTE/issues" target="_blank">Bugs</a>
        </div>
        <div class="col-md-6 text-right">
            A fork of <a href="https://phpaste.sourceforge.io" target="_blank">Paste</a>
        </div>
    </div>
    <!-- End Footer -->

</div>
<!-- End content -->


<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
</body>
</html>
