<?php
@session_start();
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>";  

if (isset($_SESSION['add_exam_type']))
	unset($_SESSION['add_exam_type']);

	//FOR DELETING 
	if(isset($_POST['btnDelete']))
	{
		$randomValue = ($_POST['rand']);
		if ($_SESSION['rand']['list_exam_type'] == $randomValue)
		{
			$exam_type_id = FilterNumber($_POST['exam_type_id']);
			$strDeletePage="DELETE FROM exam_type_chapter_question WHERE exam_type_id='$exam_type_id'";

			$strDeletePage2="DELETE FROM exam_exam_type WHERE exam_type_id='$exam_type_id'";
			$db->begin();
			$query = $db->query($strDeletePage);
			$query2 = $db->query($strDeletePage2);
			if ($query && $query2)
			{
				notify("Exam Type List","Exam Type details has been deleted.",NULL,TRUE,5000);
				$db->commit();
			}
			else
			{
			notify("Exam Type List","<font color=red>Can not delete Exam Type details. <br><br>Child Records may exists.</font>",NULL,TRUE,5000);
				$db->rollback();
			}
		}
	}

?>
<div class="page-header">
  <div class="row">
  	<div class="col-md-9">
      <h3 style="margin-top:0px; margin-bottom:0px;"><span class="fa fa-wrench"></span> Exam Type List</h3>
    </div>
    <div class="col-md-3 pull-right">    
<?php
if (!$exam_type_change)
  $disabled_add = " disabled=\"disabled\"";
?>
      <form method="post" class="pull-right form-inline" action="add.html">
        <button type="submit" name="btnAddNew" id="btnAddNew" class="btn btn-primary"<?php

        $disabled_add ='';
          if(isset($_POST['disabled_add']))
            $disabled_add = $_POST['disabled_add'];

         echo $disabled_add;unset($disabled_add);?>><span class="fa fa-plus-circle"></span> Add New Exam Type</button>
      </form>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
<?php
	$SQL = "select * from exam_exam_type ";
	$query_result = $db->query($SQL);
	$rand = RandomValue(20);
	$_SESSION['rand']['list_exam_type']=$rand;
?>
    <div class="dataTable_wrapper">
      <table class="table  table-striped table-bordered table-hover tooltip-demo" id="list_exam_type" style="font-size:85%;">
        <thead>
          <tr>
            <th>SN</th>
            <th>Exam Type</th>
            <th>Total Question</th>
            <th>Total Time</th>
            <th>Full Mark</th>
            <th>Pass Mark</th>
            <th>MCQ Mark</th>
            <th>Practical Mark</th>
            <th>&nbsp;</th>
          </tr>
        </thead>
        <tbody>
<?php
while ($row = $db->fetch_object($query_result))
{
?>
          <tr>
            <td style="width:10px"><?php

            $SN ='';
          if(isset($_Get['SN']))
            $SN = $_Get['SN'];

             echo ++$SN;?></td>
            <td><?php echo $row->exam_type ;?></td>
            <td align="center"><?php echo 	$row->total_question;?> Questions
						<?php
						$str = "select sum(no_of_question) as `question_cnt` from exam_type_chapter_question WHERE exam_type_id = '$row->exam_type_id' GROUP BY exam_type_id;";
						$query_ques = $db->query($str);
						$row_ques = $db->fetch_object($query_ques);

          $question_cnt ='';
          if(isset($_Get['question_cnt'])){
            $question_cnt = $_Get['question_cnt'];


						$question_cnt = $row_ques->question_cnt;
          }
						$db->free($query_ques);
						unset($str, $query_ques, $row_ques);
						if ($question_cnt != $row->total_question) echo " <br> <span class=\"label label-danger\">Total Questions and question <br> per chapter does not matched.</span>";
						?>
            </td>
            <td align="center"><?php 
        if ($row->total_time > 60) 
        {
          $reminder = $row->total_time % 60;
          $hour = ($row->total_time - $reminder) / 60;
					if ($reminder > 0)
						echo "$hour hour(s) $reminder minutes";
					else
						echo "$hour hour(s)";
        }
        else
        echo " $row->total_time minutes";?></td>
            <td align="center"><?php echo $row->full_mark;?></td>
            <td align="center"><?php echo $row->pass_mark;?></td>
            <td align="center"><?php echo $row->mcq_mark;?></td>
            <td align="center"><?php echo $row->practical_mark;?></td>
            <td>
              <button title="View Details" class="btn btn-primary btn-circle btn-sm view" type="submit" id="btnView2" name="btnView2" data-id="<?php echo $row->exam_type_id;?>" data-toggle-tooltip="tooltip" data-placement="top" data-toggle="modal" data-target="#exam_view"><span class="fa fa-eye icon-white"></span></button>

              <button title="Question Per Chapter" class="btn btn-success btn-circle btn-sm question" type="submit" id="btnQuestion2" name="btnQuestion2" data-id="<?php echo $row->exam_type_id;?>" data-button="btnQuestion" data-class="btn btn-success" data-input-name="exam_type_id" data-target="#question_modal" data-toggle="modal" data-url="question.html" data-toggle-tooltip="tooltip" data-placement="top"><span class="fa fa-question-circle icon-white"></span></button>

              <button title="Edit Exam Type" class="btn btn-info btn-circle btn-sm edit" type="submit" id="btnEdit2" name="btnEdit2" data-id="<?php echo $row->exam_type_id;?>" data-button="btnEdit" data-class="btn btn-info" data-input-name="exam_type_id" data-target="#edit_modal" data-toggle="modal" data-url="edit.html" data-toggle-tooltip="tooltip" data-placement="top"><span class="fa fa-pencil icon-white"></span></button>

              <button title="Delete Exam Type" class="btn btn-danger btn-circle btn-sm delete" type="submit" id="btnRemoveLogin2" name="btnRemoveLogin2"  data-id="<?php echo $row->exam_type_id;?>" data-button="btnDelete" data-class="btn btn-danger" data-input-name="exam_type_id" data-target="#delete-modal" data-toggle="modal" data-url="" data-toggle-tooltip="tooltip" data-placement="top"><span class="fa fa-trash"></span></button>
            </td>
          </tr>
<?php  }

if ($exam_type_change)
{
	confirm2("Exam List","Do you want to <b>Change Exam Type</b>?","edit","edit_modal",$rand,1);
	confirm2("Exam List","Do you want to <b>Delete Exam Type</b>?","delete","delete-modal",$rand,7);
}
else
{
		no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","edit","edit_modal",$rand,1);

		no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","delete","delete-modal",$rand,7);
}
if ($exam_type_question)
	confirm2("Exam List","Do you want to <b>View/Change Chapter and NO. of Question</b> for this Exam Type?","question","question_modal",$rand, "_question");
else
		no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","question","question_modal",$rand, "_question");

?>
        </tbody>
      </table>
<script>
$('button.view').click(function(ev){
	ev.preventDefault();
	$('#exam_view').removeData('bs.modal')
	$('div#exam_view .modal-body').css('max-height', $(window).height() * 0.5);
	$('div#exam_view .modal-body').css('overflow-y', 'auto'); 
	var ID = $(this).data('id');
	$.post("<?php echo $MAIN_URL;?>ajax.html",
	{
	  exam_type_id: ID,
		task:"view_exam_type",
	},
	function(data,status){
		$("div#exam_view .modal-body").html(data); 		
	});
});
</script>

      <div id="exam_view" class="modal fade">
        <div class="modal-dialog" style="margin-top:-30px !important;">
          <div class="modal-content">
            <div class="modal-header">
                <button data-dismiss="modal" aria-hidden="true" class="close">×</button>
                <h4 class="modal-title"><font size="+2"><strong>Exam Details</strong></font></h4>
            </div>
            <div class="modal-body">
                 <p><?php echo $text;?></p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>


    </div>
  </div>
</div>
<?php
dataTable("list_exam_type");
?>
<script>
// tooltip demo
	$('.tooltip-demo').tooltip({
			selector: "[data-toggle-tooltip=tooltip]",
			container: "body"
	});
	$('.tooltip-demo').click(function(e) {
    	e.preventDefault();
  });
</script>