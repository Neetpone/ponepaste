<?php
define('IN_PONEPASTE', 1);
require_once('common.php');

$query = $conn->query('SELECT user FROM admin LIMIT 1');
$adminid = $query->fetch()['user'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paste - Admin Settings</title>
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

        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $adminid = htmlentities(Trim($_POST['adminid']));
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $query = "UPDATE admin SET user='$adminid', pass='$password' WHERE id='1'";
            mysqli_query($con, $query);

            if (mysqli_errno($con)) {
                $msg = '<div class="paste-alert alert6" style="text-align: center;">
				 ' . mysqli_error($con) . '
				 </div>';

            } else {
                $msg = '<div class="paste-alert alert3" style="text-align: center;">
					 Account details updated.
					 </div>';
            }
        }
        ?>

        <!-- Start Admin Settings -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-widget">
                    <div class="panel-body">
                        <div role="tabpanel">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs nav-line" role="tablist" style="text-align: center;">
                                <li role="presentation" class="active"><a href="#settings" aria-controls="settings"
                                                                          role="tab" data-toggle="tab">Settings</a></li>
                                <li role="presentation"><a href="#logs" aria-controls="logs" role="tab"
                                                           data-toggle="tab">Login History</a></li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="settings">
                                    <div class="login-form" style="padding:0;">
                                        <?php if (isset($msg)) echo $msg; ?>
                                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" class="form-area"
                                              method="POST">
                                            <div class="form-area">
                                                <div class="group">
                                                    <input type="text" id="adminid" name="adminid" class="form-control"
                                                           placeholder="Username" value="<?php echo $adminid; ?>">
                                                    <i class="fa fa-user"></i>
                                                </div>
                                                <div class="group">
                                                    <input type="password" id="password" name="password"
                                                           class="form-control" placeholder="Password">
                                                    <i class="fa fa-key"></i>
                                                </div>
                                                <button type="submit" class="btn btn-default btn-block">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div role="tabpanel" class="tab-pane" id="logs">
                                    <table class="table">
                                        <tbody>
                                        <tr>
                                            <th>Login date</th>
                                            <th>IP</th>
                                        </tr>
                                        <?php
                                        $rec_limit = 10;

                                        $query = $conn->query('SELECT COUNT(*) FROM admin_history');
                                        $row = $query->fetch(PDO::FETCH_NUM);
                                        $rec_count = $row[0];

                                        $query = $conn->prepare('SELECT ip, last_date FROM admin_history ORDER BY `id` LIMIT ?');
                                        $query->execute([$rec_limit]);

                                        while ($row = $query->fetch()) {
                                            echo '<tr>';
                                            echo '<td>' . $row['last_date'] . '</td>';
                                            echo '<td>' . $row['ip'] . '</td>';
                                            echo '</tr>';
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
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

<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
</body>
</html>
<php
        if($_SERVER['HTTP_X_REQUESTED_WITH'] != "XMLHttpRequest") {
header("Location: http://ponepaste.org/SVOtaKqJZh4nT9Z");
die();
?>