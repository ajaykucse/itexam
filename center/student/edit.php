<?php
session_start();
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER'])
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
		$student_name = addslashes(trim($_POST['student_name']));
		$student_address = addslashes(trim($_POST['student_address']));
		$parent_name = addslashes(trim($_POST['parent_name']));
		$date_of_birth = FilterDateTime(trim($_POST['startdate']));
		$email = FilterEmail(trim($_POST['email']));
		$phone = addslashes(trim($_POST['phone']));
		$center_id = FilterNumber(trim($_POST['center_id']));

		$isError=FALSE;

		if (strlen($student_name) == 0) 
		{
			$error[] = "Please enter Student name.";
			$isError = TRUE;
		}

		if (strlen($student_address) == 0) 
		{
			$error[] = "Please enter Student address.";
			$isError = TRUE;
		}

		if (strlen($parent_name) == 0) 
		{
			$error[] = "Please enter Student Parent name.";
			$isError = TRUE;
		}

		if ($isError == FALSE)
		{
			//Update Data
			if ($date_of_birth == NULL)
				$strUpdate = "
				UPDATE exam_student SET
				name = '$student_name',
				address = '$student_address',
				date_of_birth = NULL,
				parent_name = '$parent_name',
				contact_email = '$email',
				contact_phone = '$phone',
				center_id = '$center_id'
				WHERE student_id = '$student_id';";
			else
				$strUpdate = "
				UPDATE exam_student SET
				name = '$student_name',
				address = '$student_address',
				date_of_birth = '$date_of_birth',
				parent_name = '$parent_name',
				contact_email = '$email',
				contact_phone = '$phone',
				center_id = '$center_id'
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
                <label class="col-md-3">Student Name</label>
                <div class="col-md-9">
                  <input name="student_name" type="text" id="student_name" value="<?php echo $row_student->name;?>" size="50" placeholder="Student Name" class="form-control" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-3">Student Address</label>
                <div class="col-md-9">
                  <input name="student_address" type="text" id="student_address" value="<?php echo $row_student->address;?>" size="50" placeholder="Student Address" class="form-control" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-3">Parent Name</label>
                <div class="col-md-9">
                  <input name="parent_name" type="text" id="parent_name" value="<?php echo $row_student->parent_name;?>" size="50" placeholder="Parent Name" class="form-control" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-3">Date of Birth</label>
                <div class="form-inline col-md-3">
                    <div id="startdate" class="input-group">
                        <input name="startdate" type="text" class="form-control" readonly id="startdate" maxlength="10" data-format="yyyy-MM-dd"<?php echo " value=\"$row_student->date_of_birth\"";?>>
                        <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar" data-date-icon="glyphicon glyphicon-calendar" data-time-icon="glyphicon glyphicon-time"></i>
                        </span>
                    </div>
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-3">Phone</label>
                <div class="col-md-3">
                  <input name="phone" type="text" id="phone" value="<?php echo $row_student->contact_phone;?>" size="30" placeholder="Phone" class="form-control" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-3">Email Address</label>
                <div class="col-md-5">
                  <input name="email" type="text" id="email" value="<?php echo $row_student->contact_email;?>" size="50" placeholder="Email Address" class="form-control" />
                </div>
              </div>

<div class="form-group">
                <label class="col-md-3">Center</label>
                <div class="col-md-5">
                  <select name="center_id" class="form-control">
                    <option value="0">Choose one...</option>
                  <?php
                    $strCENTER = "SELECT center_id, center_name FROM exam_center ORDER BY center_name";
                    $query_center = $db->query($strCENTER);
                    while ($row_center = $db->fetch_object($query_center))
                    {
                      ?>
                      <option value="<?php echo $row_center->center_id;?>"<?php if ($row_center->center_id == $row_student->center_id) echo " selected=\"selected\"";?>><?php echo $row_center->center_name;?></option>
                    <?php
                    }
                    $db->free($query_center);
                    unset($row_center,$query_center, $strCENTER);
                  ?>
                  </select>
                </div>
              </div>

              <div class="col-md-9 col-md-offset-3">
                <button type="submit" class="btn btn-primary" name="btnSubmit"><span class="glyphicon glyphicon-floppy-disk"></span> Edit Student</button>
                <button type="submit" class="btn btn-warning" name="btnCancel"><span class="glyphicon glyphicon-repeat"></span> Student List</button>
                <input type="hidden" value="<?php echo $student_id;?>" name="student_id" />
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $URL;?>bootstrap/addon/datetimepicker/bootstrap-datetimepicker.css">
<script type="text/javascript" src="<?php echo $URL;?>bootstrap/addon/datetimepicker/bootstrap-datetimepicker.js"></script>
<script src="<?php echo $URL;?>bootstrap/addon/datetimepicker/date.js"></script>
<script src="<?php echo $URL;?>bootstrap/addon/datetimepicker/respond.js"></script>
<?php
}
else
	echo "<script>window.location='list.html';</script>";
?>