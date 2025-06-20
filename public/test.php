<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/../includes/common.php');

use PonePaste\Helpers\SearchHelper;
use PonePaste\Models\Paste;

[$per_page, $current_page] = pp_setup_pagination();

if (isset($_GET['q'])) {
    $search_results = SearchHelper::instance()->fancySearch([
        'query' => trim($_GET['q']),
        'from' => $current_page * $per_page,
        'size' => $per_page,

    ], function(&$filters) {
        Paste::addFilters($filters);
    })->asArray();

    // This map will store the highlights for each hit, and is used to render the highlights in the template
    $highlights = [];

    foreach ($search_results['hits']['hits'] as $hit) {
        if (isset($hit['highlight'])) {
            $highlights[$hit['_id']] = $hit['highlight'];
        }
    }
} else {
    $search_results = SearchHelper::instance()->fancySearch([
        'from' => $current_page * $per_page,
        'size' => $per_page,
    ], function(&$filters) {
        Paste::addFilters($filters);
    })->asArray();
}

$total_records = $search_results['hits']['total']['value'];
$search_results = SearchHelper::toRecords($search_results);

var_dump($total_records);

$page_title = 'Search Test';
$page_template = 'test';
$csrf_token = setupCsrfToken();

$script_bundles[] = 'test';

require_once(__DIR__ . '/../theme/' . $default_theme . '/common.php');
