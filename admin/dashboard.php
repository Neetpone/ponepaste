<?php
define('IN_PONEPASTE', 1);
require_once(__DIR__  . '/common.php');
use PonePaste\Models\User;
use PonePaste\Models\Paste;
use PonePaste\Models\PageView;

$today_users_count = 0;
$today_pastes_count = 0;

$query = $conn->query("SELECT @last_id := MAX(id) FROM page_view");
$row = $query->fetch(PDO::FETCH_NUM);
$page_last_id = intval($row[0]);


$query = $conn->prepare('SELECT tpage, tvisit FROM page_view ORDER BY id DESC LIMIT 1');
$query->execute();
$row = $query->fetch();
$last_page_view = PageView::select('tpage', 'tvisit')
                          ->orderBy('id', 'desc')
                          ->first();
$today_page = $last_page_view->tpage;
$today_visit = $last_page_view->tvisit;

$admin_email = getSiteInfo()['site_info']['email'];
$c_date = date('jS F Y');

/* Number of users today */
$query = $conn->prepare('SELECT COUNT(*) FROM users WHERE `date` = ?');
$query->execute([$c_date]);
$today_users_count = intval($query->fetch(PDO::FETCH_NUM)[0]);

/* Number of pastes today */
$query = $conn->query('SELECT COUNT(*) FROM pastes where DATE(created_at) = DATE(NOW())');
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
                                <td>ID</td>
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
                        <br/>You have the latest version

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

</body>
</html>
