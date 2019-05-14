<?php
@session_start();

//error_reporting(0);
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header('Expires: Sat, 1 Jan 2000 00:00:00 GMT');
if (file_exists("security.php")) include_once "security.php";

//set_time_limit(300);

global $software_permission;
$software_permission = 4;			//Admin Permissions for User 

date_default_timezone_set("Asia/Katmandu"); 
$session_time = 30*60;
$_SESSION['user']['session_time'] = $session_time;
require_once "../main_url.inc.php";
$MAIN_URL = main_url();
$URL = main_url() ."student/";

$no_of_parameter = strlen(trim($_GET['page']));
if ($no_of_parameter > 0)
{
	$get = $_GET['page'];
}
else
{
	echo "<script>window.location='" .$URL."exam.html';</script>";  
}

$config = new config();
$dbname = $config->dbname;
$dbuser = $config->dbuser;
$dbhost = $config->dbhost;
$dbpass = $config->dbpass;
unset($config);

include_once "../includes/functions.inc.php";

$username = $_SESSION['user']['username'] ;
$UserRight = (isset($_SESSION['user']['right']));
$OfficeID = (isset($_SESSION['user']['office']));
$txtUserID = (isset($_SESSION['user']['ID']));
$password = (isset($_SESSION['user']['password']));
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

		if (isset($_SESSION['ONLINE-EXAM-SIMULATOR-STUDENT']))
			$page = "404.php";
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
  <title><?php echo $software;?></title>
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
if ($_SESSION['ONLINE-EXAM-SIMULATOR-STUDENT'])
{
	$student_id = FilterNumber($_SESSION['user']['student_id']);
	$center_id = FilterNumber($_SESSION['user']['center_id']);
	$exam_id = FilterNumber($_SESSION['user']['exam_id']);
	$user_id = $student_id;
	
	UpdateEndTime($db);		// Update End Time for Started Exam
	
?>
  <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
          <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            <a class="navbar-brand"><strong><?php echo $software;?></strong></a>
            </div>
            <!-- /.navbar-header -->

            <div class="navbar-default sidebar" role="navigation">
              <div class="sidebar-nav navbar-collapse collapse">
                <div class="nav text-center" id="side-menu">
                  <div class="col-md-8 col-md-offset-2" style="margin-top:25px; margin-bottom:20px;" align="center">
                    <img class="img-responsive img-circle" src="../placeholder.jpg" style="max-height:120px;">
                  </div>
                  	<div class="col-md-12" style="margin-bottom:25px;">
<?php
$student_id = $_SESSION['user']['student_id'];
$strStudent = "SELECT * FROM exam_student WHERE exam_student.student_id = '$student_id';";

$query_student = $db->query($strStudent);
$row_student = $db->fetch_object($query_student);
?>

                    <p><strong>Reg. No.: </strong><?php echo $row_student->reg_no;?></p>
                    <p><strong>Name: </strong><?php echo $row_student->name;?></p>

                    <br>
<?php if (isset($_SESSION['user']['exam']['started'])) 
{
	?>
                    <div><b>Total Time :&nbsp;<font size="+1"><span id="TotalTime"></span></font></b></div>
                    <div><b>Elapsed Time :&nbsp;<font size="+1"><span id="ElapsedTime"></span></font></b></div>
                    <br>
                    <br>
                    <?php 
}
if (isset($_SESSION['user']['exam']['startTime']))
{
	$student_exam_id= $_SESSION['user']['student_exam_id'];
										$sqlStartTime = "select timediff(NOW(),start_time) as elapsed from exam_student_exam WHERE student_exam_id = '$student_exam_id'";
										$queryStart = $db->query($sqlStartTime );
										$rowStart = $db->fetch_object($queryStart);
										echo $rowStart->elapsed;
										$db->free($queryStart);
										unset($sqlStartTime ,$queryStart,$rowStart);
}
										?>
                  <a class="btn btn-success" id="btnLogout" name="btnLogout"><i class="fa fa-sign-out"></i> Logout</a>


                    <div class="servertime" style="margin-top:50px; border-bottom:1px solid #ccc">
                      <small><strong>Exam System's Date/Time:</strong><br><span id="curr"></span> <?php CurrentDateTime("curr");?></small>
                    </div>

                    </div>
                </div>

              </div>
          </div>
        </nav> 
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

	if (isset($_POST['btnLogout']))
	{
		unset($_SESSION['user']['student_exam_id']);
		unset($_SESSION['user']['exam']);
		echo "<script>window.location='" . $MAIN_URL ."logout.html';</script>";
	}
}
?>
<style>
@media (min-width: 768px)
{
#page-wrapper {
	    margin-left: 250px !important;
}
.sidebar, .navbar-header
{
	width:250px !important;
}
}
</style>
<form id="frm_logout" method="post">
	<input type="hidden" name="btnLogout">
</form>
<script>
$("a#btnLogout").click(function(e) {
  $("#frm_logout").submit();
});
</script>
<?php
} 
?>