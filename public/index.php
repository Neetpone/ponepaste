<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */
define('IN_PONEPASTE', 1);
require_once(__DIR__ . '/../includes/common.php');
require_once(__DIR__ . '/../includes/captcha.php');

use PonePaste\Models\Paste;
use PonePaste\Models\Tag;
use PonePaste\Models\User;


function verifyCaptcha() : string|bool {
    global $captcha_enabled;
    global $current_user;

    if ($captcha_enabled && !$current_user) {
        if (empty($_POST['captcha_answer']) ||
            !checkCaptcha($_POST['captcha_token'], trim($_POST['captcha_answer']))) {
            return 'Wrong CAPTCHA.';
        }
    }

    return true;
}

/**
 * Calculate the expiry of a paste based on user input.
 *
 * @param string $expiry Expiry time.
 *                       SELF means to expire upon one view. +0Y0M0DT0H10M, +1H, +1D, +1W, +2W, +1M all do the obvious.
 *                       Anything unhandled means to expire never.
 * @return string|null 'SELF', Expiry time as Unix timestamp, or NULL if expires never.
 */
function calculatePasteExpiry(string $expiry) : ?string {
    // used to use mktime
    if ($expiry === 'self') {
        return 'SELF';
    }

    $valid_expiries = ['0Y0M0DT0H10M', 'T1H', '1D', '1W', '2W', '1M'];

    return in_array($expiry, $valid_expiries)
        ? (new DateTime())->add(new DateInterval("P{$expiry}"))->format('U')
        : null;
}

function validatePasteFields() : string|null {
    if (empty($_POST["paste_data"]) || trim($_POST['paste_data']) === '') { /* Empty paste input */
        return 'You cannot post an empty paste.';
    } elseif (!isset($_POST['title'])) { /* No paste title POSTed */
        return 'All fields must be filled out.';
    } elseif (empty($_POST["tag_input"])) { /* No tags provided */
        return 'No tags were provided.';
    } elseif (strlen($_POST["title"]) > 70) { /* Paste title too long */
        return 'Paste title is too long.';
    } elseif (mb_strlen($_POST["paste_data"], '8bit') > PP_PASTE_LIMIT_BYTES) { /* Paste size too big */
        return 'Your paste is too large. The maximum size is ' . PP_PASTE_LIMIT_BYTES . ' bytes.';
    }

    return null;
}

// Sitemap
$priority = 0.9;
$changefreq = 'weekly';

updatePageViews();

// POST Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken()) {
        $error = 'Incorrect CSRF token (do you have cookies enabled?)';
        goto OutPut;
    }

    $error = validatePasteFields();

    if ($error !== null) {
        goto OutPut;
    }

    $captchaResponse = verifyCaptcha();

    if ($captchaResponse !== true) {
        $error = $captchaResponse;
        goto OutPut;
    }

    $tags = Tag::parseTagInput($_POST['tag_input']);

    if (count($tags) < 1) {
        $error = 'You must specify at least 1 tag.';
        goto OutPut;
    } elseif (count($tags) > 32) {
        $error = 'You must specify at most 32 tags.';
        goto OutPut;
    }

    foreach ($tags as $tag) {
        if (strlen($tag) > 255) {
            $error = 'A single tag cannot be longer than 255 characters.';
            goto OutPut;
        }
    }

    $editing = isset($_POST['edit']);
    $deleting = isset($_POST['delete']);

    $paste_title = trim($_POST['title']);

    if (empty($paste_title)) {
        $paste_title = 'Untitled';
    }

    $paste_content = $_POST['paste_data'];
    $paste_visibility = $_POST['visibility'];
    $paste_code = $_POST['format'] ?? 'green';
    $tag_input = $_POST['tag_input'];

    if (!in_array($paste_code, PP_HIGHLIGHT_FORMATS)) {
        $paste_code = 'green';
    }

    $paste_content = openssl_encrypt(
        $_POST['paste_data'],
        PP_ENCRYPTION_ALGO,
        PP_ENCRYPTION_KEY
    );

    // Set expiry time
    $expires = calculatePasteExpiry(trim($_POST['paste_expire_date']));

    // Edit existing paste or create new?
    if ($editing) {
        $paste = Paste::find($_POST['paste_id']);
        if (can('edit', $paste)) {
            $paste->update([
                'title' => $paste_title,
                'content' => $paste_content,
                'visible' => $paste_visibility,
                'code' => $paste_code,
                'expiry' => $expires,
                'updated_at' => date_create(),
                'ip' => $ip,
                'encrypt' => true
            ]);

            $paste->replaceTags($tags);
            $redis->del('ajax_pastes'); /* Expire from Redis so the edited paste shows up */
        } else {
            $error = 'You must be logged in to do that.';
        }
    } else {
        $paste_owner = $current_user ?: User::find(1); /* 1 is the guest user's user ID */
        $paste = new Paste([
            'title' => $paste_title,
            'code' => $paste_code,
            'content' => $paste_content,
            'visible' => $paste_visibility,
            'expiry' => $expires,
            'encrypt' => true,
            'created_at' => date_create(),
            'ip' => $ip
        ]);

        $paste->user()->associate($paste_owner);
        $paste->save();

        $paste->replaceTags($tags);

        if ($paste_visibility == Paste::VISIBILITY_PUBLIC) {
            addToSitemap($paste, $priority, $changefreq);
        }

        $redis->del('ajax_pastes'); /* Expire from Redis so the new paste shows up */
    }

    // Redirect to paste on successful entry, or on successful edit redirect back to edited paste
    if (isset($paste)) {
        header('Location: ' . urlForPaste($paste));
        die();
    }
}


OutPut:
$csrf_token = setupCsrfToken();
$page_template = 'main';

require_once(__DIR__ . '/../theme/' . $default_theme . '/common.php');
