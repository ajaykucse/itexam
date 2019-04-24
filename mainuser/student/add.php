<?php
@session_start();
$isError=FALSE;
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>";  
?>
<?php
	if (isset($_POST['btnCancel']))
	{
		$_SESSION['editmode'] = "";
		echo "<script>window.location='list.html';</script>";
	}
	
	if (isset($_POST['btnSubmit']))
	{
		$error = array();
		$student_name = addslashes(trim($_POST['student_name']));
		$reg_no = addslashes(trim($_POST['reg_no']));
		$it_serial = addslashes(trim($_POST['it_serial']));

		$isError=FALSE;

		if (strlen($it_serial) == 0) 
		{
			$error[] = "Please enter IT Serial Number.";
			$isError = TRUE;
		}
		else
		{
			$str_it_serial = "SELECT it_serial FROM exam_student WHERE it_serial = '$it_serial';";
			$query_it_serial = $db->query($str_it_serial);
			$no_of_it_serial = $db->num_rows($query_it_serial);
			if ($no_of_it_serial > 0)
			{
				$error[] = "IT Serial Number is already Exist. Please Enter another IT Serial Number";
				$isError = TRUE;
			}
		}

		if (strlen($reg_no) == 0) 
		{
			$error[] = "Please enter Student Registration Number.";
			$isError = TRUE;
		}
		else
		{
			$str_reg_no = "SELECT reg_no FROM exam_student WHERE reg_no = '$reg_no';";
			$query_reg_no = $db->query($str_reg_no);
			$no_of_reg_no = $db->num_rows($query_reg_no);
			if ($no_of_reg_no > 0)
			{
				$error[] = "Student Registration Number is already Exist. Please Enter another Registration Number";
				$isError = TRUE;
			}
		}

		if (strlen($student_name) == 0) 
		{
			$error[] = "Please enter Student name.";
			$isError = TRUE;
		}

		if ($isError == FALSE)
		{
			$str =  "SELECT `student_id` from exam_student ORDER BY `student_id` DESC LIMIT 0,1";
			$row = $db->fetch_array_assoc($db->query($str));
			
			$student_id = $row['student_id'] +1;
			//Inserting New Data
			$strInsert = "INSERT INTO exam_student (student_id, it_serial, reg_no, name) VALUES ('$student_id', '$it_serial', '$reg_no','$student_name')";
			$query_result = $db->query($strInsert);
			
			if ($query_result)
			{
				//notify("title","message text","URL", noclose=true/false,time);
				notify("Student List","New student has been added.","list.html",TRUE,5000);
				$db->commit();
				include_once "footer.inc.php";
				exit();
			}
			else
			{
				$isError = TRUE;
			}
		}
	}
?>
<?php 
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
?>
<h3 class="page-header"><i class="fa fa-graduation-cap"></i> New Student </h3>
<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading">New Student</div>
      <div class="panel-body">
      	<div class="row">
          <form id="form1" name="form1" method="post" class="form-horizontal">
            <div class="col-md-12">

              <div class="form-group">
                <label class="col-md-3">IT Serial No.</label>
                <div class="col-md-2">
                  <input name="it_serial" type="text" id="it_serial" value="<?php if (isset($it_serial)) echo $it_serial;?>" size="15" placeholder="IT Serial Number" class="form-control" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-3">Registration Number</label>
                <div class="col-md-2">
                  <input name="reg_no" type="text" id="reg_no" value="<?php if (isset($reg_no)) echo $reg_no;?>" size="15" placeholder="Registration Number" class="form-control" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-3">Student Name</label>
                <div class="col-md-9">
                  <input name="student_name" type="text" id="student_name" value="<?php if (isset($student_name)) echo $student_name;?>" size="50" placeholder="Student Name" class="form-control" />
                </div>
              </div>

              <div class="col-md-9 col-md-offset-3">
                <button type="submit" class="btn btn-primary" name="btnSubmit"><span class="glyphicon glyphicon-plus"></span> Add New Student</button>
                <button type="submit" class="btn btn-warning" name="btnCancel"><span class="glyphicon glyphicon-repeat"></span> Student List</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
