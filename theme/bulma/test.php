<?php
use PonePaste\Models\Paste;

function highlight(string $text): string {
    return preg_replace('/\^highlight\^(.+?)\^highlight\^/', '<span class="highlight">$1</span>', $text);
}
?>
<main class="bd-main">
    <div class="bd-side-background"></div>
    <div class="bd-main-container container">
        <div class="bd-duo">
            <div class="bd-lead">
                <h1 class="title is-4">Search Test</h1>
                <form class="table_filterer" method="GET">
                    <label>
                        <i class="fa fa-search"></i>
                        <input type="search" name="q" placeholder="Search..." value="<?= pp_html_escape($_GET['q'] ?? '') ?>">
                    </label>
                    <button class="button" type="submit">Search</button>
                </form>
                <?php if (can('reindex', Paste::class)): ?>
                    <form method="post">
                        <button class="button" type="submit" name="reindex">Reindex</button>
                    </form>
                <?php endif; ?>
                <?php if (isset($search_results)): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <?php if (!empty($highlights)): ?>
                                    <th>Match</th>
                                <?php endif; ?>
                                <th>Tags</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <?php foreach($search_results as $hit): ?>
                            <tr>
                                <td><a href="<?= urlForPaste($hit) ?>"><?= pp_html_escape($hit->title) ?></a></td>
                                <td><a href="<?= urlForMember($hit->user) ?>"><?= pp_html_escape($hit->user->username) ?></a></td>
                                <?php if (isset($highlights) && isset($highlights[$hit->id])): ?>
                                    <td>...<?= highlight(pp_html_escape($highlights[$hit->id]['content'][0])) ?>...</td>
                                <?php endif; ?>
                                <td><?= tagsToHtml($hit->tags) ?></td>
                                <td><?= pp_html_escape($hit->created_at) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <?= paginate($current_page, $per_page, $total_records) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
