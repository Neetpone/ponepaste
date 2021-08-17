<?php
header('Content-Type: application/json; charset=UTF-8');

define('IN_PONEPASTE', 1);
//require_once('../includes/config.php');
require_once('../includes/common.php');
require_once('../includes/NonRetardedSSP.class.php');

function tagsToHtml(string $tags) : string {
    $output = "";
    $tagsSplit = explode(",", $tags);
    foreach ($tagsSplit as $tag) {
        if (stripos($tag, 'nsfw') !== false) {
            $tag = strtoupper($tag);
            $tagcolor = "tag is-danger";
        } elseif (stripos($tag, 'SAFE') !== false) {
            $tag = strtoupper($tag);
            $tagcolor = "tag is-success";
        } elseif (str_contains($tag, '/')) {
            $tagcolor = "tag is-primary";
        } else {
            $tagcolor = "tag is-info";
        }
        $output .= '<a href="/archive?q=' . urlencode($tag) . '"><span class="' . $tagcolor . '">' . pp_html_escape(ucfirst($tag)) . '</span></a>';
    }
    return $output;
}


function transformDataRow($row) {
    $titleHtml = '<a href="/' . urlencode($row[0]) . '">' . pp_html_escape($row[1]) . '</a>';
    $authorHtml = '<a href="' . urlencode($row[2]) . '">' . pp_html_escape($row[2]) . '</a>';
    $tagsHtml = tagsToHtml($row[3]);

    return [
        $titleHtml,
        $authorHtml,
        $tagsHtml
    ];
}

$data = NonRetardedSSP::run(
    $conn, $_GET,
    'SELECT COUNT(*) FROM pastes',
    'SELECT pastes.id AS id, title, users.username, GROUP_CONCAT(tags.name SEPARATOR \',\') AS tagsys FROM pastes
                INNER JOIN users ON users.id = pastes.user_id
                INNER JOIN paste_taggings on pastes.id = paste_taggings.paste_id
                INNER JOIN tags ON tags.id = paste_taggings.tag_id
                GROUP BY pastes.id'
);


$data['data'] = array_map('transformDataRow', $data['data']);

echo json_encode($data);
