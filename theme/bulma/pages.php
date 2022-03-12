<main class="bd-main">
    <div class="bd-side-background"></div>
    <div class="bd-main-container container">
        <div class="bd-duo">
            <div class="bd-lead">
                <?php if (isset($page)): ?>
                    <h1 class="title is-4"><?= pp_html_escape($page->page_title); ?><h1>
                    <?= $page->page_content; ?>
                <?php else: ?>
                    <h1 class="title is-4">Page not found.</h1>
                    <p class="help is-danger subtitle is-6">A page with that name doesn't seem to exist.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>