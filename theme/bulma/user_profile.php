<script>
    $(document).ready(function () {
        $("#archive").dataTable({
            rowReorder: {selector: 'td:nth-child(2)'},
            responsive: true,
            pageLength: 50,
            order: [[ 1, "desc" ]],
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
<?php if ($current_user) { ?>
    <script>
    $(document).ready(function () {
        $("#favs").dataTable({
        rowReorder: { selector: 'td:nth-child(2)'},
        responsive: true,
            order: [[ 2, "desc" ]],
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
<?php } ?>

<?php
    $public_paste_badges = [
        50 => '[ProbablyAutistic] Have more than Fifty pastes',
        25 => '[Writefag] Have Twenty Five or more pastes',
        5  => '[NewWritefag] Have Five or more pastes',
        0  => '[NewFriend] Have less than Five pastes',
    ];

    $unlisted_paste_badges = [
        10 => '',
        5 => ''
    ];

    $paste_view_badges = [
        50000 => '[HorseAyylmao] Have more than 50,000 total views',
        10000 => '[HorseIlluminatii] Have more than 10,000 total views',
        5000  => '[HorseMaster] Have more than 5000 total views',
        3000  => '[Horseidol] Have more than 3000 total views',
        2000  => '[HorseFamous] Have more than 2000 total views',
        1000  => '[HorseWriter] Have more than 1000 total views'
    ];



    function outputBadges(array $badgeCandidates, int $actualValue, string $imagePrefix) {
        foreach ($badgeCandidates as $threshold => $badgeTitle) {
            if ($actualValue >= $threshold) {
                echo "<img src=\"/img/badges/${imagePrefix}_${threshold}.png\" title='$badgeTitle' alt='$badgeTitle' style='margin: 5px;' />";
                break;
            }
        }
    }

?>
<main class="bd-main">
    <div class="bd-side-background"></div>
    <div class="bd-main-container container">
        <div class="bd-duo">
            <div class="bd-lead">
                <h1 class="title is-5"><?= pp_html_escape($profile_username) ?>'s Pastes</h1>
                <h1 class="subtitle is-6">joined: <?= $profile_join_date; ?></h1>
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
                    if (!str_contains($profile_badge, '0')) {
                        echo $profile_badge;
                    }

                    outputBadges($public_paste_badges, $profile_total_public, 'total_pastes');
                    outputBadges($paste_view_badges, $profile_total_paste_views, 'total_views');

                    if (($profile_total_unlisted >= 5) && ($profile_total_unlisted <= 9)) {
                        echo '<img src = "/img/badges/pastehidden.png" title="[ShadowWriter] Have more than Five unlisted pastes" style="margin:5px">';
                    } elseif ($profile_total_unlisted >= 10) {
                        echo '<img src = "/img/badges/pastehidden.png" title="[Ghostwriter]  Have more than Ten unlisted pastes" style="margin:5px">';
                    }

                    ?>
                </div>

                <?php
                foreach ($flashes['success'] as $success) {
                    echo '<div class="notification is-info"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>' . pp_html_escape($success) . '</div>';
                }

                foreach ($flashes['error'] as $error) {
                    echo '<div class="notification is-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>' . pp_html_escape($error) . '</div>';
                }
                ?>

                <?php if ($current_user && $current_user->username === $profile_username): ?>
                    Some of your statistics:
                    <br />
                    Total pastes: <?= $profile_total_pastes ?> &mdash;
                    Total public pastes: <?= $profile_total_public ?> &mdash;
                    Total unlisted pastes: <?= $profile_total_unlisted ?> &mdash;
                    Total private pastes: <?= $profile_total_private ?> &mdash;
                    Total views of all your pastes: <?= $profile_total_paste_views ?>
                    <br />
                    Total favourites of all your pastes: <?= $total_pfav ?> &mdash;
                    Total favorites you have given: <?= $total_yfav ?>
                    <br />
                    <br />
                    <div class="tabs">
                        <ul class="tabs-menu">
                            <li class="is-active" data-target="first-tab"><a>My Pastes</a></li>
                            <li data-target="second-tab"><a>Favorites</a></li>
                        </ul>
                    </div>
                    <?php endif;?>
                <div class="tab-content" id="first-tab">
                    <table id="archive" class="table is-fullwidth is-hoverable">
                        <thead>
                        <tr>
                            <td class="td-right">Title</td>
                            <td class="td-center">Paste Time</td>
                            <?php if ($is_current_user) {
                                echo "<td class='td-center'>Visibility</td>";
                            } ?>
                            <td class="td-center">Views</td>
                            <td class="td-center">Tags</td>
                            <?php if ($is_current_user) {
                                echo "<td class='td-center'>Delete</td>";
                            } ?>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        foreach ($profile_pastes as $row) {
                            $title = Trim($row['title']);
                            $p_id = $row['id'];
                            $p_code = Trim($row['code']);
                            $p_date = new DateTime($row['created_at']);
                            $p_dateui = $p_date->format("d F Y");
                            $p_views = Trim($row['views']);
                            $p_visible = intval($row['visible']);
                            $tagArray = array_map(function ($tag) {
                                return $tag['name'];
                            }, getPasteTags($conn, $p_id));
                            $p_tags = implode(',', $tagArray);


                            $p_visible = match ($p_visible) {
                                0 => 'Public',
                                1 => 'Unlisted',
                                2 => 'Private'
                            };
                            $p_link = urlForPaste($p_id);
                            $p_delete_message = "'Are you sure you want to delete this paste?'";

                            $p_delete_link = (PP_MOD_REWRITE) ? "user.php?del&user=$profile_username&id=$p_id" : "user.php?del&user=$profile_username&id=$p_id";
                            $p_tag_link = (PP_MOD_REWRITE) ? "user.php?user=$profile_username&q=$p_tags" : "user.php?user=$profile_username&q=$tags";
                            $title = truncate($title, 20, 50);

                            // Guests only see public pastes
                            if (!$is_current_user) {
                                if ($row['visible'] == 0) {
                                    echo '<tr> 
                                                <td>
                                                    <a href="' . urlForPaste($p_id) . '" title="' . $title . '">' . ($title) . '</a>
                                                </td>    
                                                <td data-sort="' . $p_date->format('U') . '" class="td-center">
                                                <span>' . $p_dateui . '</span>
                                                </td>
                                                <td class="td-center">
                                                    ' . $p_views . '
                                                </td>
                                                <td class="td-left">';
                                    if (strlen($p_tags) > 0) {
                                    echo tagsToHtmlUser($p_tags,$profile_username);
                                    } else {
                                        echo ' <span class="tag is-warning">No tags</span>';
                                    }


                                    echo '</td> 
                                                 </tr>';
                                }
                            } else { ?>
                                <tr>
                                                <td>
                                                       <a href="<?= urlForPaste($p_id) ?>" title="<?= $title ?>"><?= $title ?></a>
                                                </td>    
                                                <td data-sort="<?= $p_date->format('U') ?>" class="td-center">
                                                    <span><?= $p_dateui ?></span>
                                                </td>
                                                <td class="td-center">
                                                    <?= $p_visible ?>
                                                </td>
                                                <td class="td-center">
                                                    <?= $p_views ?>
                                                </td>
                                                <td class="td-center">
                                                    <?= strtoupper($p_code) ?>
                                                </td>
                                                <td class="td-center">      
                                                    <form action="' . urlForPaste($p_id) . '" method="POST">

</form>
                                                    <a href="' . $protocol . $baseurl . '/' . $p_delete_link . '" title="' . $title . '" onClick="return confirm(' . $p_delete_message . ')"><i class="far fa-trash-alt fa-lg" aria-hidden="true"></i></a>
                                                </td>    
						            </tr>
                            <?php }
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td class="td-center">Title</td>
                            <td class="td-center">Paste Time</td>
                            <?php if ($is_current_user) {
                                echo "<td class='td-center'>Visibility</td>";
                            } ?>
                            <td class="td-center">Views</td>
                            <td class="td-center">Tags</td>
                            <?php if ($is_current_user) {
                                echo "<td class='td-center'>Delete</td>";
                            } ?>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <?php if ($is_current_user) { ?>
                <div class="tab-content" id="second-tab">
                    <table id="favs" class="table is-fullwidth is-hoverable">
                        <thead>
                        <tr>
                            <td class="td-right">Title</td>
                            <td class="td-center">Date Favourited</td>
                            <td class="td-center">Status</td>
                            <td class="td-center">Tags</td>
                            <?php //if (isset($_SESSION) && $_SESSION['username'] == $profile_username) {
                            //echo "<td>Delete</td>";
                            //} ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($profile_favs as $row) {
                            $ftitle = Trim($row['title']);
                            $f_id = Trim($row['paste_id']);
                            $f_date = new DateTime($row['f_time']);
                            $f_dateui = $f_date->format("d F Y");
                            $Recent_update = new DateTime($row['updated_at']);
                            $Recent_update_u = date_format($Recent_update, 'U');
                            $tagArray2 = array_map(function ($tag) {
                                return $tag['name'];
                            }, getPasteTags($conn, $f_id));
                            $f_tags = implode(',', $tagArray2);
                            //$p_link = ($mod_rewrite == '1') ? "$f_id" : "paste.php?favdel=$fu_id";
                            //$f_delete_link = ($mod_rewrite == '1') ? "user.php?favdel&user=$profile_username&fid=$f_id" : "user.php?favdel&user=$profile_username&fid=$f_id";
                            $title = truncate($title, 20, 50);
                            $current_time = time();
                            $past = strtotime('-2 day', $current_time);
                            if ($past <= $Recent_update_u && $Recent_update_u <= $current_time) {
                                $updatenote = "<i class='far fa-check-square fa-lg' aria-hidden='true'></i>";
                            } else {
                                $updatenote = "<i class='far fa-minus-square fa-lg' aria-hidden='true'></i>";
                            }

                            echo '<tr> 
                                                <td>
                                                    <a href="' . $protocol . $baseurl . '/' . $f_id . '" title="' . $ftitle . '">' . ($ftitle) . '</a>
                                                </td>    
                                                <td  data-sort="' . date_format($f_date, 'U') . '" class="td-center">
                                                <span>' . $f_dateui . '</span>
                                                </td>
                                               <td  data-sort="' . $Recent_update_u . '" class="td-center">
                                                  <span>' . $updatenote . '</span>
                                                
                                                </td>
                                                <td class="td-left">';
                                    if (strlen($f_tags) > 0) {
                                    echo tagsToHtmlUser($f_tags,$profile_username);
                                    } else {
                                        echo ' <span class="tag is-warning">No tags</span>';
                                    }


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