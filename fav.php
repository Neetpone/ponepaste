<?php
session_start();

require_once('config.php');
require_once('includes/functions.php');

// UTF-8
header('Content-Type: text/html; charset=utf-8');

$date = date('jS F Y');
$ip = $_SERVER['REMOTE_ADDR'];
$data_ip = file_get_contents('tmp/temp.tdata');
$con = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);

if (mysqli_connect_errno()) {
    die("Unable to connect to database");
}
$query = "SELECT * FROM site_info";
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_array($result)) {
    $title = Trim($row['title']);
    $des = Trim($row['des']);
    $baseurl = Trim($row['baseurl']);
    $keyword = Trim($row['keyword']);
    $site_name = Trim($row['site_name']);
    $email = Trim($row['email']);
    $twit = Trim($row['twit']);
    $face = Trim($row['face']);
    $gplus = Trim($row['gplus']);
    $ga = Trim($row['ga']);
    $additional_scripts = Trim($row['additional_scripts']);
}

// Set theme and language
$query = "SELECT * FROM interface";
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_array($result)) {
    $default_lang = Trim($row['lang']);
    $default_theme = Trim($row['theme']);
}

require_once("langs/$default_lang");

// Check if IP is banned
if (is_banned($con, $ip)) die($lang['banned']); // "You have been banned from ".$site_name;

// Logout
if (isset($_GET['logout'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    unset($_SESSION['token']);
    unset($_SESSION['oauth_uid']);
    unset($_SESSION['username']);
    session_destroy();
}


//Fav paste
if (isset($_POST['fid'])) {
    if (isset($_SESSION['token'])) {
        $f_user = htmlspecialchars($_SESSION['username']);
        $f_pasteid = Trim(htmlspecialchars($_POST['fid']));
        $f_pasteid = preg_replace('/[^0-9]/', '', $f_pasteid);
        $f_pasteid = (int)filter_var($f_pasteid, FILTER_SANITIZE_NUMBER_INT);
        $f_time = gmmktime(date("H"), date("i"), date("s"), date("n"), date("j"), date("Y"));
//Sec
        $f_user = mysqli_real_escape_string($con, $f_user);
        $f_pasteid = mysqli_real_escape_string($con, $f_pasteid);
        $f_time = mysqli_real_escape_string($con, $f_time);
        $fav_check = "SELECT COUNT(fid) FROM pins WHERE f_paste='$f_pasteid' AND m_fav='$f_user'";
        $result = mysqli_query($con, $fav_check);
        $count = mysqli_fetch_row($result)[0];
        if ($count == 0) {
            $faved = "INSERT INTO pins (m_fav,f_paste,f_time) VALUES 
('$f_user','$f_pasteid ','$f_time')";
        } else {
            $faved = "DELETE FROM pins WHERE f_paste='$f_pasteid' and m_fav='$f_user'";
        }
        if ($con->query($faved) === true) {
            $error = "Paste has been Favorited.";
        } else {
            $error = "Fav failed";
        }
    }
}


// Theme
require_once('theme/' . $default_theme . '/header.php');
require_once('theme/' . $default_theme . '/report.php');
?>