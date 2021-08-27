<?php
define('IN_PONEPASTE', 1);
require_once('includes/common.php');
require_once('includes/functions.php');

if (empty($_GET['user'])) {
    // No username provided
    header("Location: ../error.php");
    die();
}

$profile_username = trim($_GET['user']);

$profile_info = User::with('favourites')->where('username', $profile_username)->select('id', 'date', 'badge')->first();

if (!$profile_info) {
    // Invalid username
    header("Location: ../error.php");
    die();
}

$p_title = $profile_username . "'s Public Pastes";

// FIXME: This should be incoming faves
$total_pfav = $profile_info->favourites->count();

$total_yfav = $profile_info->favourites->count();

// Badges
$profile_badge = match ($profile_info['badge']) {
    1 => '<img src="/img/badges/donate.png" title="[Donated] Donated to Ponepaste" style="margin:5px" alt="Donated to PonePaste" />',
    2 => '<img src="/img/badges/spoon.png" title="[TheWoodenSpoon] You had one job" style="margin:5px" alt="You had one job" />',
    3 => '<img src="/img/badges/abadge.png" title="[>AFuckingBadge] Won a PasteJam Competition" style="margin:5px" alt="Won a PasteJam competition" />',
    default => '',
};

$query = $conn->prepare('SELECT COUNT(*) FROM pastes WHERE user_id = ?');
$query->execute([$profile_info['id']]);
$profile_total_pastes = intval($query->fetch(PDO::FETCH_NUM)[0]);


$query = $conn->prepare('SELECT COUNT(*) FROM pastes WHERE user_id = ? AND visible = 0');
$query->execute([$profile_info['id']]);
$profile_total_public = intval($query->fetch(PDO::FETCH_NUM)[0]);

$query = $conn->prepare('SELECT COUNT(*) FROM pastes WHERE user_id = ? AND visible = 1');
$query->execute([$profile_info['id']]);
$profile_total_unlisted = intval($query->fetch(PDO::FETCH_NUM)[0]);

$query = $conn->prepare('SELECT COUNT(*) FROM pastes WHERE user_id = ? AND visible = 2');
$query->execute([$profile_info['id']]);
$profile_total_private = intval($query->fetch(PDO::FETCH_NUM)[0]);

$query = $conn->prepare('SELECT SUM(views) FROM pastes WHERE user_id = ?');
$query->execute([$profile_info['id']]);
$profile_total_paste_views = intval($query->fetch(PDO::FETCH_NUM)[0]);

$profile_join_date = $profile_info['date'];

$profile_pastes = getUserPastes($conn, $profile_info['id']);
$profile_favs = $profile_info->favourites;
$is_current_user = ($current_user !== null) && ($profile_info->id == $current_user->id);

updatePageViews($conn);

if (isset($_GET['del'])) {
    if ($current_user !== null) { // Prevent unauthorized deletes
        $paste_id = intval(trim($_GET['id']));

        $query = $conn->prepare('SELECT user_id FROM pastes WHERE id = ?');
        $query->execute([$paste_id]);
        $result = $query->fetch();

        if (empty($result) || $result['user_id'] !== $current_user->user_id) {
            $error = 'That paste does not exist, or you are not the owner of it.';
        } else {
            $query = $conn->prepare('DELETE FROM pastes WHERE id = ?');
            $query->execute([$paste_id]);
            $success = 'Paste deleted successfully.';
        }
    } else {
        $error = 'You must be logged in to do that.';
    }
}

// Theme
$page_template = 'user_profile';
require_once('theme/' . $default_theme . '/common.php');
