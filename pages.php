<?php
define('IN_PONEPASTE', 1);
require_once('includes/common.php');
require_once('includes/functions.php');

updatePageViews($conn);

if (isset($_GET['page'])) {
    $page_name = htmlspecialchars(trim($_GET['page']));

    $query = $conn->prepare('SELECT page_title, page_content, last_date FROM pages WHERE page_name = ?');
    $query->execute([$page_name]);
    if ($row = $query->fetch()) {
        $page_title = $row['page_title'];
        $page_content = $row['page_content'];
        $last_date = $row['last_date'];
        $stats = "OK";
        $p_title = $page_title;
    }
}
// Theme
$page_template = 'pages';
require_once('theme/' . $default_theme . '/common.php');


