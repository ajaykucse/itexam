<?php
@session_start();
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>"; 

if (!isset($_POST['exam_id']))
	echo "<script>window.location='list.html';</script>"; 

$exam_id = FilterNumber($_POST['exam_id']);
$center_id = FilterNumber($_POST['center_id']);

if (isset($_POST['btnFullResult']))
{
	$random = $_POST['rand'];
	if ($_SESSION['rand']['student_full_result'] == $random)
	{
		$student_id = FilterNumber($_POST['student_id']);
?>
<script>
$(document).ready(function(e) {
	$('#student_full_result_modal').modal('show');
	$('div#student_full_result_modal .modal-body').css('max-height', $(window).height() * 0.55);
	$('div#student_full_result_modal .modal-body').css('overflow-y', 'auto'); 

});
</script>
<?php 
$strStudentName = "SELECT * from exam_student WHERE student_id = '$student_id'";
$query_student_name = $db->query($strStudentName);
$row_student_name = $db->fetch_object($query_student_name);
$student_name = $row_student_name->name;
$student_it_serial = $row_student_name->it_serial;
$student_reg_no = $row_student_name->reg_no;
$db->free($query_student_name);
unset($strStudentName, $query_student_name, $row_student_name);

$strStudentExam = "SELECT * from exam_exam WHERE exam_id = '$exam_id'";
$query_student_exam = $db->query($strStudentExam);
$row_student_exam = $db->fetch_object($query_student_exam);
$exam_code = $row_student_exam->exam_code;

		$strExamCode = "SELECT * FROM exam_exam_center WHERE exam_id = '$exam_id';";
		$query_exam_code = $db->query($strExamCode);
		$no_of_exam_code = $db->num_rows($query_exam_code);

		if ($no_of_exam_code > 1) 
			$MultipleCenter = TRUE;
		else 
			$MultipleCenter = FALSE;

		if ($MultipleCenter)
		{
			$strExamCode2 = "SELECT exam_code from exam_exam_center WHERE exam_id = '$exam_id' AND center_id = '$center_id' AND exam_code is NOT NULL";

			$query_exam_code2 = $db->query($strExamCode2);
			$no_of_exam_code2 = $db->num_rows($query_exam_code2);
			if ($no_of_exam_code2 > 0) 
			{
				$row_exam_code2 = $db->fetch_object($query_exam_code2);
				if ($row_exam_code2 != NULL)
					$exam_code2 = "<span title=\"Center Exam Code\">$row_exam_code2->exam_code</span>";
				else
					$exam_code2 = $exam_code;
			}
			else
				$exam_code2 = $exam_code;
		}
		else
			$exam_code2 = $exam_code;

$db->free($query_student_exam);
unset($strStudentExam, $query_student_exam, $row_student_name);

$strTotalQuestion = "
SELECT exam_exam_type.total_question
  FROM exam_exam exam_exam
       INNER JOIN exam_exam_type exam_exam_type
          ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id)
WHERE exam_exam.exam_id = '$exam_id'";
$query_total_question = $db->query($strTotalQuestion);
$row_total_question = $db->fetch_object($query_total_question);
$total_question = $row_total_question->total_question;
$db->free($query_total_question);
unset($strTotalQuestion, $query_total_question, $row_total_question);


$strStudentCenter = "SELECT * from exam_center WHERE center_id = '$center_id'";
$query_student_center = $db->query($strStudentCenter);
$row_student_center = $db->fetch_object($query_student_center);
$center_name = $row_student_center->center_name;
$db->free($query_student_center);
unset($strStudentCenter, $row_student_center, $row_student_name);

?>

<div class="modal fade" id="student_full_result_modal" tabindex="-1" role="dialog" aria-labelledby="student_full_result_modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    	<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <br>
        <h3 class="modal-title">Attended Question / Answer </h3>
        <br>
      </div>
<?php
$strQuestionAnswer = "
SELECT exam_student_answer.choosen_answer,
       exam_student_answer.correct_answer,
       exam_student_question.question_id,
       exam_student_question.answer_order,
       exam_student_answer.student_id,
       exam_student_answer.is_mark
  FROM exam_student_question exam_student_question
       LEFT OUTER JOIN exam_student_answer exam_student_answer
          ON     (exam_student_question.exam_id = exam_student_answer.exam_id)
             AND (exam_student_question.student_id =
                     exam_student_answer.student_id)
             AND (exam_student_question.question_id =
                     exam_student_answer.question_id)
WHERE exam_student_question.exam_id = '$exam_id'
AND exam_student_question.student_id = '$student_id'
ORDER BY exam_student_question.exam_student_question_id
";

$query_question_answer = $db->query($strQuestionAnswer);
$no_of_question_answer = $db->num_rows($query_question_answer);
?>                  

        <div class="modal-body">
          <div class="row">
            <div class="col-xs-12" id="student-exam-detail">
              <div class="col-xs-12">
                <div class="row">
                  <div class="col-xs-6"><label class="col-xs-5">IT Serial No : </label>&nbsp;&nbsp; <?php echo $student_it_serial;?></div>
                  <div class="col-xs-6"><label class="col-xs-5">Registration No : </label>&nbsp;&nbsp; <?php echo $student_reg_no;?></div>
                  <div class="col-xs-6"><label class="col-xs-5">Student Name : </label>&nbsp;&nbsp; <?php echo $student_name;?></div>
                  <div class="col-xs-6"><label class="col-xs-5">Total Questions : </label> &nbsp;&nbsp;<?php echo $total_question;?></div>
                  <div class="col-xs-6"><label class="col-xs-5">Attend Questions : </label> &nbsp;&nbsp;<?php echo $no_of_question_answer;?></div>
                  <div class="col-xs-6"><label class="col-xs-5">Exam Code : </label> &nbsp;&nbsp;<?php echo "$exam_code2";?></div>
                  <div class="col-xs-6"><label class="col-xs-5">Exam Center : </label> &nbsp;&nbsp;<?php echo $center_name;?></div>
                </div>
              </div>
              <div class="col-xs-12" style="margin-top:15px; margin-bottom:15px;">
                <h4>Student Attended Question / Answer</h4>
              </div>
              <div class="col-xs-12">
<?php if ($no_of_question_answer > 0)
{
	?>
                <table width="100%" border="0" cellpadding="10" cellspacing="10" class="table table-responsive">

<?php
while ($row_question = $db->fetch_object($query_question_answer))
{

$strQuestion = "SELECT * FROM exam_question WHERE question_id = '$row_question->question_id'";
$query_question = $db->query($strQuestion);
$row_question_txt = $db->fetch_object($query_question);
?>
                  <tr>
                    <td width="60" valign="top"><strong><?php echo ++$i;?>.</strong></td>
                    <td valign="top">
          <?php
              echo "<strong>$row_question_txt->question</strong>";
							
              $strAnswer2 = "SELECT * FROM exam_answer WHERE question_id = '$row_question->question_id' AND is_correct='Y';";
          
              $query_answer2 = $db->query($strAnswer2);
              $no_of_correct_answer = $db->num_rows($query_answer2);
              $db->free($query_answer2);
              unset($strAnswer2,$query_answer2);

							$ans_order = split(",",$row_question->answer_order);

							$choosen_answer = $row_question->choosen_answer;
							$correct_answer = $row_question->correct_answer;
							$is_review = $row_question->is_mark;
							
							foreach($ans_order AS $ORDER)
							{
								$ii++;
								$strAnswer = "SELECT * FROM exam_answer WHERE question_id = '$row_question->question_id' AND answer_sn = '$ORDER';";

								$query_answer = $db->query($strAnswer);
								$row_answer = $db->fetch_object($query_answer);
								$ANS = $row_answer->answer_sn;
								$is_correct = $row_answer->is_correct;
								
								if ($ANS == 1) $answer_value = 1;
								else if ($ANS == 2) $answer_value = 2;
								else if ($ANS == 3) $answer_value = 4;
								else if ($ANS == 4) $answer_value = 8;
								else if ($ANS == 5) $answer_value = 16;
								else if ($ANS == 6) $answer_value = 32;
								else if ($ANS == 7) $answer_value = 64;
								else if ($ANS == 8) $answer_value = 128;
								else if ($ANS == 9) $answer_value = 256;
								else if ($ANS == 10) $answer_value = 512;

              unset($checked);
              if ($choosen_answer & $answer_value)
							{
								 $checked = " checked ";
								 $choosen = TRUE;
							}
							else $choosen = FALSE;
							
							if ($correct_answer & $answer_value) $correct = TRUE;
							else $correct = FALSE;
							
							if ($correct  && $choosen ) $color="1";
							else if (!$correct && $choosen)  $color="2";
							else if ($correct && !$choosen)  $color="1";
							else $color="";
							
              if ($no_of_correct_answer > 1) $multi = TRUE;
              else $multi= FALSE;
							
							if ($multi && $checked) $type ="<span class=\"fa fa-check-square-o\"></span> ";
							else if ($multi && !$checked) $type ="<span class=\"fa fa-square-o\"></span> ";
							else if (!$multi && $checked) $type ="<span class=\"fa fa-dot-circle-o\"></span> ";
							else if (!$multi && !$checked) $type ="<span class=\"fa fa-circle-o\"></span> ";
          
						?>
              <table width="100%" cellpadding="5" cellspacing="5" border="0">
                <tr>
                  <td width="25"><?php 
									if ($color=="1") echo "<font color=darkgreen>$type</font>";
									else if ($color=="2") echo "<font color=red>$type</font>";
									else echo $type;
									?></td>
                  <td class="no-margin"><?php 
										if ($color=="1") echo "<font color=darkgreen><b>$row_answer->answer</b></font>";
										else if ($color=="2") echo "<font color=red><i>$row_answer->answer</i></font>";
										else echo "$row_answer->answer";
										?></td>
                  </tr>
              </table>
          <?php		
							}
        ?>
                    </td>
                  </tr>

<?php
}
$db->free($query_question_answer);
unset($query_question_answer, $row_question, $strQuestionAnswer);
?>                  
                </table>
<?php } 
else
{
	echo "<font color=red>No question has been Attended</font>";
}
?>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
  </div>
</div>
<style>
.no-margin, .no-margin p
{
	margin:0px;
	padding:0px;
}
</style>
<?php		
	} //random value check
}

unset($_SESSION['rand']);
$rand = RandomValue(20);
$_SESSION['rand']['student_full_result']=$rand;

$strExamCode = "SELECT exam_code FROM exam_exam WHERE exam_id = '$exam_id';";
$query = $db->query($strExamCode);
$row = $db->fetch_object($query);
$exam_code = $row->exam_code;
$db->free($query);
unset($strExamCode, $query, $row);

?>
<div class="page-header">
  <div class="row">
  	<div class="col-md-9">
      <h3 style="margin-top:0px; margin-bottom:0px;"><i class="fa fa-clone"> </i> Result of Student(s) for Exam '<?php 

		$strExamCode = "SELECT * FROM exam_exam_center WHERE exam_id = '$exam_id';";

		$query_exam_code = $db->query($strExamCode);
		$no_of_exam_code = $db->num_rows($query_exam_code);

		if ($no_of_exam_code > 1) 
			$MultipleCenter = TRUE;
		else 
			$MultipleCenter = FALSE;

		if ($MultipleCenter)
		{
			$strExamCode2 = "SELECT exam_code from exam_exam_center WHERE exam_id = '$exam_id' AND center_id = '$center_id' AND exam_code is NOT NULL";

			$query_exam_code2 = $db->query($strExamCode2);
			$no_of_exam_code2 = $db->num_rows($query_exam_code2);
			if ($no_of_exam_code2 > 0) 
			{
				$row_exam_code2 = $db->fetch_object($query_exam_code2);
				if ($row_exam_code2 != NULL)
					echo "<span title=\"Center Exam Code\">$row_exam_code2->exam_code</span>";
				else
					echo $exam_code;				
			}
			else
				echo $exam_code;				
		}
		else
			echo $exam_code;				

			
			?>' </h3>
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
<?php
$strMarks = "
SELECT exam_exam_type.full_mark, exam_exam_type.pass_mark
  FROM exam_exam exam_exam
       INNER JOIN exam_exam_type exam_exam_type
          ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id)
 WHERE exam_exam.exam_id = '$exam_id'					
					";
$query_mark = $db->query($strMarks);
$row_mark = $db->fetch_object($query_mark);
$full_mark = $row_mark->full_mark;
$pass_mark = $row_mark->pass_mark;
$db->free($query_mark);
unset($row_mark, $query_mark, $strMarks);

/* 
$strResultStudent = "
SELECT exam_student.student_id,
       exam_student.reg_no,
       exam_student.name,
       exam_center.center_id,
       exam_center.center_name,
       exam_center.center_address,
       exam_exam.exam_id,
       exam_student_exam.mcq_mark,
       exam_student_exam.practical_mark
  FROM (((exam_student_exam exam_student_exam
          INNER JOIN exam_student exam_student
             ON (exam_student_exam.student_id = exam_student.student_id))
         INNER JOIN exam_exam_student exam_exam_student
            ON (exam_exam_student.student_id = exam_student.student_id))
        INNER JOIN exam_exam exam_exam
           ON     (exam_exam_student.exam_id = exam_exam.exam_id)
              AND (exam_student_exam.exam_id = exam_exam.exam_id))
       INNER JOIN exam_center exam_center
          ON     (exam_exam_student.center_id = exam_center.center_id)
             AND (exam_student_exam.center_id = exam_center.center_id)
						 
 	WHERE (exam_exam_student.exam_id = '$exam_id' OR exam_exam_student.exam_id is NULL)
ORDER BY exam_exam.exam_id DESC, exam_center.center_id ASC, exam_student.student_id ASC; ";

*/
$strResultStudent = "
SELECT exam_student.student_id,
       exam_student.it_serial,
       exam_student.reg_no,
       exam_student.name,
       exam_center.center_id,
       exam_center.center_name,
       exam_center.center_address,
       exam_exam.exam_id,
       exam_student_exam.mcq_mark,
       exam_student_exam.practical_mark
  FROM ((exam_student_exam exam_student_exam
         INNER JOIN exam_center exam_center
            ON (exam_student_exam.center_id = exam_center.center_id))
        INNER JOIN exam_student exam_student
           ON (exam_student_exam.student_id = exam_student.student_id))
       INNER JOIN exam_exam exam_exam
          ON (exam_student_exam.exam_id = exam_exam.exam_id)
 WHERE (exam_student_exam.exam_id = '$exam_id' OR exam_student_exam.exam_id IS NULL) AND exam_student_exam.center_id = '$center_id'
ORDER BY exam_exam.exam_id DESC,
         exam_center.center_id ASC,
         exam_student.student_id ASC
";

$query_assigned_student = $db->query($strResultStudent);
$no_of_assigned_student = $db->num_rows($query_assigned_student);
?>
  <div class="col-md-12">
    <table class="table table-bordered tooltip-demo" id="list_student_exam">
    	<thead>
        <tr>
          <th width="5%">SN</th>
          <th width="10%">IT Serial Number</th>
          <th width="10%">Registration Number</th>
          <th>Student Name</th>
          <th>Exam Center</th>
          <th>Full Mark</th>
          <th>Pass Mark</th>
          <th>MCQ Mark</th>
          <th>Practical Mark</th>
          <th>Total</th>
          <th>Result</th>
          <th width="65"></th>
        </tr>
      </thead>
      <tbody>
<?php $SNO =0;
while ($rowStudentResult = $db->fetch_object($query_assigned_student))
{

// Checking Absent of User
$str_abs = "SELECT * from exam_student_answer WHERE exam_id = '$exam_id' and student_id = '$rowStudentResult->student_id' AND center_id = '$center_id'";

$query_abs = $db->query($str_abs);
$no_of_abs = $db->num_rows($query_abs);
$db->free($query_abs);
unset($str_abs,$query_abs,$txtAbs, $disabled);
if ($no_of_abs < 1) $txtAbs = TRUE;

if ($txtAbs) $disabled = " disabled=\"disabled\"";
  
?>
        <tr>
          <td><?php echo ++$SNO;?></td>
          <td width="10%"><?php echo $rowStudentResult->reg_no;?></td>
          <td width="10%"><?php echo $rowStudentResult->reg_no;?></td>
          <td><?php echo $rowStudentResult->name;?></td>
          <td><?php if ($rowStudentResult->center_name != NULL) echo "$rowStudentResult->center_name, $rowStudentResult->center_address";?></td>
          <td><?php echo $full_mark;?></td>
          <td><?php echo $pass_mark;?></td>
          <td><?php echo $rowStudentResult->mcq_mark;?></td>
          <td><?php echo $rowStudentResult->practical_mark;?></td>
          <td><?php $total_marks = $rowStudentResult->mcq_mark + $rowStudentResult->practical_mark; echo $total_marks;?></td>
          <td><?php
          if ($total_marks >= $pass_mark) echo "<font color=green>Pass !</font>";
          else if ( $txtAbs ) echo "<font color=blue>Absent</font>";
          else echo "<font color=red>Fail !</font>";
          ?></td>
          <td width="65">
            <button title="View Attended Question/Answer" class="btn btn-success btn-sm full-result" type="submit" id="btnFullResult2" name="btnFullResult2" data-button="btnFullResult" data-class="btn btn-success" data-input-name="exam_id"  data-id="<?php echo $exam_id;?>" data-input-name2="student_id"  data-id2="<?php echo $rowStudentResult->student_id;?>" data-input-name3="center_id"  data-id3="<?php echo $rowStudentResult->center_id;?>" data-target="#full-result-modal" data-toggle="modal" data-url="" data-toggle-tooltip="tooltip" data-placement="top"<?php echo $disabled;?>><span class="fa fa-file-text-o"></span> View Attended <br>Question / Answer</button>
          </td>
        </tr>
<?php
}
?>
      </tbody>
    </table>
  </div>
<?php
confirm4("Attented Question/Answer","Do you want to <b>View attented Question and Answer </b> of this student ?","full-result","full-result-modal",$rand,"_full_result");

?>
<?php
dataTable("list_student_exam");
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