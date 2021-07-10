<?php
session_start();

// store session data
if (isset($_SESSION['username']))
$_SESSION['username'] = $_SESSION['username']; // or if you have any algo.
?>