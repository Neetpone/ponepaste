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

define('IN_PONEPASTE', 1);
require_once('common.php');

updateAdminHistory($conn);

$row = $conn->query('SELECT text_ads, ads_1, ads_2 FROM ads LIMIT 1')->fetch();

if ($row) {
    $text_ads = trim($row['text_ads']);
    $ads_1 = trim($row['ads_1']);
    $ads_2 = trim($row['ads_2']);
} else {
    $text_ads = '';
    $ads_1 = '';
    $ads_2 = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text_ads = trim($_POST['text_ads']);
    $ads_1 = trim($_POST['ads_1']);
    $ads_2 = trim($_POST['ads_2']);

    $conn->prepare('UPDATE ads SET text_ads = ?, ads_1 = ?, ads_2 = ? WHERE id = 1')->execute([$text_ads, $ads_1, $ads_2]);
    $msg = '<div class="paste-alert alert3">
					 Ads saved
					 </div>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paste - Ads</title>
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
        <!-- Start Ads -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-widget">
                    <div class="panel-body">
                        <div class="panel-title">Manage Ads</a></div>
                        <?php if (isset($msg)) echo $msg; ?>
                        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <div class="control-group">
                                <label class="control-label" for="text_ads">Text Ads</label>
                                <div class="controls">
                                    <textarea placeholder="Ad code" name="text_ads" rows="3"
                                              class="span6"><?php echo $text_ads; ?></textarea>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="ads_1">Image Ad - (Sidebar)</label>
                                <div class="controls">
                                    <textarea placeholder="Ad code" name="ads_1" id="ads_1" rows="3"
                                              class="span6"><?php echo $ads_1; ?></textarea>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="ads_2">Image Ad (Footer)</label>
                                <div class="controls">
                                    <textarea placeholder="Ad code" name="ads_2" id="ads_2" rows="3"
                                              class="span6"><?php echo $ads_2; ?></textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-default">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Ads -->
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