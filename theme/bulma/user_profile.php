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
?>
<script>
    $(document).ready(function () {
        $("#archive").dataTable({
            pageLength: 50,
            autoWidth: false,
            initComplete: function () {
                var search = new URLSearchParams(window.location.search);
                var query = search.get('q');
                if (query) {
                    $("#archive_filter input")
                        .val(query)
                        .trigger("input");
                }
            }
        })
    });
</script>
<?php if (isset($_SESSION['token'])) { ?>
    <script>
        $(document).ready(function () {
            $('#favs').DataTable({
                "autoWidth": false,
                "pageLength": 50,
                "order": [
                    [1, "desc"]
                ]
            });
        });

    </script>
<?php } ?>
<main class="bd-main">
    <div class="bd-side-background"></div>
    <div class="bd-main-container container">
        <div class="bd-duo">
            <div class="bd-lead">
                <h1 class="title is-5"><?php echo $profile_username . $lang['user_public_pastes']; ?></h1>
                <h1 class="subtitle is-6"><?php echo $lang['membersince'] . $profile_join_date; ?></h1>
                <!-- Badges system -->
                <div class="box">
                    <h2 class="title is-5">Badges</h2>
                    <?php
                    if (strtotime($profile_join_date) <= 1604188800) {
                        echo '<img src = "/img/badges/adopter.png" title="[EarlyAdopter] Joined during the first wave " style="margin:5px">';
                    } elseif (strtotime($profile_join_date) <= 1608422400) {
                        echo '<img src = "/img/badges/pioneer.png" title="[EarlyPioneer] Joined during the Second wave " style="margin:5px">';
                    } elseif (strtotime($profile_join_date) <= 1609459200) {
                        echo '<img src = "/img/badges/strag.png" title="[EarlyStraggeler] Joined after the Second wave " style="margin:5px">';
                    }
                    if (strpos($profile_badge, '0') !== false) {
                    } else {
                        echo $profile_badge;
                    }


                    //Paste count badges
                    if ($profile_total_public <= 4) {
                        echo '<img src = "/img/badges/totpastes.png" title="[NewFriend] Have less than Five pastes" style="margin:5px">';
                    } elseif (($profile_total_public >= 5) && ($profile_total_public <= 24)) {
                        echo '<img src = "/img/badges/totpastes2.png" title="[NewWritefag] Have Five or more pastes" style="margin:5px">';
                    } elseif (($profile_total_public >= 25) && ($profile_total_public <= 49)) {
                        echo '<img src = "/img/badges/totpastes3.png" title="[Writefag] Have more than Twenty Five pastes" style="margin:5px">';
                    } elseif (($profile_total_public >= 50) && ($profile_total_public)) {
                        echo '<img src = "/img/badges/totpastes4.png" title="[ProbablyAutistic] Have more than Fifty pastes" style="margin:5px">';
                    }

                    //Pasteviews badges

                    if (($profile_total_paste_views >= 1000) && ($profile_total_paste_views <= 2999)) {
                        echo '<img src = "/img/badges/pasteviews.png" title="[HorseWriter] Have more than 1000 total views" style="margin:5px">';
                    } elseif (($profile_total_paste_views >= 2000) && ($profile_total_paste_views <= 2999)) {
                        echo '<img src = "/img/badges/pasteviews2.png" title="[HorseFamous] Have more than 2000 total views" style="margin:5px">';
                    } elseif (($profile_total_paste_views >= 3000) && ($profile_total_paste_views <= 4999)) {
                        echo '<img src = "/img/badges/pasteviews3.png" title="[Horseidol] Have more than 3000 total views" style="margin:5px">';
                    } elseif (($profile_total_paste_views >= 5000) && ($profile_total_paste_views <= 9999)) {
                        echo '<img src = "/img/badges/pasteviews4.png" title="[HorseMaster] Have more than 5000 total views" style="margin:5px">';
                    } elseif (($profile_total_paste_views >= 10000) && ($profile_total_paste_views <= 49999)) {
                        echo '<img src = "/img/badges/pasteviews5.png" title="[HorseIlluminatii] Have more than 10,000 total views" style="margin:5px">';
                    } elseif ($profile_total_paste_views >= 50000) {
                        echo '<img src = "/img/badges/pasteviews6.png" title="[HorseAyylmao] Have more than 50,000 total views" style="margin:5px">';
                    }

                    if (($profile_total_unlisted >= 5) && ($profile_total_unlisted <= 9)) {
                        echo '<img src = "/img/badges/pastehidden.png" title="[ShadowWriter] Have more than Five unlisted pastes" style="margin:5px">';
                    } elseif ($profile_total_unlisted >= 10) {
                        echo '<img src = "/img/badges/pastehidden.png" title="[Ghostwriter]  Have more than Ten unlisted pastes" style="margin:5px">';
                    }

                    ?>
                </div>

                <?php
                if (isset($_GET['del'])) {
                    if (isset($success)) {
                        // Deleted
                        echo '<p class="help is-success subtitle is-6">' . $success . '</p>';
                    } // Errors
                    elseif (isset($error)) {
                        echo '<p class="help is-danger subtitle is-6">' . $error . '</p>';
                    }
                }
                ?>

                <?php
                if ($_SESSION['username'] == $profile_username) {
                    ?>
                    <?php echo $lang['profile-stats']; ?><br/>
                    <?php echo $lang['totalpastes'] . ' ' . $profile_total_pastes; ?> &mdash;
                    <?php echo $lang['profile-total-pub'] . ' ' . $profile_total_public; ?> &mdash;
                    <?php echo $lang['profile-total-unl'] . ' ' . $profile_total_unlisted; ?> &mdash;
                    <?php echo $lang['profile-total-pri'] . ' ' . $profile_total_private; ?> &mdash;
                    <?php echo $lang['profile-total-views'] . ' ' . $profile_total_paste_views; ?>
                    <br>
                    <?php echo $lang['pastfavs-total'] . ' ' . $total_pfav; ?> &mdash;
                    <?php echo $lang['yourfavs-total'] . ' ' . $total_yfav; ?><br>
                    <br>
                    <div class="tabs">
                        <ul class="tabs-menu">
                            <li class="is-active" data-target="first-tab"><a>My Pastes</a></li>
                            <li data-target="second-tab"><a>Favorites</a></li>
                        </ul>
                    </div>
                    <?php
                }
                ?>
                <div class="tab-content" id="first-tab">
                    <table id="archive" class="table is-fullwidth is-hoverable">
                        <thead>
                        <tr>
                            <td><?php echo $lang['pastetitle']; ?></td>
                            <td><?php echo $lang['pastetime']; ?></td>
                            <?php if (isset($_SESSION) && $_SESSION['username'] == $profile_username) {
                                echo "<td>" . $lang['visibility'] . "</td>";
                            } ?>
                            <td><?php echo $lang['pasteviews']; ?></td>
                            <td><?php echo $lang['tags']; ?></td>
                            <?php if (isset($_SESSION) && $_SESSION['username'] == $profile_username) {
                                echo "<td>" . $lang['delete'] . "</td>";
                            } ?>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <td><?php echo $lang['pastetitle']; ?></td>
                            <td><?php echo $lang['pastedate']; ?></td>
                            <?php if (isset($_SESSION) && $_SESSION['username'] == $profile_username) {
                                echo "<td>" . $lang['visibility'] . "</td>";
                            } ?>
                            <td><?php echo $lang['pasteviews']; ?></td>
                            <td><?php echo $lang['tags']; ?></td>
                            <?php if (isset($_SESSION) && $_SESSION['username'] == $profile_username) {
                                echo "<td>" . $lang['delete'] . "</td>";
                            } ?>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php
                        $res = getUserPastes($conn, $profile_username);
                        foreach ($res as $index => $row) {
                            $title = Trim($row['title']);
                            $p_id = Trim($row['id']);
                            $p_code = Trim($row['code']);
                            $p_date = strtotime(Trim($row['date']));
                            $p_dateui = date("d F Y", $p_date);
                            $p_views = Trim($row['views']);
                            $p_visible = Trim($row['visible']);
                            $p_tags = Trim($row['tagsys']);
                            $tagArray = explode(',', $p_tags);
                            $tagArray = array_filter($tagArray);


                            switch ($p_visible) {
                                case 0:
                                    $p_visible = $lang['public'];
                                    break;
                                case 1:
                                    $p_visible = $lang['unlisted'];
                                    break;
                                case 2:
                                    $p_visible = $lang['private'];
                                    break;
                            }
                            $p_link = ($mod_rewrite == '1') ? "$p_id" : "paste.php?id=$p_id";
                            $p_delete_link = ($mod_rewrite == '1') ? "user.php?del&user=$profile_username&id=$p_id" : "user.php?del&user=$profile_username&id=$p_id";
                            $p_tag_link = ($mod_rewrite == '1') ? "user.php?user=$profile_username&q=$p_tags" : "user.php?user=$profile_username&q=$tags";
                            $title = truncate($title, 20, 50);

                            // Guests only see public pastes
                            if (!isset($_SESSION['token']) || $_SESSION['username'] != $profile_username) {
                                if ($row['visible'] == 0) {
                                    echo '<tr> 
                                                <td>
                                                    <a href="' . $protocol . $baseurl . '/' . $p_link . '" title="' . $title . '">' . ($title) . '</a>
                                                </td>    
                                                <td data-sort="' . $p_date . '" class="td-center">
                                                <span>' . $p_dateui . '</span>
                                                </td>
                                                <td class="td-center">
                                                    ' . $p_views . '
                                                </td>
                                                <td class="td-left">';
                                    if (strlen($p_tags) > 0) {
                                        foreach ($tagArray as $key => $tags) {
                                            echo '<a href="' . $protocol . $baseurl . '/user.php?user=' . $profile_username . '&q=' . $tags . '"><span class="tag is-info">' . trim($tags) . '</span></a>';
                                        };
                                    } else {
                                        echo ' <span class="tag is-warning">No tags</span>';
                                    };


                                    echo '</td> 
                                                 </tr>';
                                }
                            } else {
                                echo '<tr> 
                                                <td>
                                                    <a href="' . $protocol . $baseurl . '/' . $p_link . '" title="' . $title . '">' . ($title) . '</a>
                                                </td>    
                                                <td data-sort="' . $p_date . '" class="td-center">
                                                <span>' . $p_dateui . '</span>
                                                </td>
                                                <td class="td-center">
                                                    ' . $p_visible . '
                                                </td>
                                                <td class="td-center">
                                                    ' . $p_views . '
                                                </td>
                                                <td class="td-center">
                                                    ' . strtoupper($p_code) . '
                                                </td>
                                                <td class="td-center">
                                                    <a href="' . $protocol . $baseurl . '/' . $p_delete_link . '" title="' . $title . '"><i class="far fa-trash-alt fa-lg" aria-hidden="true"></i></a>
                                                </td>    
						            </tr>';
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($_SESSION['username'] == $profile_username) { ?>
                <div class="tab-content" id="second-tab">
                    <table id="favs" class="table is-fullwidth is-hoverable">
                        <thead>
                        <tr>
                            <td><?php echo $lang['pastetitle']; ?></td>
                            <td><?php echo $lang['tags']; ?></td>
                            <td><?php echo "Updated (48hrs)"; ?></td>
                            <td><?php echo $lang['tags']; ?></td>
                            <?php //if (isset($_SESSION) && $_SESSION['username'] == $profile_username) {
                            //echo "<td>" . $lang['delete'] . "</td>";
                            //} ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $res = getUserFavs($conn, $profile_username);
                        foreach ($res as $index => $row) {
                            $ftitle = Trim($row['title']);
                            $f_id = Trim($row['f_paste']);
                            $f_date = Trim($row['f_time']);
                            $Recent_update = Trim($row['now_time']);
                            $f_tags = Trim($row['tagsys']);
                            $ftagArray = explode(',', $f_tags);
                            $ftagArray = array_filter($ftagArray);
                            $p_link = ($mod_rewrite == '1') ? "$f_id" : "paste.php?favdel=$fu_id";
                            $f_delete_link = ($mod_rewrite == '1') ? "user.php?favdel&user=$profile_username&fid=$fu_id" : "user.php?favdel&user=$profile_username&fid=$fu_id";
                            $title = truncate($title, 20, 50);
                            $current_time = time();
                            $past = strtotime('-2 day', $current_time);
                            if ($past <= $Recent_update && $Recent_update <= $current_time) {
                                $updatenote = "<i class='far fa-check-square fa-lg' aria-hidden='true'></i>";
                            } else {
                                $updatenote = "<i class='far fa-minus-square fa-lg' aria-hidden='true'></i>";
                            }

                            echo '<tr> 
                                                <td>
                                                    <a href="' . $protocol . $baseurl . '/' . $p_link . '" title="' . $ftitle . '">' . ($ftitle) . '</a>
                                                </td>    
                                                <td  data-sort="' . $f_date . '" class="td-left">
                                                <span>' . date("d F Y", $f_date) . '</span>
                                                </td>
                                               <td class="td-center">
                                                <span style="display:none;">' . $Recent_update . '</span>
                                                ' . $updatenote . '
                                                </td>
                                                <td class="td-left">';
                            if (strlen($f_tags) > 0) {
                                foreach ($ftagArray as $key => $ftags) {
                                    echo '<span class="tag is-info">' . trim($ftags) . '</span>';
                                };
                            } else {
                                echo ' <span class="tag is-warning">No tags</span>';
                            };


                            echo '</td> 
						            </tr>';
                        }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <?php
                if (isset($site_ads)) {
                    echo $site_ads['ads_2'];
                }
                ?>
            </div>
            <?php require_once('theme/' . $default_theme . '/sidebar.php'); ?>
        </div>
    </div>
</main>
<script>
    const tabSystem = {
        init() {
            document.querySelectorAll('.tabs-menu').forEach(tabMenu => {
                Array.from(tabMenu.children).forEach((child, ind) => {
                    child.addEventListener('click', () => {
                        tabSystem.toggle(child.dataset.target);
                    });
                    if (child.className.includes('is-active')) {
                        tabSystem.toggle(child.dataset.target);
                    }
                });
            });
        },
        toggle(targetId) {
            document.querySelectorAll('.tab-content').forEach(contentElement => {
                contentElement.style.display = contentElement.id === targetId ? 'block' : 'none';
                document.querySelector(`[data-target="${contentElement.id}"]`).classList[contentElement.id === targetId ? 'add' : 'remove']('is-active');
            })
        },
    };
    // use it
    tabSystem.init()
</script>