<?php

if (isset($_POST['btnLogin']))
{
	$randomValue = ($_POST['rand']);
	if ($_SESSION['user']['login']== $randomValue)
	{
		$username = addslashes($_POST['user']);
		$password = addslashes($_POST['password']);
		$time = time();
		$query = " SELECT * FROM exam_user WHERE UserName='$username'
		UNION
		SELECT exam_student.student_id as user_id,
			 exam_student.reg_no as UserName,
       exam_exam_student.txtPassword as Password,
       exam_student.name as FullName,
			 'S' as user_type,
			 'Y' as active,
			 exam_exam_student.active_date as LastLogin,
       exam_exam_student.center_id
  FROM (exam_exam_student exam_exam_student
        INNER JOIN exam_student exam_student
           ON (exam_exam_student.student_id = exam_student.student_id))
       INNER JOIN exam_exam exam_exam
          ON (exam_exam_student.exam_id = exam_exam.exam_id)
			WHERE exam_student.reg_no = '$username'
		";

		$query_result=$db->query($query);
		$no_of_rows = $db->num_rows($query_result);
		$row= $db->fetch_object($query_result);
		$crypt_password = pacrypt ($password, $row->Password);

		if($row->Password == $crypt_password)
			$isAuthenticated=TRUE;
		else
			$isAuthenticated=FALSE;
	
		if ($no_of_rows == 0 || $isAuthenticated == FALSE)
		{
			unset ($_SESSION['ONLINE-EXAM-SIMULATOR']);
			unset($_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER']);
			unset($_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER']);
			unset($_SESSION['ONLINE-EXAM-SIMULATOR-STUDENT']);
			$isError = TRUE;
			$txtError = "Username/Password not matched.";
		}
		if ($isAuthenticated)
		{

			$user_id = $row->user_id;
			$username = $row->UserName;
			$fullname = $row->FullName;
			$Status = $row->active;
			$user_type = $row->user_type;
			$center_id = $row->center_id;
			$LastLogin = $row->LastLogin;
			$Status = $row->active;

			if ($user_type == "S")
			{
				if ($LastLogin == NULL) 
				{
					$txtTime = time();
					$LastLogin2 = $txtTime + (60*60*24);
					$strLast = $LastLogin2;

					unset ($_SESSION['ONLINE-EXAM-SIMULATOR']);
					unset ($_SESSION['user']);
					unset($_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER']);
					unset($_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER']);
					unset($_SESSION['ONLINE-EXAM-SIMULATOR-STUDENT']);
					$isError = TRUE;
					$Status=NULL;
					notify($software,"<font color=\"red\">Your assigned exam has not been schedule yet.</font>", NULL , FALSE,5000);
				}
				else
				{
					$strLast = strtotime($LastLogin);
				}
				$LastLogin = $strLast;

				if ($LastLogin > time())
				{
					unset ($_SESSION['ONLINE-EXAM-SIMULATOR']);
					unset ($_SESSION['user']);
					unset($_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER']);
					unset($_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER']);
					unset($_SESSION['ONLINE-EXAM-SIMULATOR-STUDENT']);
					$Status=NULL;
					$isError = TRUE;
					notify($software,"<font color=\"red\">Your assigned exam has not been schedule yet.</font>", NULL , FALSE,5000);
				}

			}

			if ($Status != "Y")	//if not active a/c
			{
				unset ($_SESSION['ONLINE-EXAM-SIMULATOR']);
				unset ($_SESSION['user']);
				unset($_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER']);
				unset($_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER']);
				unset($_SESSION['ONLINE-EXAM-SIMULATOR-STUDENT']);
				$isError = TRUE;
				$txtError = "Your account is not active. <br>Please contact to System Administrator.";
			}
			else if ($Status == "Y")
			{
				session_regenerate_id();
				if ($user_type == "S")
					$_SESSION['user']['student_id'] = $user_id;
				else
					$_SESSION['user']['user_id'] = $user_id;
				$_SESSION['user']['username'] = $username;
				$_SESSION['user']['fullname'] = $fullname;
				$_SESSION['user']['user_type'] = $user_type;
				$_SESSION['user']['center_id'] = $center_id;
				$_SESSION['expire'] = time() + $session_time ;
				
					if ($LastLogin < 1)
					{
							$_SESSION['FirstTime'] = TRUE;
							$txtError = "Please change your password.<br>Administrator Enforced.";
							$isError = TRUE;
							?>
							<script type="text/javascript">
              $(window).load(function(ev) {
                $('#changepw').modal('show');  
              });
              </script>
                <?php
						}
						else
						{
							if ($user_type == "A")
							{
								$_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER']= true;
								create_session($db);			
								notify($software,"<b>Welcome to $software</b>", $URL ."mainuser/dashboard.html", FALSE,2000);
							}
							else if ($user_type == "C")
							{
								$_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER']= true;
								create_session($db);			
								notify($software,"<b>Welcome to $software</b>", $URL ."center/dashboard.html", FALSE,2000);
							}
							else if ($user_type == "S")
							{
								if ($isError == FALSE)
								{
									// CHECK FOR ASSOCIATED EXAM, IF EXAM EXIST, FORWARD TO PAGE OTHERWISE LOGOUT
									
									$strExam = "
SELECT exam_exam_status.*
  FROM exam_exam_student exam_exam_student
       INNER JOIN exam_exam_status exam_exam_status
          ON     (exam_exam_student.exam_id = exam_exam_status.exam_id)
             AND (exam_exam_student.center_id = exam_exam_status.center_id)
					WHERE exam_exam_student.student_id = '$user_id';";

									$query_exam = $db->query($strExam);
									$no_of_exam = $db->num_rows($query_exam);
									if ($no_of_exam > 0)
									{
										$row_Exam = $db->fetch_object($query_exam);
										$exam_id = $row_Exam->exam_id;
										$center_id = $row_Exam->center_id;
										$_SESSION['user']['exam_id'] = $exam_id;
										$db->free($query_exam);

									$strResultClose = "
										SELECT * FROM exam_student_exam WHERE student_id ='$user_id' AND exam_id = '$exam_id' AND center_id = '$center_id';";

										$query_close = $db->query($strResultClose);
										$no_of_close = $db->num_rows($query_close);
										$row_close = $db->fetch_object($query_close);
										if ($row_close->is_closed=='Y')
										{
											unset($_SESSION['ONLINE-EXAM-SIMULATOR-STUDENT']);
											notify($software,"<b><font color=red>You have been already appeared in exam.</font></b><br><br><font color=blue>Please contact to Administration.</font>",$URL ."start.html", TRUE,5000);
											include_once "footer.inc.php";
											die();
										}
										$db->free($query_close);
										unset($row_close, $no_of_close, $query_close, $strResultClose);

										$_SESSION['ONLINE-EXAM-SIMULATOR-STUDENT']= true;
										create_session($db);			
										notify($software,"<b>Welcome to $software</b>", $URL ."student/student.html", FALSE,2000);
									}
									else
									{
										unset($_SESSION['ONLINE-EXAM-SIMULATOR-STUDENT']);
										notify($software,"<b><font color=red>No exam has been assigned to you.</font></b><br><font color=blue>Please login later. <br> OR <br>contact to Administration.</font>",$URL ."start.html", TRUE,5000);
									}
								}
							}
							include_once "footer.inc.php";
							die();
						}
			}
		}
		else
		{
			notify($software,"<font color=\"red\"><br>Username/Password mis-matched.<br><br></font>", NULL, false,10000);
		}
	}
}
else
{
	unset($_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER']);
	unset($_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER']);
	unset($_SESSION['ONLINE-EXAM-SIMULATOR-STUDENT']);
}
$rand = RandomValue(10);
$_SESSION['user']['login']=$rand;
?>
<link href="animated.css" rel="stylesheet" type="text/css">

<div class="col-md-4 col-sm-6 col-md-offset-4 col-sm-offset-3 col-xs-10 col-xs-offset-1" style="margin-top:10%;" >
	<div class="animated bounceInDown" align="center">
		<h3><?php echo $software;?></h3>
  </div>
	<div class="col-xs-12 animated flipInY">
  <div class="panel panel-primary login">
    <div class="panel-heading">
      <h1 class="panel-title "><i class="fa fa-user"></i> &nbsp;Login</h1>
		</div>
<style>
.input-group-addon
{
	 min-width:40px;
}
</style>
    <div class="panel-body">
      <form role="form" method="post">
          <fieldset>
              <div class="form-group input-group">
                <span class="input-group-addon" id="sizing-addon"><span class="fa fa-user"></span></span>
                   <input name="user" type="text" id="user" placeholder="Username" title="Username" class="form-control">
              </div>
              <div class="form-group input-group">
                <span class="input-group-addon" id="sizing-addon"><span class="fa fa-key"></span></span>
                  <input class="form-control" placeholder="Password" name="password" type="password" value="">
              </div>
              <input type="hidden" name="rand" value="<?php echo $rand;?>">
              <button class="btn btn-success center-block active" name="btnLogin"> <span class="fa fa-sign-in"></span> Login</button>
          </fieldset>
      </form>        
    </div>
  </div>
</div>
</div>

<link href="../animated.css" rel="stylesheet" type="text/css">
<style>
.login
{
	background: rgba(153, 200, 250, 0.1); 
}
</style>


<style>
.center {
    margin-top:50px;   
}

.modal-header {
    padding-bottom: 5px;
}

.modal-footer .btn-group button {
    height:40px;
    border-top-left-radius : 0;
    border-top-right-radius : 0;
    border: none;
}
    
.modal-footer .btn-group:last-child > button {
    border-right: 0;
}
#page-wrapper
{
	background:#f9f9f9;
}
.img-circle
{
	height:90px;
	width:90px;
	padding:10px;
	border: 1px solid #ffffff;
	box-shadow: rgba(0, 0, 0, 0.1) 0px 9px 9px 9px;
	background: rgba(153, 200, 250, 0.1);
}
</style>