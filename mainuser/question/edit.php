<?php 
@session_start();
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>"; 

if(!isset($_POST['question_id']))
	echo "<script>window.location='list.html';</script>";

$question_id = FilterNumber($_POST['question_id']);

?>
<?php
if (isset($_POST['btnCancel']))
	echo "<script>window.location='list.html';</script>";

if (isset($_POST['btnUpdate']))
{
	$randomValue = ($_POST['rand']);
	if ($_SESSION['rand']['edit_question'] == $randomValue)
	{
			$no_of_answer = addslashes($_POST['no_of_answer']);
			$answer_type = FilterString($_POST['answer_type']);
			$rand_answer = FilterString($_POST['rand_answer']);
			$exam_type_id = FilterString($_POST['exam_type_id']);
			$question = addslashes($_POST['question']);
			$chapter_no = FilterNumber($_POST['chapter_no']);
			$correct = $_POST['correct'];
			$answer = $_POST['answer'];

                        $question = TextEditorRemove(addslashes($question));

			if (strlen($question) == 0)
			{
				$isError = TRUE;
				$error[] = "Question is empty.";	
			}
			if (count($answer) == 0) 
			{
				$isError = TRUE;
				$error[] = "Answer(s) is empty.";	
			}
			else
			{
				foreach ($answer as $key => $ANSWER)
				{
					if (strlen($ANSWER) == 0)
					{
						$isError = TRUE;
						$ANSWER2 = NumberToAlpha($key+1);
						$error[] = "Answer $ANSWER2 is empty.";	
					}
				}
			}

			if (count($correct) == 0) 
			{
				$isError = TRUE;
				$error[] = "Correct answer is missing.";	
			}
			
			if (!$isError)
			{
				$db->begin();

				$strUpdate = "UPDATE exam_question SET 
				question = '$question',
				chapter_no = '$chapter_no',
				exam_type_id = '$exam_type_id',
				answer_type = '$answer_type',
				rand_answer = '$rand_answer'
				WHERE question_id = '$question_id'
				";

				$query_question = $db->query($strUpdate);
//delete existing answer;
				$strDeleteAnswer= "DELETE FROM exam_answer WHERE question_id = '$question_id';";
				$queryDeleteAnswer = $db->query($strDeleteAnswer);

				foreach ($answer as $key => $ANSWER)
				{
					$ANS = addslashes($ANSWER);


					$strID= "SELECT answer_id FROM exam_answer ORDER BY answer_id DESC LIMIT 0,1";
					$queryID = $db->query($strID);
					$rowID = $db->fetch_object($queryID);
					$answer_id = $rowID->answer_id + 1;
					$db->free($queryID);
					unset($strID, $queryID, $rowID, $is_correct);
					$i++;

					$is_correct = 'N';
					$ANS = TextEditorRemove($ANS);
				foreach ($correct as $CORRECT)
				{
					if ($key == $CORRECT) 
						$is_correct = 'Y';
				}
				$answer_sn = $key + 1;

					$strInsertAnswer = "INSERT INTO exam_answer (answer_id,question_id,answer,is_correct,answer_sn) VALUES ('$answer_id','$question_id', '$ANS', '$is_correct', '$answer_sn')";

					$query_answer = $db->query($strInsertAnswer);
				}

				if (($query_question) && ($query_answer) )
				{
					$db->commit();
					notify("INFORMATION", "<span class=\"fa fa-question-circle\"></span> Question updated.","list.html",TRUE,5000); 
					include_once "footer.inc.php";
					exit();
				}
				
			}	// no error detected
			if ($isError)
			{
				$CountERROR = count($error);
				$text = "<ul>";
				for($i=0;$i<$CountERROR;$i++)
				{
					$text .= "<li>".$error[$i] . "</li>";
				}
				$text .= "</ul>";
				notify("<font color=red><b>Error</b></font>", $text,NULL,TRUE,0);
			}		// if error
	}		// Random Value Check
} // Submit Button
?>
<?php

$sqlSELECT = "SELECT * FROM exam_question WHERE question_id = '$question_id';";

$query_sql = $db->query($sqlSELECT);
$row_question = $db->fetch_object($query_sql);

$sqlAnswer = "SELECT * FROM exam_answer WHERE question_id ='$question_id';";

$query_answer = $db->query($sqlAnswer);
$no_of_ans = $db->num_rows($query_answer);

if ($no_of_ans == 0) $no_of_answer = 4;
else $no_of_answer = $no_of_ans;

unset($_SESSION['rand']['answer']);

if ($no_of_ans > 0)
{
	while ($row_answer = $db->fetch_object($query_answer))
	{
		$_SESSION['rand']['answer']['ans'][] = $row_answer->answer;
		$_SESSION['rand']['answer']['is_cor'][] = $row_answer->is_correct;
	}
}
$db->free($query_answer);

unset($sqlAnswer, $query_answer, $row_answer);

$rand = RandomValue(20);
$_SESSION['rand']['edit_question']=$rand;

$answer_type = $row_question->answer_type;

?>
<h3 class="page-header">Edit Question and it's answer(s)</h3>
  <div class="row">
    <form method="post" name="frmQuestion" class="form-horizontal" id="form1" >
      <div class="col-sm-3 col-20">
        <fieldset>
          <legend>Exam Type</legend>
            <select name="exam_type_id" class="form-control" id="exam_type_id">
            <?php
            $strExamType = "SELECT * FROM exam_exam_type ORDER BY exam_type_id;";
            $query_exam_type = $db->query($strExamType);
            $no_of_exam_type = $db->num_rows($query_exam_type);

            while ($row_exam_type = $db->fetch_object($query_exam_type))
            {
            ?>
                <option value="<?php echo $row_exam_type->exam_type_id;?>"<?php if ($row_exam_type->exam_type_id ==$row_question->exam_type_id) echo " selected=\"selected\"";?>><?php echo $row_exam_type->exam_type;?></option>
            <?php
            }
            ?>
            </select>
        </fieldset>
        <fieldset>
          <legend>Chapter</legend>
          <div class="form-group">
            <div class="col-md-12">
              <select class="form-control" name="chapter_no" id="chapter_no">
            <?php
              for($ii=1;$ii<=30;$ii++)
              {
								?>
                <option value="<?php echo $ii;?>"<?php if ($row_question->chapter_no ==$ii) echo " selected=\"selected\"";?>> Chapter <?php echo $ii;?></option>
						<?php
              }
            ?>
              </select>
            </div>
          </div>
        </fieldset>

        <fieldset>
          <legend>Properties</legend>
            <div class="form-group">
              <label class="col-md-12">Answer Type</label>
              <div class="col-md-12">
                <select class="form-control" name="answer_type" id="answer_type">
                <option value="S"<?php if ($row_question->answer_type == "S") echo " selected=\"selected\"";?>>Single Select</option>
                <option value="M"<?php if ($row_question->answer_type == "M") echo " selected=\"selected\"";?>>Multiple Select</option>
                </select>
              </div>

              <label class="col-md-12">No of Answer</label>
              <div class="col-md-12">
                <select class="form-control" name="no_of_answer" id="no_of_answer">
              <?php
							  if ($no_of_answer > 0)
								{
									$ii = $no_of_answer;
								}
								else $ii = 4;
								if (!isset($no_of_answer))
									$no_of_answer = 4;

                for($i=2;$i<=10;$i++)
                {
                ?>
                <option value="<?php echo $i;?>"<?php if ($ii == $i ) echo " selected=\"selected\"";?>><?php echo $i;?></option>
                <?php
                }
              ?>
                </select>
              </div>
              
              <label class="col-md-12">Shuffle Answer</label>
              <div class="col-md-12">
                <select class="form-control" name="rand_answer" id="rand_answer">
                  <option value="Y"<?php if ($row_question->rand_answer == "Y") echo " selected=\"selected\"";?>>Yes</option>
                  <option value="N"<?php if ($row_question->rand_answer == "N") echo " selected=\"selected\"";?>>No</option>
                </select>
              </div>
          </div>
        </fieldset>

      </div>
<?php
wysiwyg_script($MAIN_URL);
?>              
    <div class="col-md-9 col-80" style="margin-bottom:15px;">
      <fieldset>
        <legend>Question</legend>
        <div class="form-group">
          <label for="question" class="col-md-1">Question</label>
          <div class="col-md-11">
  <?php
  // wysiwyg($txtAreaName, $txtValue = NULL)
	wysiwyg("question",stripslashes($row_question->question), 'exam-question'); 
  ?>
  <style>
  .editor
  {
  min-height:40px !important;
  max-height:200px !important;
  resize:vertical;
  }
  </style>
          </div>
        </div>
      </fieldset>
  
      <fieldset>
        <legend>Answer(s)</legend>
          <div id="answers">
  
<?php 

	if ($answer_type == "S") $single=TRUE;
	
	if (!empty($single)) $checkbox = "radio"; else $checkbox = "checkbox";

if ($no_of_ans > 0)
{
	$ans = $_SESSION['rand']['answer']['ans'];
	$is_cor = $_SESSION['rand']['answer']['is_cor'];
}
else
	$is_cor[0] = 'Y';
	
	for ($i=0; $i<$no_of_answer; $i++)
	{
	?>
            <div class="form-group">
              <label class="col-md-1"><input type="<?php echo $checkbox;?>" name="correct[]" value="<?php echo $i;?>"
							<?php 
							if ($is_cor[$i] == 'Y') echo " checked";?>> &nbsp;<?php echo NumberToAlpha($i+1);?></label>
              <div class="col-md-11">
  <?php
		$_SESSION['question-answer'][$i+1] = $ans[$i];

//	wysiwyg("answer[]",stripslashes($ans[$i]), 'exam-answer'); 
//  wysiwyg_answer("answer[]", stripslashes($ans[$i]), 'exam-answer',$i+1); 

  ?>
          <textarea data-id="<?php echo $i+1;?>" name="answer[]" class="form-control"><?php echo stripslashes($ans[$i]);?></textarea>

              </div>
            </div>
  <?php } 
  ?>
        </div>
      </fieldset>
  
      <div class="col-md-12" style="padding-top:15px;">
        <div class="col-md-9 col-md-offset-3">
          <button type="button" class="btn btn-info" name="btnCancel" id="btnCancel"><span class="fa fa-th-list"></span> Cancel Editing</button>
          <button type="submit" class="btn btn-success" name="btnUpdate"><span class="fa fa-save"></span> Update Question / Answer</button>
          <button type="button" class="btn btn-warning" name="btnCancel" id="btnCancel"><span class="fa fa-th-list"></span> List of Question / Answer</button>
          <input type="hidden" name="question_id" value="<?php echo $question_id;?>">
          <input type="hidden" name="rand" value="<?php echo $rand;?>">
        </div>
      </div>
    </div>
  </form>
</div>
<?php
wysiwyg_ajax($MAIN_URL,60,'exam-question');
wysiwyg_ajax_answer($MAIN_URL,40,'exam-answer');
?>

<form id="frmCancel" method="post"><input type="hidden" name="btnCancel"></form>
<style>
label > .form-control
{
	font-weight:normal !important;
}
.form-group
{
	margin-bottom:5px !important;
}
@media (min-width: 768px)
{
	.col-20
	{
		width:20% !important;
	}
	.col-80
	{
		width:80% !important;
	}
	.col-80 .panel
	{
		margin-bottom:0px;
	}
}
</style>
<script>
  $('select#answer_type').change(function()
  {
		function_data();
  });
  
  $('select#no_of_answer').change(function()
  {
		function_data();
  });

	function function_data()
	{
		$("div#answers").html("<div align=center><span class=\"fa fa-spinner fa-5x fa-spin\"></span><br><h3>Loading...</h3></div>");
		var no_of_ans= $("select#no_of_answer option:selected").val();
		var answer_type = $("select#answer_type option:selected").val();
		$.post("<?php echo $MAIN_URL;?>ajax.html",
		{
			no_of_answer: no_of_ans,
			answer_type: answer_type,
			question_id: "<?php echo $question_id;?>",
			type: "edit_question",
		},
		function(data,status){
			$("div#answers").html(data); 		
		});
	};
	
$("#btnCancel").click(function(e) {
  $("#frmCancel").submit();
});
</script>
