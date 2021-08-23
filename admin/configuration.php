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

const CONFIG_FILE_PATH = '../config/site.php';


updateAdminHistory($conn);

function updateConfiguration(string $path, array $new_config) {
    $fp = fopen($path, 'w');

    $new_config_text = var_export($new_config, true);
    $code = "<?php\n/* This file has been machine-generated, but is human-editable if you so desire. */\nreturn $new_config_text;";

    fwrite($fp, $code);

    fclose($fp);
}

$current_config = require(CONFIG_FILE_PATH);
$current_site_info = $current_config['site_info'];
$current_permissions = $current_config['permissions'];
$current_mail = $current_config['mail'];
$current_captcha = $current_config['captcha'];

/* Update the configuration if necessary */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'site_info') {
        $data = $_POST['site_info'];
        $new_site_info = [
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'baseurl' => trim($data['baseurl']),
            'keywords' => trim($data['keywords']),
            'site_name' => trim($data['site_name']),
            'email' => trim($data['email']),
            'additional_scripts' => trim($data['additional_scripts'])
        ];

        $current_config['site_info'] = $new_site_info;
        $current_site_info = $new_site_info;

        updateConfiguration(CONFIG_FILE_PATH, $current_config);
        $msg = '<div class="paste-alert alert3" style="text-align: center;">
									Configuration saved.
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

                        <div role="tabpanel">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs nav-line" role="tablist" style="text-align: center;">
                                <li role="presentation" class="active"><a href="#siteinfo" aria-controls="siteinfo"
                                                                          role="tab" data-toggle="tab">Site Info</a>
                                </li>
                                <li role="presentation"><a href="#permissions" aria-controls="permissions" role="tab"
                                                           data-toggle="tab">Permissions</a></li>
                                <li role="presentation"><a href="#captcha" aria-controls="captcha" role="tab"
                                                           data-toggle="tab">Captcha Settings</a></li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="siteinfo">
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
                                            <label class="col-sm-2 control-label form-label" for="site_info_baseurl">Domain
                                                name</label>
                                            <div class="col-sm-1" style="padding:5px;">
												<span class="badge">
                                                    <?= !empty($_SERVER['HTTPS']) ? 'https://' : 'http://' ?>;
												</span>
                                            </div>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control" name="site_info[baseurl]"
                                                       id="site_info_baseurl"
                                                       placeholder="eg: ponepaste.org (no trailing slash)"
                                                       value="<?= pp_html_escape($current_site_info['baseurl']); ?>">
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

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label form-label"
                                                   for="site_info_additional_scripts">Additional Site
                                                Scripts</label>
                                            <div class="col-sm-10">
                                                <textarea class="form-control" id="additional_scripts"
                                                          id="site_info_additional_scripts"
                                                          name="site_info[additional_scripts]"
                                                          rows="8"><?= pp_html_escape($current_site_info['title']); ?></textarea>
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

                                <!-- Permissions -->

                                <div role="tabpanel" class="tab-pane" id="permissions">
                                    <form class="form-horizontal" method="POST"
                                          action="<?php echo $_SERVER['PHP_SELF']; ?>">

                                        <div class="checkbox checkbox-primary">
                                            <input <?php if ($site_disable_guests) echo 'checked="true"'; ?>
                                                    type="checkbox" name="disableguest" id="disableguest">
                                            <label for="disableguest">
                                                Only allow registered users to paste
                                            </label>
                                        </div>

                                        <div class="checkbox checkbox-primary">
                                            <input <?php if ($site_is_private) echo 'checked="true"'; ?>
                                                    type="checkbox" name="siteprivate" id="siteprivate">
                                            <label for="siteprivate">
                                                Make site private (no Recent Pastes or Archives)
                                            </label>
                                        </div>

                                        <br/>

                                        <input type="hidden" name="permissions" value="permissions"/>

                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10">
                                                <button type="submit" class="btn btn-default">Save</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- Captcha pane -->

                                <div role="tabpanel" class="tab-pane" id="captcha">
                                    <form class="form-horizontal" method="POST"
                                          action="<?= $_SERVER['PHP_SELF']; ?>">

                                        <div class="checkbox checkbox-primary">
                                            <input <?php if ($current_captcha['enabled']) echo 'checked="true"'; ?>
                                                    type="checkbox"
                                                    name="captcha[enabked]"
                                                    id="captcha_enabled">
                                            <label for="captcha_enabled">Enable Captcha</label>
                                        </div>
                                        <br/>

                                        <div class="form-group row">
                                            <label for="captcha_mode" class="col-sm-1 col-form-label">Captcha
                                                Type</label>
                                            <select id="captcha_mode" class="selectpicker" name="captcha[mode]">
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
                            </div>
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
<script type="text/javascript" src="js/bootstrap-select.js"></script>

<script>
    function show() {
        document.getElementById('smtp_pass').setAttribute('type', 'text');
    }

    function hide() {
        document.getElementById('smtp_pass').setAttribute('type', 'password');
    }

    if (document.getElementById('smtppasstoggle').hasAttribute('checked')) {
        show();
    } else {
        hide();
    }
</script>

</body>
</html>
