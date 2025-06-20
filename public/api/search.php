<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */
define('IN_PONEPASTE', 1);
require_once(__DIR__ .'/../../includes/common.php');

use PonePaste\Helpers\SearchHelper;
use PonePaste\Models\Paste;

$search_helper = SearchHelper::instance();
[$per_page, $current_page] = pp_setup_pagination();

$sortField = $_GET['sf'] ?? 'created_at';
$sortDir = $_GET['sd'] ?? 'desc';


if (!in_array($sortField, ['created_at', 'title', 'author', 'updated_at', 'tags'])) {
    $sortField = 'created_at';
}

if (!in_array($sortDir, ['asc', 'desc'])) {
    $sortDir = 'desc';
}

$sortField = match($sortField) {
    'created_at' => 'created_at',
    'title' => 'title_keyword',
    'author' => 'author_keyword',
};

if (!empty($_GET['q'])) {
    $search_results = $search_helper->fancySearch([
        'query' => $_GET['q'],
        'from' => $current_page * $per_page,
        'size' => $per_page,
        'sorts' => [[$sortField => $sortDir]],
    ], function(&$filters) {
        Paste::addFilters($filters);
    })->asArray();
} else {
    $search_results = $search_helper->fancySearch([
        'from' => $current_page * $per_page,
        'size' => $per_page,
        'sorts' => [[$sortField => $sortDir]],
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
        'updated_at' => $paste->updated_at ?? $paste->created_at,
        'tags' => $paste->tags->map(function($tag) {
            return [
                'name' => $tag->name,
                'slug' => $tag->slug,
            ];
        })->toArray(),
    ];
});

header('Content-Type: application/json; charset=UTF-8');
echo json_encode([
    'total_records' => $total_records,
    'pastes' => $pastes,
]);
