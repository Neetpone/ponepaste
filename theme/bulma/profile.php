<main class="bd-main">

    <div class="container">
        <div class="bd-duo">
            <div class="bd-lead">
                <h1 class="title is-5">Total Pastes: <?= $total_user_pastes ?></h1>
                <h1 class="subtitle is-6">
                    <a href="<?= urlForMember($current_user); ?>" target="_self">My Pastes</a>
                </h1>
                <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    if (isset($success)) {
                        echo ' <div class="notification is-success"><i class="fa fa-exclamation-circle" aria-hidden=" rue"></i> 
					' . $success . '
					</div>';
                    } elseif (isset($error)) {
                        echo ' <div class="notification is-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> 
					' . $error . '
					</div>';
                    }
                }
                ?>
                <hr>
                <h1 class="title is-5">My Profile</h1>
                <form method="post">
                    <div class="columns">
                        <div class="column">
                            <hr>
                            <div class="field">
                                <label class="label" for="username">Username</label>
                                <div class="control has-icons-left has-icons-right">
                                    <input disabled="disabled" type="text" class="input" name="username" id="username"
                                           style="cursor:not-allowed;"
                                           placeholder="<?= pp_html_escape($current_user->username); ?>">
                                    <span class="icon is-small is-left">
										<i class="fas fa-user"></i>
									</span>
                                </div>
                            </div>
                            <hr>
                            <h1 class="title is-5">Change Password</h1>
                            <div class="field">
                                <label class="label" for="current_password">Current Password</label>
                                <div class="control has-icons-left has-icons-right">
                                    <input type="password" class="input" name="old_password" id="current_password"
                                           placeholder="Current Password">
                                    <span class="icon is-small is-left">
										<i class="fas fa-key"></i>
									</span>
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="new_password">New Password</label>
                                <div class="control has-icons-left has-icons-right">
                                    <input type="password" class="input" name="password" id="new_password"
                                           placeholder="New Password">
                                    <span class="icon is-small is-left">
										<i class="fas fa-key"></i>
									</span>
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="password_confirmation">Confirm Password</label>
                                <div class="control has-icons-left has-icons-right">
                                    <input type="password" class="input" name="cpassword" id="password_confirmation"
                                           placeholder="Confirm Password"/>
                                    <span class="icon is-small is-left">
										<i class="fas fa-key"></i>
									</span>
                                </div>
                            </div>
                            <div class="field">
                                <?php if (isset($csrf_token)): ?>
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>"/>
                                <?php endif; ?>
                                <button type="submit" name="change_password" class="button is-info">Change Password</button>
                            </div>
                            <div class="field">
                                <label class="label">Generate New Recovery Key</label>
                                <div class="control has-icons-left has-icons-right">
                                    <input disabled="disabled" type="text" class="input"
                                           style="cursor:not-allowed;" placeholder="New key generated here" value="<?= isset($user_new_code) ? pp_html_escape($user_new_code) : '' ?>">
                                    <span class="icon is-small is-left">
										<i class="fas fa-user"></i>
									</span>
                                </div>
                            </div>
                            <div class="field">
                                <button type="submit" name="reset_recovery_code" class="button is-info">Generate new key</button>
                            </div>
                        </div>
                        <div class="column">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
