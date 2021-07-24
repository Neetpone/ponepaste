<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<link rel="stylesheet" href="theme/bulma/css/bulma-tagsinput.min.css"/>
<script src="theme/bulma/js/bulma-tagsinput.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        BulmaTagsInput.attach();
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tagsInput = document.getElementById('tags-with-source');
        new BulmaTagsInput(tagsInput, {
            autocomplete: {
                <!-- Json to be completed -->
                source: "",
            }
        });
    }, false);
</script>
<script>
    function openreport() {
        var x = document.getElementById("panel");
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    }

    function closereport() {
        var x = document.getElementById("panel");
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    }
</script>
<script>
    function preloaderFadeOutInit() {
        $('.preloader').fadeOut('slow');
        $('main').attr('id', '');
    }

    // Window load function
    jQuery(window).on('load', function () {
        (function ($) {
            preloaderFadeOutInit();
        })(jQuery);
    });
</script>
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
$protocol = paste_protocol();

$bg = array('/img/loader.gif', '/img/loader2.gif', '/img/loader3.gif'); // array of filenames

$i = rand(0, count($bg) - 1); // generate random number size of the array
$selectedloader = "$bg[$i]"; // set variable equal to which random filename was chosen
?>

<style>


    .preloader {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        width: 100%;
        height: 100vh;
        z-index: 99999999;
        background-image: url('<?php echo $selectedloader; ?>'); /* your icon gif file path */
        background-repeat: no-repeat;
        background-color: #FFF;
        background-position: center;
    }

    #stop-scrolling {
        height: 100% !important;
        overflow: hidden !important;
    }

</style>
<main class="bd-main" id="stop-scrolling">
    <div class="preloader"></div>
    <div class="bd-side-background"></div>
    <div class="bd-main-container container">
        <div class="bd-duo">
            <div class="bd-lead">
                <div class="content panel">
                    <article class="message is-danger" id="panel" style="display: none;">
                        <div class="message-header" style="margin-bottom: 0px;">
                            <p style="margin-bottom: 1px;">Report Paste</p>
                            <button class="delete" onclick="closereport()" aria-label="delete"></button>
                        </div>

                        <div class="message-body">
                            <div class="columns">
                                <div class="column">
                                    <p>Please select how this paste violates a rule:</p>
                                </div>
                                <div class="column">
                                    <form class="form-horizontal" id="reportpaste" name="preport" action="report.php"
                                          method="POST">
                                        <div class="select">
                                            <select name="reasonrep">
                                                <option>Select dropdown</option>
                                                <option value="0">Not /mlp/ Related</option>
                                                <option value="1">Links to Illegal Content</option>
                                                <option value="2">Paste has personal information (Dox)</option>
                                            </select>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="column">
                            <input type="hidden" name="reppasteid" value="<?php echo($paste_id); ?>">
                            <div>
                                <div style="text-align: center;">
                                    <div id="reportbutton" class="column">
                                        <input class="button is-danger is-fullwidth" type="submit" name="reportpaste"
                                               id="report" value="Report Paste"/>
                                    </div>
                                </div>
                                </form>

                            </div>
                        </div>
                    </article>

                    <div class="columns is-multiline">
                        <div class="column is-4">
                            <span class="tag is-normal"><i class="fa fa-code fa-lg"
                                                           aria-hidden="true"></i>&nbsp;&nbsp;<?php echo strtoupper($paste['code']); ?></span>
                            <span class="tag is-normal"><i class="fa fa-eye fa-lg"
                                                           aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $paste['views']; ?></span>
                            <span class="tag is-normal"><i class="fa fa-star fa-lg"
                                                           aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $fav_count; ?></span>
                            <br>
                            <span class="tag is-normal"><i class="fa fa-file-word fa-lg" aria-hidden="true"></i>&nbsp;&nbsp; <?php $wordcount = str_word_count($op_content);
                                echo $wordcount ?></span>
                            <span class="tag is-normal"><i class="fa fa-hdd fa-lg"
                                                           aria-hidden="true"></i>&nbsp;&nbsp;<?php $pastesize = strlen($op_content);
                                echo formatBytes($pastesize) ?></span>
                            <span class="tag is-normal"><i class="fa fa-list-ol fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo substr_count($op_content, "\n") + 1; ?></span>
                        </div>
                        <div class="column is-4 has-text-centered">
                            <h1 class="title is-6" style="margin-bottom:0;"><?= $paste['title'] ?></h1>
                            <small class="title is-6 has-text-weight-normal has-text-grey">
                                <?php if ($paste['member'] === NULL): ?>
                                    Guest
                                <?php else: ?>
                                    By <a href="<?= urlForMember($paste['member']) ?>"><?= $paste['member'] ?></a>
                                <?php endif; ?>
                                <br/>
                                Created: <?= $paste['created_at'] ?>
                                <br/>
                                <?php if ($paste['updated_at'] != $paste['created_at']): ?>
                                    <?= $paste['updated_at'] ?>
                                <?php endif; ?>
                            </small>
                        </div>
                        <div class="column is-4 has-text-right">
                            <div class="">
                                <div class="panel-tools">
                                    <?php if ($current_user !== null) {
                                        $fav_paste = checkFavorite($conn, $paste_id, $current_user->user_id);
                                    }
                                    ?>
                                    <a class="icon tool-icon" class="flip" onclick="openreport()"><i
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
                                    <div class="panel-embed my-5" style="display:none;">
                                        <input type="text" class="input has-background-white-ter has-text-grey"
                                               value='<?php echo '<script src="' . $protocol . $baseurl . '/';
                                               if ($mod_rewrite == '1') {
                                                   echo 'embed/';
                                               } else {
                                                   echo 'paste.php?embed&id=';
                                               }
                                               echo $paste_id . '"></script>'; ?>' readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tag display  -->
                    <div class="columns is-desktop is-centered">
                        <?php
                        $tagDisplay = htmlentities($paste['tags'], ENT_QUOTES, 'UTF-8');
                        $tagDisplay = rtrim($tagDisplay);
                        $tagArray = explode(',', $tagDisplay);
                        if (strlen($tagDisplay) > 0) {
                            foreach ($tagArray as $tag_Array) {
                                $tag_Array = ucfirst($tag_Array);
                                echo '<a href="/archive?q=' . trim($tag_Array) . '"><span class="tag is-info">' . $tag_Array . '</span></a>';
                            }
                        } else {
                            echo ' <span class="tag is-warning">No tags</span>';
                        }

                        ?>
                    </div>
                    <br>
                    <?php if (isset($error)) {
                        echo '<p class="help is-danger subtitle is-6">' . $error . '</p>';
                    } else {
                        if ($paste['code'] != "pastedown") {
                            echo '
						<div id="paste" style="line-height:1!important;">' . linkify($p_content) . '</div>';
                        } else {
                            echo '
						<div id="paste" style="line-height:1!important;">' . $p_content . '</div>';
                        }
                    } ?>
                </div>
                <!-- Guests -->
                <?php if ($current_user === null || $current_user->user_id !== $paste['user_id']) { ?>
                    <hr>
                    <label class="label">More from this Author </label>
                    <?php
                    $rec = getUserRecom($conn, $paste['user_id']);
                    foreach ($rec as $index => $row) {
                        $title = Trim($row['title']);
                        $p_id = Trim($row['id']);
                        $titlehov = ($row['title']);
                        $long_title = pp_html_escape($row['title']);
                        $title = pp_html_escape(truncate($row['title'], 24, 60));
                        ?>

                        <p class="no-margin">
                        <?php
                        if ($mod_rewrite == '1') {
                            echo '<header class="bd-category-header my-1">
									<a href="' . $p_id . '" title="' . $long_title . '">' . $title . ' </a>
									<p class="subtitle is-7">' . 'by ' . '
										<i>' . $row['member'] . '</i>' . '
									</p>' .
                                '</header>';
                        } else {
                            echo '<a href="' . $p_id . '" title="' . $titlehov . '">' . ucfirst($title) . '</a>';
                        }
                    }


                    // Display a message if the pastebin is empty
                    if ($totalpastes === 0) {
                        echo $lang['emptypastebin'];
                    } ?>
                    </p>

                <?php } else { ?>
                <!-- Paste Panel -->
                <hr>
                <h1 class="title is-6 mx-1"><?php echo $lang['modpaste']; ?>
                    <h1>
                        <form class="form-horizontal" name="mainForm" action="index.php" method="POST">
                            <nav class="level">
                                <div class="level-left">
                                    <!-- Title -->
                                    <div class="level-item is-pulled-left mx-1">
                                        <p class="control has-icons-left">
                                            <input type="text" class="input" name="title"
                                                   placeholder="<?php echo $lang['pastetitle']; ?>"
                                                   value="<?php echo($p_title); ?>">
                                            <span class="icon is-small is-left">
															<i class="fa fa-font"></i></a>
														</span>
                                        </p>
                                    </div>
                                    <!-- Format -->
                                    <div class="level-item is-pulled-left mx-1">
                                        <div class="select">
                                            <div class="select">
                                                <select data-live-search="true" name="format">
                                                    <?php // Show popular GeSHi formats
                                                    foreach ($geshiformats as $code => $name) {
                                                        if (in_array($code, $popular_formats)) {
                                                            $sel = ($paste['code'] == $code) ? 'selected="selected"' : ' ';
                                                            echo '<option ' . $sel . ' value="' . $code . '">' . $name . '</option>';
                                                        }
                                                    }

                                                    // Show all GeSHi formats.
                                                    foreach ($geshiformats as $code => $name) {
                                                        if (!in_array($code, $popular_formats)) {
                                                            $sel = ($paste['code'] == $code) ? 'selected="selected"' : '';
                                                            echo '<option ' . $sel . ' value="' . $code . '">' . $name . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="level-item is-pulled-left mx-1">
                                        <input class="button is-info" type="hidden" name="paste_id"
                                               value="<?php echo $paste_id; ?>"/>
                                    </div>
                                    <div class="level-item is-pulled-left mx-1">
                                        <a class="button"
                                           onclick="highlight(document.getElementById('code')); return false;"><i
                                                    class="fa fa-indent"></i>&nbspHighlight</a>
                                    </div>
                                </div>
                            </nav>
                            <!-- Text area -->
                            <textarea style="line-height: 1.2;" class="textarea mx-1" rows="13" id="code"
                                      name="paste_data" onkeyup="countChars(this);"
                                      onkeydown="return catchTab(this,event)"
                                      placeholder="helloworld"><?php echo htmlentities($op_content, ENT_QUOTES, 'UTF-8'); ?></textarea>
                            <p id="charNum"><b>File Size: </b><span style="color: green;">1000/1000Kb</span></p>
                            <br>

                            <div class='rows'>
                                <div class='row is-full'>
                                    <div class="columns">
                                        <div class="column">
                                            <div class="field">
                                                <label class="label">Tags</label>
                                                <div class="control">
                                                    <input id="tags-with-source" name="tags" class="input"
                                                           data-max-tags="10" data-max-chars="40" type="text"
                                                           data-item-text="name" data-case-sensitive="false"
                                                           placeholder="10 Tags Maximum"
                                                           value="<?php echo $p_tagsys; ?>">
                                                </div>
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
                                                 style="margin-left: 2px; margin-bottom: 0.6rem; font-size: 1rem;"><?php echo $lang['expiration']; ?></div>
                                            <div class="control">
                                                <!-- Expiry -->
                                                <div class="select">
                                                    <select name="paste_expire_date">
                                                        <option value="N" selected="selected">Never</option>
                                                        <option value="self">View Once</option>
                                                        <option value="10M">10 Minutes</option>
                                                        <option value="1H">1 Hour</option>
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
                                                 style="margin-left: 2px; margin-bottom: 0.6rem; font-size: 1rem;"><?php echo $lang['visibility']; ?>
                                                &nbsp;&nbsp;
                                            </div>
                                            <div class="control">
                                                <!-- Visibility -->
                                                <div class="select">
                                                    <select name="visibility">
                                                        <option value="0" <?php echo ($p_visible == "0") ? 'selected="selected"' : ''; ?>>
                                                            Public
                                                        </option>
                                                        <option value="1" <?php echo ($p_visible == "1") ? 'selected="selected"' : ''; ?>>
                                                            Unlisted
                                                        </option>
                                                        <?php if ($current_user) { ?>
                                                            <option value="2" <?php echo ($p_visible == "2") ? 'selected="selected"' : ''; ?>>
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
                                            <input type="text" class="input" name="pass" id="pass" value=""
                                                   placeholder="<?php echo $lang['pwopt']; ?>">
                                        </div>
                                    </div>
                                </div>
                            </nav>
                            <br>
                            <nav>
                                <div class="level-left">
                                    <!-- Encrypted -->
                                    <div class="b-checkbox is-info is-inline">
                                        <?php
                                        $encrypted_checked = "";
                                        if ($_POST) {
                                            // We came here from an error, carry the checkbox setting forward
                                            if (isset($_POST['encrypted'])) {
                                                $encrypted_checked = "checked";
                                            }
                                        } else {
                                            // Fresh paste. Default to encrypted on
                                            $encrypted_checked = "checked";
                                        }
                                        ?>
                                        <input class="is-checkradio is-info" id="encrypt" name="encrypted"
                                               type="checkbox" <?php echo $encrypted_checked; ?>>
                                        <label for="encrypt">
                                            <?php echo $lang['encrypt']; ?>
                                        </label>
                                        <?php
                                        if ($current_user && ($current_user['id'] === $paste['user_id'])) {
                                            ?>
                                            <input class="button is-info" type="submit" name="edit" id="edit"
                                                   value="<?php echo $lang['editpaste']; ?>"/>
                                            <?php
                                        } ?>
                                        <input class="button is-info" type="submit" name="submit" id="submit"
                                               value="<?php echo $lang['forkpaste']; ?>"/>
                                    </div>
                                    <br/>
                            </nav>
                            <?php
                            if (isset($site_ads)) {
                                echo $site_ads['ads_2'];
                            }
                            ?>
                        </form>
                        <?php } ?>

            </div>
            <?php require_once('theme/' . $default_theme . '/sidebar.php'); ?>
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

        if (graceRemain < 0) {
            document.getElementById("charNum").innerHTML = '<b>File Size: </b><span style="color: red;">File Size limit reached</span>';
        } else if ((charRemain < 0)) {
            document.getElementById("charNum").innerHTML = '<b>File Size: </b><span style="color: orange;">' + graceDisplay + '/24Kb Grace Limit</span>';
        } else {
            document.getElementById("charNum").innerHTML = '<b>File Size: </b><span style="color: green;">' + charDisplay + '/1000Kb</span>';
        }
    }
</script>
<script>
    $("#reportpaste").submit(function (e) {

        e.preventDefault(); // avoid to execute the actual submit of the form.

        var form = $(this);
        var url = form.attr('action');

        $.ajax({
            type: "POST",
            url: "report.php",
            data: form.serialize(), // serializes the form's elements.
            success: function (data) {
                document.getElementById("reportbutton").innerHTML = '<input disabled class="button is-danger is-fullwidth"  type="submit" name="reportpaste" id="report" value="Reported" />';
            }
        });

    });
</script>
<?php if ($current_user) { ?>
    <script>
        $(document).ready(function () {
            $('#favorite').on('click', null, function () {
                var _this = $(this);
                var post_id = _this.data('fid');
                $.ajax({
                    type: 'POST',
                    url: 'fav.php',
                    dataType: 'json',
                    data: 'fid=' + post_id,
                });
                location.reload(true)
            });
        });
    </script>
<?php } ?>

