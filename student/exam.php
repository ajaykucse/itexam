<?php
@session_start();

if (!isset($_SESSION['ONLINE-EXAM-SIMULATOR-STUDENT']))
		echo "<script>window.location='" . $MAIN_URL ."start.html';</script>";
if (!isset($student_id))
		echo "<script>window.location='" . $MAIN_URL ."start.html';</script>";

$strExam = "
SELECT exam_exam_status.*
FROM exam_exam_student exam_exam_student
INNER JOIN exam_exam_status exam_exam_status
ON (exam_exam_student.exam_id = exam_exam_status.exam_id)
WHERE exam_exam_status.exam_id = '$exam_id' AND exam_exam_status.center_id = '$center_id' AND exam_exam_student.student_id = '$student_id';";

$query_exam = $db->query($strExam);
$no_of_exam = $db->num_rows($query_exam);

$strTotalTime = "SELECT exam_exam_type.total_time
  FROM exam_exam exam_exam
       INNER JOIN exam_exam_type exam_exam_type
          ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id) WHERE exam_exam.exam_id = '$exam_id';";

$query_total = $db->query($strTotalTime);
$row_total  = $db->fetch_object($query_total);
$total_time = $row_total->total_time;					
$db->free($query_total);
unset($row_total, $query_total, $strTotalTime);

if ($no_of_exam > 0)
{
	$row_Exam = $db->fetch_object($query_exam);
	$exam_id = $row_Exam->exam_id;
	$db->free($query_exam);

	$strResultClose = "SELECT * FROM exam_student_exam WHERE student_id ='$user_id' AND exam_id = '$exam_id' AND center_id = '$center_id' ;";
	$query_close = $db->query($strResultClose);
	$no_of_close = $db->num_rows($query_close);
	$row_close = $db->fetch_object($query_close);
	if ($row_close->is_closed=='Y')
	{
		notify("Finished Exam","<b>You have already finished this exam.</b>",$MAIN_URL ."logout.html", TRUE,5000);
		include_once "footer.inc.php";
		die();
	}
	$db->free($query_close);
	unset($row_close, $no_of_close, $query_close, $strResultClose);
}


$previous = (isset($_POST['previous']));
$next = (isset($_POST['next']));

if (!isset($next)) $next = 2;
if (!isset($previous)) $previous = 0;

if (isset($_POST['btnShowQuestionID']))
{
	$show_question_id = FilterNumber($_POST['show_question_id']);
	$next = $show_question_id + 1;
	$previous = $show_question_id - 1;
	$ShowQuestionID = TRUE;
}

if (isset($_POST['btnPrevious']))
{
	$next = $next - 1;
	$previous = $previous - 1;
	$is_previous = TRUE;
	$is_next = FALSE;
}
if (isset($_POST['btnNext']))
{
	$next = $next + 1;
	$previous = $previous + 1;
	$is_previous = FALSE;
	$is_next = TRUE;
}

$current = $previous + 1;


if ( (isset($_POST['btnNext'])) || (isset($_POST['btnPrevious'])) || (isset($_POST['btnSave'])) )
{
	$post_question_id = FilterNumber($_POST['question_id']);
	
	$answer = $_POST['answer'];
	$is_mark = FilterNumber($_POST['is_mark']);
	
	if ($is_mark <> 1) $is_mark = '0';
	unset($answer_value);
	if (count($answer)>0)
	{
		foreach ($answer as $ANS)
		{
			$ANS = FilterNumber($ANS);
			if ($ANS == 1) $answer_value = $answer_value + 1;
			else if ($ANS == 2) $answer_value = $answer_value + 2;
			else if ($ANS == 3) $answer_value = $answer_value + 4;
			else if ($ANS == 4) $answer_value = $answer_value + 8;
			else if ($ANS == 5) $answer_value = $answer_value + 16;
			else if ($ANS == 6) $answer_value = $answer_value + 32;
			else if ($ANS == 7) $answer_value = $answer_value + 64;
			else if ($ANS == 8) $answer_value = $answer_value + 128;
			else if ($ANS == 9) $answer_value = $answer_value + 256;
			else if ($ANS == 10) $answer_value = $answer_value + 512;
		}
	}
	if ($answer_value < 1) $answer_value = '0';
	$answer_order = FilterAnswerOrder($_POST['answer_order']);
	
	unset($is_previous, $is_next);

	//finding correct answer value
	$strCorrectAnswer = "SELECT answer_sn from exam_answer WHERE is_correct = 'Y' AND question_id = '$post_question_id'";
	$query_correct = $db->query($strCorrectAnswer);
	unset($CorrectAnswer_value );
	while ($row_correct = $db->fetch_object($query_correct))
	{
		$CorrectAns = FilterNumber($row_correct->answer_sn);
		if ($CorrectAns == 1) $CorrectAnswer_value = $CorrectAnswer_value + 1;
		else if ($CorrectAns == 2) $CorrectAnswer_value = $CorrectAnswer_value + 2;
		else if ($CorrectAns == 3) $CorrectAnswer_value = $CorrectAnswer_value + 4;
		else if ($CorrectAns == 4) $CorrectAnswer_value = $CorrectAnswer_value + 8;
		else if ($CorrectAns == 5) $CorrectAnswer_value = $CorrectAnswer_value + 16;
		else if ($CorrectAns == 6) $CorrectAnswer_value = $CorrectAnswer_value + 32;
		else if ($CorrectAns == 7) $CorrectAnswer_value = $CorrectAnswer_value + 64;
		else if ($CorrectAns == 8) $CorrectAnswer_value = $CorrectAnswer_value + 128;
		else if ($CorrectAns == 9) $CorrectAnswer_value = $CorrectAnswer_value + 256;
		else if ($CorrectAns == 10) $CorrectAnswer_value = $CorrectAnswer_value + 512;
	}
	$db->free($query_correct);
	unset($query_correct,$strCorrectAnswer,$row_correct);

	$selectAnswer = "SELECT * FROM exam_student_answer WHERE student_id = '$student_id' AND exam_id = '$exam_id' and question_id = '$post_question_id';"; 

	$query_answer3 = $db->query($selectAnswer);
	$no_of_student_answer = $db->num_rows($query_answer3);

	if ($no_of_student_answer > 0)
		$strStudentAnswer = "UPDATE exam_student_answer SET 
		choosen_answer = '$answer_value',
		correct_answer = '$CorrectAnswer_value',
		is_mark = '$is_mark'
		WHERE 
		student_id = '$student_id' 
		AND exam_id = '$exam_id' 
		AND question_id = '$post_question_id';"; 		
	else	
	{
		$strAnswerID = "SELECT student_answer_id FROM exam_student_answer ORDER BY student_answer_id DESC LIMIT 0,1;";
		$query_id = $db->query($strAnswerID);
		$rowID = $db->fetch_object($query_id);
		$student_answer_id = $rowID->student_answer_id + 1;
		$db->free($query_id);
		unset($strAnswerID, $query_id, $rowID);

		$strStudentAnswer = "INSERT INTO exam_student_answer (student_answer_id, question_id, exam_id, center_id, student_id, choosen_answer, correct_answer, is_mark) VALUES ('$student_answer_id', '$post_question_id', '$exam_id', '$center_id', '$student_id', '$answer_value', '$CorrectAnswer_value', '$is_mark');";
	}

	$db->query($strStudentAnswer);
	unset($selectAnswer,$no_of_student_answer, $strStudentAnswer);
	
}


// EXAM DETAILS
$strSelect = "
SELECT exam_exam_status.start_time,
       exam_exam_status.end_time,
       exam_exam_status.full_close,
       exam_exam_student.student_id,
       exam_exam_status.exam_id,
       exam_exam.exam_code,
       exam_exam_type.total_question,
       exam_exam_type.total_time,
       exam_exam_type.mcq_mark
  FROM ((exam_exam exam_exam
         INNER JOIN exam_exam_type exam_exam_type
            ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id))
        INNER JOIN exam_exam_status exam_exam_status
           ON (exam_exam_status.exam_id = exam_exam.exam_id))
       INNER JOIN exam_exam_student exam_exam_student
          ON (exam_exam_student.exam_id = exam_exam.exam_id)
	WHERE exam_exam_student.student_id = '$student_id' AND exam_exam_status.center_id = '$center_id';";

$query = $db->query($strSelect);
$row = $db->fetch_object($query);
$max_question = $row->total_question;
$mcq_number = $row->mcq_mark;
$total_time = $row->total_time;
$exam_code = $row->exam_code;
$exam_id = $row->exam_id;
$no_of_question = $max_question;

if (isset($_POST['btnFinishConfirm']))
{
	$rand = FilterString($_POST['rand']);
	if ($_SESSION['rand']['exam'] == $rand)
	{
		$finish = CloseExam($exam_id, $student_id, $max_question,$mcq_number, $db);
		if ($finish)
		{
			notify("End Exam","<b>Your exam has been submitted.</b> <br><br>Thank you.",$MAIN_URL ."logout.html", TRUE,5000);
			include_once "footer.inc.php";
			die();
		}
	}
}


?>
<h3 class="page-header">Exam</h3>
<?php

if ($current > $no_of_question)
{
	$MaximumReached = TRUE;
}

if (isset($_POST['btnReviewAll']))
{
	$rand = FilterString($_POST['rand']);
	if ($_SESSION['rand']['exam']==$rand)
	{
		$current = FilterNumber($_POST['current']);
		$next = $current + 1;
		$previous = $current - 1;

?>

<script>
$(document).ready(function(e) {
	$('#ModalStudentExamStatus').modal('show');
	$('div#ModalStudentExamStatus .modal-body').css('max-height', $(window).height() * 0.5);
	$('div#ModalStudentExamStatus .modal-body').css('overflow-y', 'auto'); 
});
</script>

  <div class="modal fade" id="ModalStudentExamStatus" tabindex="-1" role="dialog" aria-labelledby="StudentExamStatus" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Exam Status</h4>
        </div>
        <div class="modal-body">
<?php
//	ShowQuestionCount($no_of_existing_question, $no_of_question, $current, $db, $student_id, $exam_id);
?>
        
        	<div class="col-md-12">
<?php ViewStatus ($exam_id, $student_id, $db, $no_of_question,"ALL"); ?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal" autofocus>Close</button>
        </div>
      </div>
    </div>
  </div>
<?php
	}
}

if (isset($_POST['btnReviewMark']))
{
	$rand = FilterString($_POST['rand']);
	if ($_SESSION['rand']['exam']==$rand)
	{
		$current = FilterNumber($_POST['current']);
		$next = $current + 1;
		$previous = $current - 1;

?>

<script>
$(document).ready(function(e) {
	$('#ModalStudentExamStatus').modal('show');
	$('div#ModalStudentExamStatus .modal-body').css('max-height', $(window).height() * 0.5);
	$('div#ModalStudentExamStatus .modal-body').css('overflow-y', 'auto'); 
});
</script>

  <div class="modal fade" id="ModalStudentExamStatus" tabindex="-1" role="dialog" aria-labelledby="StudentExamStatus" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Exam Status</h4>
        </div>
        <div class="modal-body">
<?php
//	ShowQuestionCount($no_of_existing_question, $no_of_question, $current, $db, $student_id, $exam_id);
?>
        
        	<div class="col-md-12">
<?php ViewStatus ($exam_id, $student_id, $db, $no_of_question,"MARKED"); ?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal" autofocus>Close</button>
        </div>
      </div>
    </div>
  </div>
<?php
	}
}

if (isset($_POST['btnReviewIncomplete']))
{
	$rand = FilterString($_POST['rand']);
	if ($_SESSION['rand']['exam']==$rand)
	{
		$current = FilterNumber($_POST['current']);
		$next = $current + 1;
		$previous = $current - 1;

?>

<script>
$(document).ready(function(e) {
	$('#ModalStudentExamStatus').modal('show');
	$('div#ModalStudentExamStatus .modal-body').css('max-height', $(window).height() * 0.5);
	$('div#ModalStudentExamStatus .modal-body').css('overflow-y', 'auto'); 
});
</script>

  <div class="modal fade" id="ModalStudentExamStatus" tabindex="-1" role="dialog" aria-labelledby="StudentExamStatus" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Exam Status</h4>
        </div>
        <div class="modal-body">
<?php
//	ShowQuestionCount($no_of_existing_question, $no_of_question, $current, $db, $student_id, $exam_id);
?>
        
        	<div class="col-md-12">
<?php ViewStatus ($exam_id, $student_id, $db, $no_of_question,"NOT ANSWERED"); ?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal" autofocus>Close</button>
        </div>
      </div>
    </div>
  </div>
<?php
	}
}

if (isset($_POST['btnFinish']))
{
	$rand = FilterString($_POST['rand']);
	if ($_SESSION['rand']['exam']=$rand)
	{
		FinishExam($exam_id, $student_id, $db, $rand, $no_of_question);
		$FINISHED=TRUE;
	}
}

if (isset($_POST['btnFinishExam']))
{
	$close = CloseExam($exam_id, $student_id, $max_question, $mcq_number, $db);
	if ($close)
	{
			notify("Exam","Your exam has been updated and submitted.<br><br>Now you are going to logout.",$MAIN_URL."logout.html",TRUE,5000);
			UpdateEndTime($db);
		include_once "footer.inc.php";
		exit();
	}
}



if (isset($MaximumReached))
{
	FinishExam($exam_id, $student_id, $db, $rand, $no_of_question);
}

if (!$MaximumReached && !$FINISHED)
{
	if ($next == 2)		// IF FIRST QUESTION INSERT DATA INTO exam_student_exam for MARK CALCULATION
	{
		$strInsert = "SELECT * FROM exam_student_exam WHERE exam_id = '$exam_id' AND student_id = '$student_id';";
		$query_check_existing_exam = $db->query($strInsert);
		$no_of_existing_exam = $db->num_rows($query_check_existing_exam);
		if ($no_of_existing_exam == 0)
		{
			$str_exam_insert = "INSERT INTO exam_student_exam (student_id, exam_id, center_id, max_question, is_closed) VALUES ('$student_id', '$exam_id', '$center_id', '$max_question','N');";
			$db->query($str_exam_insert);
			unset($str_exam_insert);
		}
	}

	$select_existing_question = "
	SELECT exam_student_question.*
		FROM exam_student_question exam_student_question
				 INNER JOIN exam_student_exam exam_student_exam
						ON     (exam_student_question.exam_id = exam_student_exam.exam_id)
							 AND (exam_student_question.student_id =
											 exam_student_exam.student_id)
	WHERE exam_student_exam.exam_id = '$exam_id' AND exam_student_exam.student_id = '$student_id'
  ;";

	$query_select_existing_question = $db->query($select_existing_question);
	$no_of_existing_question = $db->num_rows($query_select_existing_question);
	$db->free($query_select_existing_question);
	unset($query_select_existing_question,$select_existing_question, $is_new_one, $is_existing );

	if ($no_of_existing_question < $current)		// NEW QUESTION
	{
		do 
		{
			$question_id = QuestionID($exam_id, $student_id, $db);
			if (strlen($question_id) < 1) $question_id= 0 ;
		}	while ($question_id < 1);
	
		$strExam = "SELECT * from exam_question WHERE question_id = '$question_id';";
		$query_question_id = $db->query($strExam);
		$row_question_id = $db->fetch_object($query_question_id);
		$chapter_no = $row_question_id->chapter_no;
		$select_question_id = $row_question_id->question_id;		// Question ID from DB
		$db->free($query_question_id);
		unset($query_question_id,$row_question_id, $strExam);
	
		$is_new_one = TRUE;
		$student_exam_id = $_SESSION['user']['student_exam_id'];

		$strID = "SELECT exam_student_question_id FROM  exam_student_question ORDER BY exam_student_question_id DESC LIMIT 0,1";
		$query_id = $db->query($strID);
		$row_id = $db->fetch_object($query_id);
		$exam_student_question_id = $row_id->exam_student_question_id + 1;
		$db->free($query_id);
		unset($strID, $query_id, $row_id);
		$strInsertQuestion = "INSERT INTO exam_student_question (exam_student_question_id, exam_id, student_id, question_id, chapter_no) VALUES ('$exam_student_question_id', '$exam_id', '$student_id', '$question_id', '$chapter_no');";
		$query_insert = $db->query($strInsertQuestion);
	}
	else
	{
		$is_existing = TRUE;

		$str_student_question = "SELECT * FROM exam_student_question WHERE exam_id = '$exam_id' AND student_id = '$student_id' LIMIT $previous, 1";

		$query_question = $db->query($str_student_question);
		$row_question = $db->fetch_object($query_question);
	
		$select_question_id = $row_question->question_id;
		$existing_answer_order = $row_question->answer_order;
		$student_question_id = $row_question->student_question_id;
		$db->free($query_question);
		unset($str_student_question, $query_question, $row_question);

}
	unset($previous_disabled);
	if ($previous == 0) $previous_disabled = " disabled";

CheckExam($exam_id, $student_id, $center_id, $db, $MAIN_URL, $software);

?>
<style>
td label
{
	font-weight:normal;
}
</style>
<?php


if (isset($_POST['start-message']))
{
	$random = FilterString($_POST['rand']);
	if ($_SESSION['rand']['exam'] == $random)
	{
		$_SESSION['user']['exam']['message']	= TRUE;
	}
}

$rand = RandomValue(20);
$_SESSION['rand']['exam']=$rand;	

if (!isset($_SESSION['user']['exam']['message']))
{
?>
<script>
$(document).ready(function(e) {
	$('#WelcomeMessage').modal('show');
	$('div#WelcomeMessage .modal-body').css('max-height', $(window).height() * 0.5);
	$('div#WelcomeMessage .modal-body').css('overflow-y', 'auto'); 

	$('#WelcomeMessage').on('hide.bs.modal', function (e) {
		e.preventDefault();
	});
});
</script>

  <div class="modal fade" id="WelcomeMessage" tabindex="-1" role="dialog" aria-labelledby="WelcomeMessage" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Exam Start Message</h4>
        </div>
        <div class="modal-body">
        	<div class="col-md-12">
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
			echo $start_message;
			?>
          </div>
        </div>
        <div class="modal-footer">
        	<form method="post">
          	<input type="hidden" name="rand" value="<?php echo $rand;?>">
            <button type="submit" class="btn btn-primary" name="start-message" autofocus>Start Exam</button>
          </form>
        </div>
      </div>
    </div>
  </div>
<?php
}

?>
<div class="row">
	<form method="post">
	<div class="col-md-12" style="margin-bottom:15px;">
  	<div class="row">
      <div class="col-md-12">
        <div class="pull-right">
            <button title="Review All" class="btn btn-primary ReviewAll" type="submit" id="btnReviewAll2" name="btnReviewAll2"  data-id="<?php echo $current;?>" data-input-name="current" data-id2="<?php echo $exam_id;?>" data-input-name2="exam_id" data-button="btnReviewAll" data-class="btn btn-primary" data-target="#review-modal1" data-toggle="modal" data-url="">Review All </button>
  
            <button title="Review Marked" class="btn btn-warning ReviewMarked" type="submit" id="btnReviewMark2" name="btnReviewMark2"  data-id="<?php echo $current;?>" data-input-name="current" data-id2="<?php echo $exam_id;?>" data-input-name2="exam_id" data-button="btnReviewMark" data-class="btn btn-warning" data-target="#review-modal2" data-toggle="modal" data-url="">Review Marked </button>
  
            <button title="Review Incomplete" class="btn btn-info ReviewIncomplete" type="submit" id="btnReviewIncomplete2" name="btnReviewIncomplete2"  data-id="<?php echo $current;?>" data-input-name="current" data-id2="<?php echo $exam_id;?>" data-input-name2="exam_id" data-button="btnReviewIncomplete" data-class="btn btn-info" data-target="#review-modal3" data-toggle="modal" data-url="">Review Incomplete </button>
  
            <button title="End Exam" class="btn btn-success finish" type="submit" id="btnFinish2" name="btnFinish2"  data-id="<?php echo $student_id;?>" data-input-name="student_id" data-id2="<?php echo $exam_id;?>" data-input-name2="exam_id" data-button="btnFinish" data-class="btn btn-success" data-target="#finish_modal" data-toggle="modal" data-url="">End Exam</button>
          </div>
      </div>
    </div>
  </div>
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-body">
          <?php 
              $strQuestion = "SELECT * FROM exam_question WHERE question_id = '$select_question_id'";

              $query_question = $db->query($strQuestion);
              $row_question = $db->fetch_object($query_question);
          
              echo "<div class=\"col-md-12 col-xs-12 col-md-12\"><b>$row_question->question</b></div>";
              echo "<br>";
              echo "<br>";
          
              $strAnswer2 = "SELECT * FROM exam_answer WHERE question_id = '$row_question->question_id' AND is_correct='Y';";
          
              $query_answer2 = $db->query($strAnswer2);
              $no_of_correct_answer = $db->num_rows($query_answer2);
              $db->free($query_answer2);
              unset($strAnswer2,$query_answer2);
          
          if ($is_existing)
          {
            $ans_order = split(",",$existing_answer_order);
          
            $strAnswer4 = "SELECT choosen_answer, is_mark FROM exam_student_answer WHERE student_id = '$student_id' AND question_id = '$row_question->question_id' AND exam_id = '$exam_id';";
            $query_answer4 = $db->query($strAnswer4);
            $row_answer4 = $db->fetch_object($query_answer4);
            
            $choosen_answer = $row_answer4->choosen_answer;
            $is_review = $row_answer4->is_mark;
          
            foreach ($ans_order AS $ORDER)
            {
              $i++;
              $strAnswer = "SELECT * FROM exam_answer WHERE question_id = '$row_question->question_id' AND answer_sn = '$ORDER';";
          
              $query_answer = $db->query($strAnswer);
              $row_answer = $db->fetch_object($query_answer);
              $ANS = $row_answer->answer_sn;
              
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
              if ($choosen_answer & $answer_value) $checked = " checked ";
              if ($no_of_correct_answer > 1) $multi = TRUE;
              else $multi= FALSE;
          
              if ($multi) $type = "checkbox";
              else $type = "radio";
          ?>
          <div class="col-md-12">
            <table width="100%" cellpadding="5" cellspacing="5" border="0">
              <tr>
                <td width="25"><input id="answer<?php echo $i;?>" type="<?php echo $type;?>" <?php echo $checked;?> name="answer[]" value="<?php echo $row_answer->answer_sn;?>"> </td>
                <td><label for="answer<?php echo $i;?>"><?php echo $row_answer->answer;?></label></td>
              </tr>
            </table>
          </div>
          <?php		
              } // FOREACH
              $answer_order = $existing_answer_order;
          
            }	// EXISTING QUESTION ANSWER
            else
            {
          
            if ($row_question->rand_answer == "Y")
              $strAnswer = "SELECT * FROM exam_answer WHERE question_id = '$row_question->question_id' ORDER BY rand();";
            else
              $strAnswer = "SELECT * FROM exam_answer WHERE question_id = '$row_question->question_id' ORDER BY answer_sn;";
              $query_answer = $db->query($strAnswer);
              $no_of_answer = $db->num_rows($query_answer);
              unset($answer_order);
          
              while ($row_answer = $db->fetch_object($query_answer))
              {
                $i++;
                if ($no_of_correct_answer > 1) $multi = TRUE;
                else $multi= FALSE;
              if ($multi) $type = "checkbox";
              else $type = "radio";
          ?>
          <div class="col-md-12">
            <table width="100%" cellpadding="5" cellspacing="5" border="0">
              <tr>
                <td width="25"><input id="answer<?php echo $i;?>" type="<?php echo $type;?>" <?php echo $checked;?> name="answer[]" value="<?php echo $row_answer->answer_sn;?>"> </td>
                <td><label for="answer<?php echo $i;?>"><?php echo $row_answer->answer;?></label></td>
              </tr>
            </table>
          </div>
          <?php		
              $answer_order .= $row_answer->answer_sn;
              if ($i != $no_of_answer) $answer_order .= ",";
              }
            }
          
            if ($is_new_one)		//update existing answer_display_order and question_id
            {
              $str_update_answer = "UPDATE exam_student_question 
              SET answer_order = '$answer_order',
              question_id = '$row_question->question_id'
              WHERE question_id = '$question_id'
              AND exam_id = '$exam_id'
              AND student_id = '$student_id'
              ";
              $db->query($str_update_answer);
            }
              ?>
        </div>

        <div class="panel-footer">
          <div class="row">
						<?php
            unset($next_disabled);
            if ($next > $no_of_question +1) $next_disabled = " disabled";
              if ($current > 1) $previous_disabled = "";

            ?>

            <input type="hidden" name="question_id" value="<?php echo $row_question->question_id;?>">
            <input type="hidden" name="previous" value="<?php echo $previous;?>">
            <input type="hidden" name="next" value="<?php echo $next;?>">
            <div class="col-md-6 pull-left">
              <button type="submit" class="btn btn-default" name="btnPrevious"<?php echo $previous_disabled;?>>&larr;  Previous</button>&nbsp; &nbsp;
                <button type="submit" class="btn btn-primary" name="btnNext"<?php echo $next_disabled;?>> Next &rarr;</button>
              <label><input name="is_mark" type="checkbox" title="Review" value="1"<?php if ($is_review==1) echo " checked";?>> 
              Mark</label>
            </div>
            <?php echo "$current of $no_of_question";?>
            <div class="pull-right">
              <div class="col-md-12">
              </div>
            </div>
          
          </div>
        </div>
      </div>
          
    </div>
  </form>
</div>
<?php
confirm3("View Exam Status","Do you want to <b>View Exam Status (Review All)</b>?","ReviewAll","review-modal1",$rand,"_review1");
confirm3("View Exam Status","Do you want to <b>View Exam Status (Review Marked)</b>?","ReviewMarked","review-modal2",$rand,"_review2");
confirm3("View Exam Status","Do you want to <b>View Exam Status (Review Incomplete)</b>?","ReviewIncomplete","review-modal3",$rand,"_review3");

confirm3("End Exam","Do you want to <b>end this exam</b>? <br><br>","finish","finish_modal",$rand,1);
?>
<?php
$select_count_question = "SELECT * FROM exam_student_answer WHERE student_id = '$student_id' AND exam_id = '$exam_id';";
$query_count_question = $db->query($select_count_question);
$no_of_listing_question = $db->num_rows($query_count_question);
$db->free ($query_count_question);
unset($select_count_question, $query_count_question);

$percentage = (($no_of_listing_question) / $no_of_question  * 100);
$percentage = number_format($percentage);
?>
<div class="progress">
  <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $percentage;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $percentage;?>%;">
    <?php echo $percentage;?>%
  </div>
</div>
<?php
}  // NOT FINISHED

$strStartTime = "SELECT exam_exam_status.start_time,
       exam_exam_status.grace_time,
       exam_exam_status.exam_id,
       exam_exam_status.center_id,
       exam_exam_type.total_time
  FROM (exam_exam exam_exam
        INNER JOIN exam_exam_type exam_exam_type
           ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id))
       INNER JOIN exam_exam_status exam_exam_status
          ON (exam_exam_status.exam_id = exam_exam.exam_id) 
 WHERE exam_exam_status.exam_id = '$exam_id' AND exam_exam_status.center_id = '$center_id';";
$query_start_time = $db->query($strStartTime);
$row_start_time = $db->fetch_object($query_start_time);
$startTime = $row_start_time->start_time;
$total_time = $row_start_time->total_time + $row_start_time->grace_time;

$diffTime = time() - strtotime($startTime);

?>
<script>
function elapsedTime() {
	var totalTime = "<?php echo $total_time;?>";
	var endTime = new Date();
	// time difference in ms
	var timeDiff = endTime - startTime;
	// strip the miliseconds
	timeDiff /= 1000;
	// get seconds
	var seconds = Math.round(timeDiff % 60);
	// remove seconds from the date
	timeDiff = Math.floor(timeDiff / 60);
	// get minutes
	var minutes = Math.round(timeDiff % 60);
	// remove minutes from the date
	timeDiff = Math.floor(timeDiff / 60);
	// get hours
	var hours = Math.round(timeDiff % 24);
	// remove hours from the date
	timeDiff = Math.floor(timeDiff / 24);
	// the rest of timeDiff is number of days
	var days = timeDiff;
	if ( hours < 10) hours = "0" + hours;
	if ( minutes < 10) minutes = "0" + minutes;
	if ( seconds < 10) seconds= "0" + seconds;

	var TOTAL_TIME = (parseInt(hours) * 60) + parseInt(minutes);

	if (days < 1)
		$("#ElapsedTime").text(hours + ":" + minutes + ":" + seconds);
	else
		$("#ElapsedTime").text(days + " days, " + hours + ":" + minutes + ":" + seconds);
	setTimeout(elapsedTime, 1000);

	if (TOTAL_TIME >= totalTime)
	{
		$("#finish").submit()
	}
}
$(document).ready(function(e) {
	startTime = new Date();
	startTime.setSeconds(startTime.getSeconds() - (<?php echo $diffTime;?>));
    setTimeout(elapsedTime, 0);  
});
</script>
<?php
$tt = $total_time;
$minute = $tt % 60;
$tt = $tt / 60;
$hour = $tt % 24;
if ($hour < 10) $hour = "0$hour";
if ($minute < 10) $minute = "0$minute";
$txtTotalTime = "$hour:$minute:00";
?>
<script>
	$("#TotalTime").text("<?php echo $txtTotalTime;?>");
</script>
  <form method="post" id="frm_question_id">
  	<input type="hidden" name="show_question_id" id="show_question_id" value="">
    <input type="hidden" name="btnShowQuestionID">
  </form>
<form method="post" id="finish">
	<input type="hidden" name="btnFinishExam">
</form>
<?php
function QuestionID ($exam_id,$student_id,$db)
{
/*	if (strlen($question_id))
		//Question Selection
		$strExam = "
		select question_id, chapter_no from exam_question WHERE chapter_no IN 
		(
			SELECT chapter_no FROM `exam_chapter_question` WHERE exam_id = '$exam_id' AND no_of_question > 0 ORDER BY rand() 
		) 
		AND
		question_id IN 
		(
			SELECT question_id FROM `exam_exam_question` WHERE exam_id = '$exam_id' ORDER BY rand() 
		) 
		AND question_id NOT IN (
			SELECT question_id
			FROM  `exam_student_question` 
			WHERE exam_id ='$exam_id'
			AND student_id ='$student_id'
		)
		WHERE question_id <> '$question_id'
		AND is_active = 'Y'
		ORDER BY rand() LIMIT 0,1
		";
	else
	*/
		//Question Selection
		$strExam = "
		select question_id, chapter_no  from exam_question WHERE chapter_no IN 
		(
			SELECT chapter_no FROM `exam_exam_question` WHERE exam_id = '$exam_id' ORDER BY rand() 
		) 
		AND
		question_id IN 
		(
			SELECT question_id FROM `exam_exam_question` WHERE exam_id = '$exam_id' ORDER BY rand() 
		) 
		AND question_id NOT IN (
			SELECT question_id
			FROM  `exam_student_question` 
			WHERE exam_id ='$exam_id'
			AND student_id ='$student_id'
		)
		ORDER BY rand() LIMIT 0,1
		";

	$query_question_id = $db->query($strExam);
	
	$row_question_id = $db->fetch_object($query_question_id);
	$question_id = (isset($row_question_id->question_id));
	$chapter_no = (isset($row_question_id->chapter_no));
	$isExist = CheckDuplication ($question_id,$exam_id,$chapter_no, $db);
	if ($isExist) 
	{
		if ($question_id > 0)
			return $question_id;
		else
			QuestionID ($exam_id,$student_id,$db);
	}
	else
		QuestionID ($exam_id,$student_id,$db);
		
}

function CheckDuplication ($question_id, $exam_id, $chapter_no, $db)
{
	$strChap = "SELECT exam_type_id FROM exam_exam WHERE exam_id = '$exam_id';";
	$query = $db->query($strChap);
	$row = $db->fetch_object($query);
	$exam_type_id = $row->exam_type_id;
	$db->free($query);
	unset($strSQL, $query, $row);

	$strMaxChapQuestion = "SELECT * FROM exam_type_chapter_question WHERE chapter_no = '$chapter_no' AND exam_type_id = '$exam_type_id';";
	$query_max = $db->query($strMaxChapQuestion);
	$row_max = $db->fetch_object($query_max);
	$no_of_question = (isset($row_max->no_of_question));
	$db->free($query_max);
	unset($strMaxChapQuestion, $query_max, $row_max);

	$strCount = "SELECT count(question_id) as CNT from exam_student_question WHERE exam_id='$exam_id' AND student_id = '" . $_SESSION['user']['student_id'] . "' AND chapter_no = '$chapter_no';";


	$query_count = $db->query($strCount);
	$row_count = $db->fetch_object($query_count);
	$question_count = $row_count->CNT;

	$db->free($query_count);
	unset($row_count, $query_count, $strCount);

	if ($question_count > $no_of_question)
	{
		return FALSE;
	}
	else
	{
		return TRUE;
	}
}
?>
<?php
function ShowQuestionCount($no_of_shown_question, $no_of_question, $curr, $db, $student_id, $exam_id)
{
	$str = "SELECT * FROM exam_student_answer WHERE student_id = '$student_id' AND exam_id = '$exam_id' ORDER BY student_answer_id;";
	$query = $db->query($str);
	
	echo "<nav>
	<ul class=\"pagination\" style=\"margin-top:0px; margin-bottom:0px;\">";
	while ($row = $db->fetch_object($query))
	{
		$is_mark[] = $row->is_mark;
	}
	for ($i=1; $i <= $no_of_question; $i++)
	{
		if ($is_mark[$i-1] == "1") $mark = "<sub> r</sub>";
		if ($i > $no_of_shown_question)
		{
			 $disabled = " class=\"disabled\"";
			 $disabled2 = " disabled=\"disabled\"";
		}
		else
		{
			$disabled = " style=\"cursor:pointer\"";
			$disabled2 = "";
		}
		
		if ($i == $curr) 
			echo "<li class=\"active\"><a data-id=\"$i\">$i$mark</a></li>";
		else
			echo "<li$disabled><a data-id=\"$i\"$disabled2>$i$mark </a></li>";
		unset($mark);
	}
	echo "</ul>
	</nav>";
?>
	<script>
  $(".pagination a").click(function(e) {
		var ID = $(this).data('id');
		var DISABLED = $(this).attr("disabled");
		if (DISABLED != "disabled")
		{
			$("#show_question_id").val(ID);
			$("#frm_question_id").submit();
		}
  });
  </script>
<?php
}
?>
<?php
function CloseExam($exam_id, $student_id, $max_question, $mcq_number, $db)
{
/*	
// IGNORED marked for Mark calculation

$strTotalQuestion = "
	
	SELECT *
		FROM exam_student_answer 
		WHERE student_id = '$student_id' AND exam_id = '$exam_id'  AND is_mark='0' 
		";
*/
	$strTotalQuestion = "
	
	SELECT *
		FROM exam_student_answer 
		WHERE student_id = '$student_id' AND exam_id = '$exam_id'   
		";
	
	$strCorrect = " $strTotalQuestion AND exam_student_answer.choosen_answer = exam_student_answer.correct_answer ";
	
	$query_correct = $db->query($strCorrect);
	$CorrectQuestion = $db->num_rows($query_correct);
	
	$marks_per_question =  $mcq_number / $max_question;

	$mcq_mark = $CorrectQuestion * $marks_per_question;
	
		$strFinished = "UPDATE exam_student_exam SET is_closed = 'Y', mcq_mark = '$mcq_mark' WHERE student_id ='$student_id' AND exam_id = '$exam_id';";
		$query = $db->query($strFinished);
	if ($query) return TRUE;
	else return FALSE;	
}
?>
<?php
function FinishExam($exam_id, $student_id, $db, $rand, $no_of_question)
{
	if (!isset($rand))
	{
		$rand = RandomValue(20);
		$_SESSION['rand']['exam']=$rand;	
	}

			$strMessage = "SELECT end_message FROM exam_exam_message WHERE exam_id = '$exam_id';";
			$query_message = $db->query($strMessage);
			$no_of_message = $db->num_rows($query_message);
			if ($no_of_message > 0)
			{
				$row_message = $db->fetch_object($query_message);
				$end_message = $row_message->end_message;
				$db->free($query_message);
				unset($strMessage, $query_message, $row_message);
			}
			else
			{
				$strMessageDefault = "
				
				SELECT exam_exam_type.end_message
  FROM exam_exam exam_exam
       INNER JOIN exam_exam_type exam_exam_type
          ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id)
					WHERE exam_exam.exam_id = '$exam_id'";
				$query_default_message = $db->query($strMessageDefault);
				$row_default_message = $db->fetch_object($query_default_message);
				$end_message = $row_default_message->end_message;
				$db->free($query_default_message);
				unset($strMessageDefault, $query_default_message, $row_default_message);
			}

	?>
  	<form method="post" class="form-horizontal">
      <div class="panel panel-info">
          <div class="panel-heading"><b>End Exam Information</b></div>
        <div class="panel-body">
          <div class="col-md-12"><?php echo $end_message;?></div>
        </div>
      </div>

    	<div class="row">
        <div class="col-md-3 col-md-offset-2">
          <button class="btn btn-danger btn-block" style="margin-bottom:15px;" name="btnFinishConfirm">End Exam</button>
        </div>
        <div class="col-md-3 col-md-offset-2">
          <button class="btn btn-primary btn-block" style="margin-bottom:15px;" name="btnBack">Back to Exam</button>
        </div>
      </div>

      <div class="panel panel-success">
        <div class="panel-heading">Exam Status</div>
        <div class="panel-body">
				<?php ViewStatus ($exam_id, $student_id, $db, $no_of_question); ?>
        </div>
      </div>
      <input type="hidden" name="rand" value="<?php echo $rand;?>">
    </form>
<?php
confirm2("End Exam","Are you sure to <b>End Exam </b>?","finish-exam-final","finish-exam-final-modal",$rand,"_finish_final");

?>    
  <?php
}
?>
<?php
function ViewStatus ($exam_id, $student_id, $db,$no_of_question,$type="ALL")
{
		$strExamStatus = "
SELECT exam_student_question.exam_id,
 exam_student_question.question_id,
 exam_student_answer.student_answer_id,
 exam_student_answer.choosen_answer,
 exam_student_answer.is_mark
FROM exam_student_question exam_student_question
 LEFT OUTER JOIN exam_student_answer exam_student_answer
		ON     (exam_student_question.exam_id = exam_student_answer.exam_id)
			 AND (exam_student_question.student_id =
							 exam_student_answer.student_id)
			 AND (exam_student_question.question_id =
							 exam_student_answer.question_id)
WHERE exam_student_question.exam_id = '$exam_id' AND exam_student_question.student_id = '$student_id' 
ORDER BY exam_student_question.exam_student_question_id;
";					

		$query_exam_status = $db->query($strExamStatus);
		$no_of_status = $db->num_rows($query_exam_status);
		if ($no_of_status > 0)
		{
			?>
			<table class="table table-bordered">
				<tr>
					<th width="20%">Question No.</th>
					<th>Status</th>
				</tr>
			<?php
			while ($row_status = $db->fetch_object($query_exam_status))
			{
				$i++;
				$status['choosen_answer'][$i] = $row_status->choosen_answer;
				$status['is_mark'][$i] = $row_status->is_mark;
			}

			for ($i=1; $i <= $no_of_question; $i++)
			{
        
        if ($type == "MARKED")
        {
					if ($status['is_mark'][$i] == 1) $isShown = TRUE;
          else $isShown=FALSE;
        }
        else if ($type=="NOT ANSWERED")
        {
          if ($status['choosen_answer'][$i] == 0 OR $status['choosen_answer'][$i] == NULL) $isShown = TRUE;
          else $isShown = FALSE;
        }
        else
          $isShown = TRUE;
        
        if ($isShown)
        {
			?>
				<tr>
       <?php 
			 if ($i > $no_of_status)  
			 {
				 ?>
					<td style="cursor:not-allowed;"><?php echo $i;?></td>
				<?php 
			 }
			 else 
			 {
				 ?>
					<td class="question_number" data-id="<?php echo $i;?>"><?php echo $i;?></td>
<?php } ?>          
					<td><?php 
					if (isset($status['choosen_answer']))
					{
						if ($status['choosen_answer'][$i] == 0 OR $status['choosen_answer'][$i] == NULL) echo " <span class=\"label label-default\">Not Answered.</span>";
						else echo "<span class=\"label label-primary\">Answered.</span>";
					}

					if ($status['is_mark'][$i] == 1) echo " <span class=\"label label-warning\">Mark for Review.</span>";
					
					?></td>
			 </tr>
			<?php
        }
			}
			?>
		</table>
    <style>
		.question_number
		{
			cursor:pointer !important;
			text-decoration:none;
		}
		</style>
	<script>
  $("td.question_number").click(function(e) {
		var ID = $(this).data('id');
		var DISABLED = $(this).attr("disabled");
		if (DISABLED != "disabled")
		{
			$("#show_question_id").val(ID);
			$("#frm_question_id").submit();
		}
  });
  </script>

		<?php
		}
}
?>