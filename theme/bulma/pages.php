<main class="bd-main">
    <div class="bd-side-background"></div>
    <div class="bd-main-container container">
        <div class="bd-duo">
            <div class="bd-lead">
                <h1 class="title is-4"><?php echo $page_title; ?><h1>
                        <?php
                        if (isset($stats)) {
                            echo $page_content;
                        } else {
                            echo '<p class="help is-danger subtitle is-6">' . $lang['notfound'] . '</p>';
                        }

                        if (isset($site_ads)) {
                            echo $site_ads['ads_2'];
                        }
                        ?>
            </div>
            <?php require_once('theme/' . $default_theme . '/sidebar.php'); ?>
        </div>
    </div>
</main>