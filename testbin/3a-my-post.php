<?php
// INIT
require __DIR__ . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "2a-config.php";
require PATH_LIB . "2b-lib-tag.php";
$tagDB = new Tag();

// GET TAGS
$postID = 3;
$tags = $tagDB->getAll($postID);

// HTML ?>
<!DOCTYPE html>
<html>
  <head>
    <title>Simple Tag Demo</title>
    <script src="public/3b-tag.js"></script>
    <link href="public/3c-tag.css" rel="stylesheet">
  </head>
  <body>
    <!-- [DOES NOT MATTER - YOUR POST] -->
    <h1>
      Lord of the Minks
    </h1>
    <p>
      The undone complaint collapses past an east estate. The insulting nurse flames the era. A willed hierarchy surfaces. A tentative wife bites the consenting fence.
    </p>

    <!-- [TAGS] -->
    <div id="tag_dock">
      <h3>MANAGE TAGS</h3>

      <!-- [TAGS LIST] -->
      <div id="tag_list"><?php
        if (is_array($tags)) {
          foreach ($tags as $t) {
            printf("<div class='tag'>%s</div>", $t);
          }
        }
      ?></div>

      <!-- [TAGS FORM] -->
      <!-- NOTE : CONTROLS DISABLED UNTIL PAGE FULLY LOADED -->
      <label for="tag_in">
        Enter tags below, separate each with a comma.<br>
        Click on existing tags above to remove.<br>
        Remember to hit save to commit changes.
      </label>
      <input type="text" id="tag_in" maxlength="32" disabled/>
      <input type="hidden" id="post_id" value="<?=$postID?>"/>
      <input type="button" id="tag_save" value="Save" onclick="tag.save()" disabled/>
    </div>
  </body>
</html>