<?php
define('IN_PONEPASTE', 1);
require_once(__DIR__  . '/common.php');

use PonePaste\Models\AdminLog;
use PonePaste\Models\User;
use PonePaste\Models\Paste;
use PonePaste\Models\PageView;

$today_users_count = 0;
$today_pastes_count = 0;

$last_page_view = PageView::select('tpage', 'tvisit')
                          ->orderBy('id', 'desc')
                          ->first();
$today_page = $last_page_view->tpage;
$today_visit = $last_page_view->tvisit;

$admin_email = getSiteInfo()['site_info']['email'];
$c_date = date('jS F Y');

/* Number of users today */
$today_users_count = User::where(['created_at' => 'TODAY()'])->count();

/* Number of pastes today */
$today_pastes_count = Paste::where(['created_at' => 'TODAY()'])->count();


foreach (PageView::orderBy('id', 'desc')->take(7)->get() as $row) {
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

    $ldate[] = $sdate;
    $tpage[] = $row['tpage'];
    $tvisit[] = $row['tvisit'];
}

$admin_histories = AdminLog::with('user')->orderBy('id', 'desc')->take(10)->get();

function getRecentadmin($count = 5) {
    return Paste::with('user')
                ->orderBy('id')
                ->limit($count)->get();
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
                                <td>Username</td>
                                <td>Date</td>
                                <td>IP</td>
                                <td>Views</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $res = getRecentadmin(7);
                            foreach ($res as $paste) {
                                $p_date = new DateTime($paste['created_at']);
                                $p_date_formatted = $p_date->format('jS F Y h:i:s A');
                                $title = truncate($title, 5, 30);
                                echo "
										  <tr>
											<td>$paste->id</td>
											<td>" . pp_html_escape($paste->user->username) . "</td>
											<td>$p_date_formatted</td>
											<td><span class='label label-default'>$paste->ip</span></td>
											<td>$paste->views</td>
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
                            $most_recent_users = User::select('id', 'username', 'date', 'ip')->orderBy('id', 'desc')->limit(7);

                            foreach ($most_recent_users as $user) {
                                echo "
										  <tr>
											<td>$user->id</td>
											<td>" . pp_html_escape($user->username) . "</td>
											<td>$user->date</td>
											<td><span class='label label-default'>$user->ip</span></td>
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
                                <td>Username</td>
                                <td>Date</td>
                                <td>Action</td>
                                <td>IP Address</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($admin_histories as $entry): ?>
                                <tr>
                                    <td><?= pp_html_escape($entry->user->username); ?></td>
                                    <td><?= pp_html_escape($entry->time); ?></td>
                                    <td><?= pp_html_escape(AdminLog::ACTION_NAMES[$entry->action]); ?></td>
                                    <td><?= pp_html_escape($entry->ip); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <!-- End Admin History -->
        </div>
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
