<?php
@session_start();

//error_reporting(0);
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header('Expires: Sat, 1 Jan 2000 00:00:00 GMT');
if (file_exists("security.php")) include_once "security.php";

if (!isset($_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER']))
{
	echo "<script>window.location='" .$URL."../start.html';</script>";  
	exit();	
}

set_time_limit(300);
date_default_timezone_set("Asia/Katmandu"); 
$session_time = 30*60;
$_SESSION['user']['session_time'] = $session_time;
require_once "../main_url.inc.php";
$MAIN_URL = main_url();
$URL = main_url() . "mainuser/";

$no_of_parameter = strlen(trim($_GET['page']));
if ($no_of_parameter > 0)
{
	$get = $_GET['page'];
}
else
{
	if (isset($_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER']))
		echo "<script>window.location='" .$URL."dashboard.html';</script>";  
	else	
		echo "<script>window.location='" .$URL."../start.html';</script>";  
}

$config = new config();
$dbname = $config->dbname;
$dbuser = $config->dbuser;
$dbhost = $config->dbhost;
$dbpass = $config->dbpass;
unset($config);

include_once "../includes/functions.inc.php";


$UserRight = isset($_GET['user']['right']) ? $_GET['user']['right'] : '';
$OfficeID = isset($_GET['user']['office']) ? $_GET['user']['office'] : '';
$txtUserID = isset($_GET['user']['ID']) ? $_GET['user']['ID'] : '';
$password = isset($_GET['user']['password']) ? $_GET['user']['password'] : '';

$username = $_SESSION['user']['username'] ;
//$UserRight = $_SESSION['user']['right'] ;
//$OfficeID = $_SESSION['user']['office'];
//$txtUserID = $_SESSION['user']['ID'] ;
//$password = $_SESSION['user']['password'];
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
		if (isset($_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER']))
			$page = "../404.php";
		else
		echo "<script>window.location='" .$MAIN_URL."start.html';</script>";    
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
  <title><?php echo $software;?> : Admin Panel</title>
  <link href="<?php echo $MAIN_URL;?>bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo $MAIN_URL;?>new.css" rel="stylesheet">
  <link href="<?php echo $MAIN_URL;?>bootstrap/font-awesome-4.4.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <!--[if lt IE 9]>
  <script src="<?php echo $MAIN_URL;?>bootstrap/js/html5shiv.js"></script>
  <script src="<?php echo $MAIN_URL;?>bootstrap/js/respond.min.js"></script>
  <![endif]-->
  <script src="<?php echo $MAIN_URL;?>bootstrap/js/jquery-1.11.3.min.js"></script>
  <script src="<?php echo $MAIN_URL;?>bootstrap/js/bootstrap.min.js"></script>
</head>
<body>
<?php
//primary database setup
	include_once "../includes/db.inc.php";
	$db = new Database($dbhost,$dbuser,$dbpass,$dbname) ;
?>
<?php
if ($_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER'])
{
	UpdateEndTime($db);		// Update End Time for Started Exam

	//Permission Check
	$strPerm = "SELECT * FROM `exam_user_permission` WHERE `user_id` = '". $_SESSION['user']['user_id'] ."';";
	$query_perm = $db->query($strPerm);
	$no_of_perm = $db->num_rows($query_perm);
	$row_perm = $db->fetch_object($query_perm);
	$permission = $row_perm->permission;
	$db->free($query_perm);
	unset($strPerm, $query_perm, $row_perm);

	if ($no_of_perm > 0)
	{
		if ($permission & 1) $exam_type_view = TRUE;
		if ($permission & 2) $exam_type_change = TRUE;
		if ($permission & 4) $exam_type_question = TRUE;
		if ($permission & 8) $question_view = TRUE;
		if ($permission & 16) $question_change = TRUE;
		if ($permission & 32) $exam_center_view = TRUE;
		if ($permission & 64) $exam_center_change = TRUE;
		if ($permission & 128) $exam_view = TRUE;
		if ($permission & 256) $exam_start = TRUE;
		if ($permission & 512) $exam_student_view = TRUE;
		if ($permission & 1024) $exam_change = TRUE;
		if ($permission & 2048) $exam_question = TRUE;
		if ($permission & 4096) $exam_student = TRUE;
		if ($permission & 8192) $exam_schedule = TRUE;
		if ($permission & 16384) $exam_time = TRUE;
		if ($permission & 32768) $exam_result = TRUE;
		if ($permission & 65536) $student_view = TRUE;
		if ($permission & 131072) $student_change = TRUE;
		if ($permission & 262144) $user_view = TRUE;
		if ($permission & 524288) $user_change = TRUE;
		if ($permission & 1048576) $user_reset_pw = TRUE;
		if ($permission & 2097152) $user_user_perm = TRUE;
		if ($permission & 4194304) $db_backup = TRUE;

		// not accepted


		if ($exam_type_view || $exam_type_change || $exam_type_question) $isExamType = TRUE;

		if ($question_change || $question_view) $isQuestion = TRUE;

		if ($exam_center_view || $exam_center_change) $isCenter = TRUE;

		if ($exam_view ||  $exam_change || $exam_question || $exam_student || $exam_schedule || $exam_time) $isExam = TRUE;

		if ($student_view || $student_change || $student_result ) $isStudent = TRUE;

		if ($user_view || $user_change || $user_reset_pw || $user_user_perm) $isUser = TRUE;


$db_backup  = ''; 
if( isset( $_GET['db_backup '])) {
	$db_backup  = $_GET['db_backup ']; 
}



		if ($db_backup) $isSuperUser = TRUE;

	}
	else
	{
		$isExamType = TRUE;
		$isExam = TRUE;
		$isStudent = TRUE;
		$isCenter = TRUE;
		$isQuestion = TRUE;
	}

	$ii = strpos($page,"/");
	$perm_folder = '';
	if ($ii) 
	{
		$folder = explode("/",$page);
		$perm_folder = $folder[0];
		$page_link = $folder[1];
		unset($folder, $txtQuestion, $txtExam, $txtType, $txtCenter, $txtStudent);
		if ($perm_folder == "type" && $isExamType) 	//Exam Type
		{
			if ($page_link == "list.php" && $exam_type_view) 	$isPermission = TRUE ;
			if ( ($page_link == "add.php" || $page_link == "edit.php") && $exam_type_change) 	$isPermission = TRUE ;
			if ($page_link == "question.php" && $exam_type_question) 	$isPermission = TRUE ;
			$txtType = TRUE;
		}
		if ($perm_folder == "question" && $isQuestion) 
		{
			if ( ($page_link == "list.php" || $page_link = "print.php") && $question_view) 	$isPermission = TRUE ;
			if ( ($page_link == "add.php" || $page_link == "edit.php") && $question_change) 	$isPermission = TRUE ;
			$txtQuestion = TRUE;
		}
		if ($perm_folder == "exam" && $isExam) 
		{
			if ($page_link == "list.php" && $exam_view) 	$isPermission = TRUE ;
			if ( ($page_link == "add.php" || $page_link == "edit.php") && $exam_change) 	$isPermission = TRUE ;
			if ($page_link == "question.php" && $exam_question) 	$isPermission = TRUE ;
			if ($page_link == "student.php" && $exam_student) 	$isPermission = TRUE ;
			if ($page_link == "result.php" && $exam_result) 	$isPermission = TRUE ;
			$txtExam = TRUE;
		}

		if ($perm_folder == "center" && $isCenter)
		{
			if ($page_link == "list.php" && $exam_center_view) 	$isPermission = TRUE ;
			if ( ($page_link == "add.php" || $page_link == "edit.php") && $exam_center_change) 	$isPermission = TRUE ;
			$txtCenter = TRUE;
		}

		if ($perm_folder == "student" && $isStudent) 
		{
			if ($page_link == "list.php" && $student_view) 	$isPermission = TRUE ;
			if ( ($page_link == "add.php" || $page_link == "edit.php") && $student_change) 	$isPermission = TRUE ;
			$txtStudent = TRUE;
		}
		if ($perm_folder == "user" && $isUser)
		{
			if ($page_link == "list.php" && $user_view) 	$isPermission = TRUE ;
			if ( ($page_link == "add.php" || $page_link == "edit.php") && $user_change) 	$isPermission = TRUE ;
			$txtUser = TRUE;
		}
		if ($perm_folder == "db" && $isSuperUser) $isPermission = TRUE ;
	}
	else
		$isPermission = TRUE;


if( isset( $_GET['perm_folder']))
    $perm_folder = $_GET['perm_folder']; 

	if (!($perm_folder == "exam" && $isExam)) 
		unset($_SESSION['list_exam']['search'], $_SESSION['list_exam']['title']);

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
              Admin Login :<br> <?php echo $_SESSION['user']['fullname'];?>
            </div>
              <li>
                <a href="<?php echo $URL;?>dashboard.html">
                  <i class="fa fa-dashboard fa-fw"></i> Dashboard
                </a>
              </li>
            <?php 
						if ($isExamType == TRUE)
						{ 
						?>  
              <li><a href="<?php echo $URL;?>type/list.html"<?php if (isset($txtType)) echo " class=\"active\"";?>><i class="fa fa-wrench fa-fw"></i> Exam Type List</a></li>
            <?php 
						} 
						if ($isQuestion) 
						{ 
						?>  
              <li><a href="<?php echo $URL;?>question/list.html"<?php if (isset($txtQuestion)) echo " class=\"active\"";?>><i class="fa fa-question-circle fa-fw"></i> Question Bank</a></li>
            <?php } if ($isCenter) { ?>
              <li><a href="<?php echo $URL;?>center/list.html"<?php if (isset($txtCenter)) echo " class=\"active\"";?>><i class="fa fa-university fa-fw"></i> Center List</a></li>
            <?php } if ($isExam) { ?>  
              <li><a href="<?php echo $URL;?>exam/list.html"<?php if (isset($txtExam)) echo " class=\"active\"";?>><i class="fa fa-edit fa-fw"></i> Exam List</a></li>
            <?php } if ($isStudent) { ?>  
              <li><a href="<?php echo $URL;?>student/list.html"<?php if (isset($txtStudent)) echo " class=\"active\"";?>><i class="fa fa-graduation-cap fa-fw"></i> Student List</a></li>
            <?php } if ($isUser) { ?>  
              <li><a href="<?php echo $URL;?>user/list.html"<?php if(isset($txtUser)) echo " class=\"active\"";?>><i class="fa fa-users fa-fw"></i> Users</a></li>
            <?php } if(isset($isSuperUser)) { ?>
              <li><a href="#" name="db_backup" data-class="btn btn-success" class="backup" data-target="#backup-modal" data-toggle="modal" data-url="<?php echo $MAIN_URL;?>backup.html"><i class="fa fa-database fa-fw"></i> Database Backup</a>
						<?php } ?>
              <li>
                <a href="javascript:void(0)" class="changepw">
                  <i class="fa fa-key fa-fw"></i> Change Password
                </a>
              </li>
              <li><a href="<?php echo $URL;?>logout.html"><i class="fa fa-sign-out fa-fw"></i> Logout</a>

            <div class="servertime">
              <small><strong>Exam System's Date/Time:</strong><br><span id="curr"></span> <?php CurrentDateTime("curr");?></small>
            </div>

            </ul>
        </div>
      </div>
    </nav>
<?php

		unset($folder, $txtQuestion, $txtExam, $txtType, $txtCenter, $txtStudent);
		unset($_SESSION['question-answer']);

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