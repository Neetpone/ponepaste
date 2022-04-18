<main class="bd-main">
    <div class="bd-side-background"></div>
    <div class="bd-main-container container">
        <div class="bd-duo">
            <div class="bd-lead">
                <?php if (isset($success)): ?>
                    <div class="notification is-success"><i class="fa fa-exclamation-circle" aria-hidden="true"></i><?= pp_html_escape($success); ?></div>
                    <?php if (isset($new_password)): ?>
                        <p>Your new password is as follows:</p>
                        <code><?= pp_html_escape($new_password); ?></code>
                        <br>
                    <?php endif; ?>

                    <?php if (isset($recovery_code)): ?>
                        <br><span class="tag is-danger is-medium">IMPORTANT!</span>
                        <p><b>If you wish to recover your account later, you will need the following code. Store it in a safe place!</b></p>
                        <code id="recovery"><?= pp_html_escape($recovery_code); ?></code>
                        <p>If you do not save this code, and you forget your password, there is no way to get your account back!</p>
                    <?php endif; ?>
                <?php elseif (isset($error)): ?>
                    <div class="notification is-info"><i class="fa fa-exclamation-circle" aria-hidden="true"></i><?= pp_html_escape($error); ?></p></div>
                <?php endif; ?>
                <?php
                // Login page
                if (isset($_GET['login'])) {
                    ?>
                    <form action="/login" method="post">
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
                                        <input class="is-checkradio is-info" id="rememberme" name="remember_me"
                                               type="checkbox" checked="">
                                        <label for="rememberme">
                                            Remember Me
                                        </label>
                                    </div>
                                </div>
                                <div class="field">
                                    <input class="button is-info" type="submit" name="signin" value="Login" />
                                </div>
                                <hr>
                            </div>
                            <div class="column">
                            </div>
                            <div class="column">
                            </div>
                        </div>
                    </form>
                    <?php // Registration page
                } elseif (isset($_GET['registeraccount'])) {
                    ?>
                    <form action="/register" method="post">
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
                                        <span class="tags are-large"><img src="<?= $_SESSION['captcha']['image_src'] ?>" alt="CAPTCHA" class="imagever" /></span>
                                        <input type="text" class="input" name="scode" value=""
                                               placeholder="Enter the CAPTCHA">
                                        <p class="is-size-6	has-text-grey-light has-text-left mt-2">and
                                            press"Enter"</p>
                                    </div>
                                </div>
                                <div class="field">
                                    <input class="button is-info" type="submit" name="signup" value="Register" />
                                </div>
                                <hr>
                            </div>
                            <div class="column">
                            </div>
                            <div class="column">
                            </div>
                        </div>
                        <div class="field">
                            <p style="float:left;">By signing up you agree to our <a href="page/privacy">Privacy
                                    policy </a> and <a href="page/rules">Site rules</a>. This site may contain material
                                not sutible for under 18's</p>
                        </div>
                    </form>
                    <?php // Forgot password
                } elseif (isset($_GET['forgotpassw'])) {
                    ?>
                    <form action="/forgot" method="post">
                        <div class="columns">
                            <div class="column">
                                <h1 class="title is-4">Forgot Password</h1>
                                <p>You <i>did</i> save your recovery code, right?</p>
                                <div class="field">
                                    <label class="label">Username</label>
                                    <div class="control has-icons-left has-icons-right">
                                        <input type="text" class="input" name="username"
                                               placeholder="Enter your account username">
                                        <span class="icon is-small is-left">
											<i class="fas fa-envelope"></i>
										</span>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label">Recovery Code</label>
                                    <div class="control has-icons-left has-icons-right">
                                        <input type="password" class="input" name="recovery_code"
                                               placeholder="Recovery code">
                                        <span class="icon is-small is-left">
											<i class="fas fa-key"></i>
										</span>
                                    </div>
                                </div>
                                <div class="field">
                                    <input class="button" type="submit" name="forgot"/>
                                </div>
                            </div>
                            <div class="column">
                            </div>
                            <div class="column">
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
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</main>