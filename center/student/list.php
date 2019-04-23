<?php
session_start();
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER'])
	echo "<script>window.location='" .$MAIN_URL."start.html';</script>";  
?>
<?php

	// Student Login Create
	if (isset($_POST['btnCreateStudentLogin']))
	{
		if (!($_SESSION['user']['submit']) == TRUE)
		{
			$student_id = FilterNumber($_POST['student_id']);
	
			$error = array();
			$username = addslashes(trim($_POST['username']));
			$password= addslashes(trim($_POST['password']));
			$change_pw = FilterNumber($_POST['change_pw']);
	
			$isError=FALSE;
	
			if (strlen($username) == 0) 
			{
				$error[] = "Please enter username.";
				$isError = TRUE;
			}
			else
			{
				$strSELECT = "select UserName from exam_user where UserName = '$username'";
				$querySELECT = $db->query($strSELECT);
				$no_of_user = $db->num_rows($querySELECT);
		
				if ($no_of_user > 0)
				{
					$isError = TRUE;
					$error[] = "User already exist!";	
				}
				$db->free($querySELECT);
				unset($no_of_user,$querySELECT,$strSELECT);
			}
			if (strlen($password) == 0) 
			{
				$error[] = "Please enter password.";
				$isError = TRUE;
			}
	
			if ($isError == FALSE)
			{
				$str =  "SELECT `user_id` from exam_user ORDER BY `user_id` DESC LIMIT 0,1";
				$row = $db->fetch_array_assoc($db->query($str));
				$user_id = $row['user_id'] +1;
	
				$str =  "SELECT * from exam_student WHERE student_id = '$student_id';";
				$row = $db->fetch_object($db->query($str));
				$fullname = $row->name;
				$center_id = $row->center_id;
				
				unset($row,$str);
	
				//Inserting New Data
				$pass = pacrypt($password,"");
				
				if ($change_pw > 0) $LastLogin = 0;
				else $LastLogin = time();
				
				$strInsert = "INSERT INTO exam_user (user_id, UserName, Password, fullname, user_type, center_id, active, LastLogin) VALUES ('$user_id','$username', '$pass', '$fullname', 'S', '$center_id', 'Y','$LastLogin')";
	
				$strInsertStudentUser = "INSERT INTO exam_student_user (student_id, user_id) VALUES ('$student_id', '$user_id')";
				
				$query_result = $db->query($strInsert);
				$query_result2 = $db->query($strInsertStudentUser);
	
				if ($query_result && $query_result2)
				{
					//notify("title","message text","URL", noclose=true/false,time);
					notify("Student Login","Student Login Created.",NULL,TRUE,5000);
					$db->commit();
					$_SESSION['user']['submit'] = TRUE;
				}
				else
				{
					$isError = TRUE;
				}
			}
	
			if ($isError)
			{
				$CountERROR = count($error);
				$text = "<ul>";
				for($i=0;$i<$CountERROR;$i++)
				{
					$text .= "<li>".$error[$i] . "</li>";
				}
				$text .= "</ul>";
				notify("<font color=red><b>E r r o r</b></font>", $text,NULL,TRUE,0);
			}
		}
	}

	// Student Login Modal
	if (isset($_POST['btnCreateUser']))
	{
		unset($_SESSION['user']['submit']);
		$randomValue = ($_POST['rand']);
		$student_id = FilterNumber($_POST['student_id']);
		if ($_SESSION['rand']['list_student'] == $randomValue)
		{
			?>

<div class="modal fade" role="dialog" aria-labelledby="CreateStudentLogin" id="ModalCreateStudentLogin">
  <div class="modal-dialog" role="document" style="max-width:400px;">
    <div class="modal-content">
      <form id="form1" name="form1" method="post" class="form-horizontal">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="CreateStudentLogin"><i class="fa fa-user-plus"></i> <strong>Create Student Login</strong></h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label class="col-md-3">Username</label>
            <div class="col-md-9">
              <input name="username" type="text" id="username" value="<?php if (isset($username)) echo $username;?>" size="50" placeholder="username" class="form-control" />
            </div>
          </div>
  
          <div class="form-group">
            <label class="col-md-3">Password</label>
            <div class="col-md-9">
              <input name="password" type="password" id="password" value="<?php if (isset($password)) echo $password;?>" size="50" placeholder="Password" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-md-9 col-md-offset-3"><input type="checkbox" name="change_pw" value="1"> Change Password in Next Login</label>
          </div>

        </div>
        <div class="modal-footer">
          <input type="hidden" name="rand" value="<?php echo $randomValue;?>">
          <input type="hidden" name="student_id" value="<?php echo $student_id;?>">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" name="btnCreateStudentLogin"><span class="fa fa-user-plus"></span> Create Student Login</button>
        </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<style>
label
{
	font-weight:normal !important;
}
</style>

			<script type="text/javascript">
			$(window).load(function(ev) {
				$('#ModalCreateStudentLogin').modal('show');  
			});
			</script>

	<?php
		}
	}

	//FOR DELETING 
	if(isset($_POST['btnDelete']))
	{
		$randomValue = ($_POST['rand']);
		if ($_SESSION['rand']['list_student'] == $randomValue)
		{
			$student_id = FilterNumber($_POST['student_id']);
			$strSelectStudent = "SELECT student_id FROM exam_student_user WHERE student_id = '$student_id';";
			$num_student = $db->num_rows($db->query($strSelectStudent));
			if ($num_student == 0)
			{
				$strDeletePage="DELETE FROM exam_student WHERE student_id=$student_id";
				$db->begin();
				$query = $db->query($strDeletePage);
				if ($query)
				{
					notify("Student List","Student details has been deleted.",NULL,TRUE,5000);
					$db->commit();
				}
				else
				{
				notify("Student List","<font color=red>Can not delete student details. <br>Child Records may exists.</font>",NULL,TRUE,5000);
					$db->rollback();
				}
			}
			else
				notify("Student List","<font color=red>Can not delete Student Details. <br>Student Login ID may exists!!!</font>",NULL,TRUE,10000);
			
		}
	}
	?>

<div class="page-header">
  <div class="row">
  	<div class="col-md-9">
      <h3 style="margin-top:0px; margin-bottom:0px;"><span class="fa fa-graduation-cap"></span> Student List</h3>
    </div>
    <div class="col-md-3 pull-right">    
      <form method="post" class="pull-right form-inline" action="add.html">
        <button type="submit" name="btnAddNew" id="btnAddNew" class="btn btn-primary">Add New Student  <span class="glyphicon glyphicon-plus"></span></button>
      </form>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-lg-12">
<?php
$SQL = "
			SELECT exam_student.student_id,
       exam_student.name,
       exam_student.address,
 			 date_format(exam_student.date_of_birth,'%D %b %Y') as date_of_birth,
       exam_student.parent_name,
       exam_student.contact_email,
       exam_student.contact_phone,
       exam_student.center_id,
       exam_center.center_name,
       exam_student_user.user_id
  FROM (exam_student exam_student
        LEFT OUTER JOIN exam_center exam_center
           ON (exam_student.center_id = exam_center.center_id))
       LEFT OUTER JOIN exam_student_user exam_student_user
          ON (exam_student_user.student_id = exam_student.student_id)
	ORDER BY exam_student.student_id";

$query_result = $db->query($SQL);
$rand = RandomValue(20);
$_SESSION['rand']['list_student']=$rand;
?>
        <div class="dataTable_wrapper">
          <table class="table  table-striped table-bordered table-hover tooltip-demo" id="list_student" style="font-size:85%;">
            <thead>
              <tr>
                <th>SN</th>
                <th>Student Name</th>
                <th>Address</th>
                <th>Parent Name</th>
                <th>Date of Birth</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Center</th>
                <th>&nbsp;</th>
              </tr>
            </thead>
            <tbody>
<?php
while ($row = $db->fetch_object($query_result))
{
	unset($disabled, $txtUser);
	$strUserName = "SELECT UserName from exam_user WHERE user_id = '$row->user_id'";
	$query_user = $db->query($strUserName);
	$no_of_username = $db->num_rows($query_user);
	if ($no_of_username > 0)
	{
			$row_user = $db->fetch_object($query_user);
		$txtUser = "Username: $row_user->UserName";
	}
	$db->free($query_user);
	unset($strUserName, $query_user, $row_user);

?>
              <tr>
                <td style="width:15px"><?php echo $row->student_id;?></td>
                <td><?php echo $row->name ;
								if (!is_null($row->user_id)) 
								{
									echo "<div class=\"pull-right tooltip-demo\"><label class=\"badge\" data-toggle-tooltip=\"tooltip\" data-placement=\"top\" title=\"$txtUser \"><span class=\"fa fa-user\"></span></label></div>";
									$disabled = " disabled ";
								}
								else
									$disabled = " title=\"Create Student Login\" ";
								

								?></td>
                <td><?php echo 	$row->address;?></td>
                <td><?php echo 	$row->parent_name;?></td>
                <td><?php echo 	$row->date_of_birth;?></td>
                <td><?php echo 	$row->contact_phone;?></td>
                <td><?php echo 	$row->contact_email;?></td>
                <td><?php echo $row->center_name;?></td>
                <td style="width:175px;">
                    <form method="post" name="edit_<?php echo $row->student_id;?>" style="float:left; margin:0px;">
                      <button title="Edit" class="btn btn-info btn-circle btn-sm edit" type="submit" id="btnEdit2" name="btnEdit2" data-id="<?php echo $row->student_id;?>" data-button="btnEdit2" data-class="btn btn-primary" data-input-name="student_id" data-target="#edit_modal" data-toggle="modal" data-url="edit.html" data-toggle-tooltip="tooltip" data-placement="top"><span class="glyphicon glyphicon-pencil icon-white"></span></button>&nbsp;
                      <input type="hidden" name="rand" value="<?php echo $rand;?>">
                    </form>

                    <form method="post" name="create_user_<?php echo $row->student_id;?>" style="float:left; margin:0px;">
                      <button class="btn btn-success btn-circle btn-sm create_user" type="submit" id="btnCreateUser2" name="btnCreateUser2"  data-id="<?php echo $row->student_id;?>" data-button="btnCreateUser" data-class="btn btn-success" data-input-name="student_id" data-target="#create_user_modal" data-toggle="modal" data-url=""<?php echo $disabled;?> data-toggle-tooltip="tooltip" data-placement="top"><span class="fa fa-user-plus"></span></button>&nbsp;
                      <input type="hidden" name="rand" value="<?php echo $rand;?>">
                    </form>

                    <form method="post" name="list_course_<?php echo $row->student_id;?>" style="float:left; margin:0px;">
                      <button title="Enrolled Course" class="btn btn-primary btn-circle btn-sm list_course" type="submit" id="btnListCourse2" name="btnListCourse2"  data-id="<?php echo $row->student_id;?>" data-button="btnListCourse" data-class="btn btn-primary" data-input-name="student_id" data-target="#list_course_modal" data-toggle="modal" data-url="course.html" data-toggle-tooltip="tooltip" data-placement="top"><span class="fa fa-list"></span></button>&nbsp;
                      <input type="hidden" name="rand" value="<?php echo $rand;?>">
                    </form>

                    <form method="post" name="list_exam_<?php echo $row->student_id;?>" style="float:left; margin:0px;">
                      <button title="List of Given Exam" class="btn btn-warning btn-circle btn-sm list_exam" type="submit" id="btnListExam2" name="btnListExam2"  data-id="<?php echo $row->student_id;?>" data-button="btnListExam" data-class="btn btn-success" data-input-name="student_id" data-target="#list_exam_modal" data-toggle="modal" data-url="exam.html" data-toggle-tooltip="tooltip" data-placement="top"><span class="fa fa-bar-chart"></span></button>&nbsp;
                      <input type="hidden" name="rand" value="<?php echo $rand;?>">
                    </form>

                    <form method="post" name="delete_<?php echo $row->student_id;?>" style="float:left; margin:0px;">
                      <button title="Delete" class="btn btn-danger btn-circle btn-sm delete" type="submit" id="btnDelete2" name="btnDelete2"  data-id="<?php echo $row->student_id;?>" data-button="btnDelete" data-class="btn btn-danger" data-input-name="student_id" data-target="#delete_modal" data-toggle="modal" data-url=""  data-toggle-tooltip="tooltip" data-placement="top"><span class="glyphicon glyphicon-trash"></span></button>
                      <input type="hidden" name="rand" value="<?php echo $rand;?>">
                    </form>

                </td>
              </tr>
<?php  }
confirm2("Student List","Do you want to change student details?","edit","edit_modal",$rand,1);
confirm2("Student List","Do you want to create student Login?","create_user","create_user_modal",$rand,2);
confirm2("Student List","Do you want to view Enrolled Course?","list_course","list_course_modal",$rand,3);
confirm2("Student List","Do you want to view given exam?","list_exam","list_exam_modal",$rand,4);
confirm2("Student List","Do you want to delete this student detail? <br><br><font 
color=red>Note: Deleted information can not be restore.</font>","delete","delete_modal",$rand,5);
?>
            </tbody>
          </table>
    </div>
  </div>
</div>
<?php
dataTable("list_student");
?>
<script>
// tooltip demo
	$('.tooltip-demo').tooltip({
			selector: "[data-toggle-tooltip=tooltip]",
			container: "body"
	});
	$('.tooltip-demo').click(function(e) {
    	e.preventDefault();
  });
</script>