<main class="bd-main">
    <div class="bd-side-background"></div>
    <div class="bd-main-container container">
        <div class="bd-duo">
            <div class="bd-lead">
                <h1 class="title is-5"><?php echo $lang['totalpastes'] . ' ' . $total_user_pastes ?></h1>
                <h1 class="subtitle is-6"><?php echo '<a href="user.php?user=' . urlencode($current_user->username) . '" target="_self">' . $lang['mypastes'] . '</a>'; ?></h1>
                <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    if (isset($success)) {
                        echo ' <div class="notification is-success"><i class="fa fa-exclamation-circle" aria-hidden=" true"></i> 
					' . $success . '
					</div>';
                    } elseif (isset($error)) {
                        echo ' <div class="notification is-danger"><i class="fa fa-exclamation-circle" aria-hidden=" true"></i> 
					' . $error . '
					</div>';
                    }
                }
                ?>
                <hr>
                <h1 class="title is-5"><?php echo $lang['myprofile']; ?></h1>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label">Generate New Recovery Key</label>
                                <div class="control has-icons-left has-icons-right">
                                    <input disabled="" type="text" class="input" name="username"
                                           style="cursor:not-allowed;" placeholder="New gen generated here">
                                    <span class="icon is-small is-left">
										<i class="fas fa-user"></i>
									</span>
                                </div>
                            </div>
                            <div class="field">
                                <button disabled type="submit" name="Gen_key" class="button is-info">Generate New Key</button>
                                <br>
                                <small>Coming soon</small>
                            </div>
                            <hr>
                            <div class="field">
                                <label class="label">Username</label>
                                <div class="control has-icons-left has-icons-right">
                                    <input disabled="" type="text" class="input" name="username"
                                           style="cursor:not-allowed;"
                                           placeholder="<?php echo pp_html_escape($current_user->username); ?>">
                                    <span class="icon is-small is-left">
										<i class="fas fa-user"></i>
									</span>
                                </div>
                            </div>
                            <hr>
                            <h1 class="title is-5"><?php echo $lang['chgpwd']; ?></h1>
                            <div class="field">
                                <label class="label">Current Password</label>
                                <div class="control has-icons-left has-icons-right">
                                    <input type="password" class="input" name="old_password"
                                           placeholder="<?php echo $lang['curpwd']; ?>">
                                    <span class="icon is-small is-left">
										<i class="fas fa-key"></i>
									</span>
                                </div>
                            </div>
                            <div class="field">
                                <label class="label">New Password</label>
                                <div class="control has-icons-left has-icons-right">
                                    <input type="password" class="input" name="password"
                                           placeholder="<?php echo $lang['newpwd']; ?>">
                                    <span class="icon is-small is-left">
										<i class="fas fa-key"></i>
									</span>
                                </div>
                            </div>
                            <div class="field">
                                <label class="label">Confirm Password</label>
                                <div class="control has-icons-left has-icons-right">
                                    <input type="password" class="input" name="cpassword"
                                           placeholder="<?php echo $lang['confpwd']; ?>">
                                    <span class="icon is-small is-left">
										<i class="fas fa-key"></i>
									</span>
                                </div>
                            </div>
                            <div class="field">
                                <button type="submit" name="submit" class="button is-info">Submit</button>
                            </div>
                        </div>
                        <div class="column">
                        </div>
                    </div>
                </form>
            </div>
            <?php require_once('theme/' . $default_theme . '/sidebar.php'); ?>
        </div>
    </div>
</main>