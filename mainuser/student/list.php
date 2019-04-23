<?php
@session_start();
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>";  
?>
<?php

	// Add.html
	if (isset($_POST['btnAddNew']))
		echo "<script>window.location='add.html';</script>";  

	//FOR DELETING 
	if(isset($_POST['btnDelete']))
	{
		$randomValue = ($_POST['rand']);
		if ($_SESSION['rand']['list_student'] == $randomValue)
		{
			$student_id = ($_POST['student_id']);

			if (count($student_id) > 0)
			{
				$db->begin();
	
				foreach ($student_id  as $KEY => $VALUE)
				{
					$KEY = FilterNumber($KEY);
	
					$strSelectStudent = "SELECT student_id FROM exam_exam_student WHERE student_id = '$KEY';";
					$num_student = $db->num_rows($db->query($strSelectStudent));
	
					$strSelectStudent = "SELECT student_id FROM exam_student_answer WHERE student_id = '$KEY';";
					$num_student_answer = $db->num_rows($db->query($strSelectStudent));
	
					$strSelectStudent = "SELECT student_id FROM exam_student_question WHERE student_id = '$KEY';";
					$num_student_question = $db->num_rows($db->query($strSelectStudent));
	
					$strSelectStudent = "SELECT student_id FROM exam_student_exam WHERE student_id = '$KEY';";
					$num_student_exam = $db->num_rows($db->query($strSelectStudent));
		
					if ($num_student == 0 && $num_student_answer == 0 && $num_student_question == 0 && $num_student_exam == 0)
					{
						$strDeletePage="DELETE FROM exam_student WHERE student_id='$KEY'";
						$query = $db->query($strDeletePage);
					}
					else
					{
						$isError = TRUE;
						$strStudent = "SELECT reg_no, name from exam_student WHERE 	student_id = '$KEY'";
						$row = $db->fetch_object($db->query($strStudent));
						$student_name = $row->name;
						$reg_no = $row->reg_no;
						$error[] = "<font color=red>'$reg_no'</font> is already exist in another table.";
						unset($row, $student_name, $reg_no, $strStudent);
					}
				}
				if (!$isError)
				{
					notify("Student List","Student details has been deleted.",NULL,TRUE,5000);
					$db->commit();
				}
	
				if ($isError)
				{
					$db->rollback();
					$CountERROR = count($error);
					$text = "<ul>";
					for($i=0;$i<$CountERROR;$i++)
					{
						$text .= "<li>".$error[$i] . "</li>";
					}
					$text .= "</ul>";
					notify("<b><font color=red>INFORMATION</font></b>", $text,NULL,TRUE,0);
				}		// if error
			}
		}
	}
	?>
<?php
$rand = RandomValue(20);
$_SESSION['rand']['list_student']=$rand;

?>
<div class="page-header">
  <div class="row">
  	<div class="col-md-6">
      <h3 style="margin-top:0px; margin-bottom:0px;"><span class="fa fa-graduation-cap"></span> Student List</h3>
    </div>
    <div class="col-md-6 pull-right">
<?php
if (!$student_change)
  $disabled_add = " disabled=\"disabled\"";
?>

      <form method="post" class="pull-right">
        <button type="submit" name="btnAddNew" id="btnAddNew" class="btn btn-primary"<?php echo $disabled_add;unset($disabled_add);?>><span class="fa fa-plus-circle"></span> Add New Student</button>
        <input type="hidden" name="rand" value="<?php echo $rand;?>">
      </form>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-lg-12">
<?php
$SQL = "
SELECT 
			 exam_student.student_id,
			 exam_student.it_serial,
			 exam_student.reg_no,
       exam_student.name,
       exam_exam.exam_code,
       exam_exam_type.exam_type,
       exam_center.center_name,
       exam_center.center_address
  FROM (((exam_exam exam_exam
          LEFT OUTER JOIN exam_exam_type exam_exam_type
             ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id))
         LEFT OUTER JOIN exam_exam_student exam_exam_student
            ON (exam_exam_student.exam_id = exam_exam.exam_id))
        RIGHT OUTER JOIN exam_student exam_student
           ON (exam_exam_student.student_id = exam_student.student_id))
       LEFT OUTER JOIN exam_center exam_center
          ON (exam_exam_student.center_id = exam_center.center_id)
	ORDER BY exam_student.student_id
";

$query_result = $db->query($SQL);
?>
    <div class="dataTable_wrapper">
      <form class="form-horizontal" method="post" id="frmStudentList">
        <table class="table table-bordered table-hover table-striped"  id="list_student">
          <thead>
            <tr>
                  <th>SN</th>
                  <th>IT Serial No.</th>
                  <th>Reg. No.</th>
                  <th>Student Name</th>
                  <th>Assigned Exam Code</th>
                  <th>Exam Center</th>
                  <th style="width:80px"><div class="tooltip-demo"><button title="Select All" class="btn btn-primary btn-sm btn-circle" type="button" name="select-all" id="select-all" data-toggle-tooltip="tooltip" data-placement="top"><i class="fa fa-check-square-o"></i></button>&nbsp;<button class="btn btn-warning btn-circle btn-sm" type="button" name="deselect-all" id="deselect-all" title="De-select all" data-toggle-tooltip="tooltip" data-placement="top"><i class="fa fa-square-o"></i></button>
                 <button title="Delete Student" class="btn btn-danger btn-circle btn-sm delete-student" type="submit" id="btnDelete2" name="btnDelete2" data-button="btnDelete" data-class="btn btn-danger" data-target="#delete-student-modal" data-toggle="modal" data-toggle-tooltip="tooltip" data-placement="top"><span class="fa fa-trash"></span></button>
                 </div>
                  </th>
                  <th></th>
            </tr>
          </thead>
          <tbody>
      <?php
      $query_result2 = $db->query($SQL);
      while ($row = $db->fetch_object($query_result2))
      {
      ?>
            <tr>
                  <td style="width:15px"><?php echo $row->student_id;?></td>
                  <td><?php echo $row->it_serial;?></td>
                  <td><?php echo $row->reg_no;?></td>
                  <td><?php echo $row->name ;?></td>
                  <td><?php echo 	$row->exam_code;?></td>
                  <td><?php 	if ($row->center_name != NULL) echo  "$row->center_name, $row->center_address";?></td>
                  <td align="center"><?php if ($row->exam_code == NULL) 
      {
      ?>
      <input type="checkbox" name="student_id[<?php echo $row->student_id;?>]" value="1">
      <?php } ?>
      </td>
                  <td style="width:35px;" class="tooltip-demo">
                        <button title="Edit" class="btn btn-info btn-circle btn-sm edit" type="submit" id="btnEdit2" name="btnEdit2" data-id="<?php echo $row->student_id;?>" data-button="btnEdit" data-class="btn btn-info" data-input-name="student_id" data-target="#edit_modal" data-toggle="modal" data-url="edit.html" data-toggle-tooltip="tooltip" data-placement="top"><span class="fa fa-pencil icon-white"></span></button>&nbsp;
      
                  </td>
            </tr>
      <?php } ?>
          </tbody>
        </table>
        <input type="hidden" name="rand" value="<?php echo $rand;?>">
      </form>          
      <label><u><strong>NOTE:</strong></u> Only unassigned Student(s) can be deleted.</label>
    </div>
  </div>
</div>
<?php

if ($student_change)
{
      confirm2("Student List","Do you want to change student details?","edit","edit_modal",$rand,1);
      confirm2("Student List","Do you want to delete this student detail? <br><br><font 
      color=red>Note: Deleted information can not be restore.</font>","delete","delete_modal",$rand,5);

			FormSubmit("Student List","Do you want to delete this student detail? <br><br><font 
      color=red>Note: Deleted information can not be restore.</font>","delete-student","delete-student-modal","frmStudentList");
}
else
{
		no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","edit","edit_modal",$rand,1);	

		no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","delete","delete_modal",$rand,5);	

		no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","delete-student","delete-student-modal",$rand,6);	

}

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
<script>
$('#select-all').click(function(event) {   
	$(':checkbox').each(function() {
			this.checked = true;
	});
});
$('#deselect-all').click(function(event) {   
	$(':checkbox').each(function() {
			this.checked = false;
	});
});

</script>