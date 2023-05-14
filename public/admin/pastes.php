<?php
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/common.php');

use PonePaste\Models\Paste;

list($per_page, $current_page) = pp_setup_pagination();

$total_pastes = Paste::count();
$pastes = Paste::with('user')
    ->orderBy('id', 'desc')
    ->limit($per_page)
    ->offset($current_page * $per_page)
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

        <?php
        if (isset($_GET['delete'])) {
            $delid = htmlentities(Trim($_GET['delete']));
            $query = "DELETE FROM pastes WHERE id=$delid";
            $result = mysqli_query($con, $query);
            if (mysqli_errno($con)) {
                $msg = '<div class="paste-alert alert6" style="text-align: center;">
				 ' . mysqli_error($con) . '
				 </div>';
            } else {
                $msg = '<div class="paste-alert alert3" style="text-align: center;">
					 Paste deleted
					 </div>';
            }

        }
        ?>

        <!-- Start Pastes -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-widget">
                    <div class="panel-body">
                        <div class="panel-title">
                            Manage Pastes
                        </div>

                        <?php if (isset($msg)) echo $msg; ?>

                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered"
                               id="pastesTable">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Username</th>
                                <th>IP</th>
                                <th>Visibility</th>
                                <th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pastes as $paste): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= urlForPaste($paste) ?>">
                                                <?= pp_html_escape($paste->id); ?>
                                            </a>
                                        </td>
                                        <td><?= pp_html_escape($paste->title); ?></td>
                                        <td><?= pp_html_escape($paste->user->username); ?></td>
                                        <td><?= pp_html_escape($paste->ip); ?></td>
                                        <td><?= pp_html_escape($paste->visible); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?= paginate($current_page, $per_page, $total_pastes); ?>

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