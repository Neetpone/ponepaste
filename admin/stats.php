<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: .");
    exit();
}

if (isset($_GET['logout'])) {
    if (isset($_SESSION['login']))
        unset($_SESSION['login']);

    session_destroy();
    header("Location: .");
    exit();
}

$date = date('jS F Y');
$ip = $_SERVER['REMOTE_ADDR'];
require_once('../includes/config.php');
$con = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);

if (mysqli_connect_errno()) {
    $sql_error = mysqli_connect_error();
    die("Unable connect to database");
}

$query = "SELECT @last_id := MAX(id) FROM admin_history";

$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_array($result)) {
    $last_id = $row['@last_id := MAX(id)'];
}

$query = "SELECT * FROM admin_history WHERE id=" . Trim($last_id);
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_array($result)) {
    $last_date = $row['last_date'];
    $last_ip = $row['ip'];
}

if ($last_ip == $ip) {
    if ($last_date == $date) {

    } else {
        $query = "INSERT INTO admin_history (last_date,ip) VALUES ('$date','$ip')";
        mysqli_query($con, $query);
    }
} else {
    $query = "INSERT INTO admin_history (last_date,ip) VALUES ('$date','$ip')";
    mysqli_query($con, $query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paste - Statistics</title>
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

        <!-- Start Statistics -->
        <?php
        $query = "SELECT * FROM page_view";
        $result = mysqli_query($con, $query);

        while ($row = mysqli_fetch_array($result)) {
            $total_page = isset($total_page) + Trim($row['tpage']);
            $total_un = isset($total_un) + Trim($row['tvisit']);
        }


        $query = "SELECT id, expiry  FROM pastes";
        $result = mysqli_query($con, $query);
        $total_pastes = 0;
        $exp_pastes = 0;
        while ($row = mysqli_fetch_array($result)) {
            $total_pastes = $total_pastes + 1;
            $p_expiry = Trim($row['expiry']);
            if ($p_expiry == "NULL" || $p_expiry == "SELF") {
            } else {
                $input_time = $p_expiry;
                $current_time = mktime(date("H"), date("i"), date("s"), date("n"), date("j"), date("Y"));
                if ($input_time < $current_time) {
                    $exp_pastes = $exp_pastes + 1;
                }
            }
        }

        $query = "SELECT verified FROM users";
        $result = mysqli_query($con, $query);
        $total_users = 0;
        $total_ban = 0;
        $not_ver = 0;
        while ($row = mysqli_fetch_array($result)) {
            $total_users = $total_users + 1;
            $p_v = Trim($row['verified']);
            if ($p_v == '2') {
                $total_ban = $total_ban + 1;
            }
            if ($p_v == '0') {
                $not_ver = $not_ver + 1;
            }
        }
        ?>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-widget">
                    <div class="panel-body">
                        <div class="panel-title">Statistics</div>
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <th>Task</th>
                                <th>Stats</th>
                            </tr>

                            <tr>
                                <td>Total Pastes</td>
                                <td><span class="label label-default"><?php echo $total_pastes; ?></span></td>
                            </tr>

                            <tr>
                                <td>Expired Pastes</td>
                                <td><span class="label label-default"><?php echo $exp_pastes; ?></span></td>
                            </tr>

                            <tr>
                                <td>Total Users</td>
                                <td><span class="label label-default"><?php echo $total_users; ?></span></td>
                            </tr>

                            <tr>
                                <td>Total Banned Users</td>
                                <td><span class="label label-warning"><?php echo $total_ban; ?></span></td>
                            </tr>

                            <tr>
                                <td>Unverified users</td>
                                <td><span class="label label-warning"><?php echo $not_ver; ?></span></td>
                            </tr>

                            <tr>
                                <td>Total Page Views</td>
                                <td><span class="label label-default"><?php echo $total_page; ?></span></td>
                            </tr>

                            <tr>
                                <td>Total Unique Visitors</td>
                                <td><span class="label label-default"><?php echo $total_un; ?></span></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <?php
        $query = "SELECT @last_id := MAX(id) FROM page_view";

        $result = mysqli_query($con, $query);

        while ($row = mysqli_fetch_array($result)) {
            $page_last_id = $row['@last_id := MAX(id)'];
        }
        $query = "SELECT * FROM page_view WHERE id=" . Trim($page_last_id);
        $result = mysqli_query($con, $query);

        while ($row = mysqli_fetch_array($result)) {
            $date = $row['date'];
            $tpage = $row['tpage'];
            $tvisit = $row['tvisit'];
        }
        ?>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-widget">
                    <div class="panel-body">
                        <div class="panel-title">Page Views</a></div>
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <th>Date</th>
                                <th>Unique Visitors</th>
                                <th>Views</th>
                            </tr>
                            <tr>
                                <td><?php echo $date; ?></td>
                                <td><?php echo $tvisit; ?></td>
                                <td><?php echo $tpage; ?></td>
                            </tr>

                            <?php
                            $page_last_id = $page_last_id - 1;
                            $query = "SELECT * FROM page_view WHERE id=" . Trim($page_last_id);
                            $result = mysqli_query($con, $query);

                            while ($row = mysqli_fetch_array($result)) {
                                $date = $row['date'];
                                $tpage = $row['tpage'];
                                $tvisit = $row['tvisit'];
                            }
                            ?>

                            <tr>
                                <td><?php echo $date; ?></td>
                                <td><?php echo $tvisit; ?></td>
                                <td><?php echo $tpage; ?></td>
                            </tr>

                            <?php
                            $page_last_id = $page_last_id - 1;
                            $query = "SELECT * FROM page_view WHERE id=" . Trim($page_last_id);
                            $result = mysqli_query($con, $query);

                            while ($row = mysqli_fetch_array($result)) {
                                $date = $row['date'];
                                $tpage = $row['tpage'];
                                $tvisit = $row['tvisit'];
                            }
                            ?>

                            <tr>
                                <td><?php echo $date; ?></td>
                                <td><?php echo $tvisit; ?></td>
                                <td><?php echo $tpage; ?></td>
                            </tr>

                            <?php
                            $page_last_id = $page_last_id - 1;
                            $query = "SELECT * FROM page_view WHERE id=" . Trim($page_last_id);
                            $result = mysqli_query($con, $query);

                            while ($row = mysqli_fetch_array($result)) {
                                $date = $row['date'];
                                $tpage = $row['tpage'];
                                $tvisit = $row['tvisit'];
                            }
                            ?>

                            <tr>
                                <td><?php echo $date; ?></td>
                                <td><?php echo $tvisit; ?></td>
                                <td><?php echo $tpage; ?></td>
                            </tr>

                            <?php
                            $page_last_id = $page_last_id - 1;
                            $query = "SELECT * FROM page_view WHERE id=" . Trim($page_last_id);
                            $result = mysqli_query($con, $query);

                            while ($row = mysqli_fetch_array($result)) {
                                $date = $row['date'];
                                $tpage = $row['tpage'];
                                $tvisit = $row['tvisit'];
                            }
                            ?>

                            <tr>
                                <td><?php echo $date; ?></td>
                                <td><?php echo $tvisit; ?></td>
                                <td><?php echo $tpage; ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Statistics -->
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

</body>
</html>