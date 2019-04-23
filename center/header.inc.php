<?php
session_start();

//error_reporting(0);
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header('Expires: Sat, 1 Jan 2000 00:00:00 GMT');
if (file_exists("security.php")) include_once "security.php";

if (!isset($_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER']))
{
	echo "<script>window.location='" .$URL."../start.html';</script>";  
	exit();	
}

set_time_limit(600);

global $software_permission;
$software_permission = 4;			//Admin Permissions for User 

date_default_timezone_set("Asia/Katmandu"); 
$session_time = 30*60;
$_SESSION['user']['session_time'] = $session_time;
require_once "../main_url.inc.php";
$MAIN_URL = main_url();
$URL = main_url() . "center/";

$no_of_parameter = strlen(trim($_GET['page']));
if ($no_of_parameter > 0)
{
	$get = $_GET['page'];
}
else
{
	if (isset($_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER']))
		echo "<script>window.location='" .$URL."dashboard.html';</script>";  
	else	
		echo "<script>window.location='" .$MAIN_URL."start.html';</script>";  
}

$config = new config();
$dbname = $config->dbname;
$dbuser = $config->dbuser;
$dbhost = $config->dbhost;
$dbpass = $config->dbpass;
unset($config);

include_once "../includes/functions.inc.php";

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
		if (isset($_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER']))
			$page = "../404.php";
		else
			echo "<script>window.location='" .$MAIN_URL."start.html';</script>";    
	}

	if ($DisplayMenu)
	{
		if (file_exists("security.php")) include_once "security.php";
		$center_id = $_SESSION['user']['center_id'];
	}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <title><?php echo $software;?> : Center Panel</title>
  <!-- Bootstrap Core CSS -->
  <link href="<?php echo $MAIN_URL;?>bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
<!--  <link href="<?php echo $MAIN_URL;?>exam.css" rel="stylesheet"> -->
  <link href="<?php echo $MAIN_URL;?>new.css" rel="stylesheet">
  <!-- Custom Fonts -->
  <link href="<?php echo $MAIN_URL;?>bootstrap/font-awesome-4.4.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="<?php echo $MAIN_URL;?>bootstrap/js/html5shiv.js"></script>
  <script src="<?php echo $MAIN_URL;?>bootstrap/js/respond.min.js"></script>
  <![endif]-->
  <!-- jQuery Version 1.11.0 -->
  <script src="<?php echo $MAIN_URL;?>bootstrap/js/jquery-1.11.3.min.js"></script>
  <!-- Bootstrap Core JavaScript -->
  <script src="<?php echo $MAIN_URL;?>bootstrap/js/bootstrap.min.js"></script>
</head>
<body>
<?php
//primary database setup
	include_once "../includes/db.inc.php";
	$db = new Database($dbhost,$dbuser,$dbpass,$dbname) ;
?>
<?php
if ($_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER'])
{
	UpdateEndTime($db);		// Update End Time for Started Exam
?>
  <div id="wrapper">
        <nav class="navbar-default navbar-static-top" role="navigation">
          <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            <a class="navbar-brand"><strong><?php echo $software;?></strong></a>
            </div>

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse collapse">
                    <ul class="nav" id="side-menu">
                      <div class="logininfo">
                      	User Login:<br> <span style="font-weight:normal;"> <?php echo $_SESSION['user']['fullname'];?> </span>
                      </div>

                      <li>
                      	<a href="<?php echo $URL;?>dashboard.html">
                        	<i class="fa fa-dashboard fa-fw"></i> Dashboard
                        </a>
                      </li>
                      <li>
                      	<a href="<?php echo $URL;?>exam/list.html">
                        	<i class="fa fa-edit fa-fw"></i> Exam List
                        </a>
                      </li>
                        <li><a href="javascript:void(0)" class="changepw"><i class="fa fa-key fa-fw"></i> Change Password</a>
                        <li><a href="<?php echo $URL;?>logout.html"><i class="fa fa-sign-out fa-fw"></i> Logout</a>

                    <div class="servertime">
                      <small><strong>Exam System's Date/Time:</strong><br><span id="curr"></span> <?php CurrentDateTime("curr");?></small>
                    </div>

                    </ul>
                </div>
          </div>
        </nav>

<?php
	$ii = strpos($page,"/");
	if ($ii) 
	{
		$folder =  explode("/",$page);
		$perm_folder = $folder[0];
		$page_link = $folder[1];

		if ($perm_folder == "exam") 
		{
			if ($page_link == "list.php") $txtExam = TRUE;
			if ($page_link == "practical.php") $txtExam = TRUE;
		}
		else
			unset($_SESSION['center_list_exam']['search'], $_SESSION['center_list_exam']['title']);

	}
	else
		unset($_SESSION['center_list_exam']['search'], $_SESSION['center_list_exam']['title']);




	if (!check_session($db))
	{
		$noSession = TRUE;
	}
	else
	{
		$noSession = FALSE;
	}
?>
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
	include_once "changepw.php";
}
}
?>