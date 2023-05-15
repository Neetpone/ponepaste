<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */
define('IN_PONEPASTE', 1);
require_once(__DIR__ .'/../../includes/common.php');

use PonePaste\Models\Paste;

if (empty($_GET['q']) && $redis->exists('ajax_pastes')) {
    header('Content-Type: application/json; charset=UTF-8');
    echo $redis->get('ajax_pastes');
    die;
}

$pastes = Paste::with([
    'user' => function($query) {
        $query->select('users.id', 'username');
    },
    'tags' => function($query) {
        $query->select('tags.id', 'name', 'slug');
    }
])->select(['id', 'user_id', 'title', 'expiry'])
    ->where('visible', Paste::VISIBILITY_PUBLIC)
    ->where('hidden', false)
    ->whereRaw("((expiry IS NULL) OR ((expiry != 'SELF') AND (expiry > NOW())))");

if (!empty($_GET['q']) && is_string($_GET['q'])) {
    $tags = explode(',', $_GET['q']);
    $pastes = $pastes->whereHas('tags', function($query) use ($tags) {
        $query->where('name', $tags);
    });
}

$pastes = $pastes->get();

header('Content-Type: application/json; charset=UTF-8');

$pastes_json = json_encode(['data' => $pastes->map(function($paste) {
    return [
        'id' => $paste->id,
        'title' => $paste->title,
        'author' => $paste->user->username,
        'author_id' => $paste->user->id,
        'tags' => $paste->tags->map(function($tag) {
            return ['slug' => $tag->slug, 'name' => $tag->name];
        })
    ];
})]);

$redis->setEx('ajax_pastes', 3600, $pastes_json);

echo $pastes_json;
