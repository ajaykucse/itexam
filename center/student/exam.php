<?php 
session_start();
if (file_exists("security.php")) include_once "security.php";
if (!$_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>";  

if (isset($_POST['student_id'])) $student_id = FilterNumber($_POST['student_id']);

if (isset($_POST['btnBackStudent']))
	echo "<script>window.location='list.html';</script>";  

?>
<div class="page-header">
  <div class="row">
  	<div class="col-md-9">
      <h3 style="margin-top:0px; margin-bottom:0px;"><span class="fa fa-bar-chart"></span> Exam Results</h3>
    </div>
    <div class="col-md-3 pull-right">    
      <form method="post" class="pull-right form-inline">
        <button type="submit" name="btnBackStudent" id="btnBackStudent" class="btn btn-warning"><i class="fa fa-repeat"></i> Back to Student List</button>
      </form>
    </div>
  </div>
</div>
<div class="row">
	<div class="col-md-12">
        <?php
//Existing Exam Details
$strPreviousExam = "SELECT exam_student_exam.student_exam_id,
       exam_student_exam.student_id,
       exam_student_exam.course_id,
       timediff(exam_student_exam.end_time, exam_student_exam.start_time)
          AS total_time,
       date_format(exam_student_exam.start_time, '%D %b %Y, %h:%i:%s %p')
          AS start_time,
       exam_student_exam.score,
       exam_course.course_name,
       exam_course.pass_score,
       exam_student.name,
       exam_student.address
  FROM (exam_student_exam exam_student_exam
        INNER JOIN exam_student exam_student
           ON (exam_student_exam.student_id = exam_student.student_id))
       INNER JOIN exam_course exam_course
          ON (exam_student_exam.course_id = exam_course.course_id)
 WHERE exam_student_exam.is_finished = 'Y' AND exam_student_exam.student_id = '$student_id'
ORDER BY exam_student_exam.student_id ASC, exam_student_exam.course_id ASC
;";

$query = $db->query($strPreviousExam);
$no_of_exam = $db->num_rows($query);
// if ($no_of_exam > 0)
// {
?>
      <div class="dataTable_wrapper">
        <table class="table  tooltip-demo" id="list_exam">
          <thead>
            <tr>
              <th>Course</th>
              <th>Exam date/time</th>
              <th>Time Taken</th>
              <th>Passing Score (%)</th>
              <th>Score</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
<?php
while ($rowExam = $db->fetch_object($query))
{
unset($danger);
if ($rowExam->score < $rowExam->pass_score) $danger = " class=\"danger\"";
?>
            <tr<?php echo $danger;?>>
              <td><?php echo $rowExam->course_name;?></td>
              <td><?php echo $rowExam->start_time;?></td>
              <td><?php echo $rowExam->total_time;?></td>
              <td><?php echo $rowExam->pass_score;?></td>
              <td><?php echo $rowExam->score;?></td>
              <td><?php 
              if ($rowExam->score >= $rowExam->pass_score) echo "<label class=\"label label-sm label-success\">Passed</label>";
              else
              echo "<label class=\"label label-danger\">Failed</label>";
              ?>
              <form method="post" name="edit_<?php echo $row->center_id;?>" style="float:right; margin:0px;">
                <button title="View Result" class="btn btn-success btn-circle btn-sm edit" type="submit" id="btnEdit2" name="btnEdit2" data-id="<?php echo $rowExam->student_exam_id;?>" data-button="btnEdit2" data-class="btn btn-success" data-input-name="student_exam_id" data-target="#edit_modal" data-toggle="modal" data-url="result.html"  data-toggle-tooltip="tooltip" data-placement="top"><span class="fa fa-sticky-note-o icon-white"></span></button>&nbsp;
                <input type="hidden" name="rand" value="<?php echo $rand;?>">
              </form>
              </td>
            </tr>
<?php
}
confirm2("Exam Results","Do you want to view result of this exam?","edit","edit_modal",$rand,1);
?>

          </tbody>
        </table>
      </div>
<?php
// }
dataTable("list_exam");
?>        
  </div>
</div>