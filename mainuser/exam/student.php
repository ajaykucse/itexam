<?php
@session_start();
error_reporting(0);
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>"; 

if ( (!isset($_POST['exam_id'])) && (!isset($_POST['center_id'])) )
	echo "<script>window.location='list.html';</script>"; 

$exam_id = FilterNumber($_POST['exam_id']);
$center_id = FilterNumber($_POST['center_id']);

if (isset($_POST['btnAddStudent']))		//Add student from Exam
{
	$random = FilterString($_POST['rand']);
	if ($random == $_SESSION['rand']['exam_student_selection'])
	{
		$student_id = $_POST['student_id'];
		if (count($student_id) > 0)
		{
			$db->begin();
			foreach ($student_id  as $KEY => $VALUE)
			{
        unset($isExisting);
				$KEY = FilterNumber($KEY);
				$txt_rand_pass =  RandomString(8);
				$randPass = base64_encode($txt_rand_pass);
				$txtPass = pacrypt($txt_rand_pass,"");
				$strStudentCheck = "SELECT * from exam_exam_student WHERE exam_id = '$exam_id' AND student_id = '$KEY' AND center_id = '$center_id';";

				$query_check = $db->query($strStudentCheck);
				$no_of_student = $db->num_rows($query_check);

        $isPass = isPassed($KEY,$exam_id,$db);
        
        if ($isPass)
        {
          $strStu = "SELECT * FROM exam_student WHERE student_id='$KEY'";
          $query_stu = $db->query($strStu);
          $row_stu = $db->fetch_object($query_stu);
          $PASS_STUDENT[] =  $txt_std_name . " (RegNo : " . $row_stu->reg_no . ") (IT Serial : $row_stu->it_serial)";
          $db->free($query_stu);
          unset($strStu,$query_stu,$row_stu);
          $isExisting = TRUE;
        }


				if ($no_of_student == 0)
				{
					$strInsStudent = "INSERT INTO exam_exam_student (student_id, exam_id, center_id, `password`, `txtPassword`, active_date) VALUES ('$KEY', '$exam_id', '$center_id', '$randPass', '$txtPass',NOW() );";
					if (!$isExisting) $query_insert = $db->query($strInsStudent);
          else $query_insert = TRUE;
          
				}
				else $query_insert = TRUE;
			}

      if ($isExisting)
      {
        $CountERROR = count($PASS_STUDENT);
        $textExisting ="<br><br>Following Student(s) have been already passed this type of exam: <br>";
        $textExisting .= "<ul>";
        for($i=0;$i<$CountERROR;$i++)
        {
          $textExisting .= "<li>".$PASS_STUDENT[$i] . "</li>";
        }
        $textExisting .= "</ul>";
      }
      
			if ($query_insert)
      {
				notify("Student Assigned","Successfully Assigned student to this Exam." .$textExisting,NULL,TRUE,5000);
				$db->commit();
      }
			else
				$db->rollback();
		} // no of student_id
	} // random value
}

if (isset($_POST['btnUnassignStudent']))		//Unassign student from Exam
{
	$random = FilterString($_POST['rand']);
	if ($random == $_SESSION['rand']['exam_student_selection'])
	{
		$student_id = $_POST['student_id'];
		if (count($student_id) > 0)
		{
			foreach ($student_id  as $KEY => $VALUE)
			{
				$KEY = FilterNumber($KEY);
				$strDeleteStudent = "DELETE FROM exam_exam_student WHERE student_id = '$KEY' AND center_id = '$center_id' AND exam_id = '$exam_id';";
				$query_delete = $db->query($strDeleteStudent);
			}
		}
	}
}

if (isset($_POST['btnUpdateStudent']))
{
	$random = FilterString($_POST['rand']);
	if ($random == $_SESSION['rand']['exam_student_selection'])
	{
		$include = $_POST['include'];
		$field = $_POST['field'];

		$show = $_POST['show'];
		if (count($include) > 0)
		{
			foreach ($include as $INCLUDE_KEY => $VALUE)
			{
				for ($i = 0; $i < count($field[$INCLUDE_KEY]); $i++)
				{
					if ($show == "HEADING" && $i != 0)
						$FF[$i][$INCLUDE_KEY]= addslashes($field[$INCLUDE_KEY][$i]);
				}
			}
		}

		$strStudentID="SELECT student_id FROM exam_student ORDER BY student_id DESC;";
		$querystd_id = $db->query($strStudentID);
		$rowStd_ID = $db->fetch_object($querystd_id);
		$LastStudentID = $rowStd_ID->student_id;
		$db->free($querystd_id);
		unset($rowStd_ID, $querystd_id, $strStudentID);

		unset($REGISTER_NUMBER);		

		$student_id = $LastStudentID;

			foreach ($FF as $KEY => $VALUE)
			{
				foreach ($VALUE as $field_value)
				{ 
					++$SN;
					if ($SN ==1)
          {
						$it_serial = $field_value;
						$IT_SERIAL[] = $it_serial;
          }
					if ($SN ==2)
          {
						$reg_number = $field_value;
						$REGISTER_NUMBER[] = $reg_number;
          }
          if ($SN==3)
          {
            $student_name=$field_value;
            $STUDENT_NAME[]=$student_name;
          }
				}


				if ( (strlen($it_serial) > 0) && (strlen($reg_number) > 0)) // NOT IT SERIAL AND BLANK REG_NUMBER 
				{

					$exam_code = $_SESSION["exam_code"];
					 

					$exam_typeSELECT = $db->query("SELECT exam_exam_type.exam_type FROM (exam_exam_type exam_exam_type  LEFT OUTER JOIN exam_exam exam_exam ON (exam_exam_type.exam_type_id=exam_exam.exam_type_id)) WHERE exam_exam.exam_code = '$exam_code';");

					$exam_type = $exam_typeSELECT->fetch_assoc();

					$no_of_student = $db->num_rows($exam_type);

					$result=($exam_type['exam_type']);
					$getresult = substr($result, 0,2);
				 	
				 	$serial = substr($it_serial,0,2);
					 

					$strSELECT = "SELECT it_serial from exam_student WHERE it_serial = '$it_serial' OR reg_no='$reg_number';";
					$no_of_std = $db->num_rows($db->query($strSELECT));

					if ($no_of_std == 0 && $getresult==$serial)
					{
						$student_id++;
						$strINSERT = "INSERT INTO exam_student (student_id, it_serial, reg_no, name) VALUES ('$student_id', '$it_serial', '$reg_number', '$student_name');";
						$db->begin();
						$query_student = $db->query($strINSERT) ;
					}
					else if ($no_of_std > 0 && $getresult==$serial)
					{
						$strINSERT = "UPDATE exam_student SET reg_no='$reg_number', name='$student_name' WHERE it_serial='$it_serial' AND reg_no='$reg_number';";
						$db->begin();
						$query_student = $db->query($strINSERT) ;
					}
					else 
						$query_student  = true;
				} // NOT BLANK REG_NUMBER
				unset($it_serial, $student_name, $SN, $reg_number);
			}

			$regno_count= count($REGISTER_NUMBER);
			
			foreach($REGISTER_NUMBER AS $k => $KEY)
			{
        $txt_it=$IT_SERIAL[$k];
        $txt_student_name = $STUDENT_NAME[$k];
        
				$strStudent = "SELECT student_id,name FROM exam_student WHERE reg_no = '$KEY' AND it_serial ='$txt_it' ;";

				$query_student_id = $db->query($strStudent);
        $no_of_existing_user = $db->num_rows($query_student_id);                
				$row_student_id = $db->fetch_object($query_student_id);
				$txt_student_id = $row_student_id->student_id;
				$txt_student_name = $row_student_id->name;
				$db->free($query_student_id);
				unset($strStudent,$query_student_id, $row_student_id);

				$strStudentExam = "SELECT * from exam_exam_student WHERE student_id = '$txt_student_id';";

				$query_student_exam = $db->query($strStudentExam);
				$no_of_student_exam = $db->num_rows($query_student_exam);
        
        $isPass = isPassed($txt_student_id,$exam_id,$db);
        
        if ($isPass)
        {
          $txt_std_name=$STUDENT_NAME[$k];
          $PASS_STUDENT[] =  $txt_std_name . " (RegNo : " . $KEY . ") (IT Serial : $txt_it)";
          $isExisting = TRUE;
        }

				if ($no_of_student_exam == 0)
				{
					$txt_rand_pass =  RandomString(8);
					$randPass = base64_encode($txt_rand_pass);
					$txtPass = pacrypt($txt_rand_pass,"");
	
					$strInsertStudent = "INSERT INTO exam_exam_student (student_id, exam_id, center_id, `password`, `txtPassword`, active_date) VALUES ('$txt_student_id', '$exam_id', '$center_id', '$randPass', '$txtPass', NOW());";
					if (!$isPass) $query_exam_student =  $db->query($strInsertStudent);
				}
				else
				{
					$query_exam_student  = true;
					$isExist = TRUE;
				}
			}
			
			if ($query_exam_student) $exam_exam_student = TRUE;
			else $exam_exam_student = FALSE;

      if ($isExisting)
      {
        $CountERROR = count($PASS_STUDENT);
        $textExisting ="<br><br>Following Student(s) have been already passed this type of exam: <br>";
        $textExisting .= "<ul>";
        for($i=0;$i<$CountERROR;$i++)
        {
          $textExisting .= "<li>".$PASS_STUDENT[$i] . "</li>";
        }
        $textExisting .= "</ul>";
      }


			if (!$isExist && $exam_student && $exam_exam_student )
			{
        
				notify("Student Add","Successfully added student to this Exam." .$textExisting,NULL,TRUE,5000);
				$db->commit();
			}
			else if ($isExist && $exam_student && $exam_exam_student )
			{
				$db->commit();
				notify("Student Add","Some student has been already assigned to Exam.<br><br>Unassigned Student successfully added to this exam.",NULL,TRUE,5000);
			}
			else
			{
				$db->rollback();
				notify("Error","There is some error to add student. <br>Please try it later.",NULL,TRUE,6000);
			}
      
	}
}

$rand = RandomValue(20);
$_SESSION['rand']['exam_student_selection'] = $rand;

$strExamCode = "SELECT exam_code FROM exam_exam WHERE exam_id = '$exam_id';";
$query = $db->query($strExamCode);
$row = $db->fetch_object($query);
$exam_code = $row->exam_code;
$db->free($query);
unset($strExamCode, $query, $row);

$strCenter = "SELECT center_name FROM exam_center WHERE center_id = '$center_id';";
$query = $db->query($strCenter);
$row = $db->fetch_object($query);
$center_name = $row->center_name;
$db->free($query);
unset($strCenter, $query, $row);


$excelUpload = TRUE;

?>
<div class="page-header">
  <div class="row">
  	<div class="col-md-9">
      <h3 style="margin-top:0px; margin-bottom:0px;"><i class="fa fa-graduation-cap"> </i> Student for Exam '<?php 

		
	$strExamCode = "SELECT * FROM exam_exam_center WHERE exam_id = '$exam_id';";
		$query_exam_code = $db->query($strExamCode);
		$no_of_exam_code = $db->num_rows($query_exam_code);

		if ($no_of_exam_code > 1){
			$MultipleCenter = TRUE;
		}
		else{
			$exam_code = $_SESSION["exam_code"] = $exam_code;
			$MultipleCenter = FALSE;
		}

		if ($MultipleCenter)
		{
			$strExamCode2 = "SELECT exam_code from exam_exam_center WHERE exam_id = '$exam_id' AND center_id = '$center_id' AND exam_code is NOT NULL";

			$query_exam_code2 = $db->query($strExamCode2);
			$no_of_exam_code2 = $db->num_rows($query_exam_code2);
			if ($no_of_exam_code2 > 0) 
			{
				$row_exam_code2 = $db->fetch_object($query_exam_code2);
				if ($row_exam_code2 != NULL){
					echo "<span title=\"Center Exam Code\">$row_exam_code2->exam_code</span>";
					$exam_code = $_SESSION["exam_code"] = $row_exam_code2->exam_code;
				}
				else{
					echo $exam_code;
					$exam_code = $_SESSION["exam_code"] = $exam_code;
				}
			}
			else{
				echo $exam_code;
				$exam_code = $_SESSION["exam_code"] = $exam_code;
			}
		}
		else{
			echo $exam_code;
			$exam_code = $_SESSION["exam_code"] = $exam_code;
		}
			
			?>' and Exam Center '<?php echo $center_name;?>'</h3>
    </div>
    <div class="col-md-3 pull-right">    
      <button class="btn btn-warning pull-right" id="btnBack" name="btnBack"><i class="fa fa-rotate-left"></i> Exam List</button>
      <script>
      $("#btnBack").click(function(e) {
        window.location='list.html';      
      });
      </script>
    </div>
  </div>
</div>

<div class="row">
	<div class="col-md-12">
		<form method="post" enctype="multipart/form-data" class="form-horizontal" id="upload">
      <fieldset>
      	<legend>Upload Excel File (Student Details)</legend>
      	<label class="col-md-3">Upload Excel File (*.xls)</label>
        <div class="col-md-7">
        <input type="file" accept="application/vnd.ms-excel" id="excel_file" name="excel_file" class="form-control">
        <input type="hidden" name="excel_upload">
        <strong><u>Note:</u> Excel File should have only one sheet and 3 columns<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(column 1 = IT Serial No; column 2 = Reg No. & column 3 = Student Name)</strong> </div>
        <div class="col-md-2">
        	<button class="btn btn-danger btn-sm" type="button" id="btnClean"><i class="fa fa-trash"></i> Clear File</button>
        </div>
      </fieldset>
    </form>
  </div>
  <form method="post">
    <div id="test"></div>
  </form>

  
<?php
$strAssignedStudent = "
SELECT exam_student.student_id,
       exam_student.it_serial,
       exam_student.reg_no,
       exam_student.name,
       exam_center.center_id,
       exam_center.center_name,
       exam_center.center_address,
       exam_exam.exam_id
  FROM ((exam_exam_student exam_exam_student
         LEFT OUTER JOIN exam_center exam_center
            ON (exam_exam_student.center_id = exam_center.center_id))
        LEFT OUTER JOIN exam_exam exam_exam
           ON (exam_exam_student.exam_id = exam_exam.exam_id))
       RIGHT OUTER JOIN exam_student exam_student
          ON (exam_exam_student.student_id = exam_student.student_id)
 	WHERE (exam_exam_student.exam_id = '$exam_id') 
	AND 
	(exam_center.center_id = '$center_id')
ORDER BY exam_exam.exam_id DESC, exam_center.center_id ASC, exam_student.student_id ASC;

";

$query_assigned_student = $db->query($strAssignedStudent);
$no_of_assigned_student = $db->num_rows($query_assigned_student);

if ($no_of_assigned_student > 0)
{
?>
  <div class="col-md-12">
    <form method="post" id="frmStudent">        
      <fieldset>
        <legend>Assigned Student</legend>
					<table class="table table-bordered" id="exam_student_list">
          	<tr>
            	<th width="5%">SN</th>
            	<th width="10%">IT Serial Number</th>
              <th width="10%">Registration Number</th>
              <th>Student Name</th>
              <th width="350">Exam Center</th>
              <th>
              <div class="tooltip-demo" style="width:180px;"><button title="Select All" class="btn btn-primary btn-sm btn-circle" type="button" name="select-all" id="select-all1"  data-toggle-tooltip="tooltip" data-placement="top"><i class="fa fa-check-square-o"></i></button>&nbsp;<button class="btn btn-warning btn-circle btn-sm" type="button" name="deselect-all" id="deselect-all1" title="De-select all"  data-toggle-tooltip="tooltip" data-placement="top"><i class="fa fa-square-o"></i></button>
                 </div>
</th>
            </tr>
<?php
unset($no_of_std);

while ($rowAssigned = $db->fetch_object($query_assigned_student))
{
?>
          	<tr>
            	<td><?php echo ++$SNO;?></td>
            	<td width="10%"><?php echo $rowAssigned->it_serial;?></td>
              <td width="10%"><?php echo $rowAssigned->reg_no;?></td>
              <td><?php echo $rowAssigned->name;?>
              <?php if ($rowAssigned->exam_id != NULL)
							{
								echo "<div class=\"pull-right\"><span class=\"label label-primary\">Assigned</span></div>";
							}
							?>
              </td>
              <td width="350"><?php if ($rowAssigned->center_name != NULL) echo "$rowAssigned->center_name, $rowAssigned->center_address";?></td>
              <td width="180">
<input class="checkbox1" type="checkbox" name="student_id[<?php echo $rowAssigned->student_id;?>]" value="1">
<script>
$('#select-all1').click(function(event) {   
	$('.checkbox1:checkbox').each(function() {
			this.checked = true;
	});
});
$('#deselect-all1').click(function(event) {   
	$('.checkbox1:checkbox').each(function() {
			this.checked = false;
	});
});
</script>
              </td>
            </tr>
<?php
}
$no_of_std++;
if ($no_of_std > 0)
{
?>
            <tr>
            	<td></td>
            	<td width="10%"></td>
            	<td width="10%"></td>
            	<td></td>
            	<td></td>
            	<td width="180">
                 <button title="Unassign Student" class="btn btn-danger btn-sm remove-student" type="submit" id="btnUnassignStudent2" name="btnUnassignStudent2" data-button="btnUnassignStudent" data-class="btn btn-danger" data-target="#delete-student-modal" data-toggle="modal" data-toggle-tooltip="tooltip" data-placement="top"><span class="fa fa-trash"></span> Unassign Student</button>

                <input type="hidden" name="rand" value="<?php echo $rand;?>">
                <input type="hidden" name="exam_id" value="<?php echo $exam_id;?>">
                <input type="hidden" name="center_id" value="<?php echo $center_id;?>">
              </td>
           	</tr>
<?php } ?>            
          </table>
      </fieldset>
    </form>
  </div>

<?php
} // no of assigned student


$strAssignedStudent = "
SELECT exam_student.student_id,
       exam_student.it_serial,
       exam_student.reg_no,
       exam_student.name,
       exam_center.center_id,
       exam_center.center_name,
       exam_center.center_address,
       exam_exam.exam_id
  FROM ((exam_exam_student exam_exam_student
         LEFT OUTER JOIN exam_center exam_center
            ON (exam_exam_student.center_id = exam_center.center_id))
        LEFT OUTER JOIN exam_exam exam_exam
           ON (exam_exam_student.exam_id = exam_exam.exam_id))
       RIGHT OUTER JOIN exam_student exam_student
          ON (exam_exam_student.student_id = exam_student.student_id)
 	WHERE (exam_exam_student.exam_id is NULL) 
	AND 
	(exam_center.center_id = '$center_id' OR exam_center.center_id is NULL)
ORDER BY exam_exam.exam_id DESC, exam_center.center_id ASC, exam_student.student_id ASC;

";

$query_assigned_student = $db->query($strAssignedStudent);
$no_of_assigned_student = $db->num_rows($query_assigned_student);

if ($no_of_assigned_student > 0)
{
?>
  <div class="col-md-12">
    <form method="post" id="frmStudentAssign">
      <fieldset>
        <legend>Not Assigned Student</legend>
					<table class="table table-bordered" id="exam_student_list">
          	<tr>
            	<th width="5%">SN</th>
            	<th width="10%">IT Serial Number</th>
              <th width="10%">Registration Number</th>
              <th>Student Name</th>
              <th>
              <div class="tooltip-demo" style="width:180px;"><button title="Select All" class="btn btn-primary btn-sm btn-circle" type="button" name="select-all" id="select-all2"  data-toggle-tooltip="tooltip" data-placement="top"><i class="fa fa-check-square-o"></i></button>&nbsp;<button class="btn btn-warning btn-circle btn-sm" type="button" name="deselect-all" id="deselect-all2" title="De-select all"  data-toggle-tooltip="tooltip" data-placement="top"><i class="fa fa-square-o"></i></button>
                 </div>
</th>
            </tr>
<?php
unset($no_of_std);

while ($rowAssigned = $db->fetch_object($query_assigned_student))
{
?>
          	<tr>
            	<td><?php echo ++$SNO;?></td>
            	<td width="10%"><?php echo $rowAssigned->it_serial;?></td>
              <td width="10%"><?php echo $rowAssigned->reg_no;?></td>
              <td><?php echo $rowAssigned->name;?>
              <?php if ($rowAssigned->exam_id != NULL)
							{
								echo "<div class=\"pull-right\"><span class=\"label label-primary\">Assigned</span></div>";
							}
							?>
              </td>
              <td width="180">
  <input class="checkbox2" type="checkbox" name="student_id[<?php echo $rowAssigned->student_id;?>]" value="1">
  <script>
$('#select-all2').click(function(event) {   
	$('.checkbox2:checkbox').each(function() {
			this.checked = true;
	});
});
$('#deselect-all2').click(function(event) {   
	$('.checkbox2:checkbox').each(function() {
			this.checked = false;
	});
});
</script>
              </td>
            </tr>
<?php
}
$no_of_std++;
if ($no_of_std > 0)
{
?>
            <tr>
            	<td></td>
            	<td width="10%"></td>
            	<td width="10%"></td>
            	<td></td>
            	<td width="180">
                <button title="Add Student" class="btn btn-success btn-sm add-student" type="submit" id="btnAddStudent2" name="btnAddStudent2" data-button="btnAddStudent" data-class="btn btn-success" data-target="#add-student-modal" data-toggle="modal" data-toggle-tooltip="tooltip" data-placement="top"><span class="fa fa-plus"></span> Assign Student</button>

                <input type="hidden" name="rand" value="<?php echo $rand;?>">
                <input type="hidden" name="exam_id" value="<?php echo $exam_id;?>">
                <input type="hidden" name="center_id" value="<?php echo $center_id;?>">
              </td>
           	</tr>
<?php } ?>            
          </table>
      </fieldset>
    </form>
  </div>

<?php
} // no of assigned student

// FormSubmit($title, $text,$buttonClass,$modalID, $frm)

FormSubmit("Student for Exam","Do You want <b>Unassigned selected Student</b> from this exam?","remove-student","delete-student-modal","frmStudent");

FormSubmit("Student for Exam","Do You want <b>Add selected Student</b> to this exam?","add-student","add-student-modal","frmStudentAssign");

?>
</div>
<script>
$(function(){

    $("#btnClean").click(function(e) {
      $("#excel_file").val("");
      $("div#test").html("");
    });

	// To prevent Browsers from opening the file when its dragged and dropped on to the page
	$(document).on('drop dragover', function (e) {
        e.preventDefault();
    }); 

	$('input[type=file]').on('change', fileUpload);

	// File uploader function

	function fileUpload(event){  
		$("div#test").html("<div align=center><span class=\"fa fa-spinner fa-5x fa-spin\"></span><br><h3>Uploading...</h3></div>");
		files = event.target.files;
		var data = new FormData();
		var error = 0;
		for (var i = 0; i < files.length; i++) {
  			var file = files[i];
		  		data.append('excel_file', file, file.name);
		  		data.append('task', "excel_upload");
		  		data.append('center_id', "<?php echo $center_id;?>");
		  		data.append('exam_id', "<?php echo $exam_id;?>");
	 	}
	 	if(!error)
		{
		 	var xhr = new XMLHttpRequest();
			xhr.onreadystatechange = function() {
				if (xhr.readyState === 4) {
					if (xhr.status === 200) {
						 document.getElementById("test").innerHTML=xhr.responseText;
					} 
				}
			};			
		 	xhr.open('POST', '<?php echo $MAIN_URL;?>ajax.html', true);
		 	xhr.send(data);
		}
	}
});
</script>
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
<?php
dataTable("exam_student_list");
?>
<?php
// Passed User on Same TYPE of EXAM
function isPassed($student_id, $exam_id, $db)
{
    $txtExamTypeSelect = "SELECT * FROM exam_exam WHERE exam_id='$exam_id';";
    $query_exam_type = $db->query($txtExamTypeSelect);
    $row_exam_type = $db->fetch_object($query_exam_type);
    $exam_type_id = $row_exam_type->exam_type_id;
    $db->free($query_exam_type);
    unset($txtExamTypeSelect, $query_exam_type, $row_exam_type);

    $txtSELECT = "
    
    SELECT exam_exam.exam_code,
           exam_student.student_id,
           exam_student.reg_no,
           exam_student.name,
           exam_student_exam.mcq_mark,
           exam_student_exam.practical_mark,
           exam_student_exam.exam_id,
           exam_student_exam.student_id,
           exam_student_exam.center_id,
           exam_exam_type.full_mark,
           exam_exam_type.pass_mark,
           exam_exam.exam_type_id
    FROM ((exam_exam    exam_exam
           INNER JOIN exam_exam_type exam_exam_type
              ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id))
          INNER JOIN exam_student_exam exam_student_exam
             ON (exam_student_exam.exam_id = exam_exam.exam_id))
         INNER JOIN exam_student exam_student
            ON (exam_student_exam.student_id = exam_student.student_id)
    WHERE exam_exam.exam_type_id = '$exam_type_id' AND exam_student_exam.student_id = '$student_id'
    ";
    $query_existing_pass = $db->query($txtSELECT);
    $no_of_pass = $db->num_rows($query_existing_pass);
    if ($no_of_pass > 0)
    {
      $row_existing_pass = $db->fetch_object($query_existing_pass);
      if ($row_existing_pass->mcq_mark >= $row_existing_pass->pass_mark) $isPass=TRUE;
      else $isPass=FALSE;
    }
    else
    $isPass=FALSE;
  return $isPass;
}
?>