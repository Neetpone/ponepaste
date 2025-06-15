<?php

use PonePaste\Search\SearchParser;

?>
<main class="bd-main">
    <div class="bd-side-background"></div>
    <div class="bd-main-container container">
        <div class="bd-duo">
            <div class="bd-lead">
                <form method="post">
                    <button class="button" type="submit" name="reindex">Reindex</button>
                </form>
                <pre>
                    <?= var_dump((new SearchParser($_GET['test'], "tags.name"))->parsed()) ?>
                </pre>
                <?php if (isset($_GET['q'])): ?>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Title</th>
                        </tr>
                    </thead>
                    <?php foreach($search_results['hits']['hits'] as $hit): ?>
                        <tr>
                            <td><?= $hit['_source']['title'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <pre>
                    <?= var_dump($search_results['hits']) ?>
                </pre>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>