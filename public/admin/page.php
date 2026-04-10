<?php
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/common.php');

use PonePaste\Models\Page;
use PonePaste\Models\User;

checkAdminAccess(User::ROLE_ADMIN);

$page = new Page();
if (!empty($_GET['name'])) {
    $page = Page::where('page_name', $_GET['name'])
                ->first();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyCsrfToken()) {
        $msg = 'Invalid CSRF token (do you have cookies enabled?)';
    } else {
        $page->page_name = trim($_POST['name']);
        $page->page_title = trim($_POST['title']);
        $page->page_content = trim($_POST['content']);

        if (empty($_POST['name'])) {
            $msg = 'A name for the page is required.';
        } else if (empty($_POST['title'])) {
            $msg = 'A title for the page is required.';
        } else if (empty($_POST['content'])) {
            $msg = 'The page content may not be empty.';
        } else {
            $page->save();
            $msg = 'Page successfully saved.';
        }
    }
}
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
                            Edit Page
                        </div>

                        <?php if (isset($msg)) echo $msg; ?>

                        <form method="post">
                            <div class="form-group">
                                <label class="form-label">Name</label>
                                <input class="form-control" type="text" name="name" value="<?= pp_html_escape($page->page_name ?? '') ?>"/>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Title</label>
                                <input class="form-control" type="text" name="title" value="<?= pp_html_escape($page->page_title ?? '') ?>"/>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Contents (HTML)</label>
                                <textarea class="form-control" name="content" rows="50"><?= pp_html_escape($page->page_content ?? '') ?></textarea>
                            </div>
                            <div class="form-group">
                                <?= outputCsrfToken() ?>
                                <input type="submit" class="btn btn-primary" value="Save">
                            </div>
                        </form>
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