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


//Report paste
$p_reasonrep = Trim(htmlspecialchars($_POST['reasonrep']));
if (isset($_SESSION['token'])) {
    $p_memreport = htmlspecialchars($_SESSION['username']);
} else {
    $p_memreport = "Guest";
}
$p_pastereport = Trim(htmlspecialchars($_POST['reppasteid']));
$p_reporttime = gmmktime(date("H"), date("i"), date("s"), date("n"), date("j"), date("Y"));
$p_reasonrep = preg_replace("/[^0-9]/", "", $p_reasonrep);
//Sec
$p_reasonrep = mysqli_real_escape_string($con, $p_reasonrep);
$p_memreport = mysqli_real_escape_string($con, $p_memreport);
$p_pastereport = mysqli_real_escape_string($con, $p_pastereport);
$reported = "INSERT INTO user_reports (m_report,p_report,t_report,rep_reason) VALUES 
('$p_memreport','$p_pastereport ','$p_reporttime','$p_reasonrep')";
if ($con->query($reported) === true) {
    $repmes = "Paste has been reported.";
} else {
    $repmes = "Reporting failed";
}
?>
