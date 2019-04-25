<?php 
@session_start();
if (!isset($_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER']))
{
  echo '<meta http-equiv="refresh" content="0;URL='.$URL.'../start.html" />';	
}

$isLogout = true;

$_SESSION['editmode'] = "yes";

	$username = $_SESSION['user']['username'];

	$_SESSION['logout'] = true;
	$authorized = false;

	unset ($_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER']);
	unset ($_SESSION['user']);
	unset ($_SESSION['rand']);
	$_SESSION = array();
	session_destroy();
	destroy_session($db);

	echo "<div style=\"margin-top:15%;\"><center /><h2><strong>Logging out...</strong><h2></div>";
?>
  <form id="formlogout" action='../start.html' METHOD='POST'>
    <input type='hidden' name='logout' value='true'>
  </form>
	<script>
	window.setTimeout(function() {
		$("#formlogout").submit();
		return false; //Prevent the browser jump to the link anchor
	}, 700); 
  </script>