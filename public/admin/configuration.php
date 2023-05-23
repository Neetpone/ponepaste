<?php

use PonePaste\Models\AdminLog;
use PonePaste\Models\User;

define('IN_PONEPASTE', 1);
require_once('common.php');

checkAdminAccess(User::ROLE_ADMIN);

const CONFIG_FILE_PATH = '../../config/site.php';

function updateConfiguration(string $path, array $new_config) : void {
    $fp = fopen($path, 'w');

    $new_config_text = var_export($new_config, true);
    $code = "<?php\n/* This file has been machine-generated, but is human-editable if you so desire. */\nreturn $new_config_text;";

    fwrite($fp, $code);

    fclose($fp);
}

$current_config = require(CONFIG_FILE_PATH);
$current_site_info = $current_config['site_info'];
$current_allowed_image_hosts = $current_config['allowed_image_hosts'];
$current_permissions = $current_config['permissions'];
$current_captcha = $current_config['captcha'];

/* Update the configuration if necessary */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'site_info') {
        $data = $_POST['site_info'];
        $new_site_info = [
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'keywords' => trim($data['keywords']),
            'site_name' => trim($data['site_name']),
            'email' => trim($data['email'])
        ];

        $current_config['site_info'] = $new_site_info;
        $current_site_info = $new_site_info;

        updateConfiguration(CONFIG_FILE_PATH, $current_config);
        $msg = '<div class="paste-alert alert3" style="text-align: center;">
									Configuration saved.
									</div>';
    } elseif ($action === 'allowed_image_hosts') {
        $new_allowed_image_hosts = array_filter($_POST['allowed_image_hosts'], function ($host) {
            return !empty($host);
        });
        $current_config['allowed_image_hosts'] = $new_allowed_image_hosts;
        $current_allowed_image_hosts = $new_allowed_image_hosts;

        updateConfiguration(CONFIG_FILE_PATH, $current_config);
        $msg = '<div class="paste-alert alert3" style="text-align: center;">
                                    Allowed image hosts saved.
                                    </div>';
    } elseif ($action === 'permissions') {
        $new_permissions = [
            'disable_guest' => trim($_POST['disableguest']),
            'private' => trim($_POST['siteprivate'])
        ];
        $current_config['permissions'] = $new_permissions;
        $current_permissions = $new_permissions;

        updateConfiguration(CONFIG_FILE_PATH, $current_config);

        $msg = '<div class="paste-alert alert3" style="text-align: center;">
									Site permissions saved.
									</div>';
    } elseif ($action === 'captcha') {
        $new_captcha = [
            'enabled' => ($_POST['captcha']['enabled'] === '1'),
            'multiple' => ($_POST['captcha']['multiple'] === '1')
        ];

        $current_config['captcha'] = $new_captcha;
        $current_captcha = $new_captcha;

        updateConfiguration(CONFIG_FILE_PATH, $current_config);

        $msg = '<div class="paste-alert alert3" style="text-align: center;">
									Captcha settings saved
									</div>';
    }

    updateAdminHistory($current_user, AdminLog::ACTION_EDIT_CONFIG);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paste - Configuration</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="css/paste.css" rel="stylesheet" type="text/css"/>
    <style>

    </style>
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

        <!-- Start Configuration Panel -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-widget">
                    <div class="panel-body">
                        <?php if (isset($msg)) echo $msg; ?>

                        <div class="tabs">
                            <div class="tabs__content">
                                <h2>General Settings</h2>
                                <form class="form-horizontal" method="POST"
                                      action="<?= $_SERVER['PHP_SELF']; ?>">

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label form-label" for="site_info_name">Site
                                            Name</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="site_info[site_name]"
                                                   id="site_info_name"
                                                   placeholder="The name of your site"
                                                   value="<?= pp_html_escape($current_site_info['site_name']); ?>">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label form-label" for="site_info_title">Site
                                            Title</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="site_info[title]"
                                                   id="site_info_title"
                                                   placeholder="Site title tag"
                                                   value="<?= pp_html_escape($current_site_info['title']); ?>">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label form-label"
                                               for="site_info_description">Site Description</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="site_info[description]"
                                                   id="site_info_description"
                                                   placeholder="Site description"
                                                   value="<?= pp_html_escape($current_site_info['description']); ?>">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label form-label" for="site_info_keywords">Site
                                            Keywords</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="site_info[keywords]"
                                                   id="site_info_keywords"
                                                   placeholder="Keywords (separated by a comma)"
                                                   value="<?= pp_html_escape($current_site_info['keywords']); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label form-label" for="site_info_email">Admin
                                            Email</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="site_info[email]"
                                                   placeholder="Email" id="site_info_email"
                                                   value="<?= pp_html_escape($current_site_info['email']); ?>">
                                        </div>
                                    </div>

                                    <input type="hidden" name="action" value="site_info"/>

                                    <div class="form-group">
                                        <div class="col-sm-offset-2 col-sm-10">
                                            <button type="submit" class="btn btn-default">Save</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Allowed Image Hosts -->
                            <h2>Allowed Image Hosts</h2>
                            <div class="tabs__content">
                                <form class="form-horizontal" method="POST"
                                      action="<?= $_SERVER['PHP_SELF']; ?>">

                                    <?php foreach ($current_allowed_image_hosts as $index => $host): ?>
                                        <div class="form-group">

                                            <label class="col-sm-2 control-label form-label">Allowed Image Host
                                                #<?= $index ?></label>

                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="allowed_image_hosts[]"
                                                       placeholder="URL..."
                                                       value="<?= pp_html_escape($host); ?>">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label form-label">Add another?</label>

                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="allowed_image_hosts[]"
                                                   placeholder="Add another...">
                                        </div>
                                    </div>


                                    <input type="hidden" name="action" value="allowed_image_hosts"/>

                                    <div class="form-group">
                                        <div class="col-sm-offset-2 col-sm-10">
                                            <button type="submit" class="btn btn-default">Save</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- CAPTCHA -->
                            <div class="tabs__content">

                                <h2>CAPTCHA</h2>
                                <form class="form-horizontal" method="POST"
                                      action="<?= $_SERVER['PHP_SELF']; ?>">

                                    <div class="form-group">

                                        <div class="checkbox checkbox-primary">
                                            <input <?php if ($current_captcha['enabled']) echo 'checked="true"'; ?>
                                                type="checkbox"
                                                name="captcha[enabked]"
                                                id="captcha_enabled">
                                            <label for="captcha_enabled">Enable Captcha</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label form-label" for="captcha_mode">Captcha
                                            Type</label>
                                        <select class="form-control col-sm-10" id="captcha_mode" class="" name="captcha[mode]">
                                            <?php
                                            if ($current_captcha['mode'] == "Easy") {
                                                echo '<option selected="">Easy</option>';
                                            } else {
                                                echo '<option>Easy</option>';
                                            }

                                            if ($current_captcha['mode'] == "Normal") {
                                                echo '<option selected="">Normal</option>';
                                            } else {
                                                echo '<option>Normal</option>';
                                            }

                                            if ($current_captcha['mode'] == "Tough") {
                                                echo '<option selected="">Tough</option>';
                                            } else {
                                                echo '<option>Tough</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <hr/>
                                    <div class="panel-title">
                                        Internal Captcha Settings:
                                    </div>
                                    <div class="checkbox checkbox-primary">
                                        <input <?php if ($current_captcha['multiple']) echo 'checked="checked"'; ?>
                                            type="checkbox"
                                            name="captcha[multiple]"
                                            id="captcha_multiple">
                                        <label for="captcha_multiple">Enable multiple backgrounds</label>
                                    </div>
                                    <br/>
                                    <div class="form-group row">
                                        <label for="captcha_allowed" class="col-sm-1 col-form-label">Captcha
                                            Characters</label>
                                        <div class="col-sm-10">
                                            <input type="text" id="captcha_allowed" name="captcha[allowed]"
                                                   placeholder="Allowed Characters"
                                                   value="<?php echo $current_captcha['allowed']; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="captcha_colour" class="col-sm-1 col-form-label">Captcha Text
                                            Colour</label>
                                        <div class="col-sm-10">
                                            <input type="text" id="captcha_colour" name="captcha[colour]"
                                                   placeholder="Captcha Text Colour"
                                                   value="<?= $current_captcha['colour']; ?>">
                                        </div>
                                    </div>

                                    <hr/>

                                    <input type="hidden" name="cap" value="cap"/>

                                    <div class="form-group">
                                        <div class="col-sm-offset-2 col-sm-10">
                                            <button type="submit" class="btn btn-default">Save</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <h2>Permissions</h2>
                            <form class="form-horizontal" method="POST"
                                  action="<?php echo $_SERVER['PHP_SELF']; ?>">

                                <div class="checkbox checkbox-primary">
                                    <input <?php if ($site_disable_guests) echo 'checked="true"'; ?>
                                        type="checkbox" name="disableguest" id="disableguest">
                                    <label for="disableguest">
                                        Only allow registered users to paste
                                    </label>
                                </div>

                                <div class="form-group">

                                    <div class="checkbox checkbox-primary">
                                        <input <?php if ($site_is_private) echo 'checked="true"'; ?>
                                            type="checkbox" name="siteprivate" id="siteprivate">
                                        <label for="siteprivate">
                                            Make site private (no Recent Pastes or Archives)
                                        </label>
                                    </div>
                                </div>


                                <input type="hidden" name="permissions" value="permissions"/>

                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" class="btn btn-default">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Configuration Panel -->
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
