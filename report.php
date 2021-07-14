<?php
define('IN_PONEPASTE', 1);
require_once('includes/common.php');
require_once('includes/functions.php');

// UTF-8
header('Content-Type: text/html; charset=utf-8');

$date = date('jS F Y');
$ip = $_SERVER['REMOTE_ADDR'];

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

