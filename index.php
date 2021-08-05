<?php
/*
 * Paste <https://github.com/jordansamuel/PASTE>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License in GPL.txt for more details.
 */

$directory = 'install';

if (file_exists($directory)) {
    header("Location: install");
    exit();
}

// Required functions
define('IN_PONEPASTE', 1);
require_once('includes/common.php');
require_once('includes/captcha.php');
require_once('includes/functions.php');
require_once('includes/Tag.class.php');

function verifyCaptcha() : string|bool {
    global $cap_e;
    global $mode;
    global $recaptcha_secretkey;
    global $lang;
    global $current_user;

    if ($cap_e == "on" && !$current_user) {
        if ($mode == "reCAPTCHA") {
            $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptcha_secretkey . "&response=" . $_POST['g-recaptcha-response']);
            $response = json_decode($response, true);
            if ($response["success"] == false) {
                // reCAPTCHA Errors
                return match ($response["error-codes"][0]) {
                    "missing-input-response" => $lang['missing-input-response'],
                    "missing-input-secret" => $lang['missing-input-secret'],
                    "invalid-input-secret" => $lang['invalid-input-secret'],
                    default => $lang['error']
                };
            }
        } else {
            $scode = strtolower(htmlentities(Trim($_POST['scode'])));
            $cap_code = strtolower($_SESSION['captcha']['code']);
            if ($cap_code !== $scode) {
                return $lang['image_wrong']; // Wrong captcha.
            }
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
        return 'SELF'; // What does this do?
    }

    $valid_expiries = ['10M', '1H', '1D', '1W', '2W', '1M'];

    return in_array($expiry, $valid_expiries)
        ? (new DateTime())->add(new DateInterval("P{$expiry}"))->format('U')
        : null;
}

function validatePasteFields() : string|null {
    global $lang;
    global $pastelimit;

    if (empty($_POST["paste_data"]) || trim($_POST['paste_data'] === '')) { /* Empty paste input */
        return $lang['empty_paste'];
    } elseif (!isset($_POST['title'])) { /* No paste title POSTed */
        return $lang['error'];
    } elseif (empty($_POST["tag_input"])) { /* No tags provided */
        return $lang['notags'];
    } elseif (strlen($_POST["title"]) > 70) { /* Paste title too long */
        return $lang['titlelen'];
    } elseif (mb_strlen($_POST["paste_data"], '8bit') > PP_PASTE_LIMIT_BYTES) { /* Paste size too big */
        return $lang['large_paste'];
    }

    return null;
}

// UTF-8
header('Content-Type: text/html; charset=utf-8');

// Current date & user IP
$date = date('jS F Y');
$ip = $_SERVER['REMOTE_ADDR'];

// Sitemap
$site_sitemap_rows = $conn->query('SELECT * FROM sitemap_options LIMIT 1');
if ($row = $site_sitemap_rows->fetch()) {
    $priority = $row['priority'];
    $changefreq = $row['changefreq'];
}

// Captcha
$site_captcha_rows = $conn->query("SELECT * FROM captcha LIMIT 1");
if ($row = $site_captcha_rows->fetch()) {
    $color = Trim($row['color']);
    $mode = Trim($row['mode']);
    $mul = Trim($row['mul']);
    $allowed = Trim($row['allowed']);
    $cap_e = Trim($row['cap_e']);
    $recaptcha_sitekey = Trim($row['recaptcha_sitekey']);
    $recaptcha_secretkey = Trim($row['recaptcha_secretkey']);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($cap_e == "on") {
        if ($mode == "reCAPTCHA") {
            $_SESSION['captcha_mode'] = "recaptcha";
            $_SESSION['captcha'] = $recaptcha_sitekey;
        } else {
            $_SESSION['captcha_mode'] = "internal";
            $_SESSION['captcha'] = captcha($color, $mode, $mul, $allowed);
        }
    } else {
        $_SESSION['captcha_mode'] = "none";
    }
}

updatePageViews($conn);

// POST Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = validatePasteFields();

    if ($error !== null) {
        goto OutPut;
    }

    $captchaResponse = verifyCaptcha();

    if ($captchaResponse !== true) {
        $error = $captchaResponse;
        goto OutPut;
    }

    $editing = isset($_POST['edit']);

    $p_title = trim(htmlspecialchars($_POST['title']));

    if (empty($p_title)) {
        $p_title = 'Untitled';
    }

    $p_content = htmlspecialchars($_POST['paste_data']);
    $p_visible = trim(htmlspecialchars($_POST['visibility']));
    $p_code = trim(htmlspecialchars($_POST['format']));
    $p_expiry = trim(htmlspecialchars($_POST['paste_expire_date']));
    $p_password = $_POST['pass'];

    if (empty($p_password)) {
        $p_password = null;
    } else {
        $p_password = password_hash($p_password, PASSWORD_DEFAULT);
    }

    $p_encrypt = trim(htmlspecialchars($_POST['encrypted']));

    $tag_input = $_POST['tag_input'];

    if (empty($p_encrypt)) {
        $p_encrypt = "0";
    } else {
        // Encrypt option
        $p_encrypt = "1";
        $p_content = encrypt($p_content);
    }

    // Set expiry time
    $expires = calculatePasteExpiry($p_expiry);

    // Edit existing paste or create new?
    if ($editing) {
        if ($current_user &&
            $current_user->user_id === (int) $conn->querySelectOne('SELECT user_id FROM pastes WHERE id = ?', [$_POST['paste_id']])['user_id']) {
            $paste_id = intval($_POST['paste_id']);

            $conn->query(
                "UPDATE pastes SET title = ?, content = ?, visible = ?, code = ?, expiry = ?, password = ?, encrypt = ?, ip = ?, updated_at = NOW()
                    WHERE id = ?",
                [$p_title, $p_content, $p_visible, $p_code, $expires, $p_password, $p_encrypt, $ip, $paste_id]
            );

            Tag::replacePasteTags($conn, $paste_id, Tag::parseTagInput($tag_input));
        } else {
            $error = $lang['loginwarning']; //"You must be logged in to do that."
        }
    } else {
        $paste_owner = $current_user ? $current_user->user_id : 1; /* 1 is the guest user's user ID */

        $paste_id = $conn->queryInsert(
            "INSERT INTO pastes (title, content, visible, code, expiry, password, encrypt, user_id, created_at, ip, views) VALUES 
                                (?,     ?,       ?,       ?,    ?,      ?,        ?,       ?,       NOW(),      ?,  0)",
            [$p_title, $p_content, $p_visible, $p_code, $expires, $p_password, $p_encrypt, $paste_owner, $ip]
        );

        Tag::replacePasteTags($conn, $paste_id, Tag::parseTagInput($tag_input));

        if ($p_visible == '0') {
            addToSitemap($paste_id, $priority, $changefreq, $mod_rewrite);
        }
    }

    // Redirect to paste on successful entry, or on successful edit redirect back to edited paste
    if (isset($paste_id)) {
        header('Location: ' . urlForPaste($paste_id));
        die();
    }
}

OutPut:
// Theme
require_once('theme/' . $default_theme . '/header.php');
require_once('theme/' . $default_theme . '/main.php');
require_once('theme/' . $default_theme . '/footer.php');
