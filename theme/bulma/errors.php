<main class="bd-main">
    <div class="bd-side-background"></div>
    <div class="bd-main-container container">
        <div class="bd-duo">
            <div class="bd-lead">
                <?php if (isset($error)): ?>
                    <p class="help is-danger subtitle is-6"><?= pp_html_escape($error) ?></p>
                    <?php if ($password_required): ?>
                        <h1 class="title is-5">This paste is password-protected.</h1>
                        <form method="post">
                            <div class="field has-addons">
                                <div class="control">
                                    <input type="hidden" name="id" value="<?= $paste_id; ?>"/>
                                    <input type="password" class="input" name="mypass"
                                           placeholder="Password" />
                                </div>
                            </div>
                            <button type="submit" name="submit" class="button is-info">Submit</button>
                        </form>
                    <?php endif; ?>
                    <a href="/" class="btn btn-default">New Paste</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
