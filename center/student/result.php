<?php 
session_start();
if (file_exists("security.php")) include_once "security.php";
if (!$_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>";  

if (isset($_POST['btnBack']))
	echo "<script>window.location='exam.html';</script>";  

if (isset($_POST['btnStudentList']))
	echo "<script>window.location='list.html';</script>";  

if (isset($_POST['student_exam_id'])) $student_exam_id = FilterNumber($_POST['student_exam_id']);

$strTotalQuestion = "

SELECT exam_student_answer.*, exam_student_question.student_exam_id
  FROM exam_student_answer exam_student_answer
       INNER JOIN exam_student_question exam_student_question
          ON (exam_student_answer.student_question_id =
                 exam_student_question.student_question_id)	
	WHERE exam_student_question.student_exam_id = '$student_exam_id' 	
	";

$strCorrect = " $strTotalQuestion AND exam_student_answer.choosen_answer = exam_student_answer.correct_answer ";

$strWrong = " $strTotalQuestion AND exam_student_answer.choosen_answer <> exam_student_answer.correct_answer ";
$strAttempted = " $strTotalQuestion AND exam_student_answer.choosen_answer > 0 ";

$strTime = "select timediff(end_time,start_time) as DiffTime, start_time, end_time from exam_student_exam WHERE student_exam_id= '$student_exam_id' ; ";
$query_time = $db->query($strTime);
$row = $db->fetch_object($query_time);
$TimeTaken = $row->DiffTime;
$start_time = $row->start_time;
$end_time = $row->end_time ;

$strTotal = "SELECT * FROM exam_student_exam WHERE student_exam_id = '$student_exam_id';";

$strSQL = "
SELECT exam_course.*,
       exam_student_exam.max_question,
       exam_student_exam.student_id,
       exam_student.student_id as StudentID,
       exam_student.name,
       exam_student.address,
       exam_center.center_name,
       exam_center.center_address
  FROM ((exam_student exam_student
         INNER JOIN exam_center exam_center
            ON (exam_student.center_id = exam_center.center_id))
        INNER JOIN exam_student_exam exam_student_exam
           ON (exam_student_exam.student_id = exam_student.student_id))
       INNER JOIN exam_course exam_course
          ON (exam_student_exam.course_id = exam_course.course_id)
	WHERE exam_student_exam.student_exam_id = '$student_exam_id';
";

$query_score = $db->query($strSQL);
$row_score = $db->fetch_object($query_score);
$pass_score = $row_score->pass_score;
$course_name = $row_score->course_name;

$query_total = $db->query($strTotal);
$rowTOtal = $db->fetch_object($query_total);
$TotalQuestion = $rowTOtal->max_question;


$query_attempted = $db->query($strAttempted);
$TotalAttempted = $db->num_rows($query_attempted);

$query_correct = $db->query($strCorrect);
$CorrectQuestion = $db->num_rows($query_correct);
if ($CorrectQuestion == 0) $Percentage = 0;
else $Percentage = ($CorrectQuestion / $TotalQuestion) * 100;

$strFinishTime = "
UPDATE 
exam_student_exam 
SET 
end_time = NOW(), 
score = $Percentage,
is_finished = 'Y' 
WHERE student_exam_id = '$student_exam_id' AND is_finished='N';";
$db->query($strFinishTime);

?>
<?php

$strStudent = "
SELECT exam_student.*,
       exam_student_user.user_id,
       exam_center.center_name,
       exam_center.center_address
  FROM (exam_student_user exam_student_user
        RIGHT OUTER JOIN exam_student exam_student
           ON (exam_student_user.student_id = exam_student.student_id))
       INNER JOIN exam_center exam_center
          ON (exam_center.center_id = exam_student.center_id)
	WHERE exam_student_user.user_id = '$user_id';
";

$query_student = $db->query($strStudent);
$row_student = $db->fetch_object($query_student);
$center_name = $row_score->center_name;
$center_address = $row_score->center_address;


if ($pass_score < $Percentage) $passed = "<font color=green>PASSED</font>";
else $passed = "<font color=red>FAILED</font>";
?>
<div id="Print">
  <h3 class="page-header">Exam Result : <strong><?php echo $course_name;?></strong></h3>
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-green">
        <div class="panel-heading print">Student Exam Result</div>
        <div class="panel-body">
          <div class="row">
            <div class="col-sm-12">
              <div class="col-sm-4"><strong>Name: </strong><?php echo "$row_score->name";?></div>
              <div class="col-sm-4"><strong>Address: </strong><?php echo $row_score->address;?></div>
              <div class="col-sm-4"><strong>Student ID: </strong><?php echo "$row_score->StudentID";?></div>
            </div>
          <p>&nbsp;</p>
          </div>  
          <div class="row">
            <div class="col-sm-12">
              <div class="col-sm-4"><strong>Center Name: </strong><?php echo $center_name;?></div>
              <div class="col-sm-4"><strong>Center Address: </strong><?php echo $center_address;?></div>
            </div>
          </div>  
          <p>&nbsp;</p>
            
          <div class="row">
            <div class="col-sm-12">
              <div class="col-sm-4"><strong>Total Question: </strong>&nbsp;<?php echo $TotalQuestion;?></div>
              <div class="col-sm-4"><strong>Attempted Question:</strong>&nbsp;<?php echo $TotalAttempted;?></div>
            </div>
          </div>        
          <p>&nbsp;</p>
          <div class="row">
            <div class="col-sm-12">
              <div class="col-sm-4"><strong>Obtained Score: </strong>&nbsp;<?php echo number_format($Percentage,2);?>%</div>
              <div class="col-sm-4"><strong>Passing Score: </strong>&nbsp;<?php echo number_format($pass_score,2);?>%</div>
              <div class="col-sm-4"><strong>Result: </strong>&nbsp;<strong><em><?php echo $passed;?></em></strong></div>
            </div>
          </div>        
          <p>&nbsp;</p>
          
          <div class="row">
            <div class="col-sm-12">
              <div class="col-sm-4"><strong>Exam Started at:</strong>&nbsp;<?php echo $start_time;?></div>
              <div class="col-sm-4"><strong>Exam Finished at:</strong>&nbsp;<?php echo $end_time;?></div>
              <div class="col-sm-4"><strong>Total Time Elapsed:</strong>&nbsp;<?php echo $TimeTaken;?></div>
            </div>
          </div>
          <p>&nbsp;</p>
            
         <div class="progress">
          <div class="pull-right">Obtained Score</div>
          <div class="progress-bar  progress-bar-success" role="progressbar" aria-valuenow="<?php echo $Percentage;?>"
          aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $Percentage;?>%; min-height:25px;">
            <?php echo number_format($Percentage,2);?>% 
          </div>
        </div>
        <div class="progress" style="margin-top:15px;">
          <div class="pull-right">Passing Score</div>
          <div class="progress-bar progress-bar-default" role="progressbar" style="width:<?php echo $pass_score ;?>%">
            <?php echo number_format($pass_score,2) ;?>%
          </div>
        </div>

        <div class="col-md-12" style="margin-top:15px;" align="center">
          <h3 style="margin:0px;"><em><strong><?php echo $passed;?></strong></em></h3>
        </div>
        
      </div>
    </div>
  </div>
</div> <!-- PRINT DIV-->
</div>

<form method="post">
  <div class="row">
    <div class="col-md-12" align="center">
      <button type="submit" class="btn btn-warning pull-right" name="btnBack"><i class="fa fa-list"></i> Back to Result Page</button>
      <button type="submit" class="btn btn-primary" name="btnStudentList"><i class="fa fa-graduation-cap"></i> Back to Student List</button>
      <button type="button" class="btn btn-default pull-left" onClick="javascript:PrintDiv('Print')" name="btnPrint"><i class="fa fa-print"></i> Print Score</button>
    </div>
  </div>
</form>

<?php
unset($_SESSION['user']['exam']['startTime']);
?>
<style>
.progress
{
	min-height:40px;
	border-radius:25px;
	background-color:#B5B3B3;
	border:1px solid #828282;
}
.progress-bar, .progress > .pull-right
{
	padding:10px;
	font-weight:600;
	font-size:90%;
}
p
{
	margin:0;
}
</style>
<script>
$(document).ready(function(e) {
  	$("#time").text("");
});
</script>

<script type="text/javascript">
function PrintDiv(div)
{
	var divToPrint = document.getElementById(div);
	var popupWin = window.open('','_blank','width=800,height=600');
	popupWin.document.open();

	popupWin.document.write('<html><head><title><?php if (strlen(trim($software)) > 0) echo $software;?></title>\n<link href="<?php echo $MAIN_URL;?>bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />\n<link href="<?php echo $MAIN_URL;?>print.css" rel="stylesheet" type="text/css" />\n<link href="<?php echo $MAIN_URL;?>bootstrap/font-awesome-4.4.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />\n</head>\n<body onload="window.print()">\n<div class='print'>\n' +divToPrint.innerHTML + '\n</div><style>.print * { visibility: visible !important; } </style></body>\n</html>');

	popupWin.window.print();
	popupWin.window.close();
}
</script>