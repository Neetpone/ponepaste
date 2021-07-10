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

// PHP <5.5 compatibility
require_once('includes/password.php'); 
 
session_start();

// UTF-8
header('Content-Type: text/html; charset=utf-8');

// Required functions
require_once('config.php');
require_once('includes/geshi.php');
require_once('includes/functions.php');

// Path of GeSHi object
$path = 'includes/geshi/';

// Path of Parsedown object
$parsedown_path = 'includes/Parsedown/Parsedown.php';
$parsedownextra_path  = 'includes/Parsedown/ParsedownExtra.php';
$parsedownsec_path = 'includes/Parsedown/SecureParsedown.php';

// GET Paste ID
if (isset($_GET['id'])) {
    $paste_id = Trim(htmlspecialchars($_GET['id']));
    $paste_id = preg_replace( '/[^0-9]/', '', $paste_id );
    $paste_id = (int) filter_var($paste_id, FILTER_SANITIZE_NUMBER_INT);    
} elseif (isset($_POST['id'])) {
    $paste_id = Trim(htmlspecialchars($_POST['id']));
    $paste_id = preg_replace( '/[^0-9]/', '', $paste_id );
    $paste_id = (int) filter_var($paste_id, FILTER_SANITIZE_NUMBER_INT);    
}

// Prevent SQLInjection
settype($paste_id, 'integer');


// Database Connection
$conn = new PDO(
    "mysql:host=$db_host;dbname=$db_schema;charset=utf8",
    $db_user,
    $db_pass,
    $db_opts
);


// Get site info
$site_info_rows = $conn->query('SELECT * FROM site_info');
while ($row = $site_info_rows->fetch()) {
    $title				= Trim($row['title']);
    $des				= Trim($row['des']);
    $baseurl    		= Trim($row['baseurl']);
    $keyword			= Trim($row['keyword']);
    $site_name			= Trim($row['site_name']);
    $email				= Trim($row['email']);
    $twit				= Trim($row['twit']);
    $face				= Trim($row['face']);
    $gplus				= Trim($row['gplus']);
    $ga					= Trim($row['ga']);
    $additional_scripts	= Trim($row['additional_scripts']);
}

// Set theme and language
$site_theme_rows = $conn->query('SELECT * FROM interface WHERE id="1"');
while ($row = $site_theme_rows->fetch()) {
    $default_lang  = Trim($row['lang']);
    $default_theme = Trim($row['theme']);
}
require_once("langs/$default_lang");


// Check if IP is banned
if ( is_banned($conn, $ip) ) die($lang['banned']); // "You have been banned from ".$site_name;


// Current date & user IP
$date    = date('jS F Y');
$ip      = $_SERVER['REMOTE_ADDR'];
$data_ip = file_get_contents('tmp/temp.tdata');

// Ads
$site_ads_rows = $conn->query('SELECT * FROM ads WHERE id="1"');
while ($row = $site_ads_rows->fetch()) {
    $text_ads = Trim($row['text_ads']);
    $ads_1    = Trim($row['ads_1']);
    $ads_2    = Trim($row['ads_2']);
}

// Logout
if (isset($_GET['logout'])) {
	header('Location: ' . $_SERVER['HTTP_REFERER']);
    unset($_SESSION['token']);
    unset($_SESSION['oauth_uid']);
    unset($_SESSION['username']);
    session_destroy();
}

// Page views
$site_view_rows = $conn->query("SELECT @last_id := MAX(id) FROM page_view");
while ($row = $site_view_rows->fetch()) {
    $last_id = $row['@last_id := MAX(id)'];
}

$site_view_last = $conn->query("SELECT * FROM page_view WHERE id=?");
$site_view_last->execute([$last_id]);      
while ($row = $site_view_last->fetch()) {
    $last_date = $row['date'];
}

if ($last_date == $date) {
    if (str_contains($data_ip, $ip)) {
        $statement = $conn->prepare("SELECT * FROM page_view WHERE id =?");
        $statement->execute([$last_id]);        
        while ($row = $statement->fetch()) {
            $last_tpage = Trim($row['tpage']);
        }
        $last_tpage = $last_tpage + 1;
        
        // IP already exists, Update view count
        $statement = $conn->prepare("UPDATE page_view SET tpage=? WHERE id=?");
        $statement->execute([$last_tpage,$last_id]);  
    } else {
        $statement = $conn->prepare("SELECT * FROM page_view WHERE id =?");
        $statement->execute([$last_id]);  
        while ($row = $statement->fetch()) {
            $last_tpage  = Trim($row['tpage']);
            $last_tvisit = Trim($row['tvisit']);
        }
        $last_tpage  = $last_tpage + 1;
        $last_tvisit = $last_tvisit + 1;
      
        // Update both tpage and tvisit.
        $statement = $conn->prepare("UPDATE page_view SET tpage=?,tvisit=? WHERE id =?");
        $statement->execute([$last_tpage,$last_tvisit,$last_id]); 
        file_put_contents('tmp/temp.tdata', $data_ip . "\r\n" . $ip);
    }
} else {
    // Delete the file and clear data_ip
    unlink("tmp/temp.tdata");
    $data_ip = "";
    
    // New date is created
    $statement = $conn->prepare("INSERT INTO page_view (date,tpage,tvisit) VALUES (?,'1','1')");
    $statement->execute([$date]); 
    // Update the IP
    file_put_contents('tmp/temp.tdata', $data_ip . "\r\n" . $ip);
    
}
//Get fav count
$get_fav_count =  $conn->prepare("SELECT count(f_paste) as total FROM pins WHERE f_paste=?");
$get_fav_count->execute([$paste_id]); 
    while ($row = $get_fav_count->fetch()) {
    $fav_count = $row['total'];
    }


//Get paste info

$get_paste_details = $conn->prepare("SELECT * FROM pastes WHERE id=?");
$get_paste_details->execute([$paste_id]); 
    if ($get_paste_details->fetchColumn() > 0) {
    $get_paste_details = $conn->prepare("SELECT * FROM pastes WHERE id=?");
    $get_paste_details->execute([$paste_id]); 
    while ($row = $get_paste_details->fetch()) {
        $p_title    = $row['title'];
        $p_content  = $row['content'];
        $p_visible  = $row['visible'];
        $p_code     = $row['code'];
        $p_expiry   = Trim($row['expiry']);
        $p_password = $row['password'];
        $p_member   = $row['member'];
        $p_date     = $row['date'];
        $now_time   = $row['now_time'];
        $p_encrypt  = $row['encrypt'];
        $p_views    = $row['views'];
		$p_tagsys	= $row['tagsys'];
    }
    
    
    $mod_date = date("jS F Y h:i:s A", $now_time);
    
    $p_private_error = '0';
    if ($p_visible == "2") {
        if (isset($_SESSION['username'])) {
            if ($p_member == Trim($_SESSION['username'])) {
            } else {
                $notfound           = $lang['privatepaste']; //" This is a private paste.";
                $p_private_error = '1';
                goto Not_Valid_Paste;
            }
        } else {
            $notfound           = $lang['privatepaste']; //" This is a private paste. If you created this paste, please login to view it.";
            $p_private_error = '1';
            goto Not_Valid_Paste;
        }
    }
    if ($p_expiry == "NULL" || $p_expiry == "SELF") {
    } else {
        $input_time   = $p_expiry;
        $current_time = mktime(date("H"), date("i"), date("s"), date("n"), date("j"), date("Y"));
        if ($input_time < $current_time) {
            $notfound       = $lang['expired'];
            $p_private_error = 1;
            goto Not_Valid_Paste;
        }
    }
    if ($p_encrypt == "" || $p_encrypt == null || $p_encrypt == '0') {
    } else {
        $p_content = decrypt($p_content);
    }
    $op_content = Trim(htmlspecialchars_decode($p_content));
    
    // Download the paste   
    if (isset($_GET['download'])) {
        if ($p_password == "NONE") {
            doDownload($paste_id, $p_title, $p_member, $op_content, $p_code);
            exit();
        } else {
            if (isset($_GET['password'])) {
                if (password_verify($_GET['password'],$p_password)) {
                    doDownload($paste_id, $p_title, $p_member, $op_content, $p_code);
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
        if ($p_password == "NONE") {
            rawView($paste_id, $p_title, $op_content, $p_code);
            exit();
        } else {
            if (isset($_GET['password'])) {
                if (password_verify($_GET['password'],$p_password)) {
                    rawView($paste_id, $p_title, $op_content, $p_code);
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
    $highlight   = array();
    $prefix_size = strlen('!highlight!');
    if ($prefix_size) {
        $lines     = explode("\n", $p_content);
        $p_content = "";
        foreach ($lines as $idx => $line) {
            if (substr($line, 0, $prefix_size) == '!highlight!') {
                $highlight[] = $idx + 1;
                $line        = substr($line, $prefix_size);
            }
            $p_content .= $line . "\n";
        }
        $p_content = rtrim($p_content);
    } 
    
    // Apply syntax highlight
    $p_content = htmlspecialchars_decode($p_content);
    if ( $p_code == "pastedown" ) {
        include( $parsedown_path );
        include ($parsedownextra_path);
        include ($parsedownsec_path);
        $Parsedown = new Parsedown();
        $Parsedown->setSafeMode(true);
        $p_content = $Parsedown->text( $p_content );
    } else {
        $geshi     = new GeSHi($p_content, $p_code, $path);
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
        $style     = $geshi->get_stylesheet();
        $ges_style = '<style>' . $style . '</style>';
    }
    
    // Embed view after GeSHI is applied so that $p_code is syntax highlighted as it should be. 
    if (isset($_GET['embed'])) {
        if ( $p_password == "NONE" ) {
            embedView( $paste_id, $p_title, $p_content, $p_code, $title, $baseurl, $ges_style, $lang );
            exit();
        } else {
            if ( isset( $_GET['password'] ) ) {
                if ( password_verify( $_GET['password'], $p_password ) ) {
                    embedView( $paste_id, $p_title, $p_content, $p_code, $title, $p_baseurl, $ges_style, $lang );
                    exit();
                } else {
                    $error = $lang['wrongpassword']; // 'Wrong password';
                }
            } else {
                $error = $lang['pwdprotected']; // 'Password protected paste';
            }
        }
    } 
} else {
	header("HTTP/1.1 404 Not Found");
    $notfound = $lang['notfound']; // "Not found";
}

require_once('theme/' . $default_theme . '/header.php');
if ($p_password == "NONE") {
    
    // No password & diplay the paste
    
    // Set download URL
	if ($mod_rewrite == '1') {
		$p_download = "download/$paste_id";
	} else {
		$p_download = "paste.php?download&id=$paste_id";
	}
	
	// Set raw URL
	if ($mod_rewrite == '1') {
		$p_raw = "raw/$paste_id";
	} else {
		$p_raw = "paste.php?raw&id=$paste_id";
	}

	// Set embed URL
	if ( $mod_rewrite == '1' ) {
		$p_embed = "embed/$paste_id";
	} else {
		$p_embed = "paste.php?embed&id=$paste_id";
	}
    
        //pasteviews
    if($_SESSION['not_unique'] !== $paste_id) {
    $_SESSION['not_unique'] = $paste_id;
    updateMyView($conn, $paste_id);
}
    
    // Theme
    require_once('theme/' . $default_theme . '/view.php');
    if ($p_expiry == "SELF") {
        deleteMyPaste($con, $paste_id);
    }
} else {
    $p_download = "paste.php?download&id=$paste_id&password=" . password_hash(isset($_POST['mypass']), PASSWORD_DEFAULT);
    $p_raw = "paste.php?raw&id=$paste_id&password=" . password_hash(isset($_POST['mypass']), PASSWORD_DEFAULT);
    // Check password
    if (isset($_POST['mypass'])) {
        if (password_verify($_POST['mypass'], $p_password)) {
            // Theme
            require_once('theme/' . $default_theme . '/view.php');
            if ($p_expiry == "SELF") {
                deleteMyPaste($con, $paste_id);
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
if ($p_private_error == '1') {
    // Display errors
    require_once('theme/' . $default_theme . '/header.php');
    require_once('theme/' . $default_theme . '/errors.php');
}

// Footer
require_once('theme/' . $default_theme . '/footer.php');
?>
