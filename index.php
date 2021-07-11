
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
require_once('includes/password.php');

function calculatePasteExpiry($p_expiry) {
    switch ($p_expiry) {
        case '10M':
            $expires = mktime(date("H"), date("i") + "10", date("s"), date("n"), date("j"), date("Y"));
            break;
        case '1H':
            $expires = mktime(date("H") + "1", date("i"), date("s"), date("n"), date("j"), date("Y"));
        case '1D':
            $expires = mktime(date("H"), date("i"), date("s"), date("n"), date("j") + "1", date("Y"));
            break;
        case '1W':
            $expires = mktime(date("H"), date("i"), date("s"), date("n"), date("j") + "7", date("Y"));
            break;
        case '2W':
            $expires = mktime(date("H"), date("i"), date("s"), date("n"), date("j") + "14", date("Y"));
            break;
        case '1M':
            $expires = mktime(date("H"), date("i"), date("s"), date("n") + "1", date("j"), date("Y"));
            break;
        case 'self':
            $expires = "SELF";
            break;
        default:
            $expires = "NULL";
            break;
    }

    return $expires;
}

// UTF-8
header('Content-Type: text/html; charset=utf-8');

// Current date & user IP
$date    = date('jS F Y');
$ip      = $_SERVER['REMOTE_ADDR'];

// Sitemap
$site_sitemap_rows = $conn->query('SELECT * FROM sitemap_options WHERE id="1"');
while ($row = $site_sitemap_rows->fetch()) {
    $priority   = $row['priority'];
    $changefreq = $row['changefreq'];
}

// Captcha
$site_captcha_rows = $conn->query("SELECT * FROM captcha where id='1'");
while ($row = $site_captcha_rows->fetch()) {
    $color   = Trim($row['color']);
    $mode    = Trim($row['mode']);
    $mul     = Trim($row['mul']);
    $allowed = Trim($row['allowed']);
    $cap_e   = Trim($row['cap_e']);
    $recaptcha_sitekey   = Trim($row['recaptcha_sitekey']);
    $recaptcha_secretkey   = Trim($row['recaptcha_secretkey']);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
} else {
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


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
} else {
    if ($disableguest == "on") {
        $noguests = "on";
	}
	if ($siteprivate =="on") {
		$privatesite = "on";
    }
	if (isset($_SESSION['username'])) {
		$noguests = "off";
	}
}

updatePageViews($conn);

// POST Handler
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Check if fields are empty
	if (empty($_POST["paste_data"]) || trim($_POST['paste_data'] === '')) {
		$error = $lang['empty_paste'];
		goto OutPut;
		exit;
	}

    if (empty($_POST["tags"])) {
		$error = $lang['notags'];
		goto OutPut;
		exit;
	}

    if  (strlen($_POST["title"]) > 70) {
        $error = $lang['titlelen'];
		goto OutPut;
		exit;
    }	


	// Set our limits
	if (mb_strlen($_POST["paste_data"], '8bit') >  1024 * 1024 * $pastelimit) {
		$error = $lang['large_paste'];
		goto OutPut;
		exit;
	}
			
    // Check POST data status
    if (isset($_POST['title']) && isset($_POST['paste_data'])) {
        if ($cap_e == "on" && !isset($_SESSION['username'])) {
            if ($mode == "reCAPTCHA") {
                $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$recaptcha_secretkey."&response=".$_POST['g-recaptcha-response']);
                $response = json_decode($response, true);
                if ( $response["success"] == false ) {
                    // reCAPTCHA Errors
                    switch( $response["error-codes"][0] ) {
                        case "missing-input-response":
                            $error = $lang['missing-input-response']; 
                            break;
                        case "missing-input-secret":
                            $error = $lang['missing-input-secret'];
                            break;
                        case "invalid-input-response":
                            $error = $lang['missing-input-response'];
                            break;
                        case "invalid-input-secret":
                            $error = $lang['invalid-input-secret'];
                            break;
                    }
                    goto OutPut;
                }
            } else {
                $scode    = strtolower(htmlentities(Trim($_POST['scode'])));
                $cap_code = strtolower($_SESSION['captcha']['code']);
                if ($cap_code == $scode) {
                } else {
                    $error = $lang['image_wrong']; // Wrong captcha.
                    goto OutPut;
                }
            }
        }

        $p_title    = Trim(htmlspecialchars($_POST['title']));
			if (strlen($p_title)==0) $p_title='Untitled';
        $p_content  = htmlspecialchars($_POST['paste_data']);
        $p_visible  = Trim(htmlspecialchars($_POST['visibility']));
        $p_code     = Trim(htmlspecialchars($_POST['format']));
        $p_expiry   = Trim(htmlspecialchars($_POST['paste_expire_date']));
		$p_tagsys  = Trim(htmlspecialchars($_POST['tags']));
        $p_tagsys =  rtrim($p_tagsys, ',');
        $p_password = $_POST['pass'];
        if ($p_password == "" || $p_password == null) {
            $p_password = "NONE";
        } else {
            $p_password = password_hash($p_password, PASSWORD_DEFAULT);
        }
        $p_encrypt = Trim(htmlspecialchars($_POST['encrypted']));
        
        if (empty($p_encrypt)) {
            $p_encrypt = "0";
        } else {
            // Encrypt option
            $p_encrypt = "1";
            $p_content = encrypt($p_content);
        }
        
        if (isset($_SESSION['token'])) {
            $p_member = Trim($_SESSION['username']);
        } else {
            $p_member = "Guest";
        }
        // Set expiry time
        $expires = calculatePasteExpiry($p_expiry);

        $p_date    = date('jS F Y h:i:s A');
        $date      = date('jS F Y');
        $now_time  = mktime(date("H"), date("i"), date("s"), date("n"), date("j"), date("Y"));
        $timeedit  = gmmktime(date("H"), date("i"), date("s"), date("n"), date("j"), date("Y"));

        // Edit existing paste or create new?
        if ( isset($_POST['edit'] ) ) {
            if (isset($_SESSION['username'])) {
            $edit_paste_id = $_POST['paste_id'];
            $statement = $conn->prepare(
                "UPDATE pastes SET title = ?,content = ?,visible = ?,code=?,expiry=?,password=?,encrypt=?,member=?,ip=?,tagsys=?,now_time=? ,timeedit=? WHERE id = '?'"
            );

            $statement->execute([$p_title,$p_content,$p_visible,$p_code,$expires,$p_password,$p_encrypt,$p_member,$ip,$p_tagsys,$now_time,$timeedit,$edit_paste_id]);
        }}
        else {
             $statement = $conn->prepare("INSERT INTO pastes (title,content,visible,code,expiry,password,encrypt,member,date,ip,now_time,views,s_date,tagsys) VALUES 
            (?,?,?,?,?,?,?,?,?,?,?,'0',?,?)");
            $statement->execute([$p_title,$p_content,$p_visible,$p_code,$expires,$p_password,$p_encrypt,$p_member,$p_date,$ip,$now_time,$date,$p_tagsys]); 
            
        }
            $paste_id = $conn->query('SELECT MAX(id) FROM pastes')->fetch(PDO::FETCH_NUM)[0];
            $success = $paste_id;

            if ($p_visible == '0') {
                addToSitemap($paste_id, $priority, $changefreq, $mod_rewrite);
            }
        

    } else {
        $error = $lang['error']; // "Something went wrong";
    }
	
	// Redirect to paste on successful entry, or on successful edit redirect back to edited paste
	if ( isset( $success ) ) {
		if ( $mod_rewrite == '1' ) {
            if ( isset( $_POST['edit'] ) ) {
                $paste_url = "$edit_paste_id";
            } else {
                $paste_url = "$success"; 
            }
        } else {
            if ( $_POST['edit'] ) {
                $paste_url = "paste.php?id=$edit_paste_id";
            } else {
                $paste_url = "paste.php?id=$success";
            }
        }
		header("Location: ".$paste_url."");
	}

}

OutPut:
// Theme
require_once('theme/' . $default_theme . '/header.php');
require_once('theme/' . $default_theme . '/main.php');
require_once('theme/' . $default_theme . '/footer.php');
?>