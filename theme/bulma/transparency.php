<main class="bd-main">
    <div class="container">
        <div class="bd-duo">
            <div class="bd-lead">
                <h1 class="title is-4">Transparency</h1>
                <p>These are the most recently deleted pastes on the site.</p>
                <p>For pastes deleted on or before October 30, 2024, pastes will show as deleted on October 30, 2024.</p>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Author</th>
                            <th>Title</th>
                            <th>Deleted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($deleted_pastes as $paste): ?>
                            <tr>
                                <td><?= $paste->id ?></td>
                                <td><?= pp_html_escape($paste->user->username) ?></td>
                                <td><?= pp_html_escape($paste->title) ?></td>
                                <td><?= pp_html_escape($paste->deleted_at ?: 'Unknown') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>