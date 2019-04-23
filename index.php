<?php
session_start();
define ("WEB-PROGRAM","Online Exam");
define ("CODER","Sunil Kumar K. C.");
include_once "header.inc.php";
$now = time(); // Checking the time now when home page starts.
include_once $page;
include_once "changepw.php";
include_once "footer.inc.php";
?>