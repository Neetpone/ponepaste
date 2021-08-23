<?php
/* prevent inclusion of arbitrary files */
$template_candidates = scandir(__DIR__);
if (!in_array($page_template . '.php', $template_candidates)) {
    die('Failed to find template');
}

//$page_content = ob_get_clean();
$date = time();
$statrttime = microtime();
$time = explode(' ', $statrttime);
$time = $time[1] + $time[0];
$start = $time;
?>

<!DOCTYPE html>
<html lang="<?php echo basename($default_lang, ".php"); ?>">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        <?php
        $title = $global_site_info['title'];

        if (isset($paste_title)) {
            $title = $paste_title . ' - ' . $title;
        }

        echo pp_html_escape($title);
        ?>
    </title>
    <meta name="description" content="<?= pp_html_escape($global_site_info['description']) ?>"/>
    <meta name="keywords" content="<?= pp_html_escape($global_site_info['keywords']) ?>"/>
    <link rel="shortcut icon" href="<?php echo '//' . $baseurl . '/theme/' . $default_theme; ?>/img/favicon.ico">
    <link href="//<?= $baseurl ?>/theme/bulma/css/paste.css" rel="stylesheet" />
    <link href="//<?= $baseurl ?>/theme/bulma/css/table-responsive.css" rel="stylesheet" />
    <link href="//<?= $baseurl ?>/theme/bulma/css/table-row-orders.css" rel="stylesheet" />
    <script src="//<?= $baseurl ?>/theme/bulma/js/jquery.min.js"></script>
    <script src="//<?= $baseurl ?>/theme/bulma/js/jquery-ui.min.js"></script>
    <script src="//<?= $baseurl ?>/theme/bulma/js/paste.js"></script>
    <script src="//<?= $baseurl ?>/theme/bulma/js/modal-fx.min.js"></script>
    <script src="//<?= $baseurl ?>/theme/bulma/js/datatables.min.js"></script>
    <script src=//<?= $baseurl ?>/theme/bulma/js/table-responsive.js"></script>
    <script src="//<?= $baseurl ?>/theme/bulma/js/table-reorder.js"></script>
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
                    <input type="checkbox" id="checkbox" />
                    <div class="slider round"></div>
                </label>
            </div>
            <div id="navbarBurger" class="navbar-burger burger" data-target="navMenuDocumentation">
                <span></span><span></span><span></span>
            </div>
        </div>
        <div id="navMenuDocumentation" class="navbar-menu">
            <div class="navbar-end">
                <div class="navbar-item">
                    <?php if ($current_user !== null) {
                        if (!$site_is_private) {
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
                        <form action="../logout.php" method="POST">
                            <input class="button navbar-link" type="submit" value="Logout"
                                   style="border:none;padding: 0.375rem 1rem;"/>
                        </form>
                    <?php } else { ?>
                        <div class="buttons">
                            <?php
                            if (!$site_is_private) {
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
                <a href="../login.php?forgotpassw">Forgot Password?</a>
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


<!--

CONTENT HERE!

-->

<?php require_once(__DIR__ . '/' . $page_template . '.php'); ?>


<footer class="footer has-background-white" style="border-top: 1px solid #ebeaeb">
    <div class="container">
        <div class="columns">
            <div class="column">
                <hr>
                <div class="columns is-mobile is-centered">
                    <h5 class="title is-5">Support PonePaste</h5>
                </div>
                <a href='https://liberapay.com/Ponepaste/donate' target='_blank'><img src='../img/lib.png'/></a>
                <a href='https://ko-fi.com/V7V02K3I2' target='_blank'><img src='../img/kofi.png'/></a>
            </div>
            <div class="column">
                <hr>
                <div class="columns is-mobile is-centered">
                    <h5 class="title is-5">Links</h5>
                </div>
                <div class="columns">
                    <div class="column">
                        <ul>
                            <li><a href="/page/rules" target="_blank">Site Rules</a></li>
                            <li><a href="/page/privacy" target="_blank">Privacy Policy</a></li>
                            <li><a href="mailto:admin@ponepaste.org">Contact</a></li>
                        </ul>
                    </div>
                    <div class="column">
                        <ul>
                            <li><a href="/page/tags" target="_blank">Tag Guide</a></li>
                            <li><a href="/page/transparency " target="_blank">Transparency</a></li>
                            <li><a href="https://liberapay.com/Ponepaste" target="_blank">Donate </a></li>
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
                            <li> <?php
                                $endtime = microtime();
                                $time = explode(' ', $endtime);
                                $time = $time[1] + $time[0];
                                $finish = $time;
                                $total_time = round(($finish - $start), 4);
                                echo 'Page load: ' . $total_time . 's';
                                ?>
                            </li>
                            <li>
                                <?php echo 'Page Hits Today: ' . $total_page_views . ''; ?>
                            </li>
                            <li>
                                <?php echo 'Unique Visitors Today: ' . $total_unique_views . ''; ?>
                            </li>
                            <li>
                                <?php echo 'Total Pastes: ' . $total_pastes . ''; ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>



<script>
    const whenReady = (callback) => {
        if (document.readyState !== 'loading') {
            callback();
        } else {
            document.addEventListener('DOMContentLoaded', callback);
        }
    };

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

    whenReady(() => {
        // Notifications
        (document.querySelectorAll('.notification .delete') || []).forEach(($delete) => {
            $notification = $delete.parentNode;

            $delete.addEventListener('click', () => {
                $notification.parentNode.removeChild($notification);
            });
        });

        // Hamburger menu
        const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
        if ($navbarBurgers.length > 0) {
            $navbarBurgers.forEach(el => {
                el.addEventListener('click', () => {
                    const target = el.dataset.target;
                    const $target = document.getElementById(target);
                    el.classList.toggle('is-active');
                    $target.classList.toggle('is-active');
                });
            });
        }
    });
</script>
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


<!-- Additional Scripts -->
<?php echo $additional_scripts; ?>

</body>
</html>