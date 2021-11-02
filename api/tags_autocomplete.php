<?php
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/../includes/common.php');

use PonePaste\Models\Tag;

/* get rid of unintended wildcards in a parameter to LIKE queries; not a security issue, just unexpected behaviour. */
function escapeLikeQuery(string $query) : string {
    return str_replace(['\\', '_', '%'], ['\\\\', '\\_', '\\%'], $query);
}

header('Content-Type: application/json');

if (empty($_GET['tag'])) {
    die(json_encode(['error' => true, 'message' => 'No tag name provided']));
}

$tag_name = Tag::cleanTagName($_GET['tag']);

$results = Tag::select('name')
                ->where('name', 'LIKE', escapeLikeQuery($tag_name))
                ->andWhere('name', '!=', $tag_name)
                ->fetchAll()
                ->toArray();

array_push($tags, ['name' => $tag_name]);


echo json_encode($tags);
