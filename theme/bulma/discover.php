<?php
// This is cancer. I'm sorry.
function outputPasteCard($paste, $use_updated = false) {
    echo '<div class="column is-half">
            <div class="card">
                <div class="card-content">
                    <div class="media">
                        <div class="media-content" style="overflow: hidden">
                            <p class="title is-5">
                                <a href="' . urlForPaste($paste) . '">' . pp_html_escape($paste->title) . '</a>
                            </p>
                            <p class="subtitle is-6">
                                <a href="' . urlForMember($paste->user) . '">' . pp_html_escape($paste->user->username) . '</a>
                                <br>
                                <time datetime="' . $paste->created_at . '">' . friendlyDateDifference(date_create(), date_create($use_updated ? $paste->updated_at : $paste->created_at)) . '</time>
                            </p>' .
        ($paste->tags->isNotEmpty() ? tagsToHtml($paste->tags) : '<span class="tag is-warning">no tags</span>') . '
                        </div>
                    </div>
                </div>
           </div></div>';
}

?>
<main class="bd-main">
    <!-- START CONTAINER -->

    <div class="container">
        <div class="bd-duo">
            <div class="bd-lead">
                <!-- Start Row -->
                <div class="row">
                    <section class="section">
                        <div class="tabs">
                            <ul class="tabs-menu">
                                <li class="is-active" data-target="first-tab"><a>Popular</a></li>
                                <li data-target="second-tab"><a>Month's Pop</a></li>
                                <li data-target="third-tab"><a>New</a></li>
                                <li data-target="forth-tab"><a>Updated</a></li>
                                <li data-target="fifth-tab"><a>Random</a></li>
                            </ul>
                        </div>
                        <!-- Start Panel -->

                        <!-- Pop Pastes -->
                        <div class="tab-content" id="first-tab">
                            <div class="panel panel-default">
                                <h1 class="title is-4">Popular Pastes</h1>
                                <div class="columns is-multiline">
                                    <?php foreach ($popular_pastes as $paste): ?>
                                        <?php outputPasteCard($paste); ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <!-- mPop Pastes -->
                        <div class="tab-content" id="second-tab">
                            <div class="panel panel-default">
                                <h1 class="title is-4">This month's popular pastes</h1>
                                <div class="columns is-multiline">
                                    <?php foreach ($monthly_popular_pastes as $paste): ?>
                                        <?php outputPasteCard($paste); ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- New Pastes -->
                        <div class="tab-content" id="third-tab">
                            <div class="panel panel-default">
                                <h1 class="title is-4">New Pastes</h1>
                                <div class="columns is-multiline">
                                    <?php foreach ($recent_pastes as $paste): ?>
                                        <?php outputPasteCard($paste); ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Updated Pastes -->
                        <div class="tab-content" id="forth-tab">
                            <div class="panel panel-default">
                                <h1 class="title is-4">Recently Updated Pastes</h1>
                                <div class="columns is-multiline">
                                    <?php foreach ($updated_pastes as $paste): ?>
                                        <?php outputPasteCard($paste, true); ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Random Pastes -->
                        <div class="tab-content" id="fifth-tab">
                            <div class="panel panel-default">
                                <h1 class="title is-4">Random Pastes</h1>
                                <div class="columns is-multiline">
                                    <?php foreach ($random_pastes as $paste): ?>
                                        <?php outputPasteCard($paste); ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</main>
<script>
    const tabSystem = {
        init() {
            document.querySelectorAll('.tabs-menu').forEach(tabMenu => {
                Array.from(tabMenu.children).forEach(child => {
                    child.addEventListener('click', () => {
                        tabSystem.toggle(child.dataset.target);
                    });
                    if (child.className.includes('is-active')) {
                        tabSystem.toggle(child.dataset.target);
                    }
                });
            });
        },
        toggle(targetId) {
            document.querySelectorAll('.tab-content').forEach(contentElement => {
                contentElement.style.display = contentElement.id === targetId ? 'block' : 'none';
                document.querySelector(`[data-target="${contentElement.id}"]`).classList[contentElement.id === targetId ? 'add' : 'remove']('is-active');
            })
        },
    };
    // use it
    tabSystem.init()
</script>