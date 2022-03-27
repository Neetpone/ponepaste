<?php
define('IN_PONEPASTE', 1);
require_once('includes/common.php');
require_once('includes/functions.php');

use Illuminate\Support\Facades\DB;
use PonePaste\Models\User;
use PonePaste\Models\Paste;

if (empty($_GET['user'])) {
    // No username provided
    header("Location: ../error.php");
    die();
}

$profile_username = trim($_GET['user']);

$profile_info = User::with('favourites')
    ->where('username', $profile_username)
    ->select('id', 'date', 'badge')
    ->first();

if (!$profile_info) {
    // Invalid username
    header("Location: ../error.php");
    die();
}

$p_title = $profile_username . "'s Public Pastes";

// There has to be a way to do the sum in SQL rather than PHP, but I can't figure out ho to do it in Eloquent.
$total_pfav = array_sum(
    array_column(
        Paste::select('id')
            ->where('user_id', $profile_info->id)
            ->withCount('favouriters')
            ->get()->toArray(),
        'favouriters_count'
    )
);
$total_yfav = $profile_info->favourites->count();

// Badges
$profile_badge = match ($profile_info['badge']) {
    1 => '<img src="/img/badges/donate.png" title="[Donated] Donated to Ponepaste" style="margin:5px" alt="Donated to PonePaste" />',
    2 => '<img src="/img/badges/spoon.png" title="[TheWoodenSpoon] You had one job" style="margin:5px" alt="You had one job" />',
    3 => '<img src="/img/badges/abadge.png" title="[>AFuckingBadge] Won a PasteJam Competition" style="margin:5px" alt="Won a PasteJam competition" />',
    default => '',
};

$profile_total_pastes = $profile_info->pastes->count();
$profile_total_public = $profile_info->pastes->where('visible', 0)->count();
$profile_total_unlisted = $profile_info->pastes->where('visible', 1)->count();
$profile_total_private = $profile_info->pastes->where('visible', 2)->count();


$profile_total_paste_views = Paste::select('views')->where('user_id', $profile_info->id)->sum('views');

$profile_join_date = $profile_info['date'];

$profile_pastes = $profile_info->pastes;
$profile_favs = $profile_info->favourites;
$is_current_user = ($current_user !== null) && ($profile_info->id == $current_user->id);

updatePageViews();

$csrf_token = setupCsrfToken();
$page_title = 'Profile of ' . $profile_username;
$page_template = 'user_profile';
$script_bundles[] = 'user_profile';
require_once('theme/' . $default_theme . '/common.php');
