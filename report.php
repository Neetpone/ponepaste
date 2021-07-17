<?php
define('IN_PONEPASTE', 1);
require_once('includes/common.php');
require_once('includes/functions.php');

// UTF-8
header('Content-Type: text/html; charset=utf-8');

$ip = $_SERVER['REMOTE_ADDR'];

//Report paste
$p_reasonrep = Trim(htmlspecialchars($_POST['reasonrep']));
$p_memreport = $current_user ? $current_user ->username : 'Guest';
$p_pastereport = $_POST['reppasteid'];
$p_reasonrep = preg_replace("/[^0-9]/", "", $p_reasonrep);

$conn->prepare('INSERT INTO user_reports (m_report, p_report, t_report, rep_reason) VALUES (?, ?, NOW(), ?)')
    ->execute([$p_memreport, $p_pastereport, $p_reasonrep]);
$repmes = "Paste has been reported.";

