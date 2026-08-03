<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/../includes/common.php');

use PonePaste\Models\Paste;

$per_page = 20;
$current_page = 0;
$filter_value = '';

if (!empty($_GET['page'])) {
    $current_page = max(0, intval($_GET['page']));
}

if (!empty($_GET['per_page'])) {
    $per_page = max(1, min(100, intval($_GET['per_page'])));
}

if (!empty($_GET['q'])) {
    $filter_value = $_GET['q'];
}

$pastes = Paste::with([
    'user' => function($q) {
        $q->select('users.id', 'username');
    },
    'tags' => function($q) {
        $q->select('tags.id', 'name', 'slug');
    }])
    ->select('id', 'user_id', 'title', 'created_at', 'updated_at')
    ->where('visible', Paste::VISIBILITY_PUBLIC)
    ->where('is_hidden', false)
    ->where('password', null)
    ->whereRaw("((expiry IS NULL) OR ((expiry != 'SELF') AND (expiry > NOW())))");

if (!empty($filter_value)) {
    if ($filter_value === 'untagged') {
        $pastes = $pastes->doesntHave('tags');
    } else {
        $pastes = $pastes->where(function($query) use ($filter_value) {
            $query->where('title', 'LIKE', '%' . escapeLikeQuery($filter_value) . '%')
                ->orWhereHas('tags', function($q) use ($filter_value) {
                    $q->where('name', 'LIKE', '%' . escapeLikeQuery($filter_value) . '%');
                });
        });
    }
}

$total_results = $pastes->count();
$max_page = ceil($total_results / $per_page);

if ($current_page <= $max_page) {
    $pastes = $pastes->orderBy('id', 'desc')
                     ->limit($per_page)
                     ->offset($current_page * $per_page)
                     ->get();
} else {
    $pastes = null;
}

// Temp count for untagged pastes
if ($redis->exists('total_untagged')) {
    $total_untagged = (int) $redis->get('total_untagged');
} else {
    $total_untagged = Paste::doesntHave('tags')->count();
    $redis->setEx('total_untagged', 3600, $total_untagged);
}

updatePageViews();

var_dump(['total' => $total_results, 'per_page' => $per_page, 'current_page' => $current_page, 'total_untagged' => $total_untagged, 'max_page' => $max_page, 'total_results' => $total_results]);

if ($pastes === null || $pastes->isEmpty()) {
    $page_template = 'errors';
    $page_title = 'Bad Request';
    header('HTTP/1.1 400 Bad Request');
    flashError('Bad request (page does not exist.)');
} else {
    $page_template = 'archive';
    $page_title = 'Pastes Archive';
    $script_bundles[] = 'archive';
}

require_once(__DIR__ . '/../theme/' . $default_theme . '/common.php');
