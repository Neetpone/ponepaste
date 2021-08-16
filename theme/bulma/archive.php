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
?>
<script>
    $(document).ready(function () {
        $("#archive").dataTable({
            rowReorder: { selector: 'td:nth-child(2)'},
            responsive: true,
            processing: true,
            autoWidth: false,
            ajax: "api/ajax_pastes.php",
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

<main class="bd-main">
    <div class="preloader"></div>
    <div class="bd-side-background"></div>
    <div class="bd-main-container container">
        <div class="bd-duo">
            <div class="bd-lead">
                <article class="message is-info">
                    <div class="message-body">
                        There are <strong><?php echo $total_untagged ?></strong> pastes that still need to be tagged.
                    </div>
                </article>
                <?php if ($privatesite == "on") { // Site permissions
                    ?>
                    <h1 class="title is-5"><?php echo $lang['siteprivate']; ?></h1>
                <?php } else { ?>
                <h1 class="title is-4"><?php echo $lang['archives']; ?></h1>
                <table id="archive" class="table is-fullwidth is-hoverable">
                    <thead>
                    <tr>
                        <th><?php echo $lang['pastetitle']; ?></th>
                        <th><?php echo $lang['author']; ?></th>
                        <th><?php echo $lang['tags']; ?></th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th><?php echo $lang['pastetitle']; ?></th>
                        <th><?php echo $lang['author']; ?></th>
                        <th><?php echo $lang['tags']; ?></th>
                    </tr>
                    </tfoot>
                    <tbody>
                    </tbody>
                </table>

                <?php
                if (isset($site_ads)) {
                    echo $site_ads['ads_2'];
                }
                ?>
            </div>
            <?php }
            if ($privatesite != "on") {
                require_once('theme/' . $default_theme . '/sidebar.php');
            }
            ?>
        </div>
    </div>
</main>