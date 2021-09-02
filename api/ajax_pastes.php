<?php

use PonePaste\Models\Paste;

header('Content-Type: application/json; charset=UTF-8');

define('IN_PONEPASTE', 1);
require_once('../includes/common.php');
require_once('../includes/NonRetardedSSP.class.php');

function transformPaste(Paste $paste) {
    $titleHtml = '<a href="/' . urlencode($paste->id) . '">' . pp_html_escape($paste->title) . '</a>';
    $authorHtml = '<a href="/user/' . urlencode($paste->user->username) . '">' . pp_html_escape($paste->user->username) . '</a>';
    $tagsHtml = '';//tagsToHtml($row[3]);

    return [
        $titleHtml,
        $authorHtml,
        $tagsHtml
    ];
}

$pastes = Paste::with([
        'user' => function($query) {
            $query->select('users.id', 'username');
        },
        'tags' => function($query) {
            $query->select('tags.id', 'name', 'slug');
        }
    ])->select(['id', 'user_id', 'title']);

$data = NonRetardedSSP::run(
    $conn, $_GET, $pastes
);

$data['data'] = $data['data']->map('transformPaste');

echo json_encode($data);
