<?php
@session_start();
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>"; 

if (!isset($_POST['exam_type_id'])) 
	echo "<script>window.location='list.html';</script>"; 

if (isset($_POST['btnBack']))
	echo "<script>window.location='list.html';</script>"; 

$exam_type_id = FilterNumber($_POST['exam_type_id']);

if (isset($_POST['btnSaveMessage']))		// Save Messages
{

	$start_message = addslashes(TextEditorRemove($_POST['start_message']));
	$end_message = addslashes(TextEditorRemove($_POST['end_message']));
	$start_message = addslashes(TextEditorRemove($_POST['start_message']));
	$exam_type = addslashes($_POST['exam_type']);
	$full_mark = FilterNumber($_POST['full_mark']);
	$pass_mark = FilterNumber($_POST['pass_mark']);
	$total_question = FilterNumber($_POST['total_question']);
	$total_time = FilterNumber($_POST['total_time']);
	$mcq_mark = FilterNumber($_POST['mcq_mark']);
	$practical_mark = FilterNumber($_POST['practical_mark']);
	$end_message = addslashes(TextEditorRemove($_POST['end_message']));

		if (strlen($start_message) == 0)
		{
				$isError = TRUE;
				$error[] = "Exam Start Information is blank.";	
		}

		if (strlen($exam_type) == 0)
		{
				$isError = TRUE;
				$error[] = "Exam Type is blank.";	
		}
		else
		{
			$select_exam_code = "SELECT exam_type FROM exam_exam_type WHERE exam_type = UPPER('$exam_type') AND exam_type_id <> '$exam_type_id';";
			$query_exam_code = $db->query($select_exam_code);
			$no_of_exam_code = $db->num_rows($query_exam_code);
			if ($no_of_exam_code > 0)
			{
				$isError = TRUE;
				$error[] = "Exam type is already exist.<br>Please enter new Exam type.";	
			}
		}

		if ($full_mark != ($mcq_mark + $practical_mark) )
		{
				$isError = TRUE;
				$error[] = "Full Mark is not equal to MCQ Mark + Practical Mark.";	
		}
		if ($pass_mark == 0)
		{
				$isError = TRUE;
				$error[] = "Pass Mark can not be Zero (0).";	
		}

		if (strlen($end_message) == 0)
		{
				$isError = TRUE;
				$error[] = "Exam Ending Message is blank.";	
		}

	if (!$isError)
	{
		$sql_update = "UPDATE exam_exam_type SET 
		start_message = '$start_message',
		end_message = '$end_message',
		exam_type = '$exam_type',
		full_mark = '$full_mark',
		pass_mark = '$pass_mark',
		total_question = '$total_question',
		total_time = '$total_time',
		mcq_mark = '$mcq_mark',
		practical_mark = '$practical_mark'

		WHERE exam_type_id = '$exam_type_id'
		";
		$db->query($sql_update);
	}
}

if (isset($_POST['btnSaveExam']))
{
	$exam_type = addslashes($_POST['exam_type']);
	$full_mark = FilterNumber($_POST['full_mark']);
	$pass_mark = FilterNumber($_POST['pass_mark']);
	$total_time = FilterNumber($_POST['total_time']);
	$mcq_mark = FilterNumber($_POST['mcq_mark']);
	$practical_mark = FilterNumber($_POST['practical_mark']);

	if (strlen($exam_type) == 0)
	{
			$isError = TRUE;
			$error[] = "Exam Type is blank.";	
	}
	else
	{
		$select_exam_type = "SELECT exam_type FROM exam_exam_type WHERE exam_type = UPPER('$exam_type') and exam_type_id <> '$exam_type_id';";
		$query_exam_type = $db->query($select_exam_type);
		$no_of_exam_type = $db->num_rows($query_exam_type);
		if ($no_of_exam_type > 0)
		{
			$isError = TRUE;
			$error[] = "Exam Type is already exist.<br>Please enter new Exam Type.";	
		}
	}
	if ($full_mark != ($mcq_mark + $practical_mark) )
	{
			$isError = TRUE;
			$error[] = "Marks are not equal.";	
	}
	if (!$isError)
	{
		$sql_update = " UPDATE exam_exam_type SET
		exam_type = '$exam_type',
		full_mark = '$full_mark',
		pass_mark = '$pass_mark',
		total_time = '$total_time',
		mcq_mark = '$mcq_mark',
		practical_mark = '$practical_mark'
		WHERE exam_type_id = '$exam_type_id'";
		$db->query($sql_update);
	}
} // btnSaveExam

if (isset($_POST['btnSaveChapter']))
{
	$total_question = FilterNumber($_POST['total_question']);
	$chapter = $_POST['chapter'];
	$total_question = FilterNumber($_POST['total_question']);
	$no_of_question = $_POST['no_of_question'];
	$curr_question_total = FilterNumber($_POST['curr_question_total']);

	if ($total_question != $curr_question_total )
	{
			$isError = TRUE;
			$error[] = "No. of Total Question and Sum of Chapterwise question are not equal.";	
	}
	if (!$isError)
	{
		$sql_update = " UPDATE exam_exam_type SET
		total_question = '$total_question'
		WHERE exam_type_id = '$exam_type_id'";
		$db->query($sql_update);

		$db->query("DELETE FROM exam_type_chapter_question WHERE exam_type_id = '$exam_type_id';");
		if (count($chapter) > 0)
		{
			foreach ($chapter as $KEY => $value)
			{
				$no_ques = FilterNumber($no_of_question[$KEY]);
				if ($no_ques > 0)
				{
					$sql_insert_chapter_question = "INSERT INTO exam_type_chapter_question (exam_type_id, chapter_no, no_of_question) VALUES ('$exam_type_id','$KEY','$no_ques');";
					$query_chapter = $db->query($sql_insert_chapter_question);
				}
			}
		}
	}
} //btnSaveChapter
$sError='';
 if(isset($_Get['isError'])){
            $isError = $_Get['isError'];

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
}	

}
	// if error
?>
<div class="page-header">
  <div class="row">
  	<div class="col-md-9">
      <h3 style="margin-top:0px; margin-bottom:0px;">Edit Existing Exam Type</h3>
    </div>
    <div class="col-md-3 pull-right">    
      <button class="btn btn-warning pull-right" id="btnBack" name="btnBack"><i class="fa fa-rotate-left"></i> Exam Type List</button>
      <script>
      $("#btnBack").click(function(e) {
        window.location='list.html';      
      });
      </script>
    </div>
  </div>
</div>
  <?php
	$rand = RandomValue(20);
	$_SESSION['edit_exam_type']['rand']=$rand;	

	wysiwyg_script($MAIN_URL);

	$sql_exam = "SELECT * FROM exam_exam_type WHERE exam_type_id = '$exam_type_id';";
	$query_exam = $db->query($sql_exam);
	$row = $db->fetch_object($query_exam);

	?>
<?php
wysiwyg_script($MAIN_URL);
?>
<div class="row">
	<div class="col-md-12">
  	<form class="form-horizontal" method="post">
    	<div class="row">
      	<div class="col-md-6">
          <fieldset>
            <legend>Exam Start Instruction</legend>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <div class="col-md-12">
    <?php
wysiwyg("start_message", stripslashes($row->start_message), 'start-message'); 
    ?>
                  </div>
                </div>
              </div>
            </div>
          </fieldset>

          <fieldset>
            <legend>Exam Ending Message</legend>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <div class="col-md-12">
    <?php
    // wysiwyg($txtAreaName, $txtValue = NULL)
    wysiwyg("end_message", stripslashes($row->end_message), 'end-message'); 
    ?>
    <style>
    .editor
    {
      min-height:180px !important;
    }
    </style>
                  </div>
                </div>
              </div>
            </div>
          </fieldset>
        </div>
        <div class="col-md-6">
          <fieldset>
            <legend>Exam Type Information</legend>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="col-md-6">Exam Module Name / Type<font color="red"> *</font></label>
                  <div class="col-md-6">
                  <input type="text" name="exam_type" id="exam_type" class="form-control" value="<?php echo $row->exam_type;?>">
                  </div>
                </div>
              </div>
    
              <div class="col-md-12">
                <div class="form-group">
                  <label class="col-md-6">Total Question</label>
                  <div class="col-md-6">
                    <select class="form-control" name="total_question">
                      <?php 
                      for($ii=5;$ii<=150;$ii +=5)
                      {
                      ?>
                        <option value="<?php echo $ii;?>"<?php if ($ii==$row->total_question) echo " selected=\"selected\"";?>><?php echo $ii;?> Questions</option>
                      <?php }
                      ?>
                    </select>
                  </div>
                </div>
              </div>
    
              <div class="col-md-12">
                <div class="form-group">
                  <label class="col-md-6">Total Time</label>
                  <div class="col-md-6">
                    <select class="form-control" name="total_time">
                      <?php 
                      for($ii=10;$ii<=180;$ii +=5)
                      {
                      ?>
                        <option value="<?php echo $ii;?>"<?php if ($ii==$row->total_time) echo " selected=\"selected\"";?>><?php echo $ii;?> Minutes</option>
                      <?php }
                      ?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div class="form-group">
                  <label class="col-md-6">Full Mark</label>
                  <div class="col-md-6">
                    <select class="form-control" name="full_mark" id="full_mark">
                      <?php 
                      for($i=5;$i<=100;$i +=5)
                      {
                      ?>
                        <option value="<?php echo $i;?>"<?php if ($i==$row->full_mark) echo " selected=\"selected\"";?>><?php echo $i;?></option>
                      <?php }
                      ?>
                    </select>
                  </div>
                </div>
              </div>
    
              <div class="col-md-12">
                <div class="form-group">
                  <label class="col-md-6">Pass Mark</label>
                  <div class="col-md-6">
                    <select class="form-control" name="pass_mark">
                      <?php 
                      for($ii=5;$ii<=100;$ii +=5)
                      {
                      ?>
                        <option value="<?php echo $ii;?>"<?php if ($ii==$row->pass_mark) echo " selected=\"selected\"";?>><?php echo $ii;?></option>
                      <?php }
                      ?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div class="form-group">
                  <label class="col-md-6">Total MCQ Mark</label>
                  <div class="col-md-6">
                    <select class="form-control" name="mcq_mark" id="mcq_mark">
                      <?php 
                      for($ii=0;$ii<=100;$ii +=5)
                      {
                      ?>
                        <option value="<?php echo $ii;?>"<?php if ($ii==$row->mcq_mark) echo " selected=\"selected\"";?>><?php echo $ii;?></option>
                      <?php }
                      ?>
                    </select>
                  </div>
                </div>
              </div>
    
              <div class="col-md-12">
                <div class="form-group">
                  <label class="col-md-6">Total Practical Mark</label>
                  <div class="col-md-6">
                    <select class="form-control" name="practical_mark" id="practical_mark">
                      <?php 
                      for($ii=0;$ii<=100;$ii +=5)
                      {
                      ?>
                        <option value="<?php echo $ii;?>"<?php if ($ii==$row->practical_mark) echo " selected=\"selected\"";?>><?php echo $ii;?></option>
                      <?php }
                      ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
    
    <script>
    $(document).ready(function(e) {
      update();
    });
    $("#mcq_mark").change(function(e) {
      update();
    });
    $("#full_mark").change(function(e) {
      update();
    });
    
    $("#practical_mark").change(function(e) {
      update();
    });
    
    function update()
    {
    
      var full_mark = $("#full_mark :selected").val();
      var mcq = $("#mcq_mark :selected").val();
      var practical = $("#practical_mark :selected").val();
      
      var total = parseInt(mcq,10) + parseInt(practical,10);
    
      if (full_mark == total)
      {
        $("#btnAddType2").removeClass("disabled");
        $("#btnAddType2").removeAttr("disabled");
      }
      else
      {
        $("#btnAddType2").addClass("disabled");
        $("#btnAddType2").attr("disabled","disabled");
      }
    };
    </script>
    
      <div align="center">
        <input type="hidden" value="<?php echo $rand;?>" name="rand">
        <input type="hidden" name="exam_type_id" value="<?php echo $exam_type_id;?>">

        <button class="btn btn-success" name="btnSaveMessage"><i class="fa fa-save"></i> Save Exam Type</button>

      </div>

          </fieldset>
        </div>
      </div>

    </form>
  </div>    
</div>


<style>
.margin-top-10
{
	margin-top:10px;
}
.table
{
	width:95%;
	float:right !important;
	clear:both;
}
</style>
<?php
	wysiwyg_ajax($MAIN_URL,100,'start-message');
	wysiwyg_ajax($MAIN_URL,100,'end-message');
?>