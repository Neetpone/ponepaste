<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/../includes/common.php');

use PonePaste\Helpers\SearchHelper;
use PonePaste\Models\Paste;

list($per_page, $current_page) = pp_setup_pagination('', 25, 1);

if (isset($_POST['reindex'])) {
    SearchHelper::instance()->dropPasteIndex();
    SearchHelper::instance()->createPasteIndex();
    Paste::chunk(25, function($models) {
        foreach ($models as $model) {
            SearchHelper::instance()->indexPaste($model);
        }
    });
}

if (isset($_GET['q'])) {
    $search_results = SearchHelper::instance()->fancySearch([
        'query' => trim($_GET['q']),
        'from' => $current_page * $per_page,
        'size' => $per_page,

    ], function(&$filters) {
        // Public and not hidden
        $filters[] = [
            'bool' => [
                'must' => [
                    [
                        'term' => ['visible' => Paste::VISIBILITY_PUBLIC]
                    ],
                    [
                        'term' => ['is_hidden' => false]
                    ]
                ]
            ]
        ];

        // Non-expired or never expiring
        $filters[] = [
            'bool' => [
                'should' => [
                    [
                        'range' => ['expiry' => ['gte' => time()]]
                    ],
                    [
                        'term' => ['expiry' => 0]
                    ]
                ]
            ]
        ];
    })->asArray();

    // This map will store the highlights for each hit, and is used to render the highlights in the template
    $highlights = [];

    foreach ($search_results['hits']['hits'] as $hit) {
        if (isset($hit['highlight'])) {
            $highlights[$hit['_id']] = $hit['highlight'];
        }
    }

    $total_records = $search_results['hits']['total']['value'];
    $search_results = SearchHelper::toRecords($search_results);
} else {
    
}

$page_title = 'Search Test';
$page_template = 'test';
$csrf_token = setupCsrfToken();

require_once(__DIR__ . '/../theme/' . $default_theme . '/common.php');
