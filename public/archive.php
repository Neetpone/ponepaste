<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/../includes/common.php');

use PonePaste\Models\Paste;


// Temp count for untagged pastes
$total_untagged = Paste::doesntHave('tags')->count();

updatePageViews();

$page_template = 'archive';
$page_title = 'Pastes Archive';
$script_bundles[] = 'archive';

require_once(__DIR__ . '/../theme/' . $default_theme . '/common.php');
