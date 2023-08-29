<?php
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/common.php');

use PonePaste\Models\AdminLog;
use PonePaste\Models\User;
use PonePaste\Models\Paste;
use PonePaste\Models\PageView;
use PonePaste\Models\ModMessage;

if (isset($_POST['send_message']) && !empty($_POST['message'])) {
    if (!verifyCsrfToken()) {
        flashError('Invalid CSRF token (do you have cookies enabled?)');
    } else {
        $message = new ModMessage([
            'user_id' => $current_user->id,
            'message' => $_POST['message']
        ]);
        $message->save();
        header('Location: dashboard.php');
        die();
    }
}

$last_page_view = PageView::select('tpage', 'tvisit')
    ->orderBy('id', 'desc')
    ->first();
$today_page = $last_page_view->tpage;
$today_visit = $last_page_view->tvisit;

$admin_email = getSiteInfo()['site_info']['email'];
$c_date = date('jS F Y');

/* Number of users today */
$today_users_count = User::whereDate('created_at', '=', date('Y-m-d'))->count();

/* Number of pastes today */
$today_pastes_count = Paste::whereDate('created_at', '=', date('Y-m-d'))->count();

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

$admin_histories = AdminLog::with('user')
    ->orderBy('id', 'desc')
    ->take(10)
    ->get();

$mod_messages = ModMessage::with('user')
    ->orderBy('created_at', 'desc')
    ->take(20)
    ->get();

$most_recent_users = User::select('id', 'username', 'created_at', 'ip')
    ->orderBy('id', 'desc')
    ->limit(7)
    ->get();

function getRecentadmin($count = 5) {
    return Paste::with('user')
        ->orderBy('id', 'desc')
        ->limit($count)->get();
}

$is_admin = $current_user->role >= User::ROLE_ADMIN;

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
        <?php outputFlashes($flashes); ?>
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
                                <td>Title</td>
                                <td>Created At</td>
                                <td>Views</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $res = getRecentadmin(7);
                            foreach ($res as $paste) {
                                $p_date = new DateTime($paste['created_at']);
                                $p_date_formatted = $p_date->format('jS F Y h:i:s A');
                                $title = truncate($paste->title, 5, 30);
                                ?>
                                <tr>
                                    <td><?= pp_html_escape($paste->user->username); ?></td>
                                    <td>
                                        <a href="<?= urlForPaste($paste); ?>">
                                            <?= pp_html_escape($paste->title); ?>
                                        </a>
                                    </td>
                                    <td><?= pp_html_escape($p_date_formatted); ?></td>
                                    <td><?= pp_html_escape($paste->views || 0); ?></td>
                                </tr>
                            <?php } ?>
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
                                <td>Username</td>
                                <td>Date</td>
                                <td>IP</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($most_recent_users as $user): ?>
                                <tr>
                                    <td>
                                        <a href="<?= urlForMember($user); ?>">
                                            <?= pp_html_escape($user->username); ?>
                                        </a>
                                    </td>
                                    <td><?= pp_html_escape($user->created_at ?? 'Unknown'); ?></td>
                                    <td><?= $is_admin ? pp_html_escape($user->ip ?? 'Unknown') : '[masked]' ?></td>
                                </tr>
                            <?php endforeach; ?>
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
                                <td>Information</td>
                                <td>IP Address</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($admin_histories as $entry): ?>
                                <tr>
                                    <td><?= pp_html_escape($entry->user->username); ?></td>
                                    <td><?= pp_html_escape($entry->time); ?></td>
                                    <td><?= pp_html_escape(AdminLog::ACTION_NAMES[$entry->action]); ?></td>
                                    <td><?= !empty($entry->message) ? pp_html_escape($entry->message) : '[none]' ?></td>
                                    <td><?= $is_admin ? pp_html_escape($entry->ip) : '[masked]' ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <!-- End Admin History -->

            <div class="col-md-12 col-lg-6">
                <div class="panel panel-widget">
                    <div class="panel-title">
                        Mod Chat
                    </div>
                    <div class="panel-body table-responsive">
                        <p>Latest 20 messages:</p>
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <td>Mod</td>
                                <td>Date</td>
                                <td>Message</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($mod_messages as $entry): ?>
                                <tr>
                                    <td><?= pp_html_escape($entry->user->username); ?></td>
                                    <td><?= pp_html_escape($entry->created_at); ?></td>
                                    <td><?= !empty($entry->message) ? pp_html_escape($entry->message) : '[none]' ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <form method="POST" class="form-inline" style="width: 100%;">
                            <input type="hidden" name="csrf_token" value="<?= setupCsrfToken(); ?>" />
                            <input class="form-control" type="text" name="message" maxlength="255" placeholder="Message" style="width: 90%;">
                            <input class="btn btn-primary" type="submit" name="send_message" value="Send" />
                        </form>
                    </div>
                </div>
            </div>
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
