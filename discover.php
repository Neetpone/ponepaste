<?php
define('IN_PONEPASTE', 1);
require_once('includes/common.php');
require_once('includes/functions.php');

function getMonthPopularPastes(DatabaseHandle $conn, int $count) : array {
    $query = $conn->prepare(
        "SELECT pastes.id AS id, title, created_at, updated_at, users.username AS member
            FROM pastes
            INNER JOIN users ON users.id = pastes.user_id
            WHERE MONTH(created_at) = MONTH(NOW()) AND visible = '0' ORDER BY views DESC LIMIT ?");
    $query->execute([$count]);
    return $query->fetchAll();
}

function getRecentUpdatesPastes(DatabaseHandle $conn, int $count) : array {
    $query = $conn->prepare(
        "SELECT pastes.id AS id, title, created_at, updated_at, users.username AS member
            FROM pastes
            INNER JOIN users ON users.id = pastes.user_id
            WHERE visible = '0' ORDER BY updated_at DESC
            LIMIT ?");
    $query->execute([$count]);
    return $query->fetchAll();
}

function getRecentCreatedPastes(DatabaseHandle $conn, int $count) : array {
    $query = $conn->prepare("
        SELECT pastes.id, title, created_at, updated_at, users.username AS member
        FROM pastes
        INNER JOIN users ON pastes.user_id = users.id
        WHERE visible = '0'
        ORDER BY created_at DESC
        LIMIT ?");
    $query->execute([$count]);
    return $query->fetchAll();
}

function getMostViewedPastes(DatabaseHandle $conn, int $count) : array {
    $query = $conn->prepare("
        SELECT pastes.id AS id, title, created_at, updated_at, views, users.username AS member
            FROM pastes INNER JOIN users ON users.id = pastes.user_id
            WHERE visible = '0'
            ORDER BY views DESC 
            LIMIT ?
    ");
    $query->execute([$count]);
    return $query->fetchAll();
}

function getRandomPastes(DatabaseHandle $conn, int $count) : array {
    $query = $conn->prepare("
        SELECT pastes.id, title, created_at, updated_at, views, users.username AS member
            FROM pastes
            INNER JOIN users ON users.id = pastes.user_id
            WHERE visible = '0'
            ORDER BY RAND()
            LIMIT ?");
    $query->execute([$count]);
    return $query->fetchAll();
}

function transformPasteRow(array $row) : array {
    global $conn;

    return [
        'id' => $row['id'],
        'title' => $row['title'],
        'member' => $row['member'],
        'time' => $row['created_at'],
        'time_update' => $row['updated_at'],
        'friendly_update_time' => friendlyDateDifference(new DateTime($row['updated_at']), new DateTime()),
        'friendly_time' => friendlyDateDifference(new DateTime($row['created_at']), new DateTime()),
        'tags' => getPasteTags($conn, $row['id'])
    ];
}

$popular_pastes = array_map('transformPasteRow', getMostViewedPastes($conn, 10));
$monthly_popular_pastes = array_map('transformPasteRow', getMonthPopularPastes($conn, 10));
$recent_pastes = array_map('transformPasteRow', getRecentCreatedPastes($conn, 10));
$updated_pastes = array_map('transformPasteRow', getRecentUpdatesPastes($conn, 10));
$random_pastes = array_map('transformPasteRow', getRandomPastes($conn, 10));

// Theme
$page_template = 'discover';
$page_title = 'Discover';
require_once('theme/' . $default_theme . '/common.php');

