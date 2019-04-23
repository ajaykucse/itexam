<?php
if ( !(defined("WEB-PROGRAM")) 	|| !(defined("CODER")) )
{
	if (file_exists("main_url.inc.php"))
		 include_once "main_url.inc.php";
	if (file_exists("../main_url.inc.php")) 
		include_once "../main_url.inc.php";
	if (file_exists("../../main_url.inc.php")) 
		include_once "../../main_url.inc.php";
	$MAIN_URL = main_url();
	header("Location:".$MAIN_URL."start.html");	
	exit();
}

?>