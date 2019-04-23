<?php
session_start();
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>";  
$student_id = FilterNumber($_POST['student_id']);
if (!isset($student_id))
		echo "<script>window.location='list.html';</script>";

if (isset($_POST['btnBack']))
		echo "<script>window.location='list.html';</script>";


if (isset($_POST['btnDelete']))		//delete
{
	$student_course_id 	= FilterNumber($_POST['student_course_id']);
	$randomValue = FilterString($_POST['rand']);
	if ($randomValue == $_SESSION['rand']['list_course'])
	{
		$strDeleteCourse = "DELETE FROM exam_student_course WHERE student_course_id = '$student_course_id';";
		$db->query($strDeleteCourse);
	}
		
}

if (isset($_POST['btnAddStudentCourse']))
{
	$course_id = FilterNumber($_POST['course_id']);
	$join_date = FilterDateTime($_POST['startdate']);

	$strID = "SELECT student_course_id FROM exam_student_course ORDER BY student_course_id DESC LIMIT 0,1";
	$queryID = $db->query($strID);
	$rowID = $db->fetch_object($queryID);
	$student_course_id = $rowID->student_course_id + 1;

	$strINSERT = "INSERT INTO exam_student_course (student_course_id, student_id, course_id, join_date)
	VALUES ('$student_course_id', '$student_id', '$course_id', '$join_date');";
	$queryInsert = $db->query($strINSERT);
	
	unset($queryID, $queryInsert,$strID, $rowID, $strINSERT);
}

if (isset($_POST['btnAddCourseDialog']))
{
	?>
<div class="modal" role="dialog" aria-labelledby="AddStudentCourse" id="ModalAddStudentCourse">
  <div class="modal-dialog" role="document" style="max-width:400px;">
    <div class="modal-content">
      <form id="form1" name="form1" method="post" class="form-horizontal">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="AddStudentCourse"><i class="fa fa-plus"></i> <strong>Add Enrolled Course </strong></h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label class="col-md-3">Course</label>
            <div class="col-md-9">
                <select class="form-control" name="course_id" id="course_id">
									<?php
                  $strCourse = "SELECT * FROM exam_course ORDER BY course_name";
                  $query_course = $db->query($strCourse);
                  while ($row_course = $db->fetch_object($query_course))
                  {
                  ?>
                    <option value="<?php echo $row_course->course_id;?>"><?php echo $row_course->course_name;?></option>
                  <?php
                  }
                  $db->free($query_course);
                  unset($row_course, $query_course, $strCourse);
                  ?>
                </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-md-3">Join Date</label>
            <div class="form-inline col-md-9">
                <div id="startdate" class="input-group">
                    <input name="startdate" type="text" class="form-control" id="startdate" maxlength="10" readonly data-format="yyyy-MM-dd hh:mm:ss">
                    <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar" data-date-icon="glyphicon glyphicon-calendar" data-time-icon="glyphicon glyphicon-time"></i>
                    </span>
                </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <input type="hidden" name="rand" value="<?php echo $randomValue;?>">
          <input type="hidden" name="student_id" value="<?php echo $student_id;?>">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" name="btnAddStudentCourse"><span class="fa fa-plus"></span> Add Enrolled Course</button>
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
				$('#ModalAddStudentCourse').modal('show');  
			});
			</script>
	<?php
}

if (isset($_POST['btnEditStudentCourse']))
{
	$student_course_id = FilterNumber($_POST['student_course_id']);
	$course_id = FilterNumber($_POST['course_id']);
	$join_date = FilterDateTime($_POST['startdate']);

	$strUpdateCourse = "UPDATE exam_student_course SET
	course_id = '$course_id',
	join_date = '$join_date'
	WHERE student_course_id = '$student_course_id';";

	$db->query($strUpdateCourse);
	unset($strUpdateCourse);
}

if (isset($_POST['btnEditDialog']))
{
	$student_course_id = FilterNumber($_POST['student_course_id']);
	
	$strSelectCourse = "SELECT * FROM exam_student_course WHERE student_course_id = '$student_course_id';";
	$query_student_course = $db->query($strSelectCourse);
	$row_student_course = $db->fetch_object($query_student_course);
?>
<div class="modal" role="dialog" aria-labelledby="EditStudentCourse" id="ModalEditStudentCourse">
  <div class="modal-dialog" role="document" style="max-width:400px;">
    <div class="modal-content">
      <form id="form1" name="form1" method="post" class="form-horizontal">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="EditStudentCourse"><i class="fa fa-edit"></i> <strong>Edit Enrolled Course </strong></h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label class="col-md-3">Course</label>
            <div class="col-md-9">
                <select class="form-control" name="course_id" id="course_id">
									<?php
                  $strCourse = "SELECT * FROM exam_course ORDER BY course_name";
                  $query_course = $db->query($strCourse);
                  while ($row_course = $db->fetch_object($query_course))
                  {
                  ?>
                    <option value="<?php echo $row_course->course_id;?>"<?php if ($row_student_course->course_id == $row_course->course_id) echo " selected=\"selected\"";?>><?php echo $row_course->course_name;?></option>
                  <?php
                  }
                  $db->free($query_course);
                  unset($row_course, $query_course, $strCourse);
                  ?>
                </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-md-3">Join Date</label>
            <div class="form-inline col-md-9">
                <div id="startdate" class="input-group">
                    <input name="startdate" type="text" class="form-control" id="startdate" maxlength="10" readonly data-format="yyyy-MM-dd hh:mm:ss" value="<?php echo $row_student_course->join_date;?>">
                    <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar" data-date-icon="glyphicon glyphicon-calendar" data-time-icon="glyphicon glyphicon-time"></i>
                    </span>
                </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <input type="hidden" name="rand" value="<?php echo $randomValue;?>">
          <input type="hidden" name="student_id" value="<?php echo $student_id;?>">
          <input type="hidden" name="student_course_id" value="<?php echo $student_course_id;?>">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" name="btnEditStudentCourse"><span class="fa fa-edit"></span> Edit Enrolled Course</button>
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
				$('#ModalEditStudentCourse').modal('show');  
			});
			</script>
	<?php
}

$rand = RandomValue(20);
$_SESSION['rand']['list_course']=$rand;

?>
<div class="page-header">
  <div class="row">
  	<div class="col-md-6">
      <h3 style="margin-top:0px; margin-bottom:0px;"><span class="fa fa-list"></span> Enrolled Course</h3>
    </div>
  	<div class="col-md-6">
          <?php
          $strStudent = "SELECT * FROM exam_student WHERE student_id = '$student_id';";
          $query_student = $db->query($strStudent);
          $row_student = $db->fetch_object($query_student);
          echo "<strong>$row_student->name</strong><br>$row_student->address";
          $db->free($query_student);
          unset($row_student, $query_student, $strStudent);
          ?>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading"><strong>List of Enrolled Course</strong></div>
      <div class="panel-body">
      	<div class="row">
        	<div class="col-md-12">
          <form method="post" name="add_<?php echo $row->student_id;?>" class="pull-right">
            <button title="Add Course Enroll" class="btn btn-primary add" type="submit" id="btnAddCourse2" name="btnAddCourse2"  data-id="<?php echo $student_id;?>" data-button="btnAddCourseDialog" data-class="btn btn-primary" data-input-name="student_id" data-target="#add_modal" data-toggle="modal" data-url=""><span class="fa fa-plus"></span> Add Course Enroll</button>
            <input type="hidden" name="rand" value="<?php echo $rand;?>">
          </form>
<?php
confirm2("Enrolled Course","Do you want to add another Enrolled Course ?","add","add_modal",$rand,2);
?>

          <form method="post" class="form-inline pull-right">
            <button class="btn btn-warning" name="btnBack"><i class="fa fa-refresh"></i> Back to Student List</button>&nbsp;
          </form> 

        </div>
      </div>
<?php
$strSELECT = "
SELECT
 exam_student_course.student_course_id,
 exam_student_course.student_id,
 exam_student_course.course_id,
 date_format(exam_student_course.join_date,'%a, %D %b %Y, %h:%i:%s %p') as join_date,
 exam_course.course_name
  FROM exam_student_course exam_student_course
       INNER JOIN exam_course exam_course
          ON (exam_student_course.course_id = exam_course.course_id)
	WHERE student_id = '$student_id'
	ORDER BY student_id, course_id, join_date
	;";

$query_result= $db->query($strSELECT);
?>
        <div class="dataTable_wrapper">
          <table class="table" id="list_course">
            <thead>
              <tr>
                <th>SN</th>
                <th>Course Name</th>
                <th>Joined Date/Time</th>
                <th>&nbsp;</th>
              </tr>
            </thead>
            <tbody>
<?php
while ($row = $db->fetch_object($query_result))
{
	unset($disabled);
	$i++;
?>
              <tr>
                <td><?php echo $i;?></td>
                <td><?php echo $row->course_name;?></td>
                <td><?php echo $row->join_date;?></td>
                <td style="min-width:80px; max-width:105px;">
                  <form method="post" name="edit_<?php echo $row->student_course_id;?>" style="float:left; margin:0px;">
                    <button title="Edit" class="btn btn-info btn-circle btn-sm edit" type="submit" id="btnEdit2" name="btnEdit2" data-id="<?php echo $row->student_course_id;?>"  data-input-name="student_course_id" data-button="btnEditDialog" data-id2="<?php echo $row->student_id;?>"  data-input-name2="student_id" data-class="btn btn-primary" data-target="#edit_modal" data-toggle="modal" data-url="" ><span class="glyphicon glyphicon-pencil icon-white"></span></button>&nbsp;
                    <input type="hidden" name="student_id" value="<?php echo $student_id;?>">
                    <input type="hidden" name="rand" value="<?php echo $rand;?>">
                  </form>

                  <form method="post" name="delete_<?php echo $row->student_id;?>" style="float:left; margin:0px;">
                    <button title="Delete" class="btn btn-danger btn-circle btn-sm delete" type="submit" id="btnDelete2" name="btnDelete2"  data-id="<?php echo $row->student_course_id;?>" data-button="btnDelete" data-class="btn btn-danger" data-input-name="student_course_id" data-id2="<?php echo $row->student_id;?>"  data-input-name2="student_id" data-target="#delete_modal" data-toggle="modal" data-url=""><span class="glyphicon glyphicon-trash"></span></button>
                    <input type="hidden" name="student_id" value="<?php echo $student_id;?>">
                    <input type="hidden" name="rand" value="<?php echo $rand;?>">
                  </form>

                </td>
              </tr>
<?php } ?>
<?php 
confirm3("Enrolled Course","Do you want to change Enrolled Course ?","edit","edit_modal",$rand,1);
confirm3("Student List","Do you want to delete this student detail? <br><br><font 
color=red>Note: Deleted information can not be restore.</font>","delete","delete_modal",$rand,4);
?>

          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $MAIN_URL;?>bootstrap/addon/datetimepicker/bootstrap-datetimepicker.css">
<script type="text/javascript" src="<?php echo $MAIN_URL;?>bootstrap/addon/datetimepicker/bootstrap-datetimepicker.js"></script>
<script src="<?php echo $MAIN_URL;?>bootstrap/addon/datetimepicker/date.js"></script>
<script src="<?php echo $MAIN_URL;?>bootstrap/addon/datetimepicker/respond.js"></script>