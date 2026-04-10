<main class="bd-main">
    <div class="container content">
        <?php if (isset($page)): ?>
            <h1><?= pp_html_escape($page->page_title); ?></h1>
            <?= $page_content /* Already processed with HTML Purifier. */ ?>
        <?php else: ?>
            <h1>Page not found.</h1>
            <p class="help is-danger subtitle is-6">A page with that name doesn't seem to exist.</p>
        <?php endif; ?>
    </div>
</main>