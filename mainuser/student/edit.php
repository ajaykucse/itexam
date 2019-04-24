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

$student_id = FilterNumber($_POST['student_id']);
if (!isset($_POST['student_id']))
{
	echo "<script>window.location='list.html';</script>";
}

if (!isset($student_id))
		echo "<script>window.location='list.html';</script>";

	
	if (isset($_POST['btnSubmit']))
	{
		$error = array();
		$it_serial = addslashes(trim($_POST['it_serial']));
		$reg_no = addslashes(trim($_POST['reg_no']));
		$student_name = addslashes(trim($_POST['student_name']));

		$isError=FALSE;

		if (strlen($it_serial) == 0) 
		{
			$error[] = "Please enter IT Serial Number.";
			$isError = TRUE;
		}
		else
		{
			$str_it_serial = "SELECT it_serial FROM exam_student WHERE it_serial = '$it_serial'  and student_id <> '$student_id';";
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
			$str_reg_no = "SELECT reg_no FROM exam_student WHERE reg_no = '$reg_no' and student_id <> '$student_id';";
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
			//Update Data

				$strUpdate = "
				UPDATE exam_student SET
				it_serial = '$it_serial',
				reg_no = '$reg_no',
				name = '$student_name'
				WHERE student_id = '$student_id';";

			$query_result = $db->query($strUpdate);
			
			if ($query_result)
			{
				//notify("title","message text","URL", noclose=true/false,time);
				notify("Student List","Student details has been changed.","list.html",TRUE,5000);
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

$strSELECT = "SELECT * FROM exam_student WHERE student_id = '$student_id';";
$query_student = $db->query($strSELECT);
$no_of_student = $db->num_rows($query_student);
if ($no_of_student > 0)
{
	$row_student = $db->fetch_object($query_student);

?>
<h3 class="page-header"><i class="fa fa-graduation-cap"></i> Edit Student </h3>
<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading">Edit Student</div>
      <div class="panel-body">
        <div class="row">
          <form id="form1" name="form1" method="post" class="form-horizontal">
            <div class="col-md-12">

              <div class="form-group">
                <label class="col-md-3">IT Serial No.</label>
                <div class="col-md-2">
                  <input name="it_serial" type="text" id="it_serial" value="<?php echo $row_student->it_serial;?>" size="15" placeholder="IT Serial Number" class="form-control" />                
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-3">Registration Number</label>
                <div class="col-md-2">
                  <input name="reg_no" type="text" id="reg_no" value="<?php echo $row_student->reg_no;?>" size="15" placeholder="Registration Number" class="form-control" />                
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-3">Student Name</label>
                <div class="col-md-9">
                  <input name="student_name" type="text" id="student_name" value="<?php echo $row_student->name;?>" size="50" placeholder="Student Name" class="form-control" />
                </div>
              </div>

              <div class="col-md-9 col-md-offset-3">
                <button type="submit" class="btn btn-primary" name="btnSubmit"><span class="fa fa-save"></span> Edit Student</button>
                <button type="submit" class="btn btn-warning" name="btnCancel"><span class="fa fa-undo"></span> Student List</button>
                <input type="hidden" value="<?php echo $student_id;?>" name="student_id" />
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
}
else
	echo "<script>window.location='list.html';</script>";
?>