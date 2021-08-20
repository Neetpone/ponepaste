<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<link rel="stylesheet" href="theme/bulma/css/bulma-tagsinput.min.css"/>
<script src="theme/bulma/js/bulma-tagsinput.min.js"></script>
<script>
    function setupTagsInput() {
        const tagsInput = document.getElementById('tags-with-source');
        new BulmaTagsInput(tagsInput, {
            allowDuplicates: false,
            caseSensitive: false,
            clearSelectionOnTyping: false,
            closeDropdownOnItemSelect: true,
            delimiter: ',',
            freeInput: true,
            highlightDuplicate: true,
            highlightMatchesString: true,
            itemText: 'name',
            maxTags: 10,
            maxChars: 40,
            minChars: 1,
            noResultsLabel: 'No results found',
            placeholder: '10 Tags Maximum"',
            removable: true,
            searchMinChars: 1,
            searchOn: 'text',
            selectable: true,
            tagClass: 'is-rounded',
            trim: true,
            source: async function (value) {
                // Value equal input value
                // We can then use it to request data from external API
                return await fetch("/api/tags_autocomplete.php?tag=" + encodeURIComponent(value))
                    .then(function (response) {
                        return response.json();
                    });
            }
        });

        preloaderFadeOutInit();
    }

    if (document.readyState !== 'loading') {
        setupTagsInput();
    } else {
        document.addEventListener('DOMContentLoaded', setupTagsInput);
    }

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

    function preloaderFadeOutInit() {
        document.querySelector('main').classList.remove('stop-scrolling');
        document.querySelector('.preloader').classList.add('preloader-hidden');
    }
</script>
<?php if ($using_highlighter): ?>
    <link rel="stylesheet" href="/vendor/scrivo/highlight.php/styles/default.css" />
<?php endif; ?>
<?php
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
        background-image: url('<?= $selectedloader ?>'); /* your icon gif file path */
        background-repeat: no-repeat;
        background-color: #FFF;
        background-position: center;
    }

    .preloader-hidden {
        visibility: hidden;
        opacity: 0;
        transition: visibility 0s 1.25s, opacity 1.25s linear;
    }

    .stop-scrolling {
        height: 100%;
        overflow: hidden;
    }
</style>
<main class="bd-main stop-scrolling">
    <div class="preloader"></div>
    <div class="bd-side-background"></div>
    <div class="bd-main-container container">
        <div class="bd-duo">
            <div class="bd-lead">
                <div class="content panel">
                    <article class="message is-danger" id="panel" style="display: none;">
                        <div class="message-header" style="margin-bottom: 0;">
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
                                <?php if ($paste['member'] === null): ?>
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
                                    <?php
                                    if ($current_user !== null) {
                                        echo checkFavorite($conn, $paste_id, $current_user->user_id);
                                    }
                                    ?>
                                    <a class="icon tool-icon flip" onclick="openreport()"><i
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
                                               if (PP_MOD_REWRITE) {
                                                   echo 'embed/';
                                               } else {
                                                   echo 'paste.php?embed&id=';
                                               }
                                               echo $paste_id . '"></script>'; ?>' readonly />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tag display  -->
                    <div class="columns is-desktop is-centered">
                        <?php
                        $tags = $paste['tags'];
                        if (count($tags) != 0) {
                            foreach ($tags as $tag) {
                                $tagName = ucfirst(pp_html_escape($tag['name']));
                                echo '<a href="/archive?q=' . $tagName . '"><span class="tag is-info">' . $tagName . '</span></a>';
                            }
                        } else {
                            echo ' <span class="tag is-warning">No tags</span>';
                        }

                        ?>
                    </div>
                    <br>
                    <?php if (isset($error)): ?>
                        <div class="notification is-danger">
                            <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                            <p><?= pp_html_escape($error) ?></p>
                        </div>
                    <?php elseif ($using_highlighter): ?>
                        <div id="paste" style="line-height:1!important;">
                            <div class="<?= pp_html_escape($paste['code']) ?>">
                                <ol>
                                    <?php foreach ($lines as $num => $line):
                                        $line = trim($line); ?>
                                        <li class="<?= $num == 0 ? 'li1 ln-xtra' : 'li1' ?>" id="<?= $num + 1 ?>">
                                            <div class="de1"><?= $line === '' ? '&nbsp;' : $line ?></div>
                                        </li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                        </div>
                        <div id="paste" style="line-height:1!important;"><?= $p_content  ?></div>

                    <?php else: ?>
                        <div id="paste" style="line-height:1!important;"><?= $p_content  ?></div>
                    <?php endif; ?>
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
                        if (PP_MOD_REWRITE) {
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
                    <h1 class="title is-6 mx-1"><?php echo $lang['modpaste']; ?></h1>
                    <form class="form-horizontal" name="mainForm" action="index.php" method="POST">
                        <nav class="level">
                            <div class="level-left">
                                <!-- Title -->
                                <div class="level-item is-pulled-left mx-1">
                                    <p class="control has-icons-left">
                                        <input type="text" class="input" name="title"
                                               placeholder="<?= $paste['title'] ?>"
                                               value="<?= $paste['title'] ?>" />
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


                        <div class="columns">
                            <div class="column">
                                <div class="field">
                                    <label class="label">Tags</label>
                                    <div class="control">
                                        <input id="tags-with-source" name="tag_input" class="input"
                                               value="<?php
                                               $inputtags = $paste['tags'];
                                               foreach ($inputtags as $tag) {
                                                   $tagsName = ucfirst(pp_html_escape($tag['name']));
                                                   echo  ','.$tagsName.'';
                                               }?>">
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
                                               placeholder="<?php echo $lang['pwopt']; ?>" />
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
                                    if ($current_user->user_id == $paste['user_id']) {
                                        ?>
                                        <input class="button is-info" type="submit" name="edit" id="edit"
                                               value="<?php echo $lang['editpaste']; ?>"/>
                                        <?php
                                    } ?>
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

