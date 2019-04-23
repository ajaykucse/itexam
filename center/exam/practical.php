<?php
session_start();
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if ( (!$_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER']))

	echo "<script>window.location='" .$MAIN_URL."start.html';</script>";  

if (!isset($_POST['exam_id']))
	echo "<script>window.location='list.html';</script>";  

if ($_POST['rand'] != $_SESSION['center_exam_list']['rand'])
	echo "<script>window.location='list.html';</script>";  

if (isset($_POST['btnBackToExam']))
	echo "<script>window.location='list.html';</script>";  

$rand = $_SESSION['center_exam_list']['rand'];
$exam_id = FilterNumber($_POST['exam_id']);

if (isset($_POST['btnPracticalSave']))
{
	$random2 = FilterString($_POST['rand2']);
	if ($random2 == $_SESSION['center_exam_list']['rand2'])
	{
		$practical = $_POST['practical'];
		$no_of_student = count($practical);
		if ($no_of_student > 0)
		{

			$str_max_practical = "
			SELECT exam_exam_type.practical_mark
				FROM exam_exam exam_exam
						 INNER JOIN exam_exam_type exam_exam_type
								ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id)
			 WHERE exam_exam.exam_id = '$exam_id';";
			 $query_practical = $db->query($str_max_practical);
			 $row_practical = $db->fetch_object($query_practical);
			 $max_practical_value = $row_practical->practical_mark;
			 $db->free($query_practical);
			 unset($query_practical, $row_practical, $str_max_practical);

			foreach ($practical as $KEY => $value)
			{
				$practical_mark = FilterNumber($value);
				++$i;
				
				if ($max_practical_value < $practical_mark)
				{
					$isError = TRUE;
					$error[] = "SN: $i";	
				}
				if (!$isError)
				{
					$sql_insert = "UPDATE exam_student_exam SET practical_mark = '$practical_mark' WHERE exam_id = '$exam_id' AND student_id = '$KEY'  AND center_id = '$center_id';";
					$db->query($sql_insert);
				}
			}
		}
	}
}
if ($isError)
{
	$CountERROR = count($error);
	$text = "<b><font color=red>Practical is more than Maximum Number for:</font></b> <ul>";
	for($i=0;$i<$CountERROR;$i++)
	{
		$text .= "<li>".$error[$i] . "</li>";
	}
	$text .= "</ul>";
	notify("<font color=red><b>Error</b></font>", $text,NULL,TRUE,0);
}		// if error

?>
<div class="page-header">
  <div class="row">
  	<div class="col-md-9">
      <h3 style="margin-top:0px; margin-bottom:0px;"><span class="fa fa-pencil"></span> Practical Marks of Students</h3>
    </div>
    <div class="col-md-3 pull-right">    
      <form method="post" class="pull-right form-inline">
        <button type="submit" name="btnBackToExam" class="btn btn-warning"><span class="fa fa-undo"></span> 
          Back to Assigned Exam List
        </button>
      </form>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-lg-12">
    <div class="dataTable_wrapper" id="exam_list">
    <h4>Exam Code:
    
    </h4>
    	<form method="post" class="form-horizontal">
        <table class="table  table-striped table-bordered table-hover tooltip-demo" id="student_list">
          <thead>
            <tr>
              <th width="5%">SN</th>
              <th width="15%">Reg. No</th>
              <th>Student Name</th>
              <th width="15%">Exam Code</th>
              <th width="15%">MCQ Number</th>
              <th width="15%">Practical Number</th>
            </tr>
          </thead>
          <tbody>
<?php

$rand2 = RandomValue(20);
$_SESSION['center_exam_list']['rand2'] = $rand2;

$sql = "

SELECT exam_exam.exam_code,
       exam_center.center_id,
       exam_center.center_name,
       exam_student.reg_no,
       exam_student.name,
       exam_student_exam.mcq_mark,
       exam_student_exam.practical_mark,
       exam_student_exam.exam_id,
       exam_student_exam.student_id,
       exam_exam_type.practical_mark AS full_practical_mark
  FROM (((exam_student_exam exam_student_exam
          CROSS JOIN exam_exam exam_exam
             ON (exam_student_exam.exam_id = exam_exam.exam_id))
         INNER JOIN exam_student exam_student
            ON (exam_student_exam.student_id = exam_student.student_id))
        INNER JOIN exam_center exam_center
           ON (exam_student_exam.center_id = exam_center.center_id))
       INNER JOIN exam_exam_type exam_exam_type
          ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id)
	WHERE (exam_student_exam.center_id = '$center_id' AND exam_student_exam.exam_id = '$exam_id');
";

$query = $db->query($sql);
while ($row = $db->fetch_object($query))
{
?>
            <tr>
              <td><?php echo ++$SN;?></td>
              <td width="15%"><?php echo $row->reg_no;?></td>
              <td><?php echo $row->name;?>
              <?php
// Checking Absent of User
$str_abs = "SELECT * from exam_student_answer WHERE exam_id = '$exam_id' and student_id = '$row->student_id' AND center_id = '$row->center_id'";
$query_abs = $db->query($str_abs);
$no_of_abs = $db->num_rows($query_abs);
$db->free($query_abs);
unset($str_abs,$query_abs);
if ($no_of_abs < 1) echo "<div class=\"pull-right\"><label class=\"label label-default\">Absent</label></div>";

              ?>
              
              </td>
              <td width="15%"><?php echo $row->exam_code;
		$strExamCode = "SELECT * FROM exam_exam_center WHERE exam_id = '$row->exam_id';";
		$query_exam_code = $db->query($strExamCode);
		$no_of_exam_code = $db->num_rows($query_exam_code);

		if ($no_of_exam_code > 1) 
			$MultipleCenter = TRUE;
		else 
			$MultipleCenter = FALSE;

		if ($MultipleCenter)
		{
			$strExamCode2 = "SELECT exam_code from exam_exam_center WHERE exam_id = '$row->exam_id' AND center_id = '$center_id' AND exam_code is NOT NULL";
			$query_exam_code2 = $db->query($strExamCode2);
			$no_of_exam_code2 = $db->num_rows($query_exam_code2);
			if ($no_of_exam_code2 > 0) 
			{
				$row_exam_code2 = $db->fetch_object($query_exam_code2);
				if ($row_exam_code2 != NULL)
				echo "<br>&nbsp;&nbsp;&nbsp;- <i><small><span title=\"Center Exam Code\">$row_exam_code2->exam_code</span></small></i>";
			}
		}
							
							?></td>
              <td width="15%"><?php echo $row->mcq_mark;?></td>
              <td width="15%"><input name="practical[<?php echo $row->student_id;?>]" type="text" class="form-control practical-mark" size="5" maxlength="2" style="width:50px; text-align:center" value="<?php echo $row->practical_mark;?>"></td>
            </tr>
<?php
$full_practical_mark = $row->full_practical_mark;
}
?>
          </tbody>
        </table>
        <div class="col-xs-12" align="center" style="margin-top:15px;">
          <button type="submit" class="btn btn-success" name="btnPracticalSave"><i class="fa fa-save"></i> Save Practical Number</button>
          <input type="hidden" name="rand" value="<?php echo $rand;?>">
          <input type="hidden" name="rand2" value="<?php echo $rand2;?>">
          <input type="hidden" name="exam_id" value="<?php echo $exam_id;?>">
        </div>
      </form>
    </div>
  </div>
</div>