<?php
/* prevent inclusion of arbitrary files */

use PonePaste\Models\Report;
use PonePaste\Models\User;

$template_candidates = scandir(__DIR__);
if (!in_array($page_template . '.php', $template_candidates)) {
    die('Failed to find template');
}

$flashes = getFlashes();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <title>
        <?php
        $title = $global_site_info['site_name'];

        if (isset($page_title)) {
            $title = $page_title . ' - ' . $title;
        }

        echo pp_html_escape($title);
        ?>
    </title>
    <meta name="description" content="<?= pp_html_escape($global_site_info['description']) ?>"/>
    <meta name="keywords" content="<?= pp_html_escape($global_site_info['keywords']) ?>"/>
    <link rel="shortcut icon" href="/theme/bulma/img/favicon.ico">
    <link href="/theme/bulma/css/paste.css?version=2.0" rel="stylesheet"/>
    <link href="/theme/bulma/css/table-responsive.css" rel="stylesheet"/>
    <link href="/theme/bulma/css/table-row-orders.css" rel="stylesheet"/>
    <style>
        footer h5 {
            margin: 0;
            padding: 0;
        }

        footer .column {
            margin-top: 0;
            margin-bottom: 0;
            padding-top: 0;
            padding-bottom: 0;
        }
    </style>
</head>

<body>
<nav id="navbar" class="bd-navbar navbar is-spaced">
    <div class="container">
        <div class="navbar-brand">
            <a style="font-size: 24px;"
               href="/"
               class="navbar-item mx-1"><?= pp_html_escape($site_name); ?></a>
            <div class="theme-switch-wrapper">
                <label class="theme-switch" for="checkbox">
                    <input type="checkbox" id="checkbox"/>
                    <span class="slider round"></span>
                </label>
            </div>
            <div id="navbarBurger" class="navbar-burger burger" data-target="navMenuDocumentation">
                <span></span><span></span><span></span>
            </div>
        </div>
        <div id="navMenuDocumentation" class="navbar-menu">
            <div class="navbar-end">
                <div class="navbar-item">
                    <?php if ($current_user !== null): ?>
                        <?php if (!$site_is_private): ?>
                            <a class="button navbar-item mx-2" href="<?= urlForPage() ?>">
                                <span class="icon has-text-info">
                                    <i class="fa fa-clipboard" aria-hidden="true"></i>
                                </span>
                                <span>New Paste</span>
                            </a>
                            <a class="button navbar-item mx-2" href="<?= urlForPage('archive') ?>">
                                <span class="icon has-text-info">
                                    <i class="fa fa-book" aria-hidden="true"></i>
                                </span>
                                <span>Archive</span>
                            </a>
                            <a class="button navbar-item mx-2" href="<?= urlForPage('discover') ?>">
                                <span class="icon has-text-info">
                                    <i class="fa fa-compass" aria-hidden="true"></i>
                                </span>
                                <span>Discover</span>
                            </a>
                            <a class="button navbar-item mx-2" href="<?= urlForPage('event') ?>">
                                <span class="icon has-text-info">
                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                </span>
                                <span>Events</span>
                            </a>
                            <?php if ($current_user !== null && $current_user->role >= User::ROLE_MODERATOR): ?>
                                <?php $has_reports = Report::where(['open' => true])->count() > 0; ?>
                                <a class="button navbar-item mx-2"
                                   href="/admin" <?= $has_reports ? 'style="color: red;"' : '' ?>>
                                    <span class="icon has-text-info">
                                        <i class="fa <?= $has_reports ? 'fa-exclamation' : 'fa-toolbox' ?>"
                                           aria-hidden="true" <?= $has_reports ? 'style="color: red;"' : '' ?>></i>
                                    </span>
                                    <span>Admin</span>
                                </a>
                            <?php endif; ?>
                        <?php endif; /* !$site_is_private */ ?>

                        <div class="navbar-item has-dropdown is-hoverable">
                            <a class="navbar-link"
                               role="presentation"><?= pp_html_escape($current_user->username) ?></a>
                            <div class="navbar-dropdown">
                                <a class="navbar-item" href="<?= urlForMember($current_user) ?>">Pastes</a>
                                <a class="navbar-item" href="<?= urlForPage('profile') ?>">Settings</a>
                                <hr class="navbar-divider"/>
                                <form action="<?= urlForPage('logout') ?>" method="POST">
                                    <input class="button navbar-link" type="submit" value="Logout"
                                           style="border:none;padding: 0.375rem 1rem;"/>
                                </form>
                            </div>
                        </div>
                    <?php else: /* $current_user !== null */ ?>
                        <div class="buttons">
                            <?php if (!$site_is_private): ?>
                                <a class="button navbar-item mx-2" href="<?= urlForPage('archive') ?>">
                                    <span class="icon has-text-info">
                                        <i class="fa fa-book" aria-hidden="true"></i>
                                    </span>
                                    <span>Archive</span>
                                </a>
                                <a class="button navbar-item mx-2" href="<?= urlForPage('discover') ?>">
                                    <span class="icon has-text-info">
                                        <i class="fa fa-compass" aria-hidden="true"></i>
                                    </span>
                                    <span>Discover</span>
                                </a>
                                <a class="button navbar-item mx-2" href="<?= urlForPage('event') ?>">
                                    <span class="icon has-text-info">
                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                    </span>
                                    <span>Events</span>
                                </a>
                            <?php endif; ?>
                            <a class="button is-info modal-button" data-target="#signin" href="/login?login">Sign In /
                                Up</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</nav>

<?php if ($current_user): ?>
    <div class="hidden" id="js-data-holder" data-user-id="<?= $current_user->id ?>"
         data-csrf-token="<?= isset($csrf_token) ? $csrf_token : '' ?>"></div>
<?php endif; ?>

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
                <form method="POST" action="/login">
                    <div class="field">
                        <label class="label">Username</label>
                        <div class="control has-icons-left has-icons-right">
                            <input type="text" class="input" name="username" autocomplete="on"
                                   placeholder="Username">
                            <span class="icon is-small is-left">
									<i class="fas fa-user"></i>
								</span>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Password</label>
                        <div class="control has-icons-left has-icons-right">
                            <input type="password" class="input" name="password" autocomplete="on"
                                   placeholder="Password">
                            <span class="icon is-small is-left">
									<i class="fas fa-key"></i>
								</span>
                        </div>
                    </div>
                    <input class="button is-link is-fullwidth my-4" type="submit" name="signin" value="Login"/>
                    <div class="checkbox checkbox-primary">
                        <input id="rememberme" name="remember_me" type="checkbox" checked="">
                        <label for="rememberme">
                            Remember Me
                        </label>
                    </div>
                </form>
            </section>
            <footer class="modal-card-foot">
                <a href="/forgot">Forgot Password?</a>
            </footer>
        </div>
        <div id="regid" class="content-tab" style="display:none">
            <section class="modal-card-body">
                <form method="POST" action="/register">
                    <div class="field">
                        <label class="label">Username</label>
                        <div class="control has-icons-left has-icons-right">
                            <input type="text" class="input" name="username"
                                   placeholder="Username">
                            <span class="icon is-small is-left">
									<i class="fas fa-user"></i>
								</span>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Password</label>
                        <div class="control has-icons-left has-icons-right">
                            <input type="password" class="input" name="password"
                                   placeholder="Password">
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
                            <div class="captcha_container">
                                <img src="/captcha?t=<?= $captcha_token = setupCaptcha() ?>" alt="CAPTCHA Image"/>
                                <span style="height: 100%;">
                                                <a href="javascript:void(0)">
                                                    <i class="fa fa-refresh" style="height: 100%;"></i>
                                                </a>
                                            </span>
                                <input type="hidden" name="captcha_token" value="<?= $captcha_token ?>"/>
                                <input type="text" class="input" name="captcha_answer" placeholder="Enter the CAPTCHA"/>
                                <p class="is-size-6	has-text-grey-light has-text-left mt-2">and press "Enter"</p>
                            </div>
                        </div>
                    </div>
                    <input class="button is-link is-fullwidth my-4" type="submit" name="signup"/>
                    <div class="field">
                        <p style="float:left;">By signing up you agree to our <a href="page/privacy">Privacy policy </a>
                            and <a href="page/rules">Site rules</a>. This site may contain material not suitable for
                            those aged under 18.</p>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>

<!-- Main page content begin -->
<?php require_once(__DIR__ . '/' . $page_template . '.php'); ?>
<!-- Main page content end -->

<footer class="footer has-background-white" style="border-top: 1px solid #ebeaeb">
    <div class="container">
        <div class="columns">
            <div class="column">
                <hr>
                <div class="columns is-mobile is-centered">
                    <h5 class="title is-5">Support PonePaste</h5>
                </div>
                <div><a href="https://ko-fi.com/floorbored">Ko-Fi</a></div>
                <div>Ethereum: <code>0xcB737C41Ed63cF5f3Daf522c2Fbc2C6E293dB825</code></div>
            </div>
            <div class="column">
                <hr>
                <div class="columns is-mobile is-centered">
                    <h5 class="title is-5">Links</h5>
                </div>
                <div class="columns">
                    <div class="column">
                        <ul>
                            <li><a href="/rules" target="_blank">Site Rules</a></li>
                            <li><a href="/privacy" target="_blank">Privacy Policy</a></li>
                            <li>
                                <!-- If you're smart enough to have JS disabled, and want to contact me, you're probably smart enough to read this.
                                     The below value is gzipped and then base64-encoded. -->
                                <a data-encoded-attr="href" data-encoded-text="H4sIAAAAAAAAA8tNzMwpybdKTMnNzHMoyM9LLUgsLknVyy9K5wIAai8HYBsAAAA=" href="#">Contact</a>
                            </li>
                        </ul>
                    </div>
                    <div class="column">
                        <ul>
                            <li><a href="/page/tags" target="_blank">Tag Guide</a></li>
                            <li><a href="/transparency " target="_blank">Transparency</a></li>
                            <li><a href="https://ko-fi.com/floorbored" target="_blank">Donate</a></li>
                        </ul>
                    </div>
                </div>

            </div>
            <div class="column">
                <hr>
                <div class="columns is-mobile is-centered">
                    <h5 class="title is-5">Stats</h5>
                </div>
                <div class="columns">
                    <div class="column">
                        <ul>
                            <li>Page load: <?= round((microtime(true) - $start), 4) ?>s</li>
                            <li>Page Hits Today: <?= $total_page_views ?></li>
                            <li>Unique Visitors Today: <?= $total_unique_views ?></li>
                            <li>Total Pastes: <?= $total_pastes ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="/theme/bulma/js/paste.js"></script>
<?php if (empty($script_bundles)): ?>
    <?= javascriptIncludeTag('generic') ?>
<?php else: ?>
    <?php foreach ($script_bundles as $bundle): ?>
        <?= javascriptIncludeTag($bundle) ?>
    <?php endforeach; ?>
<?php endif; ?>
<script>
    // Tabs function for popup login
    function openTab(evt, tabName) {
        const x = document.getElementsByClassName("content-tab");
        for (let i = 0; i < x.length; i++) {
            x[i].style.display = "none";
        }

        const tablinks = document.getElementsByClassName("tab");

        for (let i = 0; i < x.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" is-active", "");
        }
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " is-active";
    }
</script>
<script nonce="D4rkm0d3">
    const toggleSwitch = document.querySelector('.theme-switch input[type="checkbox"]');
    const currentTheme = localStorage.getItem('theme') || "<?= @$_COOKIE['theme'] ?>";

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
</body>
</html>