<?php
@session_start();  
 
error_reporting(0);
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header('Expires: Sat, 1 Jan 2000 00:00:00 GMT');
if (file_exists("security.php")) include_once "security.php";

set_time_limit(300);

global $software_permission;
$software_permission = 4;			//Admin Permissions for User 

date_default_timezone_set("Asia/Katmandu"); 
$session_time = 30*60;
$_SESSION['user']['session_time'] = $session_time;
require_once "main_url.inc.php";
$URL = main_url();

$no_of_parameter = strlen(trim($_GET['page']));
if ($no_of_parameter > 0)
{
	$get = $_GET['page'];
}
else
{
	echo "<script>window.location='" .$URL."start.html';</script>";  
}

$config = new config();
$dbname = $config->dbname;
$dbuser = $config->dbuser;
$dbhost = $config->dbhost;
$dbpass = $config->dbpass;
unset($config);

include_once "includes/functions.inc.php";

$username = $_SESSION['user']['username'] ;
$UserRight = $_SESSION['user']['right'] ;
$OfficeID = $_SESSION['user']['office'];
$txtUserID = $_SESSION['user']['ID'] ;
$password = $_SESSION['user']['password'];
$get = FilterString(($get));

if ($get == "login")
	$DisplayMenu = FALSE;
else if ($get == "logout")
	$DisplayMenu = FALSE;
else
	$DisplayMenu = TRUE;

$file = $get .".php";

	if (file_exists($file))
	{
		$page = ($file);
		$isPage = TRUE;
	}
	if (! $isPage)
	{
		$DisplayMenu = FALSE;
		if (isset($_SESSION['ONLINE-EXAM-SIMULATOR']))
			$page = "404.php";
		else if (isset($_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER']))
			$page = "404.php";
		else if (isset($_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER']))
			$page = "404.php";
		else if (isset($_SESSION['ONLINE-EXAM-SIMULATOR-EXAM-USER']))
			$page = "404.php";
		else
			echo "<script>window.location='" .$URL."start.html';</script>";    
	}

	if ($DisplayMenu)
	{
		if (file_exists("security.php")) include_once "security.php";
	}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <title><?php echo $software;?></title>
  <!-- Bootstrap Core CSS -->
  <link href="<?php echo $URL;?>bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
<!--  <link href="<?php echo $URL;?>exam.css" rel="stylesheet"> -->
  <link href="<?php echo $URL;?>new.css" rel="stylesheet">
  <!-- Custom Fonts -->
  <link href="<?php echo $URL;?>bootstrap/font-awesome-4.4.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="<?php echo $URL;?>bootstrap/js/html5shiv.js"></script>
  <script src="<?php echo $URL;?>bootstrap/js/respond.min.js"></script>
  <![endif]-->
  <!-- jQuery Version 1.11.0 -->
  <script src="<?php echo $URL;?>bootstrap/js/jquery-1.11.3.min.js"></script>
  <!-- Bootstrap Core JavaScript -->
  <script src="<?php echo $URL;?>bootstrap/js/bootstrap.min.js"></script>
</head>
<body>
<?php
//primary database setup
	include_once "includes/db.inc.php";
	$db = new Database($dbhost,$dbuser,$dbpass,$dbname) ;
?>
  <div id="wrapper">
        <!-- Navigation -->
<!--        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
          <div class="navbar-header">
            <a class="navbar-brand"><strong><?php echo $software;?></strong></a>
            </div>
        </nav>  -->
  <div id="page-wrapper">
    <div class="container-fluid" style="padding-left:0px; padding-right:0px;">

<?php 
if ($DisplayMenu)
{
?>
	<script type="text/javascript">
  $('.changepw').click(function(ev){
    ev.preventDefault();
    $('#changepw').removeData('bs.modal')
    $('#changepw').modal('show');
  });
  </script>
<?php
}
?>
<style>
#page-wrapper
{
	margin-left:0px !important;
}
</style>