<?php
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/common.php');

use PonePaste\Models\Page;
use PonePaste\Models\User;

checkAdminAccess(User::ROLE_ADMIN);

$pages = Page::orderBy('page_title', 'asc')->get();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paste - Pages</title>
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
                        <div class="panel-title">
                            Manage Pages
                            <a href="page.php">
                                <button class="btn btn-success" style="float:right">New</button>
                            </a>
                        </div>

                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered"
                               id="pastesTable">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Title (Click to Edit)</th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pages as $page): ?>
                                    <tr>
                                        <td>
                                            <a href="/page/<?= pp_url_escape($page->page_name) ?>">
                                                <?= pp_html_escape($page->page_name); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="page.php?name=<?= pp_url_escape($page->page_name); ?>">
                                                <?= pp_html_escape($page->page_title); ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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