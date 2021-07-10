<?php
// INIT
require __DIR__ . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "2a-config.php";
require PATH_LIB . "2b-lib-tag.php";
$tagDB = new Tag();

// PROCESS AJAX REQUESTS
switch ($_POST['req']) {
  // INVALID
  default:
    echo json_encode([
      "status" => 0,
      "message" => "Invalid request"
    ]);
    break;

  // GET TAGS FOR POST
  case "get":
    $tags = $tagDB->getAll($_POST['post_id']);
    echo json_encode([
      "status" => is_array($tags) ? 1 : 0,
      "message" => $tags
    ]);
    break;

  // SAVE TAGS
  case "save":
    $pass = $tagDB->reTag($_POST['post_id'], json_decode($_POST['tags']));
    echo json_encode([
      "status" => $pass ? 1 : 0,
      "message" => $pass ? "OK" : $tagDB->error
    ]);
    break;
}
?>