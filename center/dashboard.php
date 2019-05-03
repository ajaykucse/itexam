<?php 
@session_start();
if (file_exists("security.php")) include_once "security.php";
if (!$_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER'])
	echo "<script>window.location='" .$MAIN_URL."start.html';</script>";  

$today = date("Y-m-d");
$strSQL = 
"SELECT 
			 exam_exam.exam_id,
			 exam_exam.exam_code,
       exam_exam_status.start_time,
       exam_exam_status.grace_time,
       exam_exam_status.grace_reason,
       exam_exam_status.end_time,
       exam_exam_status.send_for_approve,
       exam_exam_status.full_close,
       exam_exam_type.exam_type,
       exam_exam_center.schedule_date,
       exam_exam_type.total_time,
       exam_exam.max_time_adjustment,
       exam_exam_status.final_submit_time,
       date_format(exam_exam_center.schedule_date, '%Y %b %d, %h:%i:%s %p')
          AS format_schedule_date,
       date_format(exam_exam_status.start_time, '%Y %b %d, %h:%i:%s %p')
          AS format_start_time,
       date_format(exam_exam_center.schedule_date, '%Y-%m-%d')
          AS schedule_date_time,

       date_format(exam_exam_status.end_time, '%Y %b %d, %h:%i:%s %p')
          AS format_end_time,

       exam_exam_status.is_approved
  FROM ((exam_exam exam_exam
         LEFT OUTER JOIN exam_exam_type exam_exam_type
            ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id))
        RIGHT OUTER JOIN exam_exam_center exam_exam_center
           ON (exam_exam_center.exam_id = exam_exam.exam_id))
       LEFT OUTER JOIN exam_exam_status exam_exam_status
          ON     (exam_exam_center.exam_id = exam_exam_status.exam_id)
             AND (exam_exam_center.center_id = exam_exam_status.center_id)
  WHERE exam_exam_center.center_id = '$center_id' ";


$query_exam = $db->query($strSQL);
$total_exam = $db->num_rows($query_exam);

$strExamSchedule = " $strSQL  AND (exam_exam_center.schedule_date is NOT NULL AND date(exam_exam_center.schedule_date) = '$today' AND exam_exam_center.schedule_date <= NOW() ) AND exam_exam_status.start_time IS NULL AND exam_exam_status.end_time IS NULL ";

$query_exam_schedule = $db->query($strExamSchedule);
$total_exam_schedule = $db->num_rows($query_exam_schedule);

$strExamNOTSchedule = " $strSQL  AND (exam_exam_center.schedule_date is NULL OR exam_exam_center.schedule_date > NOW() ) AND exam_exam_status.start_time IS NULL AND exam_exam_status.end_time IS NULL ";

$query_exam_not_schedule = $db->query($strExamNOTSchedule);
$total_exam_not_schedule = $db->num_rows($query_exam_not_schedule);

$strExamScheduleExpire = " $strSQL  AND (exam_exam_center.schedule_date is NOT NULL AND date(exam_exam_center.schedule_date) < '$today' ) AND exam_exam_status.start_time IS NULL AND exam_exam_status.end_time IS NULL ";

$query_exam_schedule_expire = $db->query($strExamScheduleExpire);
$total_exam_schedule_expire = $db->num_rows($query_exam_schedule_expire);



$strExamStart = "
SELECT exam_exam.exam_id
  FROM exam_exam_status exam_exam_status
       INNER JOIN exam_exam exam_exam
          ON (exam_exam_status.exam_id = exam_exam.exam_id)
   WHERE center_id = '$center_id'
	 AND exam_exam_status.start_time IS NOT NULL AND exam_exam_status.end_time IS NULL
	 ;";
$query_exam_start = $db->query($strExamStart);
$total_exam_start = $db->num_rows($query_exam_start);

$strExamEnd = "
SELECT exam_exam.exam_id
  FROM exam_exam_status exam_exam_status
       INNER JOIN exam_exam exam_exam
          ON (exam_exam_status.exam_id = exam_exam.exam_id)
   WHERE center_id = '$center_id'
	 AND exam_exam_status.start_time IS NOT NULL AND exam_exam_status.end_time IS NOT NULL AND exam_exam_status.full_close='N'
	 ;";
$query_exam_end = $db->query($strExamEnd);
$total_exam_end = $db->num_rows($query_exam_end);

$strExamFinalApproval = " $strSQL AND exam_exam_center.schedule_date is NOT NULL AND exam_exam_status.start_time IS NOT NULL AND exam_exam_status.end_time IS NOT NULL AND exam_exam_status.full_close = 'N' AND exam_exam_status.send_for_approve = 'Y' ";

$query_exam_final_approval = $db->query($strExamFinalApproval);
$total_exam_final_approval = $db->num_rows($query_exam_final_approval);

$strExamClose = "
SELECT exam_exam.exam_id
  FROM exam_exam_status exam_exam_status
       INNER JOIN exam_exam exam_exam
          ON (exam_exam_status.exam_id = exam_exam.exam_id)
   WHERE center_id = '$center_id'
	 AND exam_exam_status.start_time IS NOT NULL AND exam_exam_status.end_time IS NOT NULL AND exam_exam_status.full_close='Y'
	 ;";
$query_exam_close = $db->query($strExamClose);
$total_exam_close = $db->num_rows($query_exam_close);

?>
<div class="row" style="margin-top:15px;">
  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
    <div class="panel panel-success">
      <div class="panel-heading">
          <div class="row">
            <div class="col-xs-3">
              <i class="fa fa-edit fa-5x"></i>
            </div>
            <div class="col-xs-9 text-right">
              <div class="huge"><?php echo $total_exam;?></div>
              <div>Total Exam(s)</div>
            </div>
          </div>
        </div>
        <a class="exam_list" data-id="1">
        <div class="panel-footer">
          <span class="pull-left">Total Exam(s)</span>
          <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
          <div class="clearfix"></div>
        </div>
        </a>
      </div>
    </div>

  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
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

  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
    <div class="panel panel-yellow">
      <div class="panel-heading">
          <div class="row">
            <div class="col-xs-3">
              <i class="fa fa-calendar-times-o fa-5x"></i>
            </div>
            <div class="col-xs-9 text-right">
              <div class="huge"><?php echo $total_exam_schedule_expire;?></div>
              <div>Scheduled Expire Exam</div>
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

  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
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

  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
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

  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
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

  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
    <div class="panel panel-primary">
      <div class="panel-heading">
          <div class="row">
            <div class="col-xs-3">
              <i class="fa fa-share-alt fa-5x"></i>
            </div>
            <div class="col-xs-9 text-right">
              <div class="huge"><?php echo $total_exam_final_approval;?></div>
              <div>Awaiting Final Approval Exam</div>
            </div>
          </div>
        </div>
        <a class="exam_list" data-id="7">
        <div class="panel-footer">
          <span class="pull-left">Awaiting Final Approval Exam</span>
          <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
          <div class="clearfix"></div>
        </div>
        </a>
    </div>
  </div>

  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
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

</div>
<style>
.huge {
    font-size: 40px;
}
</style>
<?php
	$rand = RandomValue(20);
	unset($_SESSION['rand']);
	$_SESSION['center_exam_list']['rand']=$rand;
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