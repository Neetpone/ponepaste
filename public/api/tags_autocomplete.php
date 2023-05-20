<?php
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/../includes/common.php');

use PonePaste\Models\Tag;

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

/* we want to ensure the tag name that the user input is always returned,
 * even if that tag doesn't actually exist yet. */
$tags[] = ['name' => $tag_name];

echo json_encode($tags);
