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

$conn->prepare('INSERT INTO user_reports (m_report, p_report, t_report, rep_reason) VALUES (?, ?, ?, ?)')
    ->execute([$p_memreport, $p_pastereport, $p_reporttime, $p_reasonrep]);
$repmes = "Paste has been reported.";

