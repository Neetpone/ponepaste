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

// UTF-8
header('Content-Type: text/html; charset=utf-8');

// Required functions
define('IN_PONEPASTE', 1);
require_once('includes/common.php');
require_once('includes/geshi.php');
require_once('includes/functions.php');
require_once('includes/Tag.class.php');
require_once('includes/passwords.php');

require_once('includes/Parsedown/Parsedown.php');
require_once('includes/Parsedown/ParsedownExtra.php');
require_once('includes/Parsedown/SecureParsedown.php');

$paste_id = intval(trim($_REQUEST['id']));

updatePageViews($conn);

// Get paste favorite count
$query = $conn->prepare('SELECT COUNT(*) FROM pins WHERE paste_id = ?');
$query->execute([$paste_id]);
$fav_count = intval($query->fetch(PDO::FETCH_NUM)[0]);

// Get paste info
$row = $conn->querySelectOne(
    'SELECT title, content, visible, code, expiry, pastes.password AS password, created_at, updated_at, encrypt, views, users.username AS member, users.id AS user_id
        FROM pastes
        INNER JOIN users ON users.id = pastes.user_id
        WHERE pastes.id = ?', [$paste_id]);


// This is used in the theme files.
$totalpastes = getSiteTotalPastes($conn);

if (!$row) {
    header('HTTP/1.1 404 Not Found');
    $notfound = $lang['notfound']; // "Not found";
} else {
    $paste_title = $row['title'];
    $paste_code = $row['code'];

    $paste = [
        'title' => $paste_title,
        'created_at' => (new DateTime($row['created_at']))->format('jS F Y h:i:s A'),
        'updated_at' => (new DateTime($row['updated_at']))->format('jS F Y h:i:s A'),
        'user_id' => $row['user_id'],
        'member' => $row['member'],
        'views' => $row['views'],
        'code' => $paste_code,
        'tags' => getPasteTags($conn, $paste_id)
    ];
    $p_content = $row['content'];
    $p_visible = $row['visible'];
    $p_expiry = Trim($row['expiry']);
    $p_password = $row['password'];
    $p_encrypt = $row['encrypt'];


    $is_private = $row['visible'] === '2';
    $private_error = false;

    if ($is_private && (!$current_user || $current_user['id'] !== $row['user_id'])) {
        $notfound = $lang['privatepaste']; //" This is a private paste. If you created this paste, please login to view it.";
        $private_error = true;
        goto Not_Valid_Paste;
    }

    if (!empty($p_expiry) && $p_expiry !== 'SELF') {
        $input_time = $p_expiry;
        $current_time = mktime(date("H"), date("i"), date("s"), date("n"), date("j"), date("Y"));
        if ($input_time < $current_time) {
            $notfound = $lang['expired'];
            $p_private_error = 1;
            goto Not_Valid_Paste;
        }
    }

    if (!empty($p_encrypt)) {
        $p_content = decrypt($p_content);
    }

    $op_content = Trim(htmlspecialchars_decode($p_content));

    // Download the paste
    if (isset($_GET['download'])) {
        if ($p_password == "NONE" || $p_password === null) {
            doDownload($paste_id, $paste_title, $p_member, $op_content, $paste_code);
            exit();
        } else {
            if (isset($_GET['password'])) {
                if (pp_password_verify($_GET['password'], $p_password)) {
                    doDownload($paste_id, $paste_title, $p_member, $op_content, $paste_code);
                    exit();
                } else {
                    $error = $lang['wrongpassword']; // 'Wrong password';
                }
            } else {
                $error = $lang['pwdprotected']; // 'Password protected paste';
            }
        }
    }

    // Raw view
    if (isset($_GET['raw'])) {
        if ($p_password == "NONE" || $p_password === null) {
            rawView($paste_id, $paste_title, $op_content, $paste_code);
            exit();
        } else {
            if (isset($_GET['password'])) {
                if (pp_password_verify($_GET['password'], $p_password)) {
                    rawView($paste_id, $paste_title, $op_content, $paste_code);
                    exit();
                } else {
                    $error = $lang['wrongpassword']; // 'Wrong password';
                }
            } else {
                $error = $lang['pwdprotected']; // 'Password protected paste';
            }
        }
    }

    // Preprocess
    $highlight = array();
    $prefix_size = strlen('!highlight!');
    $lines = explode("\n", $p_content);
    $p_content = "";
    foreach ($lines as $idx => $line) {
        if (substr($line, 0, $prefix_size) == '!highlight!') {
            $highlight[] = $idx + 1;
            $line = substr($line, $prefix_size);
        }
        $p_content .= $line . "\n";
    }

    $p_content = rtrim($p_content);

    // Apply syntax highlight
    $p_content = htmlspecialchars_decode($p_content);
    if ($paste_code === "pastedown") {
        $Parsedown = new Parsedown();
        $Parsedown->setSafeMode(true);
        $p_content = $Parsedown->text($p_content);
    } else {
        $geshi = new GeSHi($p_content, $paste_code, 'includes/geshi/');

        $geshi->enable_classes();
        $geshi->set_header_type(GESHI_HEADER_DIV);
        $geshi->set_line_style('color: #aaaaaa; width:auto;');
        $geshi->set_code_style('color: #757584;');
        if (count($highlight)) {
            $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
            $geshi->highlight_lines_extra($highlight);
            $geshi->set_highlight_lines_extra_style('color:#399bff;background:rgba(38,92,255,0.14);');
        } else {
            $geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS, 2);
        }
        $p_content = $geshi->parse_code();
        $style = $geshi->get_stylesheet();
        $ges_style = '<style>' . $style . '</style>';
    }

    // Embed view after GeSHI is applied so that $p_code is syntax highlighted as it should be.
    if (isset($_GET['embed'])) {
        if ($p_password == "NONE" || $p_password === null) {
            embedView($paste_id, $paste_title, $p_content, $paste_code, $title, $baseurl, $ges_style, $lang);
            exit();
        } else {
            if (isset($_GET['password'])) {
                if (pp_password_verify($_GET['password'], $p_password)) {
                    embedView($paste_id, $paste_title, $p_content, $paste_code, $title, $p_baseurl, $ges_style, $lang);
                    exit();
                } else {
                    $error = $lang['wrongpassword']; // 'Wrong password';
                }
            } else {
                $error = $lang['pwdprotected']; // 'Password protected paste';
            }
        }
    }
}

require_once('theme/' . $default_theme . '/header.php');
if ($p_password == "NONE" || $p_password === null) {
    // No password & diplay the paste

    // Set download URL
    if (PP_MOD_REWRITE) {
        $p_download = "download/$paste_id";
        $p_raw = "raw/$paste_id";
        $p_embed = "embed/$paste_id";
    } else {
        $p_download = "paste.php?download&id=$paste_id";
        $p_raw = "paste.php?raw&id=$paste_id";
        $p_embed = "paste.php?embed&id=$paste_id";
    }


    // View counter
    if ($_SESSION['not_unique'] !== $paste_id) {
        $_SESSION['not_unique'] = $paste_id;
        $conn->query("UPDATE pastes SET views = (views + 1) where id = ?", [$paste_id]);
    }

    // Theme
    require_once('theme/' . $default_theme . '/view.php');
    if ($p_expiry == "SELF") {
        $conn->query('DELETE FROM pastes WHERE id = ?', [$paste_id]);
    }
} else {
    $p_download = "paste.php?download&id=$paste_id&password=" . pp_password_hash(isset($_POST['mypass']));
    $p_raw = "paste.php?raw&id=$paste_id&password=" . pp_password_hash(isset($_POST['mypass']));
    // Check password
    if (isset($_POST['mypass'])) {
        if (pp_password_verify($_POST['mypass'], $p_password)) {
            // Theme
            require_once('theme/' . $default_theme . '/view.php');
            if ($p_expiry == "SELF") {
                $conn->prepare('DELETE FROM pastes WHERE id = ?')
                    ->execute([$paste_id]);
            }
        } else {
            $error = $lang['wrongpwd']; //"Password is wrong";
            require_once('theme/' . $default_theme . '/errors.php');
        }
    } else {
        // Display errors
        require_once('theme/' . $default_theme . '/errors.php');
    }
}

Not_Valid_Paste:
// Private paste not valid
if ($is_private == '1') {
    // Display errors
    require_once('theme/' . $default_theme . '/header.php');
    require_once('theme/' . $default_theme . '/errors.php');
}

// Footer
require_once('theme/' . $default_theme . '/footer.php');

