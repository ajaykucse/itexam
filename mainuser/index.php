<?php
session_start();
define ("WEB-PROGRAM","Online Exam");
define ("CODER","Sunil Kumar K. C.");
include_once "header.inc.php";
$now = time(); // Checking the time now when home page starts.

if (!$isPermission)
{
	notify("User Permission","<br><font color=red>You don't have permission to view this page.</font><br>&nbsp;",$URL."dashboard.html",TRUE,NULL);
//	notify("User Permission","<br><font color=red>You don't have permission to view this page.</font><br>&nbsp;",$URL."dashboard.html",TRUE,5000);
	include_once "footer.inc.php";
	exit();
}

if ($DisplayMenu)
{
	if ($now > $_SESSION['expire'])
	{
		destroy_session($db);
		$_SESSION['exp'] = TRUE;
		session_destroy();
		notify("Session Expire","Your session has been expired!", $URL."../start.html",true,5000);
		include_once "footer.inc.php";
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
	notify("No Session","<font color=red>There is no any registered Session. </font>",$URL . "../start.html");
	
}
else
{
	include_once $page;
}
	include_once "footer.inc.php";
?>