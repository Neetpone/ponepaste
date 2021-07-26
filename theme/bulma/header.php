<?php
/*
 * Paste <https://github.com/jordansamuel/PASTE> - Bulma theme
 * Theme by wsehl <github.com/wsehl> (January, 2021)
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
$statrttime = microtime();
$time = explode(' ', $statrttime);
$time = $time[1] + $time[0];
$start = $time;
?>

<!DOCTYPE html>

<html lang="<?php echo basename($default_lang, ".php"); ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php if (isset($p_title)) {
            echo $p_title . ' - ';
        }
        echo $title;
        ?>
    </title>
    <meta name="description" content="<?php echo $des; ?>"/>
    <meta name="keywords" content="<?php echo $keyword; ?>"/>
    <link rel="shortcut icon" href="<?php echo '//' . $baseurl . '/theme/' . $default_theme; ?>/img/favicon.ico">
    <link href="<?php echo '//' . $baseurl . '/theme/' . $default_theme; ?>/css/paste.css" rel="stylesheet"
          type="text/css"/>
    <script type="text/javascript"
            src="<?php echo '//' . $baseurl . '/theme/' . $default_theme; ?>/js/jquery.min.js"></script>
    <script type="text/javascript"
            src="<?php echo '//' . $baseurl . '/theme/' . $default_theme; ?>/js/jquery-ui.min.js"></script>
    <script type="text/javascript"
            src="<?php echo '//' . $baseurl . '/theme/' . $default_theme; ?>/js/paste.js"></script>
    <script type="text/javascript"
            src="<?php echo '//' . $baseurl . '/theme/' . $default_theme; ?>/js/modal-fx.min.js"></script>
    <script type="text/javascript"
            src="<?php echo '//' . $baseurl . '/theme/' . $default_theme; ?>/js/datatables.min.js"></script>
    <?php
    if (isset($ges_style)) {
        echo $ges_style;
    }
    if (isset($_SESSION['captcha_mode']) == "recaptcha") {
        echo "<script src='https://www.google.com/recaptcha/api.js'></script>";
    }
    ?>
</head>

<body>
<nav id="navbar" class="bd-navbar navbar is-spaced">
    <div class="container">
        <div class="navbar-brand">
            <a style="font-size: 24px;"
               href="<?php echo '//' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); ?>"
               class="navbar-item mx-1"><?php echo $site_name; ?></a>
            <div class="theme-switch-wrapper">
                <label class="theme-switch" for="checkbox">
                    <input type="checkbox" id="checkbox"/>
                    <div class="slider round"></div>
            </div>
            <div id="navbarBurger" class="navbar-burger burger" data-target="navMenuDocumentation">
                <span></span><span></span><span></span>
            </div>
        </div>
        <div id="navMenuDocumentation" class="navbar-menu">
            <div class="navbar-end">
                <div class="navbar-item">
                    <?php if ($current_user !== null) {
                        if (!isset($privatesite) || $privatesite !== "on") {
                            if (PP_MOD_REWRITE) {
                                echo '  <a class="button navbar-item mx-2" href="' . '//' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/">
											<span class="icon has-text-info">
												<i class="fa fa-clipboard" aria-hidden="true"></i>
											</span><span>New Paste</span>
                                            </a><a class="button navbar-item mx-2" href="' . '//' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/archive">
											<span class="icon has-text-info">
												<i class="fa fa-book" aria-hidden="true"></i>
											</span>
											<span>Archive</span></a>
                                            <a class="button navbar-item mx-2" href="' . '//' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/discover">
                                                <span class="icon has-text-info">
                                                    <i class="fa fa-compass" aria-hidden="true"></i>
                                                </span>
                                            <span>Discover</span></a>
                                            <a class="button navbar-item mx-2" href="' . '//' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/event">
                                                <span class="icon has-text-info">
                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </span>
                                            <span>Events</span></a>';
                            } else {
                                echo '
											</span>
											<span>Archive</span></a>
                                            <a class="button navbar-item mx-2" href="' . '//' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/archive">
											<span class="icon has-text-info">
												<i class="fa fa-book" aria-hidden="true"></i>
											</span>
											<span>Lightmode</span>
										</a>
                                        <a class="button navbar-item mx-2" href="' . '//' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/discover">
											<span class="icon has-text-info">
												<i class="fa fa-book" aria-hidden="true"></i>
											</span>
											<span>Lightmode</span>
										</a>
                                        <span>Discover</span></a>
                                            <a class="button navbar-item mx-2" href="' . '//' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/event">
                                                <span class="icon has-text-info">
                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </span>
                                        <span>Events</span></a>';
                            }
                        }
                        echo '<div class="navbar-item has-dropdown is-hoverable">
										<a class="navbar-link" role="presentation">' . pp_html_escape($current_user->username) . '</a>
											<div class="navbar-dropdown">';
                        if (PP_MOD_REWRITE) {
                            echo '<a class="navbar-item" href="' . '//' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/user/' . urlencode($current_user->username) . '">Pastes</a>';
                            echo '<a class="navbar-item" href="' . '//' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/profile">Settings</a>';
                        } else {
                            echo '<a class="navbar-item" href="' . '//' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/user.php?user=' . urlencode($current_user->username) . '">Pastes</a>';
                            echo '<a class="navbar-item" href="' . '//' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/profile.php">Settings</a>';
                        }
                        ?>
                        <hr class="navbar-divider"/>
                        <form action="logout.php" method="POST">
                            <input class="button navbar-link" type="submit" value="Logout"
                                   style="border:none;padding: 0.375rem 1rem;"/>
                        </form>
                    <?php } else { ?>
                        <div class="buttons">
                            <?php
                            if (!isset($privatesite) || $privatesite != "on") {
                                if (PP_MOD_REWRITE) {
                                    echo '<a class="button navbar-item mx-2" href="' . '//' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/archive">
											<span class="icon has-text-info">
												<i class="fa fa-book" aria-hidden="true"></i>
											</span>
											<span>Archive</span></a>
                                            
                                            <a class="button navbar-item mx-2" href="' . '//' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/discover">
                                                <span class="icon has-text-info">
                                                    <i class="fa fa-compass" aria-hidden="true"></i>
                                                </span>
											<span>Discover</span></a>
                                            <a class="button navbar-item mx-2" href="' . '//' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/event">
                                                <span class="icon has-text-info">
                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </span>
                                        <span>Events</span>';
                                } else {
                                    echo '<a class="button" href="' . '//' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/discover.php">
											<span class="icon has-text-info">
												<i class="fa fa-book" aria-hidden="true"></i>
											</span>
											<span>Archive</span>
									</a>
                                         <a class="button navbar-item mx-2" href="' . '//' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/event.php">
                                                <span class="icon has-text-info">
                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </span>
                                        <span>Events</span>';
                                }
                            }
                            ?>
                            <a class="button is-info modal-button" data-target="#signin">Sign In</a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</nav>

<div id="#signin" class="modal modal-fx-fadeInScale">
    <div class="modal-background"></div>
    <div class="modal-content modal-card is-tiny">
        <header class="modal-card-head">
            <nav class="tabs" style="margin-bottom: -1.25rem;flex-grow:1;">
                <div class="container">
                    <ul>
                        <li class="tab is-active" onclick="openTab(event,'logid')"><a>Login</a></li>
                        <li class="tab" onclick="openTab(event,'regid')"><a>Register</a></li>
                    </ul>
                </div>
            </nav>
            <button class="modal-button-close delete" aria-label="close"></button>
        </header>
        <div id="logid" class="content-tab">
            <section class="modal-card-body">
                <form method="POST" action="../login.php">
                    <div class="field">
                        <label class="label"><?php echo $lang['username']; ?></label>
                        <div class="control has-icons-left has-icons-right">
                            <input type="text" class="input" name="username" autocomplete="on"
                                   placeholder="<?php echo $lang['username']; ?>">
                            <span class="icon is-small is-left">
									<i class="fas fa-user"></i>
								</span>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label"><?php echo $lang['curpwd']; ?></label>
                        <div class="control has-icons-left has-icons-right">
                            <input type="password" class="input" name="password" autocomplete="on"
                                   placeholder="<?php echo $lang['curpwd']; ?>">
                            <span class="icon is-small is-left">
									<i class="fas fa-key"></i>
								</span>
                        </div>
                    </div>
                    <input class="button is-link is-fullwidth my-4" type="submit" name="signin" value="Login"
                           value="<?php echo md5($date . $ip); ?>">
                    <div class="checkbox checkbox-primary">
                        <input id="rememberme" name="remember_me" type="checkbox" checked="">
                        <label for="rememberme">
                            <?php echo $lang['rememberme']; ?>
                        </label>
                    </div>
                </form>
            </section>
            <footer class="modal-card-foot">
                <a href="../login.php?forgot">Forgot Password?</a>
            </footer>
        </div>
        <div id="regid" class="content-tab" style="display:none">
            <section class="modal-card-body">
                <form method="POST" action="../login.php?register">
                    <div class="field">
                        <label class="label"><?php echo $lang['username']; ?></label>
                        <div class="control has-icons-left has-icons-right">
                            <input type="text" class="input" name="username"
                                   placeholder="<?php echo $lang['username']; ?>">
                            <span class="icon is-small is-left">
									<i class="fas fa-user"></i>
								</span>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label"><?php echo $lang['newpwd']; ?></label>
                        <div class="control has-icons-left has-icons-right">
                            <input type="password" class="input" name="password"
                                   placeholder="<?php echo $lang['newpwd']; ?>">
                            <span class="icon is-small is-left">
									<i class="fas fa-key"></i>
								</span>
                        </div>
                    </div>
                    <div class="field">
                        <div class="checkbox checkbox-primary">
                            <input required id="agecheck" name="agecheck" type="checkbox">
                            <label for="agecheck">
                                I'm over 18.
                            </label>
                        </div>
                        <div class="field">
                            <div class="notification">
                                <span class="tags are-large"><?php echo '<img src="' . $_SESSION['captcha']['image_src'] . '" alt="CAPTCHA" class="imagever">'; ?></span>
                                <input type="text" class="input" name="scode" value=""
                                       placeholder="<?php echo $lang['entercode']; ?>">
                                <p class="is-size-6	has-text-grey-light has-text-left mt-2">and press
                                    "Enter"</p>
                            </div>
                        </div>
                    </div>
                    <input class="button is-link is-fullwidth my-4" type="submit" name="signup" value="Register"
                           value="<?php echo md5($date . $ip); ?>">
                    <div class="field">
                        <p style="float:left;">By signing up you agree to our <a href="page/privacy">Privacy policy </a>
                            and <a href="page/rules">Site rules</a>. This site may contain material not sutible for
                            under 18's</p>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
<script nonce="D4rkm0d3">
    const toggleSwitch = document.querySelector('.theme-switch input[type="checkbox"]');
    const currentTheme = localStorage.getItem('theme');

    if (currentTheme) {
        document.documentElement.setAttribute('data-theme', currentTheme);

        if (currentTheme === 'dark') {
            toggleSwitch.checked = true;
        }
    }

    function switchTheme(e) {
        if (e.target.checked) {
            document.documentElement.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.setAttribute('data-theme', 'light');
            localStorage.setItem('theme', 'light');
        }
    }

    toggleSwitch.addEventListener('change', switchTheme, false);
</script>
