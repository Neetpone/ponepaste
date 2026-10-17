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

$tag_json_query = $capsule->getConnection()->table('paste_taggings')
    ->join('tags', 'tags.id', '=', 'paste_taggings.tag_id')
    ->whereColumn('paste_taggings.paste_id', 'pastes.id')
    ->selectRaw("COALESCE(JSON_ARRAYAGG(JSON_OBJECT('slug', tags.slug, 'name', tags.name)), JSON_ARRAY())");

$pastes_query = Paste::query()
    ->leftJoin('users', 'users.id', '=', 'pastes.user_id')
    ->select([
        'pastes.id',
        'pastes.user_id',
        'pastes.title',
        'pastes.expiry',
        'pastes.created_at',
        'pastes.updated_at',
        'pastes.visible',
        'pastes.code',
        'users.username as author'
    ])
    ->selectSub($tag_json_query, 'tags_json')
    ->where('pastes.visible', '!=', Paste::VISIBILITY_PRIVATE)
    ->whereNull('pastes.password')
    ->availableForListing()
    ->orderBy('pastes.id', 'desc')
    ->toBase();

$encoded_pastes = [];
foreach ($pastes_query->cursor() as $paste) {
    $encoded_pastes[] = json_encode([
        'id' => $paste->id,
        'created_at' => $paste->created_at,
        'updated_at' => $paste->updated_at ?? $paste->created_at,
        'visibility' => (int) $paste->visible,
        'title' => $paste->title,
        'format' => match ($paste->code) {
            'text', 'plaintext' => 'plaintext',
            'pastedown_old', 'pastedown' => 'pastedown',
            default => 'green',
        },
        'author' => $paste->author,
        'author_id' => $paste->user_id,
        'tags' => json_decode($paste->tags_json, true, 512, JSON_THROW_ON_ERROR)
    ], JSON_THROW_ON_ERROR);
}
$pastes_json = '{"data":[' . implode(',', $encoded_pastes) . ']}';

if (!PP_DEBUG) {
    $redis->setEx(Paste::AJAX_PASTES_CACHE_KEY, 3600, $pastes_json);
}

echo $pastes_json;
