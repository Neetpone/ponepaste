<style>
    .paginator > a {
        margin: 0.25rem;
    }

    .paginator > a.disabled {
        pointer-events: none;
        color: gray;
    }

    .paginator__sort > th {
        cursor: pointer;
    }

    .paginator__sort--down, .paginator__sort--up {
        background-color: lightblue;
    }

    .paginator__sort--down:after {
        padding-left: 0.25rem;
        content: '▼';
    }

    .paginator__sort--up:after {
        padding-left: 0.25rem;
        content: '▲';
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
                        There are <strong><?= $total_untagged ?></strong> pastes that still need to be tagged.
                    </div>
                </article>
                <?php if ($site_is_private): ?>
                    <h1 class="title is-5">This pastebin is private.</h1>
                <?php else: ?>
                <h1 class="title is-4">Pastes Archive</h1>
                <div class="table_filterer">
                    <label><i class="fa fa-search"></i>
                        <input class="search" type="search" name="search" placeholder="Filter..."/>
                    </label>
                    Show&nbsp;
                    <select name="per_page">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    &nbsp;per page
                </div>
                <table id="archive" class="table is-fullwidth is-hoverable">
                    <thead>
                    <tr class="paginator__sort">
                        <th data-sort-field="title">Title</th>
                        <th data-sort-field="author">Author</th>
                        <th data-sort-field="tags">Tags</th>
                    </tr>
                    </thead>
                    <tbody>
<!--                        <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>-->
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
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>