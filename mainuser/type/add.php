<?php
@session_start();
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>"; 

if (isset($_POST['btnAddType']))
{	
	$random = FilterString($_POST['rand']);
	if ($_SESSION['add_exam_type']['rand'] == $random)
	{	


    $start_message ='';
          if(isset($_POST['start_message'])){
            $start_message = $_POST['start_message'];

		$start_message = addslashes(TextEditorRemove($_POST['start_message']));

  }
		$exam_type = addslashes($_POST['exam_type']);
		$full_mark = FilterNumber($_POST['full_mark']);
		$pass_mark = FilterNumber($_POST['pass_mark']);
		$total_question = FilterNumber($_POST['total_question']);
		$total_time = FilterNumber($_POST['total_time']);
		$mcq_mark = FilterNumber($_POST['mcq_mark']);
		$practical_mark = FilterNumber($_POST['practical_mark']);

 $start_message ='';
          if(isset($_POST['start_message'])){
            $start_message = $_POST['start_message'];

		$end_message = addslashes(TextEditorRemove($_POST['end_message']));

  }

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
			$select_exam_code = "SELECT exam_type FROM exam_exam_type WHERE exam_type = ('$exam_type');";
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

    $end_message ='';
          if(isset($_POST['end_message']))
            $end_message = $_POST['end_message'];

		if (strlen($end_message) == 0)
		{
				$isError = TRUE;
				$error[] = "Exam Ending Message is blank.";	
		}
		
		if (!$isError)
		{
			//INSERTING DATA TO exam_exam_type TABLE	
			$sql_exam_id = "SELECT exam_type_id FROM exam_exam_type ORDER BY exam_type_id DESC LIMIT 0,1;";
			$query_id = $db->query($sql_exam_id);
			$row_id = $db->fetch_object($query_id);
			$exam_type_id = $row_id->exam_type_id + 1;
			
			$sql_insert_exam = "INSERT INTO exam_exam_type (exam_type_id, exam_type, total_question, full_mark, pass_mark, total_time, mcq_mark, practical_mark, start_message, end_message) VALUES ('$exam_type_id', '$exam_type',  '$total_question', '$full_mark', '$pass_mark', '$total_time', '$mcq_mark', '$practical_mark','$start_message', '$end_message');";
	
			$db->begin();
			$query_insert = $db->query($sql_insert_exam);
	
			if ($query_insert)
			{
				$db->commit();
				notify("Exam Type", "<b>New Exam Type has been added.</b>","list.html",TRUE,5000); 
				include_once "footer.inc.php";
				exit();
			}
			else
			{
				$db->rollback();
				$isError = TRUE;
				$error[] = "There is some problem while Creating Exam Type.";
			}
		}
	}

 $isError ='';
          if(isset($_POST['isError']))
            $isError = $_POST['isError'];

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
}
$rand = RandomValue(20);
$_SESSION['add_exam_type']['rand']=$rand;	

?>
<div class="page-header">
  <div class="row">
  	<div class="col-md-9">
      <h3 style="margin-top:0px; margin-bottom:0px;"> Add Exam Type</h3>
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

    $start_message ='';
          if(isset($_POST['start_message']))
            $start_message = $_POST['start_message'];

    wysiwyg("start_message", stripslashes($start_message), 'start-message'); 
    
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
    //wysiwyg($txtAreaName, $txtValue = NULL)
     $end_message ='';
          if(isset($_POST['end_message']))
            $end_message = $_POST['end_message'];

    wysiwyg("end_message", stripslashes($end_message),  'end-message'); 
    

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
                  <input type="text" name="exam_type" id="exam_type" class="form-control" autocomplete="off" value="<?php 


                    $exam_type ='';
          if(isset($_POST['exam_type']))
            $exam_type = $_POST['exam_type'];

                  echo $exam_type;?>">
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
                        <option value="<?php echo $ii;?>"<?php


                        $total_question ='';
          if(isset($_Get['total_question']))
            $total_question = $_Get['total_question']; 


                         if ($ii==$total_question) echo " selected=\"selected\"";?>><?php echo $ii;?> Questions</option>
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
                        <option value="<?php echo $ii;?>"<?php

                                    $total_time ='';
          if(isset($_Get['total_time']))
            $total_time = $_Get['total_time']; 

                         if ($ii==$total_time) echo " selected=\"selected\"";?>><?php echo $ii;?> Minutes</option>
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
                        <option value="<?php echo $i;?>"<?php

          $full_mark ='';
          if(isset($_Get['full_mark']))
            $full_mark = $_Get['full_mark']; 

                         if ($i==$full_mark) echo " selected=\"selected\"";?>><?php echo $i;?></option>
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
                        <option value="<?php echo $ii;?>"<?php

          $pass_mark ='';
          if(isset($_Get['pass_mark']))
            $pass_mark = $_Get['pass_mark'];

                         if ($ii==$pass_mark) echo " selected=\"selected\"";?>><?php echo $ii;?></option>
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
                        <option value="<?php echo $ii;?>"<?php

        $mcq_mark ='';
          if(isset($_Get['mcq_mark']))
            $mcq_mark = $_Get['mcq_mark'];

                         if ($ii==$mcq_mark) echo " selected=\"selected\"";?>><?php echo $ii;?></option>
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
                        <option value="<?php echo $ii;?>"<?php


                        $practical_mark ='';
          if(isset($_Get['practical_mark']))
            $practical_mark = $_Get['practical_mark'];

                         if($ii==$practical_mark) echo " selected=\"selected\"";?>><?php echo $ii;?></option>
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
    
      <div>
        <button title="Add New Exam Type" class="btn btn-success create_exam" style="margin-left:33%;" type="submit" id="btnAddType2" name="btnAddType2"  data-id="<?php echo (isset($step));?>" data-button="btnAddType" data-class="btn btn-success" data-input-name="step" data-target="#create_exam" data-toggle="modal" data-url=""  data-toggle-tooltip="tooltip" data-placement="top">Add New Exam Type</button>
        
        <input type="hidden" value="<?php echo $rand;?>" name="rand">
        <?php confirm2("Exam Type","All the information is correct?<br>If no, please click on \"No\"","create_exam","create_exam",$rand,1); ?>
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