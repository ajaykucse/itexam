<?php 
@session_start();
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>";  

if (isset($_POST['btnAddQuestion']))
	echo "<script>window.location='add.html';</script>";
if (isset($_POST['btnList']))
{
	echo "<script>window.location='print.html';</script>";
}

if (isset($_POST['btnSaveQuestionReason']))
{
	$question_id = FilterNumber($_POST['question_id']);
	$question_reason = addslashes($_POST['question_reason']);
	$db->query("DELETE FROM exam_question_remarks WHERE question_id = '$question_id'");
	$strReasonInsert = "INSERT INTO exam_question_remarks (question_id, question_remarks) VALUES ('$question_id', '$question_reason');";
	$db->begin();
	$query  = $db->query($strReasonInsert);
	if ($query)
	{
		$db->commit();
		notify("INFORMATION", "<b>Reason for Disable Question is recorded.</b>",NULL,TRUE,5000); 
	}
	else
	{
		$db->rollback();
		notify("PROBLEM", "<b><font color=red>There is some problem while adding Reason for Disable Question. Please try later.</font></b>",NULL,TRUE,5000); 
	}
	
}

if (isset($_POST['btnReason']))
{
	$randomValue = ($_POST['rand']);
	if ($_SESSION['list_question'] == $randomValue)
	{
		$question_id = FilterNumber($_POST['question_id']);
		$random = FilterString($_POST['rand']);

?>
	<script>
  $(document).ready(function(e) {
    $('#reason_exam_modal').modal('show');
  });
  </script>
  
  <div class="modal fade" id="reason_exam_modal" tabindex="-1" role="dialog" aria-labelledby="reason_exam_modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h3 class="modal-title"><strong>Reason for Question Disable</strong></h3>
        </div>
        <form class="form-horizontal" method="post">
          <div class="modal-body">

              <div class="row">
                <div class="col-xs-12">
                  <div class="form-group">
                    <label class="col-xs-2"><strong>Reason </strong></label>
                    <div class="col-xs-10">
  <?php
  $strReason = "SELECT question_remarks FROM exam_question_remarks WHERE question_id='$question_id';";
  $question_reason = $db->query($strReason);
  $row_reason = $db->fetch_object($question_reason);
  $question_remarks = $row_reason->question_remarks;
  $db->free($question_reason);
  unset($strReason, $question_reason, $row_reason);
  ?>
                      <input class="form-control" name="question_reason" value="<?php echo $question_remarks;?>">
                  </div>
                </div>
              </div>
            </div>
          	
          </div>
          <div class="modal-footer">
            <input type="hidden" name="question_id" value="<?php echo $question_id;?>">
            <input type="hidden" name="rand" value="<?php echo $random;?>">
            <button type="submit" class="btn btn-primary" name="btnSaveQuestionReason">Save Reason</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php
	}
}

if (isset($_POST['btnChangeStatus']))
{
	$randomValue = ($_POST['rand']);
	if ($_SESSION['list_question'] == $randomValue)
	{

		$visible = (isset($_POST['visible']));
		$chapter = FilterNumber($_POST['chapter']);
     
		if(count($visible) > 0 )
		{
			if ($chapter > 0)
				$strUpdate = "UPDATE exam_question SET is_active = 'N' WHERE chapter_no = '$chapter';";
			else
				$strUpdate = "UPDATE exam_question SET is_active = 'N';";
			$db->query($strUpdate);
			foreach ($visible as $KEY => $value)
			{
				$strUpdate = "UPDATE exam_question SET is_active = 'Y' WHERE question_id = '$KEY';";
				$db->query($strUpdate);
			}
		}
  }
}

if (isset($_POST['btnShow']))
{
	$randomValue = ($_POST['rand']);
	if ($_SESSION['list_question'] == $randomValue)
	{
		$question_id = FilterNumber($_POST['question_id']);
		$update_question = "UPDATE exam_question SET is_active='Y' WHERE question_id = '$question_id'";
		$db->query($update_question);
	}
}
if (isset($_POST['btnHide']))
{
	$randomValue = ($_POST['rand']);
	if ($_SESSION['list_question'] == $randomValue)
	{
		$question_id = FilterNumber($_POST['question_id']);
		$update_question = "UPDATE exam_question SET is_active='N' WHERE question_id = '$question_id'";
		$db->query($update_question);
	}
}
?>
<?php
	$rand = RandomValue(20);
	$_SESSION['rand']['list_question']=$rand;
?>
<div class="page-header">
  <div class="row">
  	<div class="col-md-9">
      <h3 style="margin-top:0px; margin-bottom:0px;"><span class="fa fa-question-circle"></span> Question/Answer List</h3>
    </div>
    <div class="col-md-3 pull-right">    
      <form method="post" class="pull-right form-inline" action="add.html">
<?php
if (!$question_change)
  $disabled_add = " disabled=\"disabled\"";
?>
        <button type="submit" name="btnAddNew" id="btnAddNew" class="btn btn-primary"<?php 

        $disabled_add ='';
  if(isset($_POST['disabled_add']))
    $disabled_add = $_POST['disabled_add'];

        echo $disabled_add;unset($disabled_add);?>><span class="fa fa-plus-circle"></span> Add Question</button>
      </form>
    </div>
  </div>
</div>
<?php
if (isset($_POST['btnFilter']))
{
	$chapter = FilterNumber($_POST['chapter']);
	$status = FilterString($_POST['status']);
	$exam_type_id = FilterString($_POST['exam_type_id']);
}
if (!isset($chapter))$chapter = '0';
if (!isset($status)) $status = 'A';
if (!isset($exam_type_id)) $exam_type_id = '0';
?>
<div class="row">
	<div class="col-md-12 panel-body">
    <form class="form-inline pull-right" method="post">
    	<button class="btn btn-info" name="btnList"><i class="fa fa-list"></i>  List of All Question(s)</button>
    </form>

    <form class="form-inline pull-right" method="post">
<?php
$strChapter = "SELECT DISTINCT chapter_no FROM exam_question GROUP BY chapter_no ORDER BY chapter_no";
$query_chapter = $db->query($strChapter);
?>    
        <select name="exam_type_id" class="form-control">
          <option value="0"<?php if ($exam_type_id == "0") echo " selected=\"selected\"";?>>All Exam Type</option>
					<?php
          $strExamType = "SELECT * FROM exam_exam_type ORDER BY exam_type_id;";
          $query_exam_type = $db->query($strExamType);
          $no_of_exam_type = $db->num_rows($query_exam_type);
  
          while ($row_exam_type = $db->fetch_object($query_exam_type))
          {
          ?>
            <option value="<?php echo $row_exam_type->exam_type_id;?>"<?php if ($row_exam_type->exam_type_id ==$exam_type_id) echo " selected=\"selected\"";?>><?php echo $row_exam_type->exam_type;?></option>
          <?php
          }
          ?>
        </select>
        <select name="chapter" class="form-control">
          <option value="0"<?php if ($chapter == "0") echo " selected=\"selected\"";?>>All Chapter</option>
<?php
while ($row_chapter = $db->fetch_object($query_chapter))
{
	$chapter_no = $row_chapter->chapter_no;
	?>        
          <option value="<?php echo $chapter_no;?>"<?php if ($chapter_no == $chapter) echo " selected=\"selected\"";?>>Chapter <?php echo $row_chapter->chapter_no;?></option>
<?php  } 
$db->free($query_chapter);
unset($query_chapter,$row_chapter,$strChapter);
?>
        </select>

        <select name="status" class="form-control">
          <option value="A"<?php if ($status == "A") echo " selected=\"selected\"";?>>All Status</option>
          <option value="Y"<?php if ($status == "Y") echo " selected=\"selected\"";?>>Active Only</option>
          <option value="N"<?php if ($status == "N") echo " selected=\"selected\"";?>>Inactive Only</option>
        </select>

        <button class="btn btn-success" name="btnFilter"><i class="fa fa-search"></i> Filter</button>&nbsp;

    </form>
  </div>
</div>
<div class="row">
  <div class="col-lg-12">

<?php
$txtSearch='';  //removable
if ($chapter != "0") $txtSearch = " AND exam_question.chapter_no = '$chapter'";
if ($exam_type_id > 0)
{
  
	$txtSearch .= " AND exam_question.exam_type_id = '$exam_type_id'";
 
}
if ($status != "A") 
	$SQL = "
SELECT exam_question.*, exam_exam_type.exam_type
  FROM exam_question exam_question
       INNER JOIN exam_exam_type exam_exam_type
          ON (exam_question.exam_type_id = exam_exam_type.exam_type_id)	
	WHERE exam_question.is_active = '$status' $txtSearch
	ORDER BY exam_question.question_id					
					";
else
	$SQL = "
SELECT exam_question.*, exam_exam_type.exam_type
  FROM exam_question exam_question
       INNER JOIN exam_exam_type exam_exam_type
          ON (exam_question.exam_type_id = exam_exam_type.exam_type_id)	

WHERE (exam_question.is_active = 'Y' OR exam_question.is_active ='N') $txtSearch
	ORDER BY exam_question.question_id					
					";					

	$query_result = $db->query($SQL);

	$no_of_question = $db->num_rows($query_result);
	$rand = RandomValue(20);
	$_SESSION['list_question']=$rand;
	?>
    <div class="dataTable_wrapper">
      <form class="form-horizontal" method="post" id="frmList">
        <table class="table table-striped table-bordered table-hover" id="list_question" style="font-size:100%;">
          <thead>
            <tr>
              <th width="20">SN</th>
              <th>Question</th>
              <th>Answer(s)</th>
              <th width="30">Exam Type</th>
              <th width="30">Chapter</th>
              <th width="100">Properties</th>
              <th width="70"><div class="tooltip-demo"><button title="Select All" class="btn btn-primary btn-sm btn-circle" type="button" name="select-all" id="select-all"  data-toggle-tooltip="tooltip" data-placement="top"><i class="fa fa-check-square-o"></i></button>&nbsp;<button class="btn btn-warning btn-circle btn-sm" type="button" name="deselect-all" id="deselect-all" title="De-select all"  data-toggle-tooltip="tooltip" data-placement="top"><i class="fa fa-square-o"></i></button></div>
  </th>
              <th width="70">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
  <?php
  $i='0';
  while ($row = $db->fetch_object($query_result))
  {

  ?>
            <tr>
              <td width="20"><?php echo ++$i;?></td>
              <td><?php echo stripslashes($row->question); 
							if ($row->is_active != "Y")
							{
								$strReasonList = "SELECT * FROM exam_question_remarks WHERE question_id = '$row->question_id' ;";
								$query_remarks2 = $db->query($strReasonList);
								$no_of_remarks = $db->num_rows($query_remarks2);
								if ($no_of_remarks > 0)
								{
									$row_remark = $db->fetch_object($query_remarks2);
									echo "<label class=\"badge badge-inverse\">$row_remark->question_remarks</label>";
								}
							}
							
							?></td>
              <td><?php 
              $strAnswer = "SELECT * FROM exam_answer WHERE question_id = '$row->question_id' ORDER BY answer_id";
              $query_answer = $db->query($strAnswer);
              $no_of_answer = $db->num_rows($query_answer);
              if ($no_of_answer > 0)
              {
                while ($row_answer = $db->fetch_object($query_answer))
                {
                  if ($row_answer->is_correct == "Y") $checked = " checked = \"checked\"";
                  else $checked="";
                  
                  $answer = str_replace("\n","<br>",stripslashes($row_answer->answer));
                  if ($row->answer_type=="S")
                    echo "<table  class='table-checkbox' width=\"100%\" style=\"font-size:90%\"><tr><td valign=\"top\" width=\"20px\"><input type=\"radio\" disabled $checked></td><td>$answer</td></tr></table>";
                  
                  if ($row->answer_type=="M") 
                    echo "<table class='table-checkbox' width=\"100%\" style=\"font-size:100%\"><tr><td valign=\"top\"  width=\"20px\"><input type=\"checkbox\" disabled $checked></td><td>$answer</td></tr></table>";
                }
              }
              ?>
              </td>
              <td align="center"><?php echo $row->exam_type;?></td>
              <td width="30" align="center"><?php echo $row->chapter_no;?></td>
              <td width="100"><?php 
                if ($row->is_active != "Y") echo "<span class=\"label label-danger\">Not Active</span><br>";
                if ($row->rand_answer == 'Y')
                  echo "<i class=\"fa fa-random\" title=\"Shuffle Answer\" data-toggle-tooltip=\"tooltip\" data-placement=\"top\"></i>";
  
                if ($row->rand_answer != 'Y')
                  echo "<i class=\"fa fa-reorder\" title=\"No Shuffle Answer\" data-toggle-tooltip=\"tooltip\" data-placement=\"top\"></i>";
              echo " &nbsp;&nbsp;";
              if ($row->answer_type == 'M') echo "<label><input type=\"checkbox\" disabled> <input type=\"checkbox\" disabled></label>";
              else echo "<label><input type=\"radio\" disabled></label>";
              ?></td>
              <td>
<?php
$strQuestion = "SELECT * FROM exam_student_question WHERE question_id = '$row->question_id'";
$queryQuestion = $db->query($strQuestion);
$no_of_student_question = $db->num_rows($queryQuestion);
if ($no_of_student_question > 0) $disable_No_of_Question = " disabled=\"disabled\"";
else $disable_No_of_Question  = "";
?>
              
              <label><input type="checkbox" name="visible[<?php echo $row->question_id;?>]"<?php if ($row->is_active == 'Y') echo " checked=\"checked\""; echo $disable_No_of_Question; ?>> Visible</label>
<?php if ($row->is_active != "Y")
	{
		?><div class="tooltip-demo"><button title="Reasons for Disable" class="btn btn-primary btn-circle btn-xs reason" type="submit" id="btnReason" name="btnReason2"  data-id="<?php echo $row->question_id;?>" data-button="btnReason" data-class="btn btn-primary" data-input-name="question_id" data-target="#show_reason" data-toggle="modal" data-url=""  data-toggle-tooltip="tooltip" data-placement="top"><span class="fa fa-file-text-o"></span> </button></div>

    <?php } ?>
              
              </td>
              <td>
                <div class="tooltip-demo" style="width:65px;" align="center">
  <?php if ($row->is_active != "Y")
  {
    ?>
                    <button title="Make Active" class="btn btn-default btn-circle btn-sm active2" type="submit" id="btnShow2" name="btnDelete2"  data-id="<?php echo $row->question_id;?>" data-button="btnShow" data-class="btn btn-success" data-input-name="question_id" data-target="#show_modal" data-toggle="modal" data-url=""  data-toggle-tooltip="tooltip" data-placement="top"<?php echo $disable_No_of_Question ;?>><span class="fa fa-star"></span></button>

  <?php } ?>                
  <?php if ($row->is_active == "Y")
  {
    ?>
                    <button title="Make In-active" class="btn btn-success btn-circle btn-sm inactive2" type="submit" id="btnHide2" name="btnDelete2"  data-id="<?php echo $row->question_id;?>" data-button="btnHide" data-class="btn btn-warning" data-input-name="question_id" data-target="#hide_modal" data-toggle="modal" data-url=""  data-toggle-tooltip="tooltip" data-placement="top"<?php echo $disable_No_of_Question ;?>><span class="fa fa-star"></span></button>
  <?php } ?>
 
                    <button title="Edit" class="btn btn-primary btn-circle btn-sm edit" type="submit" id="btnEdit2" name="btnEdit2"  data-id="<?php echo $row->question_id;?>" data-button="btnEdit" data-class="btn btn-primary" data-input-name="question_id" data-target="#edit_modal" data-toggle="modal" data-url="edit.html"  data-toggle-tooltip="tooltip" data-placement="top"<?php echo $disable_No_of_Question;?>><span class="fa fa-pencil icon-white"></span></button>
  
                </div>
              </td>
            </tr>
  <?php } ?>
          </tbody>
  <?php if ($no_of_question > 0) { ?>        
          <tfoot>
            <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>
  
                 <button title="Change Status" class="btn btn-primary btn-xs change-question-status" type="submit" id="btnChangeStatus2" name="btnChangeStatus2" data-button="btnChangeStatus" data-class="btn btn-primary" data-target="#change-question-status-modal" data-toggle="modal" data-toggle-tooltip="tooltip" data-placement="top">Change Status</button>
            <input type="hidden" name="rand" value="<?php echo $rand;?>">
            <input type="hidden" name="chapter" value="<?php echo $chapter;?>">
            </td>
            <td></td>
            </tr>
          </tfoot>
  <?php } ?>
        </table>
        <small><strong>Note:</strong> Question can not be edit which is already shown to student.</small>
      </form>
    </div>
<?php 

if ($question_change)
{
confirm2("Question List","Do you want to change information of this question?","edit","edit_modal",$rand,"_edit_1");
confirm2("Question List","Do you want to make Inactive of this question? <br><br><font color=red>Inactive Question will not be asked to students.</font>","inactive2","hide_modal",$rand,2);
confirm2("Question List","Do you want to make Active this question?","active2","show_modal",$rand,3);

confirm2("Question List","Do you want to add Reasons for Disable this question?","reason","show_reason",$rand,"_reason");

			FormSubmit("Question List","Do you want to Change Status of selected Questions? <br><br><font 
      color=blue>Note: Only visible question will be asked.</font>","change-question-status","change-question-status-modal","frmList");
}
else
{
		no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","edit","edit_modal",$rand,"_edit_1");
		no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","inactive2","hide_modal",$rand,2);
		no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","active2","show_modal",$rand,3);
		no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","change-question-status","change-question-status-modal",$rand,4);

	
}
?>
<style>
.green
{
	color:green;
}
.red 
{
	color:red;
}
label
{
	font-weight:normal !important;
	
}
table.table-checkbox td > p
{
	margin:0 !important;
}
table img
{
	max-width:250px;
}
</style>
<?php 
dataTable("list_question");
?>
<script>
// tooltip demo
	$('.tooltip-demo').tooltip({
			selector: "[data-toggle-tooltip=tooltip]",
//			container: "body"
	});
	$('.tooltip-demo').click(function(e) {
    	e.preventDefault();
  });
</script>
<script>
$('#select-all').click(function(event) {   
	$(':checkbox').each(function() {
			this.checked = true;
	});
});
$('#deselect-all').click(function(event) {   
	$(':checkbox').each(function() {
			this.checked = false;
	});
});
</script>