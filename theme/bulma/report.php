<main class="bd-main">
    <div class="bd-side-background"></div>
    <div class="bd-main-container container">
        <div class="bd-duo">
            <div class="bd-lead">
                <h1 class="title is-5">Reporting <b><?= pp_html_escape($paste->title) ?></b></h1>
                <form method="post">
                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="reason">Reason</label>
                                <div class="control has-icons-left has-icons-right">
                                    <input type="text" class="input" name="reason" id="reason"
                                           placeholder="Reason for reporting this paste" maxlength="255">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-info"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="field">
                                <?php if (isset($csrf_token)): ?>
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>" />
                                <?php endif; ?>
                                <button type="submit" class="button is-info">Report</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
