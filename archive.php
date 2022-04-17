<?php
require_once('includes/common.php');

use PonePaste\Models\Paste;


$date = date('jS F Y');

// Temp count for untagged pastes
$total_untagged = Paste::doesntHave('tags')->count();

updatePageViews();

// Theme
$page_template = 'archive';
$page_title = 'Pastes Archive';
$script_bundles[] = 'archive';
require_once('theme/' . $default_theme . '/common.php');
