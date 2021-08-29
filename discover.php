<?php
define('IN_PONEPASTE', 1);
require_once('includes/common.php');
require_once('includes/functions.php');

use PonePaste\Models\Paste;

function transformPasteRow(Paste $row) : array {
    return [
        'id' => $row['id'],
        'title' => $row['title'],
        'member' => $row['member'],
        'time' => $row['created_at'],
        'time_update' => $row['updated_at'],
        'friendly_update_time' => friendlyDateDifference(new DateTime($row['updated_at']), new DateTime()),
        'friendly_time' => friendlyDateDifference(new DateTime($row['created_at']), new DateTime()),
        'tags' => $row->tags
    ];
}

$popular_pastes = Paste::getMostViewed();//->map('transformPasteRow');
$monthly_popular_pastes = Paste::getMonthPopular();//->map('transformPasteRow');
$recent_pastes = Paste::getRecent();//->map('transformPasteRow');
$updated_pastes = Paste::getRecentlyUpdated();//->map('transformPasteRow');
$random_pastes = Paste::getRandom();//->map('transformPasteRow');

// Theme
$page_template = 'discover';
$page_title = 'Discover';
require_once('theme/' . $default_theme . '/common.php');

