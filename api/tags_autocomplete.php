<?php
define('IN_PONEPASTE', 1);

require_once(__DIR__ . '/../includes/common.php');
require_once(__DIR__ . '/../includes/Tag.class.php');

header('Content-Type: application/json');

if (empty($_GET['tag'])) {
    die(json_encode(['error' => true, 'message' => 'No tag name provided']));
}

$tag_name = Tag::cleanTagName($_GET['tag']);
$tag_name = str_replace('%', '', $tag_name); /* get rid of MySQL LIKE wildcards */

$results = $conn->query('SELECT name FROM tags WHERE name LIKE ? AND name != ?', [$tag_name . '%', $tag_name]);
$tags = $results->fetchAll();

array_push($tags, ['name' => $tag_name]);

echo json_encode($tags);
