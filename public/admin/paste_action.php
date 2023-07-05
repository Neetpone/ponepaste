<?php
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/common.php');

use PonePaste\Models\AdminLog;
use PonePaste\Models\Paste;

if (empty($_POST['paste_id'])) {
    echo "Error: No paste ID specified.";
    die();
}

$paste = Paste::find((int) $_POST['paste_id']);

if (!$paste) {
    echo "Error: Paste not found.";
    die();
}

if (isset($_POST['hide'])) {
    if (!can('hide', $paste)) {
        flashError('You do not have permission to hide this paste.');
    } else {
        $is_hidden = !$paste->is_hidden;

        if ($is_hidden) {
            $paste->reports()->update(['open' => false]);
        }

        $paste->is_hidden = $is_hidden;
        $paste->save();
        $redis->del('ajax_pastes'); /* Expire from Redis so it doesn't show up anymore */

        updateAdminHistory($current_user, AdminLog::ACTION_HIDE_PASTE, 'Paste ' . $paste->id . ' ' . ($is_hidden ? 'hidden' : 'unhidden') . '.');
        flashSuccess('Paste ' . ($is_hidden ? 'hidden' : 'unhidden') . '.');
    }

    header('Location: ' . urlForPaste($paste));
    die();
} elseif (isset($_POST['blank'])) {
    if (!can('blank', $paste)) {
        flashError('You do not have permission to blank this paste.');
    } else {
        $paste->content = '';
        $paste->title = 'Removed by moderator';
        $paste->tags()->detach();

        $paste->save();
        $redis->del('ajax_pastes'); /* Expire from Redis so it doesn't show up anymore */
        updateAdminHistory($current_user, AdminLog::ACTION_BLANK_PASTE, 'Paste ' . $paste->id . 'blanked.');

        flashSuccess('Paste contents blanked.');
    }

    header('Location: ' . urlForPaste($paste));
    die();
} else {
    flashError('Internal Error: No action specified.');
    header('Location: ' . urlForPaste($paste));
}
