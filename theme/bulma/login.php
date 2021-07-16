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
?>

<main class="bd-main">
    <div class="bd-side-background"></div>
    <div class="bd-main-container container">
        <div class="bd-duo">
            <div class="bd-lead">
                <?php
                // Logged in
                if (isset($success)) {
                    echo '<p class="help is-success subtitle is-6">' . $success . '</p>';
                } // Errors
                elseif (isset($error)) {
                    echo '<p class="help is-danger subtitle is-6">' . $error . '</p>';
                }
                // Login page
                if (isset($_GET['login'])) {
                    ?>
                    <form action="../login.php?login" method="post">
                        <div class="columns">
                            <div class="column">
                                <h1 class="title is-4">Login</h1>
                                <div class="field">
                                    <label class="label">Username</label>
                                    <div class="control has-icons-left has-icons-right">
                                        <input type="text" class="input" name="username" placeholder="Username">
                                        <span class="icon is-small is-left">
											<i class="fas fa-user"></i>
										</span>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label">Password</label>
                                    <div class="control has-icons-left has-icons-right">
                                        <input type="password" class="input" name="password" placeholder="Password">
                                        <span class="icon is-small is-left">
											<i class="fas fa-key"></i>
										</span>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="b-checkbox is-info is-inline">
                                        <input class="is-checkradio is-info" id="rememberme" name="rememberme"
                                               type="checkbox" checked="">
                                        <label for="rememberme">
                                            <?php echo $lang['rememberme']; ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="field">
                                    <input class="button is-info" type="submit" name="signin" value="Login"
                                           value="<?php echo md5($date . $ip); ?>">
                                </div>
                                <hr>
                            </div>
                            <div class="column">
                            </div>
                            <div class="column">
                                <?php
                                if (isset($site_ads) && !isset($_SESSION['username'])) {
                                    echo $site_ads['ads_2'];
                                }
                                ?>
                            </div>
                        </div>
                    </form>
                    <!-- Oauth -->
                    <?php if ($enablegoog == "no") {
                    } else { ?>
                        <div class="control">
                            <a href="oauth/google.php?login">
                                <div class="google-btn">
                                    <div class="google-icon-wrapper">
                                        <img class="google-icon"
                                             src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg"/>
                                    </div>
                                    <p class="btn-text">Sign up with Google</p>
                                </div>
                            </a>
                        </div>
                        <br>
                    <?php }
                    if ($enablefb == "no") {
                    } else { ?>
                        <div class="control">
                            <a class="btn-fb" href="oauth/facebook.php?login">
                                <div class="fb-content">
                                    <div class="logo">
                                        <img src="https://facebookbrand.com/wp-content/uploads/2019/04/f_logo_RGB-Hex-Blue_512.png?w=512&h=512"
                                             alt="" width="32px" height="32px">
                                    </div>
                                    <p>Sign up with Facebook</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>
                    <!-- // -->
                    <?php // Registration page
                } elseif (isset($_GET['registeraccount'])) {
                    ?>
                    <form action="../login.php?registeraccount" method="post">
                        <div class="columns">
                            <div class="column">
                                <h1 class="title is-4">Register</h1>
                                <div class="field">
                                    <label class="label">Username</label>
                                    <div class="control has-icons-left has-icons-right">
                                        <input type="text" class="input" name="username" placeholder="Username"
                                               required>
                                        <span class="icon is-small is-left">
											<i class="fas fa-user"></i>
										</span>
                                    </div>
                                </div>
                                <div class="field mb-4">
                                    <label class="label">Password</label>
                                    <div class="control has-icons-left has-icons-right">
                                        <input type="password" class="input" name="password" placeholder="Password">
                                        <span class="icon is-small is-left">
											<i class="fas fa-key"></i>
										</span>
                                    </div>
                                </div>
                                <div class="checkbox checkbox-primary">
                                    <input required id="agecheck" name="agecheck" type="checkbox">
                                    <label for="agecheck">
                                        I'm over 18.
                                    </label>
                                </div>
                                <div class="field">
                                    <div class="notification">
                                        <span class="tags are-large"><?php echo '<img src="' . $_SESSION['captcha']['image_src'] . '" alt="CAPTCHA" class="imagever">'; ?></span>
                                        <input type="text" class="input" name="scode" value=""  placeholder="<?php echo $lang['entercode']; ?>">
                                        <p class="is-size-6	has-text-grey-light has-text-left mt-2">and press"Enter"</p>
                                    </div>
                                </div>
                                <div class="field">
                                    <input class="button is-info" type="submit" name="signup" value="Register"
                                           value="<?php echo md5($date . $ip); ?>">
                                </div>
                                <hr>
                            </div>
                            <div class="column">
                            </div>
                            <div class="column">
                                <?php
                                if (isset($site_ads) && !isset($_SESSION['username'])) {
                                    echo $site_ads['ads_2'];
                                }
                                ?>
                            </div>
                        </div>
                        <div class="field">
                            <p style="float:left;">By signing up you agree to our <a href="page/privacy">Privacy
                                    policy </a> and <a href="page/rules">Site rules</a>. This site may contain material
                                not sutible for under 18's</p>
                        </div>
                    </form>
                    <!-- Oauth -->
                    <?php if ($enablegoog == "no") {
                    } else { ?>
                        <div class="control">
                            <a href="oauth/google.php?login">
                                <div class="google-btn">
                                    <div class="google-icon-wrapper">
                                        <img class="google-icon"
                                             src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg"/>
                                    </div>
                                    <p class="btn-text">Sign up with Google</p>
                                </div>
                            </a>
                        </div>
                        <br>
                    <?php }
                    if ($enablefb == "no") {
                    } else { ?>
                        <div class="control">
                            <a class="btn-fb" href="oauth/facebook.php?login">
                                <div class="fb-content">
                                    <div class="logo">
                                        <img src="https://facebookbrand.com/wp-content/uploads/2019/04/f_logo_RGB-Hex-Blue_512.png?w=512&h=512"
                                             alt="" width="32px" height="32px">
                                    </div>
                                    <p>Sign up with Facebook</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>
                    <!-- // -->
                    <?php // Forgot password
                } elseif (isset($_GET['forgotpassw'])) {
                    ?>
                    <form action="../login.php?forgotpassw" method="post">
                        <div class="columns">
                            <div class="column">
                                <h1 class="title is-4">Forgot Password</h1>
                                <div class="field">
                                    <label class="label">Email</label>
                                    <div class="control has-icons-left has-icons-right">
                                        <input type="text" class="input" name="email"
                                               placeholder="Enter your email address">
                                        <span class="icon is-small is-left">
											<i class="fas fa-envelope"></i>
										</span>
                                    </div>
                                </div>
                                <div class="field">
                                    <input class="button" type="submit" name="forgot" value="Submit"
                                           value="<?php echo md5($date . $ip); ?>"/>
                                </div>
                            </div>
                            <div class="column">
                            </div>
                            <div class="column">
                                <?php
                                if (isset($site_ads) && !isset($_SESSION['username'])) {
                                    echo $site_ads['ads_2'];
                                }
                                ?>
                            </div>
                        </div>
                    </form>
                <?php } else { ?>
                    <div class="columns">
                        <div class="column">
                            <h1 class="title is-4">Where to?</h1>
                            <a href="login.php?login">Login</a><br/>
                            <a href="login.php?registeraccount">Register</a> <br/>
                            <a href="login.php?forgotpassw">Forgot Password</a><br/>
                        </div>
                        <div class="column">
                        </div>
                        <div class="column">
                            <?php
                            if (isset($site_ads) && !isset($_SESSION['username'])) {
                                echo $site_ads['ads_2'];
                            }
                            ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php require_once('theme/' . $default_theme . '/sidebar.php'); ?>
        </div>
    </div>
</main>