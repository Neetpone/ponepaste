<?php

use PonePaste\Models\Paste;
use PonePaste\Models\Tag;
use PonePaste\Models\User;

require_once('includes/common.php');
require_once('includes/captcha.php');
require_once('includes/functions.php');

function verifyCaptcha() : string|bool {
    global $captcha_config;
    global $current_user;

    if ($captcha_config['enabled'] && !$current_user) {
        $scode = strtolower(trim($_POST['scode']));
        $cap_code = strtolower($_SESSION['captcha']['code']);
        if ($cap_code !== $scode) {
            return 'Wrong CAPTCHA.';
        }
    }

    return true;
}

/**
 * Calculate the expiry of a paste based on user input.
 *
 * @param string $expiry Expiry time.
 *                       SELF means to expire upon one view. +10M, +1H, +1D, +1W, +2W, +1M all do the obvious.
 *                       Anything unhandled means to expire never.
 * @return string|null Expiry time, or NULL if expires never.
 */
function calculatePasteExpiry(string $expiry) {
    // used to use mktime
    if ($expiry === 'self') {
        return 'SELF';
    }

    $valid_expiries = ['10M', '1H', '1D', '1W', '2W', '1M'];

    return in_array($expiry, $valid_expiries)
        ? (new DateTime())->add(new DateInterval("P{$expiry}"))->format('U')
        : null;
}

function validatePasteFields() : string|null {
    if (empty($_POST["paste_data"]) || trim($_POST['paste_data'] === '')) { /* Empty paste input */
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($captcha_config['enabled']) {
        $_SESSION['captcha'] = captcha($captcha_config['colour'], $captcha_config['mode'], $captcha_config['multiple'], $captcha_config['allowed']);
    }
}

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

    $editing = isset($_POST['edit']);

    $paste_title = trim($_POST['title']);

    if (empty($paste_title)) {
        $paste_title = 'Untitled';
    }

    $paste_content = $_POST['paste_data'];
    $paste_visibility = $_POST['visibility'];
    $paste_code = $_POST['format'];
    $paste_password = $_POST['pass'];

    $p_expiry = trim(htmlspecialchars($_POST['paste_expire_date']));
    $tag_input = $_POST['tag_input'];

    if (empty($paste_password)) {
        $paste_password = null;
    } else {
        $paste_password = password_hash($paste_password, PASSWORD_DEFAULT);
    }

    $paste_content = openssl_encrypt(
        $_POST['paste_data'],
        PP_ENCRYPTION_ALGO,
        PP_ENCRYPTION_KEY
    );

    // Set expiry time
    $expires = calculatePasteExpiry($p_expiry);

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
                'password' => $paste_password,
                'updated_at' => date_create(),
                'ip' => $ip
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
            'password' => $paste_password,
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
require_once('theme/' . $default_theme . '/common.php');
