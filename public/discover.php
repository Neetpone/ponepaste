<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/../includes/common.php');

use PonePaste\Models\Paste;

$popular_pastes = Paste::getMostViewed();
$monthly_popular_pastes = Paste::getMonthPopular();
$recent_pastes = Paste::getRecent();
$updated_pastes = Paste::getRecentlyUpdated();
$random_pastes = Paste::getRandom();

$page_template = 'discover';
$page_title = 'Discover';

require_once(__DIR__ . '/../theme/' . $default_theme . '/common.php');
