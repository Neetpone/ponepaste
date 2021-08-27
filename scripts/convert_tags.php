<?php
define('IN_PONEPASTE', 1);
require_once('../includes/common.php');

function upgrade_tagsys(DatabaseHandle $conn) {
    $result = $conn->query('SELECT id, tagsys FROM pastes')
        ->fetchAll(PDO::FETCH_NUM);

    foreach ($result as $row) {
        list($paste_id, $tagsys) = $row;

        $tag_names = explode(',', $tagsys);

        foreach ($tag_names as $tag_name) {
            $tag_name = html_entity_decode($tag_name);
            if (Tag::cleanTagName($tag_name) === '') continue;

            $tag = Tag::getOrCreateByName($conn, $tag_name);

            try {
                $conn->queryInsert('INSERT INTO paste_taggings (paste_id, tag_id) VALUES (?, ?)', [$paste_id, $tag->id]);
            } catch (Exception $e) {
                if (str_contains($e->getMessage(), 'Duplicate entry')) {
                    var_dump($e);
                } else throw $e;
            }
        }
    }
}

echo 'hi';
upgrade_tagsys($conn);

