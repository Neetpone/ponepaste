<?php
define('IN_PONEPASTE', 1);
require_once('../includes/common.php');

use PonePaste\Models\Paste;

$pastes = Paste::with([
    'user' => function($query) {
        $query->select('users.id', 'username');
    },
    'tags' => function($query) {
        $query->select('tags.id', 'name', 'slug');
    }
])->select(['id', 'user_id', 'title'])->get();

header('Content-Type: application/json; charset=UTF-8');

echo json_encode(['data' => $pastes->map(function($paste) {
    return [
        'id' => $paste->id,
        'title' => $paste->title,
        'author' => $paste->user->username,
        'tags' => $paste->tags->map(function($tag) {
            return ['slug' => $tag->slug, 'name' => $tag->name];
        })
    ];
})]);
