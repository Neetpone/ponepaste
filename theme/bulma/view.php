<link rel="stylesheet" href="theme/bulma/css/bulma-tagsinput.min.css"/>
<script>
    function openreport() {
        const x = document.getElementById("panel");
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    }

    function closereport() {
        const x = document.getElementById("panel");
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    }
</script>
<?php if ($using_highlighter): ?>
    <link rel="stylesheet" href="/vendor/scrivo/highlight.php/styles/default.css"/>
<?php endif; ?>
<style>
    #stop-scrolling {
        height: 100% !important;
        overflow: hidden !important;
    }
</style>
<main class="bd-main" id="stop-scrolling">
    <div class="container">
        <div class="bd-duo">
            <div class="bd-lead">
                <div class="content panel">
                    <?php outputFlashes($flashes) ?>
                    <div class="columns is-multiline">
                        <div class="column is-4">
                            <span class="tag is-normal"><i class="fa fa-code fa-lg"
                                                           aria-hidden="true"></i>&nbsp;&nbsp;<?= strtoupper(pp_html_escape($paste['code'])); ?></span>
                            <span class="tag is-normal"><i class="fa fa-eye fa-lg"
                                                           aria-hidden="true"></i>&nbsp;&nbsp;<?= pp_html_escape($paste['views']); ?></span>
                            <span class="tag is-normal"><i class="fa fa-star fa-lg"
                                                           aria-hidden="true"></i>&nbsp;&nbsp;<?= pp_html_escape($fav_count); ?></span>
                            <br>
                            <span class="tag is-normal">
                                <i class="fa fa-file-word fa-lg" aria-hidden="true"></i>
                                &nbsp;&nbsp;
                                <?= str_word_count($op_content); ?>
                            </span>
                            <span class="tag is-normal">
                                <i class="fa fa-hdd fa-lg" aria-hidden="true"></i>
                                <?= formatBytes(strlen($op_content)) ?>
                            </span>
                            <span class="tag is-normal">
                                <i class="fa fa-list-ol fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;
                                <?= substr_count($op_content, "\n") + 1; ?>
                            </span>
                        </div>
                        <div class="column is-4 has-text-centered">
                            <h1 class="title is-6" style="margin-bottom:0;"><?= pp_html_escape($paste->title); ?></h1>
                            <small class="title is-6 has-text-weight-normal has-text-grey">
                                By <a
                                    href="<?= urlForMember($paste->user) ?>"><?= pp_html_escape($paste->user->username) ?></a>
                                <br/>
                                Created: <?= $paste->created_at ?>
                                <br/>
                                <?php if ($paste->updated_at !== null && $paste->updated_at !== $paste->created_at): ?>
                                    Updated: <?= $paste->updated_at ?>
                                    <br/>
                                <?php endif; ?>
                                Expiry: <?= $paste->expiryDisplay() ?>
                            </small>
                        </div>
                        <div class="column is-4 has-text-right">
                            <div class="">
                                <div class="panel-tools">
                                    <?php if ($current_user !== null): ?>
                                        <form method="POST" class="form--inline">
                                            <?php if (isset($csrf_token)): ?>
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>"/>
                                            <?php endif; ?>
                                            <input type="hidden" name="fave" value="1"/>
                                            <button type="submit" class="icon tool-icon button--no-style"><i
                                                    class="fas fa-star fa-lg <?= $paste_is_favourited ? '' : 'has-text-grey' ?>"
                                                    title="Favourite"></i></button>
                                        </form>
                                    <?php endif; ?>
                                    <a class="icon tool-icon flip" href="<?= urlForReport($paste); ?>"><i
                                            class="far fa-flag fa-lg has-text-grey" title="Report Paste"></i></a>
                                    <?php if ($paste['code'] != "pastedown") { ?>
                                        <a class="icon tool-icon" href="javascript:togglev();"><i
                                                class="fas fa-list-ol fa-lg has-text-grey"
                                                title="Toggle Line Numbers"></i></a>
                                    <?php } ?>
                                    <a class="icon tool-icon" href="#" onclick="selectText('paste');"><i
                                            class="far fa-clipboard fa-lg has-text-grey"
                                            title="Select Text"></i></a>
                                    <a class="icon tool-icon" href="<?php echo $p_raw; ?>"><i
                                            class="far fa-file-alt fa-lg has-text-grey" title="View Raw"></i></a>
                                    <a class="icon tool-icon" href="<?php echo $p_download; ?>"><i
                                            class="fas fa-file-download fa-lg has-text-grey"
                                            title="Download Paste"></i></a>
                                    <a class="icon tool-icon embed-tool "><i
                                            class="far fa-file-code fa-lg has-text-grey"
                                            title="Embed This Paste"></i></a>
                                    <a class="icon tool-icon expand-tool"><i class="fas fa-expand-alt has-text-grey"
                                                                             title="Full Screen"></i></a>
                                    <div class="panel-embed my-5 is-hidden">
                                        <input type="text" class="input has-background-white-ter has-text-grey"
                                               value='<?php echo '<script src="' . pp_site_url() . '/';
                                               if (PP_MOD_REWRITE) {
                                                   echo 'embed/';
                                               } else {
                                                   echo 'paste.php?embed&id=';
                                               }
                                               echo $paste->id . '"></script>'; ?>' readonly/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tag display  -->
                    <div class="columns is-desktop is-centered">
                        <?= tagsToHtml($paste->tags); ?>
                    </div>
                    <br>
                    <?php if (isset($error)): ?>
                        <div class="notification is-danger">
                            <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                            <p><?= pp_html_escape($error) ?></p>
                        </div>
                    <?php elseif ($using_highlighter): ?>
                        <div id="paste" style="line-height: 18px;">
                            <div class="<?= pp_html_escape($paste['code']) ?>">
                                <ol>
                                    <?php foreach ($lines as $num => $line):
                                        $line = trim($line); ?>
                                        <li class="<?= $num == 0 ? 'li1 ln-xtra' : 'li1' ?>" id="<?= $num + 1 ?>">
                                            <div class="de1"><?= $line === '' ? '&nbsp;' : linkify($line) ?></div>
                                        </li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                        </div>
                    <?php else: ?>
                        <div id="paste" style="line-height: 18px;"><?= $p_content ?></div>
                    <?php endif; ?>
                </div>
                <?php if (can('hide', $paste)): ?>
                    <div class="mod-tools">
                        <p>Moderation Tools</p>
                        <form action="/admin/paste_action.php" method="post">
                            <input type="hidden" name="paste_id" value="<?= $paste->id ?>" />
                            <?php if (isset($csrf_token)): ?>
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>"/>
                            <?php endif; ?>
                            <div class="field is-grouped">
                                <div class="control">
                                    <input class="button is-small <?= $paste->is_hidden ? 'is-success' : 'is-danger' ?>"
                                           type="submit" name="hide" id="hide"
                                           value="<?= $paste->is_hidden ? 'Unhide' : 'Hide' ?> Paste" />
                                </div>
                                <?php if (can('blank', $paste)): ?>
                                    <div class="control">
                                        <input class="button is-small is-danger" type="submit" name="blank" id="blank" value="Blank Paste Contents (No Undo)" />
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if ($paste->mark === null): ?>
                                <p>The following buttons train the spam filter. Don't use them unless you're sure the paste belongs to the given category.</p>
                                <p>Right now, the spam filter thinks this paste is
                                    <b><?php echo match ($paste_guessed_mark) {
                                        'spam' => 'spam',
                                        'ham' => 'not spam',
                                        default => 'unknown',
                                    }; ?></b>. Even if that's correct, you should still hit the right button to improve the spam filter.
                                </p>
                                <div class="field is-grouped">
                                    <div class="control">
                                        <input class="button is-small is-success" type="submit" name="mark[ham]" value="Mark as Not Spam" />
                                    </div>
                                    <div class="control">
                                        <input class="button is-small is-danger" type="submit" name="mark[spam]" value="Mark as Spam" />
                                    </div>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                <?php endif; ?>
                <!-- Guests -->
                <?php if ($totalpastes !== 0 && !can('edit', $paste)) { ?>
                    <hr>
                    <label class="label">More from this Author </label>
                    <?php foreach ($recommended_pastes as $paste) { ?>
                        <p class="no-margin">
                        <header class="bd-category-header my-1">
                            <a href="<?= urlForPaste($paste) ?>" title="<?= pp_html_escape($paste->title) ?>">
                                <?= pp_html_escape(truncate($paste->title, 24, 60)) ?>
                            </a>
                            <p class="subtitle is-7">by <i><?= pp_html_escape($paste->user->username) ?></i></p>
                        </header>
                    <?php } ?>
                <?php } elseif (can('edit', $paste)) { ?>
                    <!-- Paste Panel -->
                    <hr>
                    <h1 class="title is-6 mx-1">Edit Paste</h1>
                    <form class="form-horizontal" action="/" method="POST">
                        <nav class="level">
                            <div class="level-left">
                                <!-- Title -->
                                <div class="level-item is-pulled-left mx-1">
                                    <p class="control has-icons-left">
                                        <input type="text" class="input" name="title"
                                               placeholder="<?= pp_html_escape($paste['title']) ?>"
                                               value="<?= pp_html_escape($paste['title']) ?>"/>
                                        <span class="icon is-small is-left">
                                            <i class="fa fa-font"></i>
                                        </span>
                                    </p>
                                </div>
                                <!-- Format -->
                                <div class="level-item is-pulled-left mx-1">
                                    <div class="select">
                                        <div class="select">
                                            <select data-live-search="true" name="format">
                                                <?= optionsForSelect(array_values(PP_HIGHLIGHT_FORMATS), array_keys(PP_HIGHLIGHT_FORMATS), $paste->code); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="level-item is-pulled-left mx-1">
                                    <input type="hidden" name="paste_id"
                                           value="<?= $paste->id; ?>"/>
                                </div>
                                <div class="level-item is-pulled-left mx-1">
                                    <a class="button"
                                       onclick="highlight(document.getElementById('code')); return false;"><i
                                            class="fa fa-indent"></i>&nbsp;Highlight</a>
                                </div>
                                <div class="level-item mx-1">
                                    <?php if (isset($csrf_token)): ?>
                                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>"/>
                                    <?php endif; ?>
                                    <input class="button is-info" type="submit" name="edit" id="edit"
                                           value="Save Changes"/>
                                </div>
                            </div>
                        </nav>
                        <!-- Text area -->
                        <textarea style="line-height: 1.2;" class="textarea mx-1" rows="13" id="code"
                                  name="paste_data" onkeyup="countChars(this);"
                                  onkeydown="return catchTab(this,event)"><?= pp_html_escape($op_content); ?></textarea>
                        <p id="charNum"><b>File Size: </b><span style="color: green;">1000/1000Kb</span></p>
                        <br>

                        <div class="columns">
                            <div class="column">
                                <div class="field">
                                    <label class="label" for="field_tags">Tags</label>
                                    <small>Type a comma to separate each tag.</small>

                                    <div class="control">
                                        <div class="control">
                                            <input name="tag_input" class="input js-tag-input" id="field_tags"
                                                   value="<?= pp_html_escape($paste->tags->map(function ($t) {
                                                       return $t->name;
                                                   })->join(',')) ?>"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <nav class="level">
                            <div class="level-left">
                                <div class="level-item is-pulled-left mr-1">
                                    <div class="field">
                                        <div class="subtitle has-text-weight-semibold "
                                             style="margin-left: 2px; margin-bottom: 0.6rem; font-size: 1rem;">Expiry
                                        </div>
                                        <div class="control">
                                            <!-- Expiry -->
                                            <div class="select">
                                                <select name="paste_expire_date">
                                                    <option value="N" selected="selected">Never</option>
                                                    <option value="self">View Once</option>
                                                    <option value="0Y0M0DT0H10M">10 Minutes</option>
                                                    <option value="T1H">1 Hour</option>
                                                    <option value="1D">1 Day</option>
                                                    <option value="1W">1 Week</option>
                                                    <option value="2W">2 Weeks</option>
                                                    <option value="1M">1 Month</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="level-item is-pulled-left mx-1">
                                    <div class="field">
                                        <div class="subtitle has-text-weight-semibold "
                                             style="margin-left: 2px; margin-bottom: 0.6rem; font-size: 1rem;">
                                            Visibility
                                            &nbsp;&nbsp;
                                        </div>
                                        <div class="control">
                                            <!-- Visibility -->
                                            <div class="select">
                                                <select name="visibility">
                                                    <?php
                                                    $visibility_names = ['Public', 'Unlisted'];
                                                    $visibility_codes = ['0', '1'];
                                                    if ($current_user) {
                                                        $visibility_names[] = 'Private';
                                                        $visibility_codes[] = '2';
                                                    }

                                                    echo optionsForSelect($visibility_names, $visibility_codes, $paste->visible);
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </nav>
                        <br>
                        <div class="level-left">
                            <!-- Encrypted -->
                            <div class="b-checkbox is-info is-inline">
                                <input class="is-checkradio is-info" id="encrypt" name="encrypted"
                                       type="checkbox" disabled="disabled" checked="checked"/>
                                <label for="encrypt">
                                    Encrypt on server (always enabled)
                                </label>
                            </div>
                        </div>
                    </form>
                <?php } ?>
            </div>
        </div>
    </div>
</main>

<script>
    function roundToTwo(num) {
        return +(Math.round(num + "e+2") + "e-2");
    }

    function countChars(obj) {
// True limit
        var maxLength = 1000000;
        var strLength = obj.value.length;
        var charRemain = (maxLength - strLength);
        var char2kb = charRemain / 1000;
        var charDisplay = roundToTwo(char2kb);
// Grace limit
        var gracelimit = 11480;
        var newstrLength = obj.value.length - 1000000;
        var graceRemain = (gracelimit - newstrLength);
        var grace2kb = graceRemain / 1000;
        var graceDisplay = roundToTwo(grace2kb);

        var element = document.getElementById('charNum');

        if (graceRemain < 0) {
            element.innerHTML = '<b>File Size: </b><span style="color: red;">File Size limit reached</span>';
        } else if (charRemain < 0) {
            element.innerHTML = '<b>File Size: </b><span style="color: orange;">' + graceDisplay + '/24Kb Grace Limit</span>';
        } else {
            element.innerHTML = '<b>File Size: </b><span style="color: green;">' + charDisplay + '/1000Kb</span>';
        }
    }
</script>

