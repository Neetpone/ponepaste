<?php
/*
 * Paste <https://github.com/jordansamuel/PASTE> - Clean theme
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License in GPL.txt for more details.
 */
?>

<main class="bd-main">
    <!-- START CONTAINER -->
    <div class="bd-side-background"></div>
    <div class="bd-main-container container">
        <div class="bd-duo">
            <div class="bd-lead">
                <!-- Start Row -->
                <div class="row">
                    <section class="section">
                        <div class="tabs">
                            <ul class="tabs-menu">
                                <li class="is-active" data-target="first-tab"><a>Popular</a></li>
                                <li data-target="second-tab"><a>Months Pop</a></li>
                                <li data-target="third-tab"><a>New</a></li>
                                <li data-target="forth-tab"><a>Updated</a></li>
                                <li data-target="fifth-tab"><a>Random</a></li>
                            </ul>
                        </div>
                        <!-- Start Panel -->

                        <!-- Pop Pastes -->
                        <div class="tab-content" id="first-tab">
                            <div class="panel panel-default">
                                <h1 class="title is-4"><?php echo $lang['popular']; ?></h1>
                                <div class="columns is-multiline">
                                    <?php foreach ($popular_pastes as $paste): ?>
                                        <div class="column is-half">
                                            <div class="card">
                                                <div class="card-content">
                                                    <div class="media">
                                                        <div class="media-content" style="overflow: hidden">
                                                            <p class="title is-5">
                                                                <a href="<?= urlForPaste($paste['id']) ?>"
                                                                   title="<?= $paste['title'] ?>"> <?= $paste['title'] ?> </a>
                                                            </p>
                                                            <p class="subtitle is-6">
                                                                <a href="<?= urlForMember($paste['member']) ?>"><?= $paste['member'] ?></a>
                                                                <br>
                                                                <time datetime="<?= $paste['time'] ?>"><?= $paste['friendly_time'] ?></time>
                                                            </p>
                                                            <?php
                                                            if (count($paste['tags']) !== 0) {
                                                                echo tagsToHtml($paste['tags']);
                                                            } else {
                                                                echo ' <span class="tag is-warning">No tags</span>';
                                                            }
                                                            ?>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <!-- mPop Pastes -->
                        <div class="tab-content" id="second-tab">
                            <div class="panel panel-default">
                                <h1 class="title is-4"><?php echo $lang['monthpopular']; ?></h1>
                                <div class="columns is-multiline">
                                    <?php foreach ($monthly_popular_pastes as $paste): ?>
                                        <div class="column is-half">
                                            <div class="card">
                                                <div class="card-content">
                                                    <div class="media">
                                                        <div class="media-content" style="overflow: hidden">
                                                            <p class="title is-5">
                                                                <a href="<?= urlForPaste($paste['id']) ?>"
                                                                   title="<?= $paste['title'] ?>"> <?= $paste['title'] ?> </a>
                                                            </p>
                                                            <p class="subtitle is-6">
                                                                <a href="<?= urlForMember($paste['member']) ?>"><?= $paste['member'] ?></a>
                                                                <br>
                                                                <time datetime="<?= $paste['time'] ?>"><?= $paste['friendly_time'] ?></time>
                                                            </p>
                                                            <?php
                                                            if (count($paste['tags']) !== 0) {
                                                                echo tagsToHtml($paste['tags']);
                                                            } else {
                                                                echo ' <span class="tag is-warning">No tags</span>';
                                                            }
                                                            ?>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- New Pastes -->
                        <div class="tab-content" id="third-tab">
                            <div class="panel panel-default">
                                <h1 class="title is-4"><?php echo $lang['recentpastes']; ?></h1>
                                <div class="columns is-multiline">
                                    <?php foreach ($recent_pastes as $paste): ?>
                                        <div class="column is-half">
                                            <div class="card">
                                                <div class="card-content">
                                                    <div class="media">
                                                        <div class="media-content" style="overflow: hidden">
                                                            <p class="title is-5">
                                                                <a href="<?= urlForPaste($paste['id']) ?>"
                                                                   title="<?= $paste['title'] ?>"> <?= $paste['title'] ?> </a>
                                                            </p>
                                                            <p class="subtitle is-6">
                                                                <a href="<?= urlForMember($paste['member']) ?>"><?= $paste['member'] ?></a>
                                                                <br>
                                                                <time datetime="<?= $paste['time'] ?>"><?= $paste['friendly_time'] ?></time>
                                                            </p>
                                                            <?php
                                                            if (count($paste['tags']) !== 0) {
                                                                echo tagsToHtml($paste['tags']);
                                                            } else {
                                                                echo ' <span class="tag is-warning">No tags</span>';
                                                            }
                                                            ?>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Updated Pastes -->
                        <div class="tab-content" id="forth-tab">
                            <div class="panel panel-default">
                                <h1 class="title is-4"><?php echo $lang['updatedgreen']; ?></h1>
                                <div class="columns is-multiline">
                                    <?php foreach ($updated_pastes as $paste): ?>
                                        <div class="column is-half">
                                            <div class="card">
                                                <div class="card-content">
                                                    <div class="media">
                                                        <div class="media-content" style="overflow: hidden">
                                                            <p class="title is-5">
                                                                <a href="<?= urlForPaste($paste['id']) ?>"
                                                                   title="<?= $paste['title'] ?>"> <?= $paste['title'] ?> </a>
                                                            </p>
                                                            <p class="subtitle is-6">
                                                                <a href="<?= urlForMember($paste['member']) ?>"><?= $paste['member'] ?></a>
                                                                <br>
                                                                <time datetime="<?= $paste['time'] ?>"><?= $paste['friendly_update_time'] ?></time>
                                                            </p>
                                                            <?php
                                                            if (count($paste['tags']) !== 0) {
                                                                echo tagsToHtml($paste['tags']);
                                                            } else {
                                                                echo ' <span class="tag is-warning">No tags</span>';
                                                            }
                                                            ?>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Random Pastes -->
                        <div class="tab-content" id="fifth-tab">
                            <div class="panel panel-default">
                                <h1 class="title is-4"><?php echo $lang['random']; ?></h1>
                                <div class="columns is-multiline">
                                    <?php foreach ($random_pastes as $paste): ?>
                                        <div class="column is-half">
                                            <div class="card">
                                                <div class="card-content">
                                                    <div class="media">
                                                        <div class="media-content" style="overflow: hidden">
                                                            <p class="title is-5">
                                                                <a href="<?= urlForPaste($paste['id']) ?>"
                                                                   title="<?= $paste['title'] ?>"> <?= $paste['title'] ?> </a>
                                                            </p>
                                                            <p class="subtitle is-6">
                                                                <a href="<?= urlForMember($paste['member']) ?>"><?= $paste['member'] ?></a>
                                                                <br>
                                                                <time datetime="<?= $paste['time'] ?>"><?= $paste['friendly_time'] ?></time>
                                                            </p>
                                                            <?php
                                                            if (count($paste['tags']) !== 0) {
                                                                echo tagsToHtml($paste['tags']);
                                                            } else {
                                                                echo ' <span class="tag is-warning">No tags</span>';
                                                            }
                                                            ?>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script>
    const tabSystem = {
        init() {
            document.querySelectorAll('.tabs-menu').forEach(tabMenu => {
                Array.from(tabMenu.children).forEach((child, ind) => {
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