<?php
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/common.php');

use PonePaste\Models\Report;

if (isset($_POST['close_report']) && isset($_POST['report_id'])) {
    $report = Report::find((int) $_POST['report_id']);
    if ($report) {
        $report->open = false;
        $report->save();
    }

    flashSuccess('Report has been closed.');
}

$reports_count = Report::count();
$reports = Report::with('paste', 'user')
    ->orderBy('id', 'desc')
    ->get();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paste - Pastes</title>
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
        <!-- Start Menu -->
        <?php include 'menu.php'; ?>
        <!-- End Menu -->

        <!-- Start Pastes -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-widget">
                    <div class="panel-body">
                        <?php outputFlashes($flashes); ?>
                        <div class="panel-title">
                            Reports
                        </div>

                        <?php if (isset($msg)) echo $msg; ?>

                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered"
                               id="pastesTable">
                            <thead>
                            <tr>
                                <th>Time</th>
                                <th>Paste</th>
                                <th>User</th>
                                <th>Report Reason</th>
                                <th>Close Report</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($reports as $report): ?>
                                <tr class="<?= $report->open ? 'success' : 'danger' ?>">
                                    <td><?= pp_html_escape($report->created_at); ?></td>
                                    <td>
                                        <a href="<?= urlForPaste($report->paste); ?>">
                                            <?= pp_html_escape($report->paste->title); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="<?= urlForMember($report->user); ?>">
                                            <?= pp_html_escape($report->user->username); ?>
                                        </a>
                                    </td>
                                    <td><?= pp_html_escape($report->reason); ?></td>
                                    <td>
                                        <?php if ($report->open): ?>
                                            <form method="post">
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?> "/>
                                                <input type="hidden" name="report_id" value="<?= $report->id ?> "/>
                                                <input type="submit" name="close_report" value="Close"
                                                       class="btn btn-danger"/>
                                            </form>
                                        <?php else: ?>
                                            Already Closed
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
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