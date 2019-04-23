<?php 
session_start();
define ("WEB-PROGRAM","Online Exam");
define ("CODER","Sunil Kumar K. C.");
include_once "header.inc.php";
$now = time(); // Checking the time now when home page starts.
if ($DisplayMenu)
{
	if ($now > $_SESSION['expire'])
	{
		destroy_session($db);
		notify("Session Expire","Your session has been expired!", $MAIN_URL."start.html",true,5000);
		include_once "footer.inc.php";
		$_SESSION['exp'] = TRUE;
		exit();
	}
	else
	{
		$_SESSION['expire'] = time() + $session_time ;
		$_SESSION['exp'] = FALSE;
		update_session($db);
	}
}

if ($noSession)
{
	notify("No Session","<font color=red>There is no any registered Session.</font>",$URL . "logout.html");
}
else
{
	include_once $page;
}
	include_once "footer.inc.php";
?>