<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/../includes/common.php');

use PonePaste\Models\Paste;

$deleted_pastes = Paste::select('id', 'updated_at', 'title', 'user_id')
                        ->with(['user' => function($q) { $q->select('id', 'username'); }])
                        ->where('is_hidden', true)
                        ->orderBy('deleted_at', 'desc')
                        ->limit(20)
                        ->get();

$page_template = 'transparency';
$page_title = 'Transparency';

require_once(__DIR__ . '/../theme/' . $default_theme . '/common.php');
