<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */
define('IN_PONEPASTE', 1);
require_once(__DIR__ .'/../../includes/common.php');

use PonePaste\Helpers\SearchHelper;
use PonePaste\Models\Paste;

$search_helper = SearchHelper::instance();
[$per_page, $current_page] = pp_setup_pagination();

if (isset($_GET['q'])) {
    $search_results = $search_helper->search([
        'query' => $_GET['q'],
        'from' => $current_page * $per_page,
        'size' => $per_page,
    ], function(&$filters) {
        Paste::addFilters($filters);
    })->asArray();
} else {
    $search_results = $search_helper->search([
        'from' => $current_page * $per_page,
        'size' => $per_page,
    ], function(&$filters) {
        Paste::addFilters($filters);
    })->asArray();
}

$total_records = $search_results['hits']['total']['value'];
$search_results = SearchHelper::toRecords($search_results);
$pastes = $search_results->map(function($paste) {
    return [
        'id' => $paste->id,
        'title' => $paste->title,
        'author' => $paste->user->username,
    ];
});

header('Content-Type: application/json; charset=UTF-8');
echo json_encode([
    'total_records' => $total_records,
    'pastes' => $pastes,
]);
