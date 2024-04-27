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
    ->where('hidden', false)
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

$pastes = $pastes->orderBy('id', 'desc');
$total_results = $pastes->count();

$pastes = $pastes->limit($per_page)->offset($per_page * $current_page);

$pastes = $pastes->get();

// Temp count for untagged pastes
$total_untagged = Paste::doesntHave('tags')->count();

updatePageViews();

$page_template = 'archive';
$page_title = 'Pastes Archive';
$script_bundles[] = 'archive';

require_once(__DIR__ . '/../theme/' . $default_theme . '/common.php');
