<?php
session_start();
if (!isset($_SESSION['ONLINE-EXAM-SIMULATOR-STUDENT']))
		echo "<script>window.location='" . $MAIN_URL ."start.html';</script>";
?>
<h3 class="page-header">Exam List</h3>
<?php
$rand = RandomValue(20);
$_SESSION['rand']['view_exam_result']=$rand;

/*
	$strExamCheck = "

SELECT exam_exam_status.exam_id,
       exam_exam_status.center_id,
       exam_exam_status.start_time,
       exam_exam_status.end_time,
       exam_exam_status.full_close,
       exam_exam.exam_code,
       exam_exam_student.student_id
  FROM (exam_exam_status exam_exam_status
        INNER JOIN exam_exam exam_exam
           ON (exam_exam_status.exam_id = exam_exam.exam_id))
       INNER JOIN exam_exam_student exam_exam_student
          ON     (exam_exam_status.exam_id = exam_exam_student.exam_id)
             AND (exam_exam_status.center_id = exam_exam_student.center_id)
	WHERE exam_exam_student.student_id = '$student_id' AND exam_exam_status.exam_id = '$exam_id'
AND exam_exam_status.center_id = '$center_id'
 ; ";

	SELECT exam_exam_status.exam_id,
       exam_exam_status.center_id,
       exam_exam_status.start_time,
       exam_exam_status.end_time,
       exam_exam_status.full_close,
       exam_exam.exam_code,
       exam_student_exam.is_closed,
       exam_exam_student.student_id
  FROM ((exam_exam_status exam_exam_status
         INNER JOIN exam_exam exam_exam
            ON (exam_exam_status.exam_id = exam_exam.exam_id))
        INNER JOIN exam_exam_student exam_exam_student
           ON     (exam_exam_status.exam_id = exam_exam_student.exam_id)
              AND (exam_exam_status.center_id = exam_exam_student.center_id))
       LEFT OUTER JOIN exam_student_exam exam_student_exam
          ON     (exam_exam_status.exam_id = exam_student_exam.exam_id)
             AND (exam_exam_status.center_id = exam_student_exam.center_id)


*/
	$strExamCheck = "

SELECT exam_exam_status.exam_id,
       exam_exam_status.center_id,
       exam_exam_status.start_time,
       exam_exam_status.end_time,
       exam_exam_status.full_close,
       exam_exam.exam_code,
       exam_exam_student.student_id
  FROM (exam_exam_status exam_exam_status
        INNER JOIN exam_exam_student exam_exam_student
           ON     (exam_exam_status.exam_id = exam_exam_student.exam_id)
              AND (exam_exam_status.center_id = exam_exam_student.center_id))
       INNER JOIN exam_exam exam_exam
          ON (exam_exam_status.exam_id = exam_exam.exam_id)
					
		WHERE exam_exam_student.student_id = '$student_id' AND exam_exam_status.exam_id = '$exam_id'
	AND exam_exam_status.center_id = '$center_id'
	";

$querycheck = $db->query($strExamCheck);
$no_of_exam = $db->num_rows($querycheck);

$strFullClose = "SELECT * from exam_student_exam WHERE exam_id = '$exam_id' AND center_id = '$center_id' AND student_id = '$student_id';";
$query_full_close = $db->query($strFullClose);
$no_of_full_close = $db->num_rows($query_full_close);

if ($no_of_full_close > 0)
{
	$row_full = $db->fetch_object($query_full_close);
	if ($row_full->is_closed=='Y') $StudentExamFullClose = TRUE;
	else $StudentExamFullClose = FALSE;
}
	else $StudentExamFullClose = FALSE;

if ($StudentExamFullClose )
{
		notify("Exam List","Assigned Exam has been fully closed.<br><br>Please contact to Administration.",$MAIN_URL."start.html",TRUE,10000);
		include_once "footer.inc.php";
		exit();
}

if ($no_of_exam > 0)
{
	$row_exam = $db->fetch_object($querycheck);
	if ($row_exam->start_time != NULL)
	{
		$db_start_time = strtotime($row_exam->start_time);
		if ($db_start_time < time()) $isStarted = TRUE;
	}
	
	if ($row_exam->end_time != NULL)
	{
		$db_end_time = strtotime($row_exam->end_time);
		if ($db_end_time < time()) $isEnd = TRUE;
	}
	
	if ($row_exam->full_close == 'Y')
		$isClosed = TRUE;

	if ($isClosed == FALSE)
	{
		if ($isStarted)
		{
			if ($isEnd == FALSE) 
				$StartedNotFinished = TRUE;
			else 
				$Finished = TRUE;
		}
	}

	if ($row_exam->is_closed == 'Y')
	{
		notify("Exam List","Assigned Exam has been fully closed.<br><br>Please contact to Administration.",$MAIN_URL."start.html",TRUE,10000);
		include_once "footer.inc.php";
		exit();
	}
}
else
{
?>
    <div class="alert alert-danger alert-dismissible" role="alert">
      No Exam has been assigned for you. <br>Please contract to Administration.
    </div>
<?php
		notify($software,"<b><font color=red>No exam has been assigned to you.</font></b><br><font color=blue>Please login later. <br> OR <br>contact to Administration.</font>",$MAIN_URL ."logout.html", TRUE,10000);
		include_once "footer.inc.php";
		exit();
}
?>
<?php

if ($Finished)
{

?>
    <div class="alert alert-danger alert-dismissible" role="alert">
      Assigned Exam has already been Closed. <br>Please contract to Administration.
    </div>
<?php
		notify($software,"<b><font color=red>Assigned Exam has already been Closed.</font></b><br><font color=blue>Please login later. <br> OR <br>contact to Administration.</font>",$MAIN_URL ."logout.html", TRUE,10000);
		include_once "footer.inc.php";
		exit();
}
?>
<?php if ($StartedNotFinished)
{
	$_SESSION['user']['exam']['started'] = TRUE;
	echo "<script>window.location='exam.html';</script>";
}
?>      
<?php

if (!$StartedNotFinished)
{
?>
<div class="row">
  <div class="col-xs-12">
  	<div class="panel panel-info">
    	<div class="panel-heading" style="min-height:50px;">
      	<div class="row">
        	<div class="col-md-4"><strong>Assigned Exam :</strong></div>
          <div class="col-md-8"><h4><u><?php 

		$strExamCode = "SELECT * FROM exam_exam_center WHERE exam_id = '$row_exam->exam_id';";

		$query_exam_code = $db->query($strExamCode);
		$no_of_exam_code = $db->num_rows($query_exam_code);

		if ($no_of_exam_code > 1) 
			$MultipleCenter = TRUE;
		else 
			$MultipleCenter = FALSE;

		if ($MultipleCenter)
		{
			$strExamCode2 = "SELECT exam_code from exam_exam_center WHERE exam_id = '$row_exam->exam_id' AND center_id = '$center_id' AND exam_code is NOT NULL";
			$query_exam_code2 = $db->query($strExamCode2);
			$no_of_exam_code2 = $db->num_rows($query_exam_code2);
			if ($no_of_exam_code2 > 0) 
			{
				$row_exam_code2 = $db->fetch_object($query_exam_code2);
				if ($row_exam_code2 != NULL)
					echo "<span title=\"Center Exam Code\">$row_exam_code2->exam_code</span>";
				else
					echo $row_exam->exam_code;
			}
			else
				echo $row_exam->exam_code;
		}
		else
			echo $row_exam->exam_code;
					?></u></h4></div>
        </div>
      </div>
      <div class="panel-body">
      <?php
			$strMessage = "SELECT start_message FROM exam_exam_message WHERE exam_id = '$exam_id';";
			$query_message = $db->query($strMessage);
			$no_of_message = $db->num_rows($query_message);
			if ($no_of_message > 0)
			{
				$row_message = $db->fetch_object($query_message);
				$start_message = $row_message->start_message;
				$db->free($query_message);
				unset($strMessage, $query_message, $row_message);
			}
			else
			{
				$strMessageDefault = "
				
				SELECT exam_exam_type.start_message
  FROM exam_exam exam_exam
       INNER JOIN exam_exam_type exam_exam_type
          ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id)
					WHERE exam_exam.exam_id = '$exam_id'";
				$query_default_message = $db->query($strMessageDefault);
				$row_default_message = $db->fetch_object($query_default_message);
				$start_message = $row_default_message->start_message;
				$db->free($query_default_message);
				unset($strMessageDefault, $query_default_message, $row_default_message);
			}
			?>
      <div class="panel panel-default">
      	<div class="panel-heading">
        	Information about Exam
        </div>
      	<div class="panel-body">
        <?php echo $start_message;?>
        </div>
      </div>
			<form class="form-horizontal" method="post">
      	<button class="btn btn-success" name="btnNext">Check Exam Started or not</button>
      </form>
      <div id="check"></div>
      <script>
			$(document).ready(function(e) {
       		setTimeout(check_exam, 1000);
      });
			function check_exam()
			{
				$.post("<?php echo $MAIN_URL;?>ajax.html",
				{
					exam_id: "<?php echo $exam_id;?>",
					center_id: "<?php echo $center_id;?>",
					task:"exam-started-or-not",
				},
				function(data,status)
				{
					if ( data.length > 0)
						$("div#check").html(data);
					else
						setTimeout(check_exam, 1000);
				});
			}
			</script>
      </div>
    </div>
  </div>
</div>

<?php
}
?>