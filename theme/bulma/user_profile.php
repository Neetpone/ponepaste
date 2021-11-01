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

                <?php if ($is_current_user): ?>
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
                        <?php foreach ($profile_pastes as $paste): ?>
                            <?php
                                $escaped_title = pp_html_escape(truncate($paste->title, 20, 50));
                                $p_date = new DateTime($paste->created_at);
                                $p_visible = match (intval($paste->visible)) {
                                    0 => 'Public',
                                    1 => 'Unlisted',
                                    2 => 'Private'
                                };
                            ?>
                            <?php if ($is_current_user || $row['visible'] == Paste::VISIBILITY_PUBLIC): ?>
                                <tr>
                                    <td><a href="<?= urlForPaste($paste) ?>" title="<?= $escaped_title ?>"><?= $escaped_title ?></a></td>
                                    <td data-sort="<?= $p_date->format('U') ?>" class="td-center"><?= $p_date->format('d F Y') ?></td>
                                    <td class="td-center"><?= $p_visible; ?></td>
                                    <td class="td-center"><?= $paste->views ?></td>
                                    <td class="td-left"><?= tagsToHtmlUser($paste->tags, $profile_username); ?></td>
                                    <!-- Delete button here? -->
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
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
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($profile_favs as $paste): ?>
                            <?php
                            $escaped_title = pp_html_escape(truncate($paste->title, 20, 50));
                            $f_date = new DateTime($paste->pivot->f_time);
                            $update_date = new DateTime($paste->updated_at);
                            $delta = $update_date->diff(new DateTime(), true);
                            ?>
                            <?php if ($is_current_user || $row['visible'] == Paste::VISIBILITY_PUBLIC): ?>
                                <tr>
                                    <td><a href="<?= urlForPaste($paste) ?>" title="<?= $escaped_title ?>"><?= $escaped_title ?></a></td>
                                    <td data-sort="<?= $p_date->format('U') ?>" class="td-center"><?= $p_date->format('d F Y') ?></td>
                                    <td class="td-center">
                                        <?php if ($delta->days <= 2): ?>
                                            <i class='far fa-check-square fa-lg' aria-hidden='true'></i>
                                        <?php else: ?>
                                            <i class='far fa-minus-square fa-lg' aria-hidden='true'></i>
                                        <?php endif; ?>
                                    </td>
                                    <td class="td-left"><?= tagsToHtmlUser($paste->tags, $profile_username); ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td class="td-right">Title</td>
                            <td class="td-center">Date Favourited</td>
                            <td class="td-center">Status</td>
                            <td class="td-center">Tags</td>
                        </tr>
                        </tfoot>
                        <?php } ?>
                    </table>
                </div>
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