<?php
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/common.php');

use PonePaste\Models\AdminLog;
use PonePaste\Models\Paste;
use PonePaste\Helpers\SpamHelper;

if (empty($_POST['paste_id'])) {
    echo "Error: No paste ID specified.";
    die();
}

$paste = Paste::find((int) $_POST['paste_id']);

if (!$paste) {
    echo "Error: Paste not found.";
    die();
}

if (!verifyCsrfToken()) {
    flashError('Invalid CSRF token (do you have cookies enabled?)');
    header('Location: ' . urlForPaste($paste));
    die();
}

if (isset($_POST['hide'])) {
    if (!can('hide', $paste)) {
        flashError('You do not have permission to hide this paste.');
    } else {
        $is_hidden = !$paste->is_hidden;

        if ($is_hidden) {
            $paste->reports()->update(['open' => false]);
            $paste->deleted_at = date_create();
            $paste->deleted_by_id = $current_user->id;
        } else {
            $paste->deleted_at = null;
            $paste->deleted_by_id = null;
        }

        $paste->is_hidden = $is_hidden;
        $paste->save();
        $redis->del('ajax_pastes'); /* Expire from Redis so it doesn't show up anymore */

        AdminLog::updateAdminHistory($current_user, AdminLog::ACTION_HIDE_PASTE, 'Paste ' . $paste->id . ' ' . ($is_hidden ? 'hidden' : 'unhidden') . '.');
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
        AdminLog::updateAdminHistory($current_user, AdminLog::ACTION_BLANK_PASTE, 'Paste ' . $paste->id . ' blanked.');

        flashSuccess('Paste contents blanked.');
    }

    header('Location: ' . urlForPaste($paste));
    die();
} elseif (isset($_POST['mark'])) {
    if (!can('mark', $paste)) {
        flashError('You do not have permission to mark this paste.');
    } elseif ($paste->mark !== null) {
        flashError('Paste has already been marked as ' . $paste->mark . '.');
    } else {
        $mark = $_POST['mark'];

        if (isset($mark['ham'])) {
            $mark = 'ham';
        } else {
            $mark = 'spam';
        }

        SpamHelper::markPaste($paste, $mark);
        $paste->mark = $mark;
        $paste->save();

        AdminLog::updateAdminHistory($current_user, AdminLog::ACTION_MARK_PASTE, 'Paste ' . $paste->id . ' marked ' . $mark . '.');
        flashsuccess('Paste marked as ' . $mark . '.');
    }
    header('Location: ' . urlForPaste($paste));
    die();
} else {
    flashError('Internal Error: No action specified.');
    header('Location: ' . urlForPaste($paste));
}
