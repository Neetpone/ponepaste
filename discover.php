<?php
require_once('includes/common.php');
require_once('includes/functions.php');

use PonePaste\Models\Paste;

$popular_pastes = Paste::getMostViewed();//->map('transformPasteRow');
$monthly_popular_pastes = Paste::getMonthPopular();//->map('transformPasteRow');
$recent_pastes = Paste::getRecent();//->map('transformPasteRow');
$updated_pastes = Paste::getRecentlyUpdated();//->map('transformPasteRow');
$random_pastes = Paste::getRandom();//->map('transformPasteRow');

// Theme
$page_template = 'discover';
$page_title = 'Discover';
require_once('theme/' . $default_theme . '/common.php');

