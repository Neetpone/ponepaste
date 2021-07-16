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
define('IN_ADMIN', 1);
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

/** @noinspection PhpIncludeInspection */
$current_config = require(CONFIG_FILE_PATH);
$current_site_info = $current_config['site_info'];
$current_permissions = $current_config['permissions'];
$current_mail = $current_config['mail'];

$result = $conn->query('SELECT * FROM captcha WHERE id = 1');

if ($row = $result->fetch()) {
    $cap_e = $row['cap_e'];
    $mode = $row['mode'];
    $mul = $row['mul'];
    $allowed = $row['allowed'];
    $color = $row['color'];
    $recaptcha_sitekey = $row['recaptcha_sitekey'];
    $recaptcha_secretkey = $row['recaptcha_secretkey'];
}

$result = $conn->query("SELECT * FROM mail WHERE id='1'");

if ($row = $result->fetch()) {
    $verification = Trim($row['verification']);
    $smtp_host = Trim($row['smtp_host']);
    $smtp_username = Trim($row['smtp_username']);
    $smtp_password = Trim($row['smtp_password']);
    $smtp_port = Trim($row['smtp_port']);
    $protocol = Trim($row['protocol']);
    $auth = Trim($row['auth']);
    $socket = Trim($row['socket']);
}

/* Update the configuration if necessary */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'manage') {
        $new_site_info = [
            'title' =>  trim($_POST['title']),
            'description' => trim($_POST['description']),
            'baseurl' => trim($_POST['baseurl']),
            'keywords' => trim($_POST['keywords']),
            'site_name' => trim($_POST['site_name']),
            'email' => trim($_POST['email']),
            'google_analytics' => trim($_POST['ga']),
            'additional_scripts' => trim($_POST['additional_scripts'])
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
    } elseif ($action === 'mail') {
        $new_mail = [
            'verification' => trim($_POST['verification']),
            'smtp_host' => trim($_POST['smtp_host']),
            'smtp_port' => trim($_POST['smtp_port']),
            'smtp_user' => trim($_POST['smtp_user']),
            'socket' => trim($_POST['socket']),
            'auth' => trim($_POST['auth']),
            'protocol' => trim($_POST['protocol'])
        ];

        $current_config['mail'] = $new_mail;
        $current_mail = $new_mail;

        updateConfiguration(CONFIG_FILE_PATH, $current_config);

        $msg = '
							<div class="paste-alert alert3" style="text-align: center;">
							Mail settings updated
							</div>';
    }

    if (isset($_POST['cap'])) {
        $query = $conn->prepare(
            'UPDATE captcha SET cap_e = ?, mode = ?, mul = ?, allowed = ?, color = ?, recaptcha_sitekey = ?, recaptcha_secretkey = ? WHERE id = 1'
        );
        $query->execute([
            trim($_POST['cap_e']),
            trim($_POST['mode']),
            trim($_POST['mul']),
            trim($_POST['allowed']),
            trim($_POST['color']),
            trim($_POST['recaptcha_sitekey']),
            trim($_POST['recaptcha_secretkey'])
        ]);
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
                                <li role="presentation"><a href="#mail" aria-controls="mail" role="tab"
                                                           data-toggle="tab">Mail Settings</a></li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="siteinfo">
                                    <form class="form-horizontal" method="POST"
                                          action="<?php echo $_SERVER['PHP_SELF']; ?>">

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label form-label">Site Name</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="site_name"
                                                       placeholder="The name of your site"
                                                       value="<?php echo htmlentities($current_site_info['site_name'], ENT_QUOTES); ?>">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label form-label">Site Title</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="title"
                                                       placeholder="Site title tag"
                                                       value="<?php echo htmlentities($current_site_info['title'], ENT_QUOTES); ?>">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label form-label">Domain name</label>
                                            <div class="col-sm-1" style="padding:5px;">
												<span class="badge">
												<?php if ($_SERVER['HTTPS'] == "on") {
                                                    echo "https://";
                                                } else {
                                                    echo "http://";
                                                } ?>
												</span>
                                            </div>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control" name="baseurl"
                                                       placeholder="eg: pastethis.in (no trailing slash)"
                                                       value="<?php echo htmlentities($current_site_info['baseurl'], ENT_QUOTES); ?>">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label form-label">Site Description</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="description"
                                                       placeholder="Site description"
                                                       value="<?php echo htmlentities($current_site_info['description'], ENT_QUOTES); ?>">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label form-label">Site Keywords</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="keywords"
                                                       placeholder="Keywords (separated by a comma)"
                                                       value="<?php echo htmlentities($current_site_info['keywords'], ENT_QUOTES); ?>">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label form-label">Google Analytics</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="ga"
                                                       value="<?php echo htmlentities($current_site_info['google_analytics'], ENT_QUOTES); ?>">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label form-label">Admin Email</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="email" placeholder="Email"
                                                       value="<?php echo htmlentities($current_site_info['email'], ENT_QUOTES); ?>">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label form-label">Additional Site
                                                Scripts</label>
                                            <div class="col-sm-10">
                                                <textarea class="form-control" id="additional_scripts"
                                                          name="additional_scripts"
                                                          rows="8"><?php echo htmlentities($current_site_info['title'], ENT_QUOTES); ?></textarea>
                                            </div>
                                        </div>

                                        <input type="hidden" name="action" value="manage"/>

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
                                            <input <?php if ($disableguest == "on") echo 'checked="true"'; ?>
                                                    type="checkbox" name="disableguest" id="disableguest">
                                            <label for="disableguest">
                                                Only allow registered users to paste
                                            </label>
                                        </div>

                                        <div class="checkbox checkbox-primary">
                                            <input <?php if ($siteprivate == "on") echo 'checked="true"'; ?>
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
                                          action="<?php echo $_SERVER['PHP_SELF']; ?>">

                                        <div class="checkbox checkbox-primary">
                                            <input <?php if ($cap_e == "on") echo 'checked="true"'; ?> type="checkbox"
                                                                                                       name="cap_e"
                                                                                                       id="cap_e">
                                            <label for="cap_e">Enable Captcha</label>
                                        </div>
                                        <br/>

                                        <div class="form-group row">
                                            <label for="mode" class="col-sm-1 col-form-label">Captcha Type</label>
                                            <select class="selectpicker" name="mode">
                                                <?php
                                                if ($mode == "reCAPTCHA") {
                                                    echo '<option selected="">reCAPTCHA</option>';
                                                } else {
                                                    echo '<option>reCAPTCHA</option>';
                                                }
                                                if ($mode == "Easy") {
                                                    echo '<option selected="">Easy</option>';
                                                } else {
                                                    echo '<option>Easy</option>';
                                                }
                                                if ($mode == "Normal") {
                                                    echo '<option selected="">Normal</option>';
                                                } else {
                                                    echo '<option>Normal</option>';
                                                }
                                                if ($mode == "Tough") {
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
                                            <input <?php if ($mul == "on") echo 'checked="true"'; ?> type="checkbox"
                                                                                                     name="mul"
                                                                                                     id="mul">
                                            <label for="mul">Enable multiple backgrounds</label>
                                        </div>
                                        <br/>
                                        <div class="form-group row">
                                            <label for="allowed" class="col-sm-1 col-form-label">Captcha
                                                Characters</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="allowed" name="allowed"
                                                       placeholder="Allowed Characters" value="<?php echo $allowed; ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="color" class="col-sm-1 col-form-label">Captcha Text
                                                Colour</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="color" name="color"
                                                       placeholder="Captcha Text Colour" value="<?php echo $color; ?>">
                                            </div>
                                        </div>

                                        <hr/>
                                        <div class="panel-title">
                                            reCAPTCHA Settings:
                                        </div>
                                        <div class="form-group row">
                                            <label for="recaptcha_sitekey" class="col-sm-1 col-form-label">Site
                                                Key</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="recaptcha_sitekey" name="recaptcha_sitekey"
                                                       placeholder="Site Key" value="<?php echo $recaptcha_sitekey; ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="recaptcha_secretkey" class="col-sm-1 col-form-label">Secret
                                                Key</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="recaptcha_secretkey" name="recaptcha_secretkey"
                                                       placeholder="Site Key"
                                                       value="<?php echo $recaptcha_secretkey; ?>">
                                            </div>
                                        </div>

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
        var smtppassword = document.getElementById('smtp_pass');
        smtppassword.setAttribute('type', 'text');
    }

    function hide() {
        var smtppassword = document.getElementById('smtp_pass');
        smtppassword.setAttribute('type', 'password');
    }

    if ($('#smtppasstoggle').is(':checked')) {
        show();
    } else {
        hide();
    }
</script>

</body>
</html>
