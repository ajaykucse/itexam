<?php 
@session_start();
if (file_exists("security.php")) include_once "security.php";
if (!$_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>";  

$strQuestion = "SELECT question_id from exam_question";
$query_question = $db->query($strQuestion);
$total_question = $db->num_rows($query_question);

$strexam = "SELECT exam_id from exam_exam_center";
$query_exam = $db->query($strexam);
$total_exam = $db->num_rows($query_exam);

$strcenter = "SELECT center_id from exam_center";
$query_center = $db->query($strcenter);
$total_center = $db->num_rows($query_center);

$strstudent = "SELECT student_id from exam_student";
$query_student = $db->query($strstudent);
$total_student = $db->num_rows($query_student);

$struser = "SELECT user_id from exam_user";
$query_user = $db->query($struser);
$total_user = $db->num_rows($query_user);

$strTotalExam = "SELECT * from exam_student_exam";
$query_TotalExam = $db->query($strTotalExam);
$total_TotalExam = $db->num_rows($query_TotalExam);

$strExam = "
SELECT exam_exam.*,
			 date(exam_exam_center.schedule_date) as ExamSchedule,
			 exam_exam_center.schedule_date,
       exam_exam_type.exam_type,
       exam_exam_type.total_question,
       exam_exam_type.full_mark,
       exam_exam_type.pass_mark,
       exam_exam_type.total_time,
       exam_exam_type.mcq_mark,
       exam_exam_type.practical_mark,
       exam_center.center_name,
       exam_center.center_id,
       exam_exam_status.start_time,
       exam_exam_status.grace_time,
       exam_exam_status.grace_reason,
       exam_exam_status.end_time,
       exam_exam_status.full_close,
       exam_exam_status.send_for_approve,
       exam_exam_status.is_approved
  FROM (((exam_exam exam_exam
          INNER JOIN exam_exam_type exam_exam_type
             ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id))
         LEFT OUTER JOIN exam_exam_center exam_exam_center
            ON (exam_exam_center.exam_id = exam_exam.exam_id))
        INNER JOIN exam_center exam_center
           ON (exam_exam_center.center_id = exam_center.center_id))
       LEFT OUTER JOIN exam_exam_status exam_exam_status
          ON     (exam_exam_center.exam_id = exam_exam_status.exam_id)
             AND (exam_exam_center.center_id = exam_exam_status.center_id) 

";
$today = date("Y-m-d");


$strExamSchedule = " $strExam

WHERE (exam_exam_center.schedule_date is NOT NULL AND date(exam_exam_center.schedule_date) = '$today' AND exam_exam_center.schedule_date <= NOW() ) AND exam_exam_status.start_time IS NULL AND exam_exam_status.end_time IS NULL ";


$query_exam_schedule = $db->query($strExamSchedule);
$total_exam_schedule = $db->num_rows($query_exam_schedule);

$today = date("Y-m-d");
$strExpireSchedule = "

SELECT exam_exam_center.exam_id,
       exam_exam_status.start_time,
       exam_exam_status.end_time,
       exam_exam_status.full_close
  FROM exam_exam_center exam_exam_center
       INNER JOIN exam_exam_status exam_exam_status
          ON     (exam_exam_center.exam_id = exam_exam_status.exam_id)
             AND (exam_exam_center.center_id = exam_exam_status.center_id)
 WHERE ( (exam_exam_center.schedule_date is NOT NULL AND date(exam_exam_center.schedule_date) < '$today' )
        AND exam_exam_status.start_time IS NULL AND exam_exam_status.end_time is NULL); ";


$query_exam_expire = $db->query($strExpireSchedule);
$total_exam_expire = $db->num_rows($query_exam_expire);


$strExamType = "SELECT exam_type_id from exam_exam_type;";
$query_exam_type = $db->query($strExamType);
$total_exam_type = $db->num_rows($query_exam_type);


$strExamNOTSchedule = "
SELECT exam_exam.*,
			 date(exam_exam_center.schedule_date) as ExamSchedule,
			 exam_exam_center.schedule_date,
       exam_exam_type.exam_type,
       exam_exam_type.total_question,
       exam_exam_type.full_mark,
       exam_exam_type.pass_mark,
       exam_exam_type.total_time,
       exam_exam_type.mcq_mark,
       exam_exam_type.practical_mark,
       exam_center.center_name,
       exam_center.center_id,
       exam_exam_status.start_time,
       exam_exam_status.grace_time,
       exam_exam_status.grace_reason,
       exam_exam_status.end_time,
       exam_exam_status.full_close,
       exam_exam_status.send_for_approve,
       exam_exam_status.is_approved
  FROM (((exam_exam exam_exam
          INNER JOIN exam_exam_type exam_exam_type
             ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id))
         LEFT OUTER JOIN exam_exam_center exam_exam_center
            ON (exam_exam_center.exam_id = exam_exam.exam_id))
        INNER JOIN exam_center exam_center
           ON (exam_exam_center.center_id = exam_center.center_id))
       LEFT OUTER JOIN exam_exam_status exam_exam_status
          ON     (exam_exam_center.exam_id = exam_exam_status.exam_id)
             AND (exam_exam_center.center_id = exam_exam_status.center_id) 
 WHERE     ((   exam_exam_center.schedule_date > NOW() OR exam_exam_center.schedule_date IS NULL)
       AND exam_exam_status.start_time IS NULL AND exam_exam_status.end_time IS NULL )
;";

//					$strStatus = " WHERE (exam_exam_center.schedule_date is NULL OR exam_exam_center.schedule_date > NOW() ) AND exam_exam_status.start_time IS NULL AND exam_exam_status.end_time IS NULL ";


$query_exam_not_schedule = $db->query($strExamNOTSchedule);
$total_exam_not_schedule = $db->num_rows($query_exam_not_schedule);

$strExamStart = "
SELECT exam_exam.exam_id
  FROM exam_exam_status exam_exam_status
       INNER JOIN exam_exam exam_exam
          ON (exam_exam_status.exam_id = exam_exam.exam_id)
   WHERE exam_exam_status.start_time IS NOT NULL AND exam_exam_status.end_time IS NULL;";
$query_exam_start = $db->query($strExamStart);
$total_exam_start = $db->num_rows($query_exam_start);

$strExamEnd = "
SELECT exam_exam.exam_id
  FROM exam_exam_status exam_exam_status
       INNER JOIN exam_exam exam_exam
          ON (exam_exam_status.exam_id = exam_exam.exam_id)
   WHERE exam_exam_status.start_time IS NOT NULL AND exam_exam_status.end_time IS NOT NULL AND exam_exam_status.full_close='N'
	 ;";
$query_exam_end = $db->query($strExamEnd);
$total_exam_end = $db->num_rows($query_exam_end);


$strExamFinalApproval = " $strExam WHERE exam_exam_center.schedule_date is NOT NULL AND exam_exam_status.start_time IS NOT NULL AND exam_exam_status.end_time IS NOT NULL AND exam_exam_status.full_close = 'N' AND exam_exam_status.send_for_approve = 'Y' ";

$query_exam_final_approval = $db->query($strExamFinalApproval);
$total_exam_final_approval = $db->num_rows($query_exam_final_approval);

$strExamClose = "
SELECT exam_exam.exam_id
  FROM exam_exam_status exam_exam_status
       INNER JOIN exam_exam exam_exam
          ON (exam_exam_status.exam_id = exam_exam.exam_id)
   WHERE exam_exam_status.start_time IS NOT NULL AND exam_exam_status.end_time IS NOT NULL AND exam_exam_status.full_close='Y'
	 ;";
$query_exam_close = $db->query($strExamClose);
$total_exam_close = $db->num_rows($query_exam_close);


?>
<div class="row" style="margin-top:15px;">
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <div class="row">
          <div class="col-xs-3">
            <i class="fa fa-wrench fa-4x"></i>
          </div>
          <div class="col-xs-9 text-right">
            <div class="huge"><?php echo $total_exam_type;?></div>
            <div>Exam Types</div>
          </div>
        </div>
      </div>
      <a href="<?php echo $URL;?>type/list.html">
        <div class="panel-footer">
          <span class="pull-left">Exam Type List</span>
          <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
          <div class="clearfix"></div>
        </div>
      </a>
    </div>
  </div>
  
  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="panel panel-info">
      <div class="panel-heading">
        <div class="row">
          <div class="col-xs-3">
            <i class="fa fa-question-circle fa-4x"></i>
          </div>
          <div class="col-xs-9 text-right">
            <div class="huge"><?php echo $total_question;?></div>
            <div>Question(s)</div>
          </div>
        </div>
      </div>
      <a href="<?php echo $URL;?>question/list.html">
        <div class="panel-footer">
          <span class="pull-left">Question Bank</span>
          <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
          <div class="clearfix"></div>
        </div>
      </a>
    </div>
  </div>

  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="panel panel-yellow">
      <div class="panel-heading">
        <div class="row">
          <div class="col-xs-3">
            <i class="fa fa-university fa-4x"></i>
          </div>
          <div class="col-xs-9 text-right">
            <div class="huge"><?php echo $total_center;?></div>
            <div>Center(s)</div>
          </div>
        </div>
      </div>
      <a href="<?php echo $URL;?>center/list.html">
        <div class="panel-footer">
          <span class="pull-left">Center List</span>
          <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
          <div class="clearfix"></div>
        </div>
      </a>
    </div>
  </div>


  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="panel panel-success">
      <div class="panel-heading">
        <div class="row">
          <div class="col-xs-3">
            <i class="fa fa-edit fa-4x"></i>
          </div>
          <div class="col-xs-9 text-right">
            <div class="huge"><?php echo $total_exam;?></div>
            <div>Total Exam(s)</div>
          </div>
        </div>
      </div>
      <a class="exam_list" data-id="1">
        <div class="panel-footer">
          <span class="pull-left">Total Exam List</span>
          <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
          <div class="clearfix"></div>
        </div>
      </a>
    </div>
  </div>

  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="panel panel-info">
      <div class="panel-heading">
          <div class="row">
            <div class="col-xs-3">
              <i class="fa fa-calendar fa-5x"></i>
            </div>
            <div class="col-xs-9 text-right">
              <div class="huge"><?php echo $total_exam_schedule;?></div>
              <div>Scheduled Exam</div>
            </div>
          </div>
        </div>
        <a class="exam_list" data-id="3">
        <div class="panel-footer">
          <span class="pull-left">Schedule Exam</span>
          <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
          <div class="clearfix"></div>
        </div>
        </a>
    </div>
  </div>

  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="panel panel-yellow">
      <div class="panel-heading">
          <div class="row">
            <div class="col-xs-3">
              <i class="fa fa-calendar-times-o fa-5x"></i>
            </div>
            <div class="col-xs-9 text-right">
              <div class="huge"><?php echo $total_exam_expire;?></div>
              <div>Schedule Expire Exam</div>
            </div>
          </div>
        </div>
        <a class="exam_list" data-id="4">
        <div class="panel-footer">
          <span class="pull-left">Schedule Expire Exam</span>
          <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
          <div class="clearfix"></div>
        </div>
        </a>
    </div>
  </div>

  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="panel panel-default">
      <div class="panel-heading">
          <div class="row">
            <div class="col-xs-3">
              <i class="fa fa-calendar-minus-o fa-5x"></i>
            </div>
            <div class="col-xs-9 text-right">
              <div class="huge"><?php echo $total_exam_not_schedule;?></div>
              <div>Not Scheduled Exam</div>
            </div>
          </div>
        </div>
        <a class="exam_list" data-id="2">
        <div class="panel-footer">
          <span class="pull-left">Not Schedule Exam</span>
          <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
          <div class="clearfix"></div>
        </div>
        </a>
    </div>
  </div>

  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="panel panel-green">
      <div class="panel-heading">
          <div class="row">
            <div class="col-xs-3">
              <i class="fa fa-play-circle fa-5x"></i>
            </div>
            <div class="col-xs-9 text-right">
              <div class="huge"><?php echo $total_exam_start;?></div>
              <div>Started Exam</div>
            </div>
          </div>
        </div>
        <a class="exam_list" data-id="5">
        <div class="panel-footer">
          <span class="pull-left">Started Exam</span>
          <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
          <div class="clearfix"></div>
        </div>
        </a>
    </div>
  </div>

  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="panel panel-danger">
      <div class="panel-heading">
          <div class="row">
            <div class="col-xs-3">
              <i class="fa fa-stop fa-5x"></i>
            </div>
            <div class="col-xs-9 text-right">
              <div class="huge"><?php echo $total_exam_end;?></div>
              <div>Finished Exam</div>
            </div>
          </div>
        </div>
        <a class="exam_list" data-id="6">
        <div class="panel-footer">
          <span class="pull-left">Finished Exam</span>
          <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
          <div class="clearfix"></div>
        </div>
        </a>
    </div>
  </div>

  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="panel panel-primary">
      <div class="panel-heading">
          <div class="row">
            <div class="col-xs-3">
              <i class="fa fa-share-alt fa-5x"></i>
            </div>
            <div class="col-xs-9 text-right">
              <div class="huge"><?php echo $total_exam_final_approval;?></div>
              <div>Awaiting Final Approval</div>
            </div>
          </div>
        </div>
        <a class="exam_list" data-id="7">
        <div class="panel-footer">
          <span class="pull-left">Awaiting Final Approval</span>
          <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
          <div class="clearfix"></div>
        </div>
        </a>
    </div>
  </div>

  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="panel panel-warning">
      <div class="panel-heading">
          <div class="row">
            <div class="col-xs-3">
              <i class="fa fa-check-square-o fa-5x"></i>
            </div>
            <div class="col-xs-9 text-right">
              <div class="huge"><?php echo $total_exam_close;?></div>
              <div>Result Published Exam</div>
            </div>
          </div>
        </div>
        <a class="exam_list" data-id="8">
        <div class="panel-footer">
          <span class="pull-left">Result Published Exam</span>
          <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
          <div class="clearfix"></div>
        </div>
        </a>
    </div>
  </div>

  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="panel panel-red">
      <div class="panel-heading">
          <div class="row">
            <div class="col-xs-3">
              <i class="fa fa-graduation-cap fa-4x"></i>
            </div>
            <div class="col-xs-9 text-right">
              <div class="huge"><?php echo $total_student;?></div>
              <div>Student(s)</div>
            </div>
          </div>
        </div>
        <a href="<?php echo $URL;?>student/list.html">
        <div class="panel-footer">
          <span class="pull-left">Student List</span>
          <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
          <div class="clearfix"></div>
        </div>
        </a>
      </div>
    </div>

  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="panel panel-info">
      <div class="panel-heading">
          <div class="row">
            <div class="col-xs-3">
              <i class="fa fa-users fa-4x"></i>
            </div>
            <div class="col-xs-9 text-right">
              <div class="huge"><?php echo $total_user;?></div>
              <div>User(s)</div>
            </div>
          </div>
        </div>
        <a href="<?php echo $URL;?>user/list.html">
        <div class="panel-footer">
          <span class="pull-left">User List</span>
          <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
          <div class="clearfix"></div>
        </div>
        </a>

    </div>
  </div>


</div>
<style>
.huge {
    font-size: 40px;
}
</style>
<script>
$(document).ready(function(e) {
	startCounter();
});
function startCounter() {
	$('.huge').each(function () {
			var $this = $(this);
			jQuery({ Counter: 0 }).animate({ Counter: $this.text() }, {
					duration: 2000,
					easing: 'swing',
					step: function () {
							$this.text(Math.ceil(this.Counter));
					}
			});
	});
}
</script>
<?php
	$rand = RandomValue(20);
	unset($_SESSION['rand']);
	$_SESSION['rand']['list_exam']=$rand;
?>
<form method="post" id="frmExamType" action="exam/list.html" >
	<input type="hidden" name="isSearchFilter">
	<input type="hidden" name="btnSearchFilter">
	<input type="hidden" name="rand" value="<?php echo $rand;?>">
	<input type="hidden" name="exam_type" id="exam_type_id" value="">
</form>
<script>
$(".exam_list").click(function(e) {
	var exam_type = $(this).data("id");
	$("#exam_type_id").val(exam_type);

	$("#frmExamType").submit();
});
</script>
<style>
.exam_list
{
	cursor:pointer;
	
}
</style>