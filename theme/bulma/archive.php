<script>
    /*$(document).ready(function () {
        $("#archive").dataTable({
            rowReorder: {selector: 'td:nth-child(2)'},
            responsive: true,
            processing: true,
            language: {processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '},
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
    });*/


</script>

<style>
    .paginator > a {
        margin: 0.25rem;
    }

    .paginator > a.disabled {
        pointer-events: none;
        color: gray;
    }
</style>

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
                <?php if ($site_is_private): ?>
                    <h1 class="title is-5">This pastebin is private.</h1>
                <?php else: ?>
                <h1 class="title is-4">Pastes Archive</h1>
                <table id="archive" class="table is-fullwidth is-hoverable">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Tags</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Filled by DataTables -->
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Tags</th>
                    </tr>
                    </tfoot>
                </table>

                <div class="paginator"></div>


                <?php
                if (isset($site_ads)) {
                    echo $site_ads['ads_2'];
                }
                ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>
<script>

</script>