<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/../includes/common.php');

use PonePaste\Helpers\SearchHelper;
use PonePaste\Models\Paste;

if (isset($_POST['reindex'])) {
    Paste::chunk(5, function($models) {
        foreach ($models as $model) {
            SearchHelper::instance()->indexPaste($model);

        }
    });
}

$search_results = SearchHelper::instance()->search($_GET['q']);

$page_title = 'Search Test';
$page_template = 'test';
$csrf_token = setupCsrfToken();




require_once(__DIR__ . '/../theme/' . $default_theme . '/common.php');
