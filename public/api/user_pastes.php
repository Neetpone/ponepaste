<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */
define('IN_PONEPASTE', 1);
require_once(__DIR__ .'/../../includes/common.php');

use PonePaste\Models\Paste;

header('Content-Type: application/json; charset=UTF-8');

if (!isset($_GET['user_id'])) {
    echo json_encode(['error' => 'No user ID provided.']);
    die();
}

$user_id = (int) $_GET['user_id'];

$is_current_user = $current_user !== null && $user_id === $current_user->id;

$pastes = Paste::with([
    'user' => function($query) {
        $query->select('users.id', 'username');
    },
    'tags' => function($query) {
        $query->select('tags.id', 'name', 'slug');
    }
])->select(['id', 'user_id', 'title', 'expiry', 'created_at', 'views', 'visible'])
    ->where('hidden', false)
    ->where('user_id', $user_id)
    ->whereRaw("((expiry IS NULL) OR ((expiry != 'SELF') AND (expiry > NOW())))");

if (!$is_current_user) {
    $pastes = $pastes->where('visible', Paste::VISIBILITY_PUBLIC);
}

$pastes = $pastes->get();

$pastes_json = json_encode(['data' => $pastes->map(function($paste) {
    return [
        'id' => $paste->id,
        'created_at' => $paste->created_at,
        'title' => $paste->title,
        'author' => $paste->user->username,
        'author_id' => $paste->user->id,
        'views' => $paste->views,
        'visibility' => $paste->visible,
        'tags' => $paste->tags->map(function($tag) {
            return ['slug' => $tag->slug, 'name' => $tag->name];
        })
    ];
})]);

echo $pastes_json;
