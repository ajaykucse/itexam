<?php
@session_start();
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>";  

if (isset($_SESSION['add_exam'])) unset ($_SESSION['add_exam']);

$max_adjust_time = 30; //Maximum Time Adjustment
?>
<?php

// AttendanceSheet
if (isset($_POST['btnAttendanceSheet']))
{
$randomValue = ($_POST['rand']);
if ($_SESSION['rand']['list_exam'] == $randomValue)
{
	$exam_id = FilterNumber($_POST['exam_id']);
	$center_id = FilterNumber($_POST['center_id']);
?>
<script>
$(document).ready(function(e) {
	$('#center_print_result_modal').modal('show');
});
</script>

<div class="modal fade" id="center_print_result_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    	<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Exam Result</h4>
      </div>
      <div class="modal-body">
        <div id="PrintStudent">
          <div align=center><span class="fa fa-spinner fa-5x fa-spin"></span><br><h3>Loading...</h3></div>
        </div>
      </div>
        <div class="modal-footer">
          <button type="button" id="btnPrintStudent" class="btn btn-primary" tabindex="-1" onClick="javascript:PrintDiv('PrintStudent')"><i class="fa fa-print"></i> Print Attendance Sheet</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
  </div>
</div>

<script>
	$('#center_print_result_modal').on('shown.bs.modal', function (ev) {
		ev.preventDefault();
		$('#center_print_result_modal').removeData('bs.modal')
		$('div#center_print_result_modal .modal-body').css('max-height', $(window).height() * 0.5);
		$('div#center_print_result_modal .modal-body').css('overflow-y', 'auto'); 
		$.post("<?php echo $MAIN_URL;?>ajax.html",
		{
			exam_id: <?php echo $exam_id;?>,
			center_id: <?php echo $center_id;?>,
			task:"attendance-sheet",
			user:"admin",
		},
		function(data,status){
			$("div#PrintStudent").html(data); 		
		});
	});
</script>

<script type="text/javascript">
function PrintDiv(div)
{
	var divToPrint = document.getElementById(div);
	var popupWin = window.open('','_blank','width=800,height=600');
	popupWin.document.open();
	popupWin.document.write('<html><head><link href="<?php echo $MAIN_URL;?>print.css" rel="stylesheet" type="text/css" />\n <link href="<?php echo $MAIN_URL;?>bootstrap/css/bootstrap_none.css" rel="stylesheet" type="text/css" />\n	<style>.print * { visibility: visible !important; } </style>\n</head>\n<body>\n' + "<div class='print'>\n" +divToPrint.innerHTML + '\n</div></body>\n</html>');
//	popupWin.window.print();
//	popupWin.window.close();
}
</script>

<?php
		} //random
	}

// Result Publish
if (isset($_POST['btnPublishResult']))
{
$randomValue = ($_POST['rand']);
if ($_SESSION['rand']['list_exam'] == $randomValue)
{
	$exam_id = FilterNumber($_POST['exam_id']);
	$center_id = FilterNumber($_POST['center_id']);
?>
<script>
$(document).ready(function(e) {
	$('#center_print_result_modal').modal('show');
});
</script>

<div class="modal fade" id="center_print_result_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    	<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Exam Result</h4>
      </div>
      <div class="modal-body">
        <div id="PrintStudent">
          <div align=center><span class="fa fa-spinner fa-5x fa-spin"></span><br><h3>Loading...</h3></div>
        </div>
      </div>
        <div class="modal-footer">
          <button type="button" id="btnPrintStudent" class="btn btn-success" tabindex="-1" onClick="javascript:PrintDiv('PrintStudent')"><i class="fa fa-print"></i> Print Result</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
  </div>
</div>

<script>
	$('#center_print_result_modal').on('shown.bs.modal', function (ev) {
		ev.preventDefault();
		$('#center_print_result_modal').removeData('bs.modal')
		$('div#center_print_result_modal .modal-body').css('max-height', $(window).height() * 0.5);
		$('div#center_print_result_modal .modal-body').css('overflow-y', 'auto'); 
		$.post("<?php echo $MAIN_URL;?>ajax.html",
		{
			exam_id: <?php echo $exam_id;?>,
			center_id: <?php echo $center_id;?>,
			task:"publish-result",
			user:"admin",
		},
		function(data,status){
			$("div#PrintStudent").html(data); 		
		});
	});
</script>

<script type="text/javascript">
function PrintDiv(div)
{
	var divToPrint = document.getElementById(div);
	var popupWin = window.open('','_blank','width=800,height=600');
	popupWin.document.open();
	popupWin.document.write('<html><head><link href="<?php echo $MAIN_URL;?>print.css" rel="stylesheet" type="text/css" />\n <link href="<?php echo $MAIN_URL;?>bootstrap/css/bootstrap_none.css" rel="stylesheet" type="text/css" />\n	<style>.print * { visibility: visible !important; } </style>\n</head>\n<body>\n' + "<div class='print'>\n" +divToPrint.innerHTML + '\n</div></body>\n</html>');
//	popupWin.window.print();
//	popupWin.window.close();
}
</script>

<?php
		} //random
	}

if (isset($_POST['SaveCenterExamCode']))
{
	$exam_code = addslashes(FilterString($_POST['txt_center_exam_code']));
	$center_id = FilterNumber($_POST['center_id']);
	$exam_id = FilterNumber($_POST['exam_id']);
	$is_exist = FilterNumber($_POST['is_exist']);

	if ($is_exist == 0)
		$strExamCodeUnique = "SELECT exam_code FROM `exam_exam_center` WHERE exam_code IS NOT NULL AND exam_code = UPPER('$exam_code')
UNION 
select exam_code from exam_exam WHERE exam_code = UPPER('$exam_code')";
	else
		$strExamCodeUnique = "
SELECT exam_code FROM `exam_exam_center` WHERE exam_code = UPPER('$exam_code') AND exam_id <> '$exam_id' AND center_id <> '$center_id'
UNION 
select exam_code from exam_exam		
		WHERE exam_code = UPPER('$exam_code') AND exam_id <> '$exam_id' ";

	$query_existing_code = $db->query($strExamCodeUnique);
	$no_of_existing_code  = $db->num_rows($query_existing_code);
	if ($no_of_existing_code > 0)
	{
		$isError = TRUE;
		$error[] = "Exam Code is already exist. Please enter New Exam Code.";
	}
	if (!$isError)
	{
		if (strlen($exam_code) ==0)
			$strInsertExamCode = "UPDATE exam_exam_center SET exam_code = NULL WHERE exam_id='$exam_id' AND center_id = '$center_id';";
		else
			$strInsertExamCode = "UPDATE exam_exam_center SET exam_code = '$exam_code' WHERE exam_id='$exam_id' AND center_id = '$center_id';";
		
		$db->query($strInsertExamCode );

	}

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

if (isset($_POST['btnChangeExamCode']))		// Change Exam Code for Multiple Center Dialog Box
{
	$random = FilterString($_POST['rand']);
	if ($_SESSION['rand']['list_exam'] == $random)
	{
		$center_id = FilterNumber($_POST['center_id']);
		$exam_id = FilterNumber($_POST['exam_id']);

	?>
	<script>
  $(document).ready(function(e) {
    $('#center_exam_code_modal').modal('show');
  });
  </script>
  
  <div class="modal fade" id="center_exam_code_modal" tabindex="-1" role="dialog" aria-labelledby="center_exam_code_modal">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Change Exam Code for Exam Center</h4>
        </div>
        <form class="form-horizontal" method="post">
          <div class="modal-body">
              <div class="row">
                <div class="col-xs-12">
                  <div class="form-group">
                    <label class="col-xs-6">Exam Code</label>
                    <div class="col-xs-6">
  <?php
  $strCode = "SELECT exam_code FROM exam_exam_center WHERE exam_id='$exam_id' AND center_id = '$center_id';";
  $query_code = $db->query($strCode);
  $row_code = $db->fetch_object($query_code);
  $exam_code = $row_code->exam_code;
	if (strlen($exam_code)> 0) $is_exist = 1;
	else $is_exist = 0;
  $db->free($query_code);
  unset($strCode, $query_code, $row_code);
  ?>
                      <input class="form-control" name="txt_center_exam_code" value="<?php echo $exam_code;?>">
                  </div>
                </div>
              </div>
          </div>
          </div>
          <div class="modal-footer">
            <input type="hidden" name="exam_id" value="<?php echo $exam_id;?>">
            <input type="hidden" name="center_id" value="<?php echo $center_id;?>">
            <input type="hidden" name="rand" value="<?php echo $random;?>">
            <input type="hidden" name="is_exist" value="<?php echo $is_exist;?>">
            <button type="submit" class="btn btn-info" name="SaveCenterExamCode">Save Exam Code for Center</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php
  }
}
if (isset($_POST['SaveExtraTime']))
{
	$center_id = FilterNumber($_POST['center_id']);
	$exam_id = FilterNumber($_POST['exam_id']);
	$grace_time = FilterNumber($_POST['txt_extra_time']);
	$grace_reason = FilterString($_POST['txt_extra_time_reason']);
	if ($grace_time == "0")
		$str_update = "UPDATE exam_exam_status SET grace_time=0, grace_reason = NULL WHERE exam_id ='$exam_id'  AND center_id = '$center_id';";
	else
		$str_update = "UPDATE exam_exam_status SET grace_time='$grace_time', grace_reason = '$grace_reason' WHERE exam_id ='$exam_id' AND center_id = '$center_id';";

	$db->query($str_update);
}

if (isset($_POST['btnExtraTime']))		// Add Extra Time
{
	$random = FilterString($_POST['rand']);
	if ($_SESSION['rand']['list_exam'] == $random)
	{
		$exam_id = FilterNumber($_POST['exam_id']);
		$center_id = FilterNumber($_POST['center_id']);
		$str_extra_time = "SELECT * FROM exam_exam_status WHERE exam_id = '$exam_id' AND center_id = '$center_id';";
		$query_extra_time = $db->query($str_extra_time);
		$row_extra_time = $db->fetch_object($query_extra_time);
		$extra_time = $row_extra_time->grace_time;
		$extra_time_reason = $row_extra_time->grace_reason;
		$db->free($query_extra_time);
		unset($str_extra_time,$query_extra_time,$row_extra_time);
?>
<script>
$(document).ready(function(e) {
	$('#extra_time_modal').modal('show');
});
</script>

<div class="modal fade" id="extra_time_modal" tabindex="-1" role="dialog" aria-labelledby="extra_time_modal">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
    	<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Extra Time Adjustment</h4>
      </div>
      <form class="form-horizontal" method="post">
        <div class="modal-body">
            <div class="row">
              <div class="col-xs-12">
                <div class="form-group">
                  <label class="col-xs-6">Extra Time</label>
                  <div class="col-xs-6">
<?php
$strMaxTime = "SELECT max_time_adjustment FROM exam_exam WHERE exam_id='$exam_id';";
$query_max_time = $db->query($strMaxTime);
$row_max_time = $db->fetch_object($query_max_time);
$max_extra_time = $row_max_time->max_time_adjustment;

if ($max_extra_time == 0) $max_extra_time = 10;
$db->free($query_max_time);
unset($strMaxTime, $query_max_time, $row_max_time);
?>
                    <select class="form-control" name="txt_extra_time" id="txt_extra_time">
                    <?php
                    for ($tt = 0; $tt <= $max_extra_time; $tt++)
                    {
                      ?>
                        <option value="<?php echo $tt;?>"<?php if ($tt == $extra_time) echo " selected=\"selected\"";?>><?php echo $tt;?> Minutes</option>
                      <?php
                    }
                    ?>
                    </select>
                    <script>
										function FunctionChangeReason()
										{
											var time = $("#txt_extra_time :selected").val();
											if(time == "0") {
												$("input#time_reason").prop('disabled', true);
											}
											else
											{
													$("input#time_reason").prop('disabled', false);
											}
										}
										$("#txt_extra_time").change(function(e) {
											FunctionChangeReason();
                    });
										$(document).ready(function(e) {
											FunctionChangeReason();
                    });
										</script>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-xs-6">Reason for Extra Time</label>
                  <div class="col-xs-6">
                    <input name="txt_extra_time_reason" type="text" class="form-control" id="time_reason" value="<?php echo $extra_time_reason;?>" maxlength="50">
                  </div>
                </div>
              </div>
            </div>
        </div>
        <div class="modal-footer">
        	<div class="col-xs-11"  style="text-align:left">
          	<label>Note:</label><br>Adjustment in Time will also effect in the ending of Exam.
          </div>
          <input type="hidden" name="exam_id" value="<?php echo $exam_id;?>">
          <input type="hidden" name="center_id" value="<?php echo $center_id;?>">
          <input type="hidden" name="rand" value="<?php echo $random;?>">
          <button type="submit" class="btn btn-primary" name="SaveExtraTime">Save changes</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php		
	}
}

if (isset($_POST['btnStart']))		// Start Exam
{

	$random = FilterString($_POST['rand']);
	if ($_SESSION['rand']['list_exam'] == $random)
	{
		$exam_id = FilterNumber($_POST['exam_id']);
		$center_id = FilterNumber($_POST['center_id']);
		$sql_start = "UPDATE exam_exam_status SET start_time = NOW() WHERE exam_id = '$exam_id' AND center_id = '$center_id';";
		$db->query($sql_start);
	}
}


	if (isset($_POST['btnApprove']))		//Approve Result
	{
		$randomValue = ($_POST['rand']);
		if ($_SESSION['rand']['list_exam'] == $randomValue)
		{
			$exam_id = FilterNumber($_POST['exam_id']);
			$center_id = FilterNumber($_POST['center_id']);
			$strApprove = "UPDATE exam_exam_status SET is_approved = 'Y', full_close = 'Y', final_submit_time = NOW() WHERE exam_id = '$exam_id' AND center_id = '$center_id'";
			$db->query($strApprove);
			
			$strDeleteStudent = "DELETE FROM exam_exam_student WHERE exam_id = '$exam_id' AND center_id = '$center_id'";
			$db->query($strDeleteStudent);
		}
		
	} // Approve Result

	if (isset($_POST['btnDisApprove']))		//DisApprove Result
	{
		$randomValue = ($_POST['rand']);
		if ($_SESSION['rand']['list_exam'] == $randomValue)
		{
			$exam_id = FilterNumber($_POST['exam_id']);
			$center_id = FilterNumber($_POST['center_id']);
			$strDisApprove = "UPDATE exam_exam_status SET is_approved = 'N', send_for_approve = 'N' WHERE exam_id = '$exam_id' AND center_id = '$center_id'";
			$db->query($strDisApprove);
		}
	} // DisApprove Result

	if (isset($_POST['btnRemoveLogin']))		//Remove Login
	{
		$randomValue = ($_POST['rand']);
		if ($_SESSION['rand']['list_exam'] == $randomValue)
		{
			$exam_id = FilterNumber($_POST['exam_id']);
			$str_full = "SELECT * FROM  exam_exam_status WHERE exam_id = '$exam_id';";
			$query_full = $db->query($str_full);
			$row_full = $db->fetch_object($query_full);
			if ($row_full->full_close=='Y')
			{
				$str_del_login = "DELETE FROM exam_exam_student WHERE exam_id = '$exam_id';";
				$db->query($str_del_login);
			}

		} //random
	} //Remove Login

if (isset($_POST['SaveExamMessage']))
{
	$exam_id = FilterNumber($_POST['exam_id']);
	$start_message= addslashes(TextEditorRemove($_POST['start_message']));
	$end_message= addslashes(TextEditorRemove($_POST['end_message']));

	$str_check_message = " 
	SELECT exam_exam_type.start_message, exam_exam_type.end_message
		FROM exam_exam exam_exam
				 INNER JOIN exam_exam_type exam_exam_type
						ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id)
		WHERE exam_exam.exam_id = '$exam_id';						
	";	
	$query_check = $db->query($str_check_message);
	$row_check = $db->fetch_object($query_check);

	if ($row_check->start_message == $start_message) $start_message_equal = true;	
	if ($row_check->end_message == $end_message) $end_message_equal = true;	

	$db->begin();
	$db->query("DELETE FROM exam_exam_message WHERE exam_id = '$exam_id';");

	if (!$start_message_equal OR !$end_message_equal)
	{
		$strUpdateMessage = "REPLACE INTO `exam_exam_message` (`exam_id`, `start_message`, `end_message`) VALUES
('$exam_id', '$start_message', '$end_message');";
	$query_update = $db->query($strUpdateMessage);
	}
	else $query_update = TRUE;
	
	if ($query_update)
	{
		$db->commit();
		notify("Exam List","Exam Start / End Message has been recorded.",NULL,TRUE,5000);
	}
	else
	{
		$db->rollback();
	}
	
}
if (isset($_POST['btnMessage']))		// Change Message
{
	$random = FilterString($_POST['rand']);
	if ($_SESSION['rand']['list_exam'] == $random)
	{
		$exam_id = FilterNumber($_POST['exam_id']);
		$str_message = "
	SELECT exam_exam_type.start_message,
				 exam_exam_type.end_message,
				 exam_exam_message.start_message AS exam_start_message,
				 exam_exam_message.end_message AS exam_end_message
		FROM (exam_exam exam_exam
					LEFT OUTER JOIN exam_exam_type exam_exam_type
						 ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id))
				 LEFT OUTER JOIN exam_exam_message exam_exam_message
						ON (exam_exam_message.exam_id = exam_exam.exam_id)
		WHERE exam_exam.exam_id = '$exam_id';";

		$query_message = $db->query($str_message);
		$row_message = $db->fetch_object($query_message);

		if ($row_message->exam_start_message != NULL) $exam_start_message = $row_message->exam_start_message;
		else $exam_start_message = $row_message->start_message;
	
		if ($row_message->exam_end_message != NULL) $exam_end_message = $row_message->exam_end_message;
		else $exam_end_message = $row_message->end_message;
	
		$db->free($query_message);
		unset($str_message,$query_message,$row_message);
?>
<script>
$(document).ready(function(e) {
	$('#exam_start_end_message_modal').modal('show');
	$('div#exam_start_end_message_modal .modal-body').css('max-height', $(window).height() * 0.5);
	$('div#exam_start_end_message_modal .modal-body').css('overflow-y', 'auto'); 

});
</script>
<?php
wysiwyg_script($MAIN_URL);
?>
<div class="modal fade" id="exam_start_end_message_modal" tabindex="-1" role="dialog" aria-labelledby="exam_start_end_message_modal">
  <div class="modal-dialog  modal-lg">
    <div class="modal-content">
    	<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Exam Start / End Message</h4>
      </div>
      <form class="form-horizontal" method="post">
        <div class="modal-body">
            <div class="row">
              <div class="col-xs-12">
                <div class="form-group">
                  <label class="col-xs-2">Start Message</label>
                  <div class="col-xs-10">
<?php
// wysiwyg($txtAreaName, $txtValue = NULL)
wysiwyg("start_message", stripslashes($exam_start_message),'message'); 
?>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-xs-2">End Message</label>
                  <div class="col-xs-10">
<?php
// wysiwyg($txtAreaName, $txtValue = NULL)
wysiwyg("end_message", stripslashes($exam_end_message), 'message'); 
?>
                  </div>
                </div>

              </div>
            </div>
        </div>
        <div class="modal-footer">
          <input type="hidden" name="exam_id" value="<?php echo $exam_id;?>">
          <input type="hidden" name="rand" value="<?php echo $random;?>">
          <button type="submit" class="btn btn-success" name="SaveExamMessage">Save Messages</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
<style>
#exam_start_end_message_modal > .modal.in  .modal-dialog {
    transform: translate(0px, 5%) !important;
}
</style>
<?php		
wysiwyg_ajax($MAIN_URL,50,'message');
	}
}

if (isset($_POST['SaveMaxAdjustTime']))		// SAVE Exatra Time
{
	$exam_id = FilterNumber($_POST['exam_id']);
	$grace_time = FilterNumber($_POST['txt_extra_time']);
	if ($grace_time == "0")
		$str_update = "UPDATE exam_exam SET max_time_adjustment=0 WHERE exam_id ='$exam_id';";
	else
		$str_update = "UPDATE exam_exam SET max_time_adjustment='$grace_time' WHERE exam_id ='$exam_id';";
	$db->query($str_update);

}

if (isset($_POST['btnTimeAdjustment']))		// Add Extra Time
{
	$random = FilterString($_POST['rand']);
	if ($_SESSION['rand']['list_exam'] == $random)
	{
		$exam_id = FilterNumber($_POST['exam_id']);
		$str_extra_time = "SELECT * FROM exam_exam WHERE exam_id = '$exam_id';";
		$query_extra_time = $db->query($str_extra_time);
		$row_extra_time = $db->fetch_object($query_extra_time);
		$extra_time = $row_extra_time->max_time_adjustment;
		$db->free($query_extra_time);
		unset($str_extra_time,$query_extra_time,$row_extra_time);
?>
<script>
$(document).ready(function(e) {
	$('#extra_time_modal').modal('show');
});
</script>

<div class="modal fade" id="extra_time_modal" tabindex="-1" role="dialog" aria-labelledby="extra_time_modal">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
    	<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Time Adjustment</h4>
      </div>
      <form class="form-horizontal" method="post">
        <div class="modal-body">
            <div class="row">
              <div class="col-xs-12">
                <div class="form-group">
                  <label class="col-xs-6">Extra Time</label>
                  <div class="col-xs-6">
                    <select class="form-control" name="txt_extra_time" id="txt_extra_time">
                    <?php
                    for ($tt = 0; $tt <= $max_adjust_time; $tt++)
                    {
                      ?>
                        <option value="<?php echo $tt;?>"<?php if ($tt == $extra_time) echo " selected=\"selected\"";?>><?php echo $tt;?> Minutes</option>
                      <?php
                    }
                    ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
        </div>
        <div class="modal-footer">
        	<div class="col-xs-11"  style="text-align:left">
          	<label>Note:</label><br>Adjustment in Time will also effect in Closing Time of the Exam.
          </div>
          <input type="hidden" name="exam_id" value="<?php echo $exam_id;?>">
          <input type="hidden" name="rand" value="<?php echo $random;?>">
          <button type="submit" class="btn btn-primary" name="SaveMaxAdjustTime">Save changes</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php		
	}
}

// List Exam Result
if (isset($_POST['btnListExam']))
{
$randomValue = ($_POST['rand']);
if ($_SESSION['rand']['list_exam'] == $randomValue)
{
	$exam_id = FilterNumber($_POST['exam_id']);
	$center_id = FilterNumber($_POST['center_id']);
?>
<script>
$(document).ready(function(e) {
	$('#center_print_result_modal').modal('show');
});
</script>

<div class="modal fade" id="center_print_result_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    	<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Exam Result</h4>
      </div>
      <div class="modal-body">
        <div id="PrintStudent">
          <div align=center><span class="fa fa-spinner fa-5x fa-spin"></span><br><h3>Loading...</h3></div>
        </div>
      </div>
        <div class="modal-footer">
          <button type="button" id="btnPrintStudent" class="btn btn-success" tabindex="-1" onClick="javascript:PrintDiv('PrintStudent')"><i class="fa fa-print"></i> Print Result</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
  </div>
</div>

<script>
	$('#center_print_result_modal').on('shown.bs.modal', function (ev) {
		ev.preventDefault();
		$('#center_print_result_modal').removeData('bs.modal')
		$('div#center_print_result_modal .modal-body').css('max-height', $(window).height() * 0.5);
		$('div#center_print_result_modal .modal-body').css('overflow-y', 'auto'); 
		$.post("<?php echo $MAIN_URL;?>ajax.html",
		{
			exam_id: <?php echo $exam_id;?>,
			center_id: <?php echo $center_id;?>,
			task:"print-result",
			user:"admin",
		},
		function(data,status){
			$("div#PrintStudent").html(data); 		
		});
	});
</script>

<script type="text/javascript">
function PrintDiv(div)
{
	var divToPrint = document.getElementById(div);
	var popupWin = window.open('','_blank','width=800,height=600');
	popupWin.document.open();
	popupWin.document.write('<html><head><link href="<?php echo $MAIN_URL;?>print.css" rel="stylesheet" type="text/css" />\n <link href="<?php echo $MAIN_URL;?>bootstrap/css/bootstrap_none.css" rel="stylesheet" type="text/css" />\n	<style>.print * { visibility: visible !important; } </style>\n</head>\n<body>\n' + "<div class='print'>\n" +divToPrint.innerHTML + '\n</div></body>\n</html>');
//	popupWin.window.print();
//	popupWin.window.close();
}
</script>

<?php
		} //random
	}
	//Schedule Exam Updae
	if ( (isset($_POST['btnUpdateScheduleExam'])) || (isset($_POST['btnRemoveScheduleExam'])) )
	{
		$schedule_date = FilterDateTime($_POST['schedule-date']);
		$exam_id = FilterNumber($_POST['exam_id']);
		$center_id = FilterNumber($_POST['center_id']);

		if (strlen($schedule_date) != 0) $schedule_date = "'".$schedule_date."'";
		else $schedule_date = "NULL";

		if (isset($_POST['btnRemoveScheduleExam']))
			$schedule_date = "NULL";
		
			$sql_update = "UPDATE exam_exam_center SET schedule_date = $schedule_date WHERE exam_id = '$exam_id' and center_id = '$center_id';";
			$db->query($sql_update);
	
			$sql_update_student = "UPDATE exam_exam_student SET active_date = $schedule_date WHERE exam_id = '$exam_id' AND center_id = '$center_id';";
			$db->query($sql_update_student);

$strExamStatus = "
SELECT exam_exam_center.center_id, exam_exam.exam_id
  FROM exam_exam_center exam_exam_center
       INNER JOIN exam_exam exam_exam
          ON (exam_exam_center.exam_id = exam_exam.exam_id)
		WHERE exam_exam.exam_id = '$exam_id'";	

		$query_exam_status = $db->query($strExamStatus);

		$strDeleteStatus = "DELETE  FROM exam_exam_status WHERE exam_id = '$exam_id';";


		$db->query($strDeleteStatus);
		
		while ($row_exam_status = $db->fetch_object($query_exam_status))
		{
			$strInsertStatus = "INSERT INTO exam_exam_status (exam_id, center_id) VALUES ('$exam_id', '".$row_exam_status->center_id."');";
			$query_insert_status = $db->query($strInsertStatus);
		}
	}
	// Schedule Exam Dialog
	if (isset($_POST['btnScheduleExam']))
	{
		$randomValue = ($_POST['rand']);
		if ($_SESSION['rand']['list_exam'] == $randomValue)
		{
			$exam_id = FilterNumber($_POST['exam_id']);
			$center_id = FilterNumber($_POST['center_id']);

			$str_select_schedule = "SELECT schedule_date FROM exam_exam_center WHERE exam_id = '$exam_id' AND center_id = '$center_id';";
			$query_schedule = $db->query($str_select_schedule);
			$row_schedule = $db->fetch_object($query_schedule);
			$db_schedule_date = $row_schedule->schedule_date;
			unset($row_schedule, $query_schedule, $str_select_schedule);
			?>
				<script>
        $(document).ready(function(e) {
          $('#ScheduleExam').modal('show');
        });
        </script>
        <div class="modal fade" id="ScheduleExam" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-sm">
            <div class="modal-content">
            	<form class="form-horizontal" method="post">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title"><i class="fa fa-calendar"></i> Schedule Exam</h4>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                  	<div class="col-xs-12">
                        <label class="col-xs-4" style="padding:0px;padding-top:3px;;">Schedule Date</label>
                        <div class="form-inline">
                            <div class="input-group date">
                                <input name="schedule-date" type="text" class="form-control" data-format="yyyy-MM-dd hh:mm:ss" value="<?php echo $db_schedule_date;?>">
                                <span class="add-on input-group-addon"><i class="fa fa-calendar" data-date-icon="fa fa-calendar" data-time-icon="fa fa-clock-o"></i>
                                </span>
                            </div>
                          <label class="col-xs-4"></label>
                          <small>YYYY-MM-DD HH:MM:SS</small>

                          <label class="col-xs-12"></label>
                          <small style="color:#081EA2"><br><strong><u>Note:</u></strong> Question/Student should be assigned for all Exam Center before exam schedule.</small>

                        </div>
                    </div>
                  </div>  
                </div>
                <div class="modal-footer">
<?php if (strlen($db_schedule_date) > 0) { ?>
                  <button type="submit" name="btnRemoveScheduleExam" class="btn btn-danger" tabindex="-1"><i class="fa fa-trash"></i> Clear Schedule</button>
<?php } ?>                  
                  <button type="submit" name="btnUpdateScheduleExam" class="btn btn-success" tabindex="-1"><i class="fa fa-save"></i> Schedule Exam</button>
                  <input type="hidden" name="exam_id" value="<?php echo $exam_id;?>">
                  <input type="hidden" name="center_id" value="<?php echo $center_id;?>">
                  <button type="button" class="btn btn-default" data-dismiss="modal" autofocus>Close</button>
                </div>
              </form>
            </div>
          </div>
        </div>

<?php		
		} //Random Check
	}

	// FOR PRINT Student Credentials
	if(isset($_POST['btnPrintUser']))
	{
		$randomValue = ($_POST['rand']);
		if ($_SESSION['rand']['list_exam'] == $randomValue)
		{
			$center_id = FilterNumber($_POST['center_id']);
			$exam_id = FilterNumber($_POST['exam_id']);
			?>
				<script>
        $(document).ready(function(e) {
          $('#ModalUserCredentials').modal('show');
        });
        </script>

        <div class="modal fade" id="ModalUserCredentials" tabindex="-1" role="dialog" aria-labelledby="UserCredentials" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
            	<div class="modal-header">
              	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Student Username/Password</h4>
              </div>
              <div class="modal-body">
              	<p align="center"><strong>Student Credentials</strong></p>
                <div id="PrintStudent" class="print">
                	<div align=center><span class="fa fa-spinner fa-5x fa-spin"></span><br><h3>Loading...</h3></div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" id="btnPrintStudent" class="btn btn-success" tabindex="-1" onClick="javascript:PrintDiv('PrintStudent')"><i class="fa fa-print"></i> Print</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" autofocus>Close</button>
              </div>
            </div>
          </div>
        </div>
        <script>
					$('#ModalUserCredentials').on('shown.bs.modal', function (ev) {
						ev.preventDefault();
						$('#ModalUserCredentials').removeData('bs.modal')
						$('div#ModalUserCredentials .modal-body').css('max-height', $(window).height() * 0.5);
						$('div#ModalUserCredentials .modal-body').css('overflow-y', 'auto'); 
						$.post("<?php echo $MAIN_URL;?>ajax.html",
						{
							exam_id: <?php echo $exam_id;?>,
							center_id: <?php echo $center_id;?>,
							task:"student_login_admin",
						},
						function(data,status){
							$("div#PrintStudent").html(data); 		
						});
					});
				</script>
<script type="text/javascript">
function PrintDiv(div)
{
	var divToPrint = document.getElementById(div);
	var popupWin = window.open('','_blank','width=800,height=600');
	popupWin.document.open();
	popupWin.document.write('<html><head><link href="<?php echo $MAIN_URL;?>print.css" rel="stylesheet" type="text/css" />\n <link href="<?php echo $MAIN_URL;?>bootstrap/css/bootstrap_none.css" rel="stylesheet" type="text/css" />\n <style>.print * { visibility: visible !important; } </style>\n	</head>\n<body>\n' + "<h3><strong><p align='center' style='margin:0;'>Student Credentials</p></strong></h3><br><div class='print visible-print-block'>\n" +divToPrint.innerHTML + '\n</div></body>\n</html>');

//	popupWin.window.print();
//	popupWin.window.close();
}
</script>
			<?php
		}
	}
	//FOR ACTIVE 
	if(isset($_POST['btnActive']))
	{
		$randomValue = ($_POST['rand']);
		if ($_SESSION['rand']['list_exam'] == $randomValue)
		{
			$exam_id = FilterNumber($_POST['exam_id']);
			$strSelectStudent = "SELECT exam_id FROM exam_exam_user WHERE exam_id = '$exam_id';";
			$num_exam = $db->num_rows($db->query($strSelectStudent));
			if ($num_exam == 0)
			{
				$strDeletePage="DELETE FROM exam_exam WHERE exam_id=$exam_id";
				$db->begin();
				$query = $db->query($strDeletePage);
				if ($query)
				{
					notify("Exam List","Exam details has been deleted.",NULL,TRUE,5000);
					$db->commit();
				}
				else
				{
				notify("Exam List","<font color=red>Can not delete exam details. <br>Child Records may exists.</font>",NULL,TRUE,5000);
					$db->rollback();
				}
			}
			else
				notify("Exam List","<font color=red>Can not delete exam details. <br>Student Login ID may exists!!!</font>",NULL,TRUE,10000);
			
		}
	}
	?>

<div class="page-header">
  <div class="row">
  	<div class="col-md-9">
      <h3 style="margin-top:0px; margin-bottom:0px;"><span class="fa fa-edit"></span> Exam List</h3>
    </div>
    <div class="col-md-3 pull-right">    
<?php
if (!$exam_change)
  $disabled_add = " disabled=\"disabled\"";
?>
      <form method="post" class="pull-right form-inline" action="add.html">
        <button type="submit" name="btnAddNew" id="btnAddNew" class="btn btn-primary"<?php

        	$disabled_add ='';
	if(isset($_POST['disabled_add']))
		$disabled_add = $_POST['disabled_add'];

         echo $disabled_add;unset($disabled_add);?>><span class="fa fa-plus-circle"></span> Add New Exam</button>
      </form>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-lg-12">
 
<?php
	$SQL = "
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
             AND (exam_exam_center.center_id = exam_exam_status.center_id) ";

$strORDER = "	ORDER BY exam_exam.exam_create_date DESC, exam_exam_status.full_close DESC, exam_exam_status.end_time DESC, exam_exam_status.start_time DESC, exam_exam_status.final_submit_time, (exam_exam_center.schedule_date is NULL), exam_exam_center.schedule_date DESC, exam_exam_center.exam_id, exam_exam_center.center_id";

$strTitle = "All Exam";

if (isset($_POST['isSearchFilter']))
{
	$random = FilterString($_POST['rand']);
	if ($random == $_SESSION['rand']['list_exam'])
	{
		if (isset($_POST['btnSearchFilter']))		//Search
		{
			$exam_status = FilterNumber($_POST['exam_type']);
			$date_type = FilterNumber(isset($_POST['date_type']));
			$start_date = FilterDateTime(isset($_POST['start_date']));
			$end_date = FilterDateTime(isset($_POST['end_date']));

			if (isset($_POST['exam_type']))
			{
				$today = date("Y-m-d");
				if ($exam_status == 1) 		// All Exam
				{
					if ( (isset($_POST['start_date'])) || (isset($_POST['end_date'])) ) 
					$strStatus = " WHERE ";
					$strTitle = "All Exam";
				}
				if ($exam_status == 2)	// NOT Schedule Exam Only
				{
					$strStatus = " WHERE (exam_exam_center.schedule_date is NULL OR exam_exam_center.schedule_date > NOW() ) AND exam_exam_status.start_time IS NULL AND exam_exam_status.end_time IS NULL ";
					$strTitle = "Not Scheduled Exam";
				}
				if ($exam_status == 3)	// Schedule Exam Only
				{
					$strStatus = " WHERE (exam_exam_center.schedule_date is NOT NULL AND date(exam_exam_center.schedule_date) = '$today' AND exam_exam_center.schedule_date <= NOW() ) AND exam_exam_status.start_time IS NULL AND exam_exam_status.end_time IS NULL ";
					$strTitle = "Scheduled Exam";
				}

				if ($exam_status == 4)	// Schedule Expire Exam Only
				{
					$strStatus = " WHERE (exam_exam_center.schedule_date is NOT NULL AND date(exam_exam_center.schedule_date) < '$today' ) AND exam_exam_status.start_time IS NULL AND exam_exam_status.end_time IS NULL ";
					$strTitle = "Schedule Expire Exam";
				}

				if ($exam_status == 5)	// Running Exam Only
				{
					$strStatus = " WHERE exam_exam_center.schedule_date is NOT NULL AND exam_exam_status.start_time IS NOT NULL AND exam_exam_status.end_time IS NULL ";
					$strTitle = "Running Exam";
				}

				if ($exam_status == 6)	// Stopped Exam Only
				{
					$strStatus = " WHERE exam_exam_center.schedule_date is NOT NULL AND exam_exam_status.start_time IS NOT NULL AND exam_exam_status.end_time IS NOT NULL AND exam_exam_status.full_close = 'N' AND (exam_exam_status.send_for_approve = 'N' OR exam_exam_status.send_for_approve IS NULL) ";
					$strTitle = "Awaiting Final Mark Exam";
				}

				if ($exam_status == 7)	// Awaiting Approval Only
				{
					$strStatus = " WHERE exam_exam_center.schedule_date is NOT NULL AND exam_exam_status.start_time IS NOT NULL AND exam_exam_status.end_time IS NOT NULL AND exam_exam_status.full_close = 'N' AND exam_exam_status.send_for_approve = 'Y' ";
					$strTitle = "Awaiting Final Mark Exam";
				}

				if ($exam_status == 8)	// Result Published Only
				{
					$strStatus = " WHERE exam_exam_center.schedule_date is NOT NULL AND exam_exam_status.start_time IS NOT NULL AND exam_exam_status.end_time IS NOT NULL AND exam_exam_status.full_close = 'Y' ";
					$strTitle = "Result Published Exam";
				}
			}

			if ($date_type > 1)
			{
				if ($date_type == 2) $date_field = "exam_exam_status.start_time";
				if ($date_type == 3) $date_field = "exam_exam_status.end_time";

				if ( (strlen($start_date) > 0) && (strlen($end_date) > 0 ) ) 
					$strSearchDate = " AND $date_field >= '$start_date 00:00:00' AND $date_field <= '$end_date 23:59:59' ";
				else if ( (strlen($start_date) > 0) && (strlen($end_date) == 0 ) ) 
					$strSearchDate = " AND $date_field >= '$start_date 00:00:00' ";
				else if ( (strlen($start_date) == 0) && (strlen($end_date) > 0 ) ) 
					$strSearchDate = " AND $date_field <= '$end_date 23:59:59' ";
				else if ( (strlen($start_date) == 0 ) && (strlen($end_date) == 0) ) 
				{
					$date_type = 0;
					$strSearchDate = "";
				}
			}

			unset($_SESSION['list_exam']['search'], $_SESSION['list_exam']['exam_status'], $_SESSION['list_exam']['title']);

		}	//Search		
		
		if (isset($_POST['btnSearchClear']))
		{
			unset($_SESSION['list_exam']['search'], $_SESSION['list_exam']['exam_status'], $_SESSION['list_exam']['title']);
		}
	}
}

if (isset($_SESSION['list_exam']['exam_status']))
{
	$exam_status = $_SESSION['list_exam']['exam_status'];
}
else
{
	$_SESSION['list_exam']['exam_status'] = (!empty($exam_status));
}

if (isset($_SESSION['list_exam']['search']))
{
	$strWHERE = $_SESSION['list_exam']['search'];
}
else 
{
	$strWHERE = "$strStatus $strSearchDate";
	$_SESSION['list_exam']['search'] = $strWHERE;
}

if (isset($_SESSION['list_exam']['title']))
{
	$strTitle   = $_SESSION['list_exam']['title'];	
}
else
{
	$_SESSION['list_exam']['title'] = $strTitle ;	
}

//$sql2 = "$SQL $strStatus $strSearchDate $strORDER";

$sql2 = "$SQL $strWHERE $strORDER";

$sql2 = str_replace("\n","",$sql2);
$sql2 = str_replace("\n\r","",$sql2);

$sql2 = str_replace(" WHERE    	ORDER "," ORDER ",$sql2);
$sql2 = str_replace(" WHERE ORDER "," ORDER ",$sql2);
$sql2 = str_replace(" WHERE   ORDER "," ORDER ",$sql2);
$sql2 = str_replace(" WHERE  ORDER "," ORDER ",$sql2);
$sql2 = str_replace(" WHERE ORDER "," ORDER ",$sql2);
$sql2 = str_replace(" WHERE   	ORDER "," ORDER ",$sql2);
$sql2 = str_replace(" WHERE AND "," WHERE ",$sql2);
$sql2 = str_replace(" WHERE   AND "," WHERE ",$sql2);
$sql2 = str_replace(" WHERE  AND "," WHERE ",$sql2);

$query_result = $db->query($sql2);
$rand = RandomValue(20);
unset($_SESSION['rand']);
$_SESSION['rand']['list_exam']=$rand;

?>
<div class="col-sm-12">
	<div class="row pull-left">
  	<strong><u><?php echo $strTitle;?></u></strong>
  </div>
	<div class="row pull-right">
      <form method="post" class="form-inline">
        <div class="form-group">
          <select class="form-control" name="exam_type" title="Select Exam Type">
            <option value="1"<?php if (!empty($exam_status) == 1) echo " selected=\"selected\"";?>>All Exam Type</option>
            <option value="2"<?php if (!empty($exam_status) == 2) echo " selected=\"selected\"";?>>Not Scheduled Exam</option>
            <option value="3"<?php if (!empty($exam_status) == 3) echo " selected=\"selected\"";?>>Scheduled Exam</option>
            <option value="4"<?php if (!empty($exam_status) == 4) echo " selected=\"selected\"";?>>Schedule Expire Exam</option>
            <option value="5"<?php if (!empty($exam_status) == 5) echo " selected=\"selected\"";?>>Running Exam</option>
            <option value="6"<?php if (!empty($exam_status) == 6) echo " selected=\"selected\"";?>>Awaiting Final Mark</option>
            <option value="7"<?php if (!empty($exam_status) == 7) echo " selected=\"selected\"";?>>Awaiting Approval</option>
            <option value="8"<?php if (!empty($exam_status) == 8) echo " selected=\"selected\"";?>>Result Published</option>
          </select>

          <select class="form-control" name="date_type" title="Select Date Type" id="date_type">
            <option value="1">Select Date Type</option>
            <option value="2"<?php if (!empty($date_type) == 2) echo " selected=\"selected\"";?>>Start Date</option>
            <option value="3"<?php if (!empty($date_type) == 3) echo " selected=\"selected\"";?>>End Date</option>
          </select>
					
					<span id="date-time-exam" style="display: none;">
            <label> From: </label>
            <div class="input-group date" title="Select Start-Date" id="start-date">
            <input id="start_date" name="start_date" type="text" size="10" class="form-control" placeholder="YYYY-MM-DD" data-format="yyyy-MM-dd" value="<?php echo $start_date;?>">
            <span class="add-on input-group-addon"><i class="fa fa-calendar" data-date-icon="fa fa-calendar" data-time-icon="fa fa-clock-o"></i>
            </span>
          </div>
          <label> TO: </label>
          <div class="input-group date" title="Select End-Date"  id="end-date">
            <input id="end_date" name="end_date" type="text" size="10" class="form-control" placeholder="YYYY-MM-DD" data-format="yyyy-MM-dd" value="<?php echo $end_date;?>">
            <span class="add-on input-group-addon"><i class="fa fa-calendar" data-date-icon="fa fa-calendar" data-time-icon="fa fa-clock-o"></i>
            </span>
          </div>
        </span>
<script>
$(document).ready(function(e) {
	show_hide_date()
});
$("#date_type").change(function(e) {
	show_hide_date()
});

function show_hide_date()
{
  var date_type = $("#date_type :selected").val();
	if (date_type > 1)
	{
		$("#date-time-exam").show();
	}
	else
	{
		$("#date-time-exam").hide();
		$("#start_date").val("");
		$("#end_date").val("");
	}
}
</script>
          <button type="submit" class="btn btn-success" name="btnSearchFilter">Filter</button>
          <button type="submit" class="btn btn-danger"  name="btnSearchClear">Clear Filter</button>
          <input name="isSearchFilter" type="hidden">
          <input name="rand" value="<?php echo $rand;?>" type="hidden">
        </div>
      </form>
  </div>
</div>

    <table class="table  table-striped table-bordered table-hover tooltip-demo" id="list_all_exam" style="font-size:85%;">
  <?php
	$strExamType = "SELECT * FROM exam_exam_type";
	$query_type = $db->query($strExamType);
	while ($row_type = $db->fetch_object($query_type))
	{
		$a = '';
		$a .= "<option value=\"$row_type->exam_type\">$row_type->exam_type</option>";
	}
?>
      <thead>
        <tr>
          <th>SN</th>
          <th>Exam Code</th>
          <th>Exam Type</th>
          <th>Create Date</th>
          <th>Center</th>
          <th>Schedule Date</th>
          <th>Start Date/Time</th>
          <th>End Date/Time</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>
  <?php
  $SN=0;
while ($row = $db->fetch_object($query_result))
{

?>
        <tr>
          <td style="width:10px"><?php  echo ++$SN;?></td>
          <td><div style="width:150px;"><?php 

		$strExamCode = "SELECT * FROM exam_exam_center WHERE exam_id = '$row->exam_id';";
		$query_exam_code = $db->query($strExamCode);
		$no_of_exam_code = $db->num_rows($query_exam_code);

		if ($no_of_exam_code > 1) 
			$MultipleCenter = TRUE;
		else 
			$MultipleCenter = FALSE;

		if ($MultipleCenter)
		{
			$strExamCode2 = "SELECT exam_code from exam_exam_center WHERE exam_id = '$row->exam_id' AND center_id = '$row->center_id' AND exam_code is NOT NULL";
			$query_exam_code2 = $db->query($strExamCode2);
			$no_of_exam_code2 = $db->num_rows($query_exam_code2);
			if ($no_of_exam_code2 > 0) 
			{
				$row_exam_code2 = $db->fetch_object($query_exam_code2);
				if ($row_exam_code2 != NULL)
					echo "<span title=\"Center Exam Code\">$row_exam_code2->exam_code</span>";
				else
					echo $row->exam_code ;
			}
			else
				echo $row->exam_code ;
		}
		else
			echo $row->exam_code ;
	
			?>
  <?php
	if ($row->schedule_date == NULL)
	{
		if ($MultipleCenter)
		{
			?>
        <div class="pull-right">
          <form class="form-horizontal" method="post">
            <button title="Change Exam-Code for Center" class="btn btn-default btn-xs change-exam-code" id="btnChangeCode2" name="btnChangeCode2"  data-id="<?php echo $row->exam_id;?>" data-input-name="exam_id" data-id2="<?php echo $row->center_id;?>" data-input-name2="center_id" data-button="btnChangeExamCode" data-class="btn btn-info" data-target="#change-exam-code-modal" data-toggle="modal" data-url="" data-toggle-tooltip="tooltip" data-placement="top" style="font-size:90%">Change Code</button>
          </form>
        </div>
      <?php
		}
	}

if ($row->start_time!=NULL && $row->end_time == NULL) 
	echo "<br><span class=\"label label-success pull-right\" style=\"margin-top:5px;\"><i class=\"fa fa-spin fa-spinner fa-2x\"></i> &nbsp;<b>On Going</b></span>";

	if ($row->full_close == 'Y')
  {
?>
    <div class="pull-right"><button class="btn btn-success btn-xs result-publish" id="btnPublishResult2" name="btnPublishResult2"  data-id="<?php echo $row->exam_id;?>" data-id2="<?php echo $row->center_id;?>" data-input-name2="center_id" data-button="btnPublishResult" data-class="btn btn-success" data-input-name="exam_id" data-target="#result-publish-modal" data-toggle="modal" data-url="" data-toggle-tooltip="tooltip" data-placement="top">Result Published</button></div>

<?php    
  }
?>
<?php
if (($row->schedule_date != NULL)  && ($row->start_time == NULL) )
{
?>
<div class="pull-right"><button class="btn btn-primary btn-xs attendance-sheet" title="Attendance-sheet" id="btnAttendanceSheet2" name="btnAttendanceSheet2"  data-id="<?php echo $row->exam_id;?>" data-id2="<?php echo $row->center_id;?>" data-input-name2="center_id" data-button="btnAttendanceSheet" data-class="btn btn-primary" data-input-name="exam_id" data-target="#attendance-sheet-modal" data-toggle="modal" data-url="" data-toggle-tooltip="tooltip" data-placement="top">Attendance</button></div>
<?php } ?>
<?php	
	if ($row->send_for_approve == 'Y' && $row->is_approved!='Y')
	{
	echo "<div class=\"pull-right\"><span class=\"label label-warning\" style=\"margin-top:15px; font-size:90%;\">Waiting for approval.</span>
	</div>";
?>
  <div style="width:180px; margin-top:5px;">
  <form class="form-horizontal" method="post">
    <button title="Approve Result" class="btn btn-success btn-xs approve-exam-result" id="btnApprove2" name="btnApprove2"  data-id="<?php echo $row->exam_id;?>" data-id2="<?php echo $row->center_id;?>" data-input-name2="center_id" data-button="btnApprove" data-class="btn btn-success" data-input-name="exam_id" data-target="#approve-exam-result-modal" data-toggle="modal" data-url="" data-toggle-tooltip="tooltip" data-placement="top">Approve</button>
    
    <button title="Don't Approve Result" class="btn btn-danger btn-xs pull-right disapprove-exam-result" id="btnDisApprove2" name="btnDisApprove2"  data-id="<?php echo $row->exam_id;?>" data-id2="<?php echo $row->center_id;?>" data-input-name2="center_id" data-button="btnDisApprove" data-class="btn btn-danger" data-input-name="exam_id" data-target="#disapprove-exam-result-modal" data-toggle="modal" data-url="" data-toggle-tooltip="tooltip" data-placement="top">Don't Approve</button>
    
  </form>
  </div>
  <?php
	}
	?>
              <?php
if ($row->schedule_date != NULL)
	$sched = " disabled = \"disabled\"";
else
	$sched = "";

if ($row->schedule_date == NULL)
	$txt_schedule_date_null = " disabled = \"disabled\"";
else
	$txt_schedule_date_null= "";


if ($row->schedule_date == NULL)
	$txt_schedule_date_null = " disabled = \"disabled\"";
else
	$txt_schedule_date_null= "";

if ( time() < strtotime($row->schedule_date))
	$txt_schedule_date_null2 = " disabled = \"disabled\"";
else
	$txt_schedule_date_null2 = "";
?>
              </div>
          </td>
          <td><?php echo $row->exam_type;?></td>
          <td><?php echo $row->exam_create_date;?></td>
          <td><?php echo $row->center_name;?></td>
          <td><?php echo $row->schedule_date;?></td>
          <td><?php echo $row->start_time;?></td>
          <td><?php echo $row->end_time;
						if (!$row->start_time==NULL) 
						{
							if ($row->grace_time > 0) echo "<br><i><small><small>+ ".$row->grace_time . " minutes ($row->grace_reason)</small></small></i>";
						}
        $today = date("Y-m-d");

        if ($row->schedule_date != NULL && $row->start_time == NULL && $row->ExamSchedule < $today) 
          $schedule_expire = true;
				else
					$schedule_expire = FALSE;

						if ($row->start_time==NULL) 
						{
							if ($schedule_expire) 
							{
								echo "<span class=\"label label-danger pull-right\" style=\"font-size:85%;\">Schedule Expire</span>";
								$schedule_time_expire = " disabled =\"disabled\"";
							}
							else $schedule_time_expire="";
						}


							if ($row->start_time == NULL && !$schedule_expire)
							{
unset($txt_schedule_date);
if ($row->schedule_date != NULL)
{
	 if ($row->start_time != NULL)
		$txt_schedule_date = " disabled = \"disabled\"";
}

$strCheckQuestion = "SELECT * FROM exam_exam_question WHERE exam_id = '$row->exam_id' ;";
$queryCheck = $db->query($strCheckQuestion);
$noof_question = $db->num_rows($queryCheck);
unset($no_of_question, $no_of_students);

if ($noof_question == 0) $no_of_question = " disabled=\"disabled\"";
unset($strCheckQuestion, $queryCheck, $noof_question);


$strCheckStudent = "SELECT * FROM exam_exam_student WHERE exam_id = '$row->exam_id' AND center_id = '$row->center_id'; ";
$queryCheckStudent = $db->query($strCheckStudent);
$no_of_student2 = $db->num_rows($queryCheckStudent);

if ($no_of_student2 == 0) $no_of_students = " disabled=\"disabled\"";
	
?>
            <div align="right" class="pull-right">

              <button title="Start Exam" class="btn btn-success btn-circle btn-sm start-exam" type="submit" id="btnStart2" name="btnStart2"  data-button="btnStart" data-class="btn btn-success" data-id="<?php echo $row->exam_id;?>" data-input-name="exam_id" data-id2="<?php echo $row->center_id;?>" data-input-name2="center_id"  data-target="#start-exam-modal" data-toggle="modal" data-url=""  data-toggle-tooltip="tooltip" data-placement="top"<?php echo $txt_schedule_date_null;?><?php echo $txt_schedule_date_null2;?><?php echo $schedule_time_expire; echo (!empty($noof_question)); echo (!empty($no_of_students));?>><span class="fa fa-play"></span></button>
            </div>
            <?php }

if ($row->start_time!=NULL && $row->end_time == NULL) 
{
	echo "<h5 style=\"margin:0;margin-bottom:5px;\"><span class=\"label label-warning\" style=\"margin-top:5px;margin-botton:5px;\"><span id=\"elapsed$row->exam_id\"></span> remaining.</span></h5>";
	$strStart = strtotime($row->start_time);
	$startTime = $row->start_time;
	$Total_Time = $row->total_time + $row->grace_time;
	$strEnd = strtotime("+$Total_Time minutes",$strStart);
	$endTime = date("Y-m-d H:i:s",$strEnd);
	$diff = $strEnd - time();
		CountDownRemainingTime($diff,"elapsed".$row->exam_id,$row->exam_id,"list.html");
		?>
            <button title="Extra Time" class="btn btn-warning btn-sm btn-circle extra-time"  id="btnExtraTime2" name="btnExtraTime2"  data-id="<?php echo $row->exam_id;?>" data-id2="<?php echo $row->center_id;?>" data-input-name2="center_id" data-button="btnExtraTime" data-class="btn btn-warning" data-input-name="exam_id" data-target="#extra-time-modal" data-toggle="modal" data-url="" data-toggle-tooltip="tooltip" data-placement="top"><span class="fa fa-clock-o fa-spin" style="font-size:120%"></span></button>
                
            <?php
}
							
							 ?>
                
          </td>
          <td align="center" valign="middle"><div style="width:295px;">
              <?php
if ($row->start_time != NULL)
	$started = " disabled = \"disabled\"";
else
	$started = "";
?>

              <button title="Exam Start/End Message" class="btn btn-success btn-circle btn-sm exam-message" type="button" id="btnMessage2" name="btnMessage2" data-button="btnMessage" data-id="<?php echo $row->exam_id;?>" data-input-name="exam_id" data-id2="<?php echo $row->center_id;?>" data-input-name2="center_id" data-class="btn btn-success" data-toggle-tooltip="tooltip" data-placement="top" data-toggle="modal" data-target="#exam-message-modal"<?php echo $started;?>><span class="fa fa-comment icon-white"></span></button>
                
              <?php 
if ($row->full_close == "Y") $full = " disabled=\"disabled\"";
else isset($full); 
?>
              <button title="View Details" class="btn btn-primary btn-circle btn-sm view" type="button" id="btnView2" name="btnView2" data-id="<?php echo $row->exam_id;?>" data-id2="<?php echo $row->center_id;?>" data-input-name2="center_id" data-class="btn btn-primary" data-toggle-tooltip="tooltip" data-placement="top" data-toggle="modal" data-target="#exam_view"<?php echo (!empty($full)); ?>><span class="fa fa-eye icon-white"></span></button>
              <button title="Edit" class="btn btn-info btn-circle btn-sm edit" type="submit" id="btnEdit2" name="btnEdit2" data-id="<?php echo $row->exam_id;?>" data-id2="<?php echo $row->center_id;?>" data-input-name2="center_id" data-button="btnEdit" data-class="btn btn-info" data-input-name="exam_id" data-target="#edit_modal" data-toggle="modal" data-url="edit.html" data-toggle-tooltip="tooltip" data-placement="top"<?php echo $sched;?><?php echo (!empty($full));?><?php echo $started;?>><span class="fa fa-pencil icon-white"></span></button>
                
              <button title="Question Selection" class="btn btn-default btn-circle btn-sm question" type="submit" id="btnQuestion2" name="btnQuestion2" data-id2="<?php echo $row->center_id;?>" data-input-name2="center_id" data-id="<?php echo $row->exam_id;?>" data-button="btnQuestion" data-class="btn btn-default" data-input-name="exam_id" data-target="#question-modal" data-toggle="modal" data-url="question.html"<?php echo (!empty($disabled));?> data-toggle-tooltip="tooltip" data-placement="top"<?php echo $sched;?><?php echo (!empty($full));?><?php echo $started;?>><span class="fa fa-question"></span></button>
                
              <button title="Student Selection" class="btn btn-primary btn-circle btn-sm student" type="submit" id="btnStudent2" name="btnStudent2" data-id="<?php echo $row->exam_id;?>" data-id2="<?php echo $row->center_id;?>" data-input-name2="center_id" data-button="btnStudent" data-class="btn btn-primary" data-input-name="exam_id" data-target="#student-modal" data-toggle="modal" data-url="student.html"<?php echo (!empty($disabled));?> data-toggle-tooltip="tooltip" data-placement="top"<?php echo $sched;?><?php echo (!empty($full));?><?php echo $started;?>><span class="fa fa-user-plus"></span></button>
              <button title="Schedule Exam" class="btn btn-success btn-circle btn-sm schedule_exam" type="submit" id="btnScheduleExam2" name="btnScheduleExam2"  data-id="<?php echo $row->exam_id;?>" data-button="btnScheduleExam" data-class="btn btn-success" data-input-name="exam_id" data-id2="<?php echo $row->center_id;?>" data-input-name2="center_id" data-target="#schedule_exam_modal" data-toggle="modal" data-url="" data-toggle-tooltip="tooltip" data-placement="top" <?php echo (!empty($txt_schedule_date));?><?php echo (!empty($full));?><?php echo (!empty($no_of_question));?><?php echo (!empty($no_of_students));?> <?php echo $started;?>><span class="fa fa-calendar"></span></button>
                
  <?php 
if ($row->schedule_date == NULL) 
	$student = " disabled = \"disabled\"";
else if ($no_of_student2 == 0)  
	$student = " disabled = \"disabled\"";
else if ($row->end_time != NULL) 
	$student = " disabled = \"disabled\"";
else if ($schedule_expire)
	$student = " disabled = \"disabled\"";

else $student = "";
?>
              <button title="View Student Credentials" class="btn btn-info btn-circle btn-sm print_user" type="submit" id="btnPrintUser2" name="btnPrintUser2" data-button="btnPrintUser" data-class="btn btn-info" data-id="<?php echo $row->exam_id;?>" data-input-name="exam_id" data-id2="<?php echo $row->center_id;?>" data-input-name2="center_id" data-target="#print_user_modal" data-toggle="modal" data-url="" data-toggle-tooltip="tooltip" data-placement="top"<?php echo $student;?>><span class="fa fa-user"></span></button>
                
                
              <?php
unset($exam_started, $time_adjusted);
if ($row->start_time != NULL AND $row->end_time == NULL)
	$time_adjusted ="";
else
	$time_adjusted = " disabled = \"disabled\" ";
?>
              <button title="Maximum Time Adjustment" class="btn btn-danger btn-circle btn-sm time-adjustment"  id="btnTimeAdjustment2" name="btnTimeAdjustment2"  data-id="<?php echo $row->exam_id;?>" data-id2="<?php echo $row->center_id;?>" data-input-name2="center_id" data-button="btnTimeAdjustment" data-class="btn btn-danger" data-input-name="exam_id" data-target="#time-adjustment-modal" data-toggle="modal" data-url="" data-toggle-tooltip="tooltip" data-placement="top" <?php echo $time_adjusted;?> ><span class="fa fa-clock-o"></span></button>
              <?php 
if ($row->full_close != 'Y')
	$full_close = " disabled = \"disabled\"";
else
	unset($full_close);

if ($row->send_for_approve == 'Y')
	unset($full_close);
?>
              <button title="View/Print Result" class="btn btn-warning btn-circle btn-sm list_exam" type="submit" id="btnListExam2" name="btnListExam2"  data-class="btn btn-success" data-button="btnListExam" data-id="<?php echo $row->exam_id;?>" data-input-name="exam_id" data-id2="<?php echo $row->center_id;?>" data-input-name2="center_id" data-target="#list_exam_modal" data-toggle="modal" data-url="" data-toggle-tooltip="tooltip" data-placement="top"<?php echo $full_close;?>><span class="fa fa-file-text-o"></span></button>
              <button title="Individual Result" class="btn btn-danger btn-circle btn-sm individual-exam" type="submit" id="btnIndividualResult2" name="btnIndividualResult2"  data-id="<?php echo $row->exam_id;?>" data-button="btnIndividualResult" data-class="btn btn-danger" data-input-name="exam_id" data-id2="<?php echo $row->center_id;?>" data-input-name2="center_id" data-target="#individual-exam-modal" data-toggle="modal" data-url="result.html" data-toggle-tooltip="tooltip" data-placement="top"<?php echo $full_close;?>><span class="fa fa-clone"></span></button>
              </div>
          </td>
        </tr>
  <?php  
unset($strCheckStudent, $queryCheckStudent, $no_of_student2);
} ?>
      </tbody>
    </table>

<?php 

if ($exam_change)
{
	confirm3("Exam List","Do you want to <b>change exam Start/End Message</b>?","exam-message","exam-message-modal",$rand,"_message");
	
	confirm3("Exam List","Do you want to change exam details?","edit","edit_modal",$rand,1);

confirm3("Exam List","Do you want to <b>Approve the Result of this Exam</b>?","approve-exam-result","approve-exam-result-modal",$rand,"_approve_result");
confirm3("Exam List","Do you want to <b>Dis-approve the Result of this Exam</b>?","disapprove-exam-result","disapprove-exam-result-modal",$rand,"_disapprove_result");

confirm3("Exam List","Do you want to <b>Change Exam Code for This Exam Center</b>?","change-exam-code","change-exam-code-modal",$rand,"_change_exam_code");

}
else
{

	no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","edit","edit_modal",$rand,1);

	no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","exam-message","exam-message-modal",$rand,"_message");

	no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","approve-exam-result","approve-exam-result-modal",$rand,"_approve_result");

	no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","disapprove-exam-result","disapprove-exam-result-modal",$rand,"_disapprove_result");

	no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","change-exam-code","change-exam-code-modal",$rand,"_change_exam_code");

}

if ($exam_question)
	confirm3("Exam List","Do you want to <b>Add/Change Question Selection for this Exam</b>?","question","question-modal",$rand,2);
else
	no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","question","question-modal",$rand,2);

if ($exam_student)
	confirm3("Exam List","Do you want to <b>Add/Change Student for this Exam</b>?","student","student-modal",$rand,6);
else
	no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","student","student-modal",$rand,6);

confirm3("Exam List","Do you want to view Enrolled Course?","list_course","list_course_modal",$rand,3);

if ($exam_schedule)
	confirm3("Exam List","Do you want to <b>Schedule this Exam</b>?<br><br><small><font color=blue>Exam will not start before Exam Date and Time.</font></small>","schedule_exam","schedule_exam_modal",$rand,5);
else
	no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","schedule_exam","schedule_exam_modal",$rand,5);

if ($exam_time)
	confirm3("Exam List","Do you want to <b>Set Maximum Adjust Time for this exam</b>?<br><br><small><font color=blue>Extra Time can be adjusted upto this time only.</font></small>","time-adjustment","time-adjustment-modal",$rand,"_time_adjustment");
else
	no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","time-adjustment","time-adjustment-modal",$rand,"_time_adjustment");

if ($exam_result)
{
	confirm3("Exam List","Do you want to <b>View Result</b> of Exam?","list_exam","list_exam_modal",$rand,4);
	confirm3("Exam List","Do you want to <b>Publish Result</b> of Exam?","result-publish","result-publish-modal",$rand,44);
	confirm3("Exam List","Do you want to <b>View Student Individual Result</b>?","individual-exam","individual-exam-modal",$rand,"_individual_exam");
}
else
{
	no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","list_exam","list_exam_modal",$rand,4);

	no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","individual-exam","individual-exam-modal",$rand,"_individual_exam");

	no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","result-publish","result-publish-modal",$rand,44);

}

if ($exam_student_view)
{
	confirm3("Exam List","Do you want to <b>View Student Credentials</b> (Username/Password)?","print_user","print_user_modal",$rand,"_print_user_credentials");
}
else
{
		no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","print_user","print_user_modal",$rand,"_print_user_credentials");

}

if ($exam_start)
{
	confirm3("Exam List","Do you want to <b>Start</b> This Exam?<br><br><small><font color=blue>Starting Exam will set current date and time and count-down of time will begin.</font></small>","start-exam","start-exam-modal",$rand,"_start_exam");
confirm3("Exam List","Do you want to <b>Add Extra Time to</b> This Exam?<br><br><font color=blue>Adding Extra time can be add with proper reason for this and it will be recorded and submitted.</font>","extra-time","extra-time-modal",$rand,"_extra_time");

	confirm3("Exam List","Do you want to <b>View/Print Attendance Sheet</b> This Exam?","attendance-sheet","attendance-sheet-modal",$rand,"_attendance-sheet");

}
else
{
		no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","start-exam","start-exam-modal",$rand,"_start_exam");
		no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","extra-time","extra-time-modal",$rand,"_extra_time");
		no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","attendance-sheet","attendance-sheet-modal",$rand,"_attendance-sheet");

	
}

?>

<script>
$('button.view').click(function(ev){
	ev.preventDefault();
	$('#exam_view').removeData('bs.modal')
	$('div#exam_view .modal-body').css('max-height', $(window).height() * 0.5);
	$('div#exam_view .modal-body').css('overflow-y', 'auto'); 
	var ID = $(this).data('id');
	var center_id = $(this).data('id2');
	$.post("<?php echo $MAIN_URL;?>ajax.html",
	{
	  exam_id: ID,
		center_id: center_id,
		task:"view_exam",
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
    <span><small>User and Question(s) must be assigned for exam to schedule/start.</small></span><br>
    <span><small>After Exam Schedule, user and question can not changed.</small></span>
  </div>
</div>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $MAIN_URL;?>bootstrap/addon/datetimepicker/bootstrap-datetimepicker.css">
<script type="text/javascript" src="<?php echo $MAIN_URL;?>bootstrap/addon/datetimepicker/bootstrap-datetimepicker.js"></script>
<script src="<?php echo $MAIN_URL;?>bootstrap/addon/datetimepicker/date.js"></script>
<script src="<?php echo $MAIN_URL;?>bootstrap/addon/datetimepicker/respond.js"></script>

<?php
dataTable("list_all_exam");
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
<style>
.fa-spin {
  -webkit-animation: fa-spin 4s infinite linear !important;
  animation: fa-spin 4s infinite linear  !important;
}
</style>