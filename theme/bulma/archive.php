<main class="bd-main">
    <div class="container">
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
                <form class="table_filterer" method="GET">
                    <label><i class="fa fa-search"></i>
                        <input class="search" type="search" name="q" placeholder="Filter..."
                               value="<?= pp_html_escape($filter_value); ?>"/>
                    </label>
                    <label>
                        Show&nbsp;
                        <select name="per_page">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        &nbsp;per page
                    </label>
                    <button type="submit" class="button js-hidden">Search</button>
                </form>
                <table id="archive" class="table is-fullwidth is-hoverable">
                    <thead>
                    <tr class="paginator__sort">
                        <th data-sort-field="title">Title</th>
                        <th data-sort-field="author">Author</th>
                        <th data-sort-field="updated_at">Updated</th>
                        <th data-sort-field="tags">Tags</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($pastes as $paste): ?>
                        <tr>
                            <td><a href="<?= urlForPaste($paste); ?>"><?= pp_html_escape($paste->title) ?></a></td>
                            <td><?= pp_html_escape($paste->user->username) ?></td>
                            <td><?= pp_html_escape($paste->updated_at ?? $paste->created_at) ?></td>
                            <td><?= tagsToHtml($paste->tags) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Updated</th>
                        <th>Tags</th>
                    </tr>
                    </tfoot>
                </table>
                <div class="loading_container is-hidden">
                </div>

                <div class="paginator">
                    <?= paginate($current_page, $per_page, $total_results) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>