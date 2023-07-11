<link rel="stylesheet" href="theme/bulma/css/bulma-tagsinput.min.css"/>
<main class="container">
    <div class="bd-duo">
        <div class="bd-lead">
            <?php if (isset($global_site_info['banner'])): ?>
                <div class="notification is-primary">
                    <?= $global_site_info['banner'] /* Intentionally not escaped to allow HTML */ ?>
                </div>
            <?php endif; ?>
            <!-- Paste Panel -->
            <?php if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($error)) { ?>
                    <!-- Error Panel -->
                    <i class="fa fa-exclamation-circle" aria-hidden="true"></i> <?php echo $error; ?>
                <?php }
            }
            ?>
            <?php outputFlashes($flashes); ?>
            <h1 class="subtitle is-4">
                New Paste
            </h1>
            <form method="POST">
                <div class="level">
                    <div class="level-left">
                        <!-- Title -->
                        <div class="level-item is-pulled-left" style="margin-right: 5px;">
                            <p class="control has-icons-left">
                                <input type="text" class="input" name="title" placeholder="Title"
                                       value="<?php echo (isset($_POST['title'])) ? pp_html_escape($_POST['title']) : ''; ?>">
                                <span class="icon is-small is-left">
                                        <i class="fa fa-font"></i>
                                    </span>
                            </p>
                        </div>
                        <!-- Format -->
                        <div class="level-item is-pulled-left mx-1">
                            <div class="select">
                                <select name="format">
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
                </div>
                <!-- Text area -->
                <textarea class="textarea" rows="15" id="code" name="paste_data" onkeyup="countChars(this);"
                          onkeydown="return catchTab(this,event)"
                          placeholder="Paste or drop text file here."><?php echo (isset($_POST['paste_data'])) ? pp_html_escape($_POST['paste_data']) : ''; ?></textarea>
                <p id="charNum"><b>File Size: </b><span style="color: green;">1000/1000Kb</span></p>
                <!-- Tag system -->

                <div class="field">
                    <label class="label" for="field_tags">Tags</label>
                    <div class="control">
                        <input name="tag_input" class="input js-tag-input" id="field_tags"
                               value="<?= (isset($_POST['tag_input'])) ? pp_html_escape($_POST['tag_input']) : ''; ?>"/>
                    </div>
                    <p class="help">32 tags maximum.</p>
                </div>
                <!-- This whole hack is just to get the "Expiry" and "Visibility" fields on the same line -->
                <div class="level">
                    <div class="level-left">
                        <div class="level-item is-pulled-left mr-1">
                            <div class="field">
                                <label class="label" for="paste_expire_date">Expiry</label>
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
                                        <select name="paste_expire_date" id="paste_expire_date">
                                            <?= optionsForSelect(
                                                ['Never', 'View Once', '10 minutes', '1 hour', '1 day', '1 week', '2 weeks', '1 month'],
                                                ['N', 'self', '0Y0M0DT0H10M', '1H', '1D', '1W', '2W', '1M'],
                                                $post_expire
                                            ); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="level-item is-pulled-left mx-1">
                            <div class="field">
                                <label class="label" for="visibility">Visibility</label>
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
                                        <select name="visibility" id="visibility">
                                            <option
                                                value="0" <?php echo ($post_visibility == "0") ? 'selected="selected"' : ''; ?>>
                                                Public
                                            </option>
                                            <option
                                                value="1" <?php echo ($post_visibility == "1") ? 'selected="selected"' : ''; ?>>
                                                Unlisted
                                            </option>
                                            <?php if ($current_user) { ?>
                                                <option
                                                    value="2" <?php echo ($post_visibility == "2") ? 'selected="selected"' : ''; ?>>
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
                </div>
                <div class="field">
                    <div class="control">
                        <input type="text" class="input pp-width-auto" name="pass" id="pass"
                               placeholder="Password (optional)<?= $current_user ? ' (must be logged in)': '' ?>" autocomplete="new-password"<?= $current_user ? '' : ' disabled="disabled"' ?>
                               value="<?php echo (isset($_POST['pass'])) ? pp_html_escape($_POST['pass']) : ''; ?>"/>
                    </div>
                </div>

                <div class="field">
                    <div class="control">
                        <input class="is-checkradio is-info has-background-color" id="encrypt"
                               checked="checked" disabled="disabled" type="checkbox">
                        <label for="encrypt">
                            Encrypt on Server (always enabled)
                        </label>
                    </div>
                </div>

                <?php if ($captcha_enabled && $current_user === null): ?>
                    <div class="is-one-quarter">
                        <div class="captcha_container">
                            <img src="/captcha?t=<?= $captcha_token = setupCaptcha() ?>"
                                 alt="CAPTCHA Image"/>
                            <span style="height: 100%;">
                                <a href="javascript:void(0)">
                                    <i class="fa fa-refresh" style="height: 100%;"></i>
                                </a>
                            </span>
                            <input type="hidden" name="captcha_token" value="<?= $captcha_token ?>"/>
                            <input type="text" class="input" name="captcha_answer"
                                   placeholder="Enter the CAPTCHA"/>
                            <p class="is-size-6	has-text-grey-light has-text-left mt-2">and press
                                "Enter"</p>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (isset($csrf_token)): ?>
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>"/>
                <?php endif; ?>
            </form>
        </div>
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
