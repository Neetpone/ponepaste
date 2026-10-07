<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/../../includes/common.php');

use PonePaste\Models\Paste;

header('Content-Type: application/json; charset=UTF-8');

if (!PP_DEBUG && $redis->exists(Paste::AJAX_PASTES_CACHE_KEY)) {
    echo $redis->get(Paste::AJAX_PASTES_CACHE_KEY);
    die;
}

$pastes = Paste::with([
    'user' => function($query) {
        $query->select('users.id', 'username');
    },
    'tags' => function($query) {
        $query->select('tags.id', 'name', 'slug');
    }
])->select(['id', 'user_id', 'title', 'expiry', 'created_at', 'updated_at', 'visible', 'code'])
    ->where('visible', '!=', Paste::VISIBILITY_PRIVATE)
    ->where('is_hidden', false)
    ->where('password', null)
    ->whereRaw("((expiry IS NULL) OR ((expiry != 'SELF') AND (expiry > NOW())))")
    ->orderBy('id', 'desc')
    ->get();

$pastes_json = json_encode(['data' => $pastes->map(function($paste) {
    return [
        'id' => $paste->id,
        'created_at' => $paste->created_at,
        'updated_at' => $paste->updated_at ?? $paste->created_at,
        'visibility' => $paste->visible,
        'title' => $paste->title,
        'format' => match ($paste->code) {
            'text', 'plaintext' => 'plaintext',
            'pastedown_old', 'pastedown' => 'pastedown',
            default => 'green',
        },
        'author' => $paste->user->username,
        'author_id' => $paste->user->id,
        'tags' => $paste->tags->map(function($tag) {
            return ['slug' => $tag->slug, 'name' => $tag->name];
        })
    ];
})]);

if (!PP_DEBUG) {
    $redis->setEx(Paste::AJAX_PASTES_CACHE_KEY, 3600, $pastes_json);
}

echo $pastes_json;
