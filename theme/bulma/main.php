<link rel="stylesheet" href="theme/bulma/css/bulma-tagsinput.min.css"/>
<main class="bd-main">
    <div class="bd-side-background"></div>
    <div class="bd-main-container container">
        <div class="bd-duo">
            <div class="bd-lead">
                <!-- Guests -->
                <?php if ($site_disable_guests) { // Site permissions
                    ?>
                    <section class="hero is-medium">
                        <div class="">
                            <article class="message is-info">
                                <div class="message-header">
                                    <p>Site News:</p>
                                </div>
                                <div class="message-body">
                                    <div class="content is-normal">
                                        <ul>
                                            <li>Ponepaste has now a favorite system. You can now favorite pastes and
                                                bookmark them on your user page under "Favorites"</li>
                                            <li>Report function and UI has been overhauled.</li>
                                            <li>The archive page has now been updated, tags are now clickable for a
                                                faster search.</li>
                                            <li>Tags UI has been overhauled. Tags containing "SAFE" and "NSFW" will
                                                appear green and red.</li>
                                            <li>When Creating paste the tag input box has been updated with a new visual
                                                style.</li>
                                            <li>Tags are now being canonized, if you see your tags change, it's just the
                                                admin working in the background</li>
                                        </ul>
                                    </div>
                                </div>
                            </article>
                            <div class="container">
                                <div class="columns is-multiline is-mobile">
                                    <div class="column">
                                        <div class="panel-body">
                                            <div class="list-widget pagination-content">
                                                <?php
                                                $res = getRandomPastes($conn, 10);
                                                foreach ($res

                                                as $index => $row) {
                                                $title = Trim($row['title']);
                                                $titlehov = ($row['title']);
                                                $p_member = Trim($row['member']);
                                                $p_id = Trim($row['id']);
                                                $p_date = Trim($row['date']);
                                                $p_time = Trim($row['now_time']);
                                                $nowtime = time();
                                                $oldtime = $p_time;
                                                $title = truncate($title, 24, 60);
                                                ?>

                                                <p class="no-margin">
                                                    <?php
                                                    if (PP_MOD_REWRITE) {
                                                        echo '<header class="bd-category-header my-1">
									<a data-tooltip="' . $titlehov . '" href="' . $p_id . '" title="' . $title . '">' . $title . ' </a>
									<a class="icon is-pulled-right has-tooltip-arrow has-tooltip-left-mobile has-tooltip-bottom-desktop has-tooltip-left-until-widescreen" data-tooltip="' . $p_time . '">
										<i class="far fa-clock has-text-grey" aria-hidden="true"></i>
									</a>
									<p class="subtitle is-7">' . 'by ' . '
										<i><a href="https://Ponepaste.org/user/' . $p_member . '">' . $p_member . '</a></i>
									</p>' .
                                                            '</header>';
                                                    } else {
                                                        echo '<a href="' . $p_id . '" title="' . $titlehov . '">' . ucfirst($title) . '</a>';
                                                    }
                                                    }
                                                    ?>

                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="column">
                                        <div class="panel-body">
                                            <div class="list-widget pagination-content">
                                                <?php
                                                $res = getRandomPastes($conn, 10);
                                                foreach ($res

                                                as $index => $row) {
                                                $title = Trim($row['title']);
                                                $titlehov = ($row['title']);
                                                $p_member = Trim($row['member']);
                                                $p_id = Trim($row['id']);
                                                $p_date = Trim($row['date']);
                                                $p_time = Trim($row['now_time']);
                                                $nowtime = time();
                                                $oldtime = $p_time;

                                                $title = truncate($title, 24, 60);
                                                ?>

                                                <p class="no-margin">
                                                    <?php
                                                    if (PP_MOD_REWRITE) {
                                                        echo '<header class="bd-category-header my-1">
									<a data-tooltip="' . $titlehov . '" href="' . $p_id . '" title="' . $title . '">' . $title . ' </a>
									<a class="icon is-pulled-right has-tooltip-arrow has-tooltip-left-mobile has-tooltip-bottom-desktop has-tooltip-left-until-widescreen" data-tooltip="' . $p_time . '">
										<i class="far fa-clock has-text-grey" aria-hidden="true"></i>
									</a>
									<p class="subtitle is-7">' . 'by ' . '
										<i><a href="https://Ponepaste.org/user/' . $p_member . '">' . $p_member . '</a></i>
									</p>' .
                                                            '</header>';
                                                    } else {
                                                        echo '<a href="' . $p_id . '" title="' . $titlehov . '">' . ucfirst($title) . '</a>';
                                                    }
                                                    }
                                                    ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                <?php } else { ?>
                <!-- Paste Panel -->
                <?php if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    if (isset($error)) { ?>
                        <!-- Error Panel -->
                        <i class="fa fa-exclamation-circle" aria-hidden=" true"></i> <?php echo $error; ?>
                    <?php }
                }
                ?>
                <h1 class="subtitle is-4">
                    New Paste
                </h1>
                <form method="POST">
                    <nav class="level">
                        <div class="level-left">
                            <!-- Title -->
                            <div class="level-item is-pulled-left" style="margin-right: 5px;">
                                <p class="control has-icons-left">
                                    <input type="text" class="input" name="title" onchange="getFileName()"
                                           placeholder="Title"
                                           value="<?php echo (isset($_POST['title'])) ? pp_html_escape($_POST['title']) : ''; ?>">
                                    <span class="icon is-small is-left">
											<i class="fa fa-font"></i>
										</span>
                                </p>
                            </div>
                            <!-- Format -->
                            <div class="level-item is-pulled-left mx-1">
                                <div class="select">
                                    <select data-live-search="true" name="format">
                                        <?php
                                        foreach (PP_HIGHLIGHT_FORMATS as $code => $name) {
                                            if (isset($_POST['format'])) {
                                                $sel = ($_POST['format'] == $code) ? 'selected="selected"' : ''; // Pre-populate if we come here on an error
                                            } else {
                                                $sel = ($code == "markdown") ? 'selected="selected"' : '';
                                            }
                                            echo '<option ' . $sel . ' value="' . $code . '">' . $name . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="level-item is-pulled-left mx-1">
                                <a class="button" onclick="highlight(document.getElementById('code')); return false;"><i
                                            class="fas fa-indent"></i>&nbsp;Highlight</a>
                            </div>
                            <div class="level-item is-pulled-left mx-1">
                                <input class="button is-info" type="submit" name="submit" id="submit" value="Paste"/>
                            </div>
                        </div>
                    </nav>
                    <!-- Text area -->
                    <textarea class="textarea" rows="15" id="code" name="paste_data" onkeyup="countChars(this);"
                              onkeydown="return catchTab(this,event)"
                              placeholder="Paste Or Drop Text File Here."><?php echo (isset($_POST['paste_data'])) ? pp_html_escape($_POST['paste_data']) : ''; ?></textarea>
                    <p id="charNum"><b>File Size: </b><span style="color: green;">1000/1000Kb</span></p>
                    <br>
                    <!-- Tag system -->
                    <div class='rows'>
                        <div class='row is-full'>
                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label" for="field_tags">Tags</label>
                                        <div class="control">
                                            <input name="tag_input" class="input js-tag-input" id="field_tags"
                                                   value="<?= (isset($_POST['tag_input'])) ? pp_html_escape($_POST['tag_input']) : ''; ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class='row is-full'>
                        <div class="columns">
                            <div class="column is-5">
                                <nav class="level">
                                    <div class="level-left">
                                        <div class="level-item is-pulled-left mr-1">
                                            <div class="field">
                                                <div class="subtitle has-text-weight-semibold "
                                                     style="margin-left: 2px; margin-bottom: 0.6rem; font-size: 1rem;">Expiry</div>
                                                <div class="control">
                                                    <div class="select">
                                                        <?php
                                                        $post_expire = "";
                                                        if ($_POST) {
                                                            if (isset($_POST['paste_expire_date'])) {
                                                                $post_expire = $_POST['paste_expire_date'];
                                                            }
                                                        }
                                                        ?>
                                                        <select name="paste_expire_date">

                                                        <?= optionsForSelect(
                                                                    ['Never', 'View Once', '10 minutes', '1 hour', '1 day', '1 week', '2 weeks', '1 month'],
                                                                    ['N',     'self',      '10M',        '1H',     '1D',    '1W',     '2W',      '1M'],
                                                                    $post_expire
                                                        ); ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="level-item is-pulled-left mx-1">
                                            <div class="field">
                                                <div class="subtitle has-text-weight-semibold "
                                                     style="margin-left: 2px; margin-bottom: 0.6rem; font-size: 1rem;">Visibility
                                                    &nbsp;&nbsp;
                                                </div>
                                                <div class="control">
                                                    <div class="select">
                                                        <?php
                                                        $post_visibility = "";
                                                        if ($_POST) {
                                                            if (isset($_POST['visibility'])) {
                                                                $post_visibility = $_POST['visibility'];
                                                            }
                                                        }
                                                        ?>
                                                        <select name="visibility">
                                                            <option value="0" <?php echo ($post_visibility == "0") ? 'selected="selected"' : ''; ?>>
                                                                Public
                                                            </option>
                                                            <option value="1" <?php echo ($post_visibility == "1") ? 'selected="selected"' : ''; ?>>
                                                                Unlisted
                                                            </option>
                                                            <?php if ($current_user) { ?>
                                                                <option value="2" <?php echo ($post_visibility == "2") ? 'selected="selected"' : ''; ?>>
                                                                    Private
                                                                </option>
                                                            <?php } else { ?>
                                                                <option disabled>Private</option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </nav>
                                <nav>
                                    <div class="level-left">
                                        <!-- Password -->
                                        <div class="columns">
                                            <div class="column">
                                                <input type="text" class="input" name="pass" id="pass"
                                                       placeholder="Password" autocomplete="new-password"
                                                       value="<?php echo (isset($_POST['pass'])) ? pp_html_escape($_POST['pass']) : ''; ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                </nav>
                                <br>
                                <nav>
                                    <div class="level-left">
                                        <!-- Encrypted -->
                                        <div class="field">
                                            <input class="is-checkradio is-info has-background-color" id="encrypt"
                                                   checked="checked" disabled="disabled" type="checkbox">
                                            <label for="encrypt">
                                                Encrypt on Server (always enabled)
                                            </label>
                                        </div>
                                    </div>
                                </nav>
                            </div>
                            <div class="column is-3">
                            </div>
                            <div class="column is-4">
                                <!-- CAPTCHA -->
                                <?php if ($captcha_config['enabled'] && $current_user === null): ?>
                                    <div class="is-one-quarter">
                                        <div class="captcha_container">
                                            <img src="/captcha?t=<?= setupCaptcha() ?>" alt="CAPTCHA Image" />
                                            <span id="captcha_refresh" style="height: 100%;">
                                                <a href="javascript:void(0)">
                                                    <i class="fa fa-refresh" style="height: 100%;"></i>
                                                </a>
                                            </span>
                                            <input type="text" class="input" name="scode" placeholder="Enter the CAPTCHA" />
                                            <p class="is-size-6	has-text-grey-light has-text-left mt-2">and press "Enter"</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($csrf_token)): ?>
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>" />
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php } ?>
    </div>
</main>

<script>
    function roundToTwo(num) {
        return +(Math.round(num + "e+2") + "e-2");
    }

    function countChars(obj) {
        // True limit
        const maxLength = 1000000;
        const strLength = obj.value.length;
        const charRemain = (maxLength - strLength);
        const char2kb = charRemain / 1000;
        const charDisplay = roundToTwo(char2kb);
        // Grace limit
        const gracelimit = 11480;
        const newstrLength = obj.value.length - 1000000;
        const graceRemain = (gracelimit - newstrLength);
        const grace2kb = graceRemain / 1000;
        const graceDisplay = roundToTwo(grace2kb);

        if (graceRemain < 0) {
            document.getElementById("charNum").innerHTML = '<b>File Size: </b><span style="color: red;">File Size limit reached</span>';
        } else if (charRemain < 0) {
            document.getElementById("charNum").innerHTML = '<b>File Size: </b><span style="color: orange;">' + graceDisplay + '/24Kb Grace Limit</span>';
        } else {
            document.getElementById("charNum").innerHTML = '<b>File Size: </b><span style="color: green;">' + charDisplay + '/1000Kb</span>';
        }
    }
</script>
