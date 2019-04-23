<?php
session_start();

if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";
if ( (!$_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER']))
	echo "<script>window.location='" .$MAIN_URL."start.html';</script>";  

if (isset($_POST['SaveExtraTime']))
{
	$exam_id = FilterNumber($_POST['exam_id']);
	$grace_time = FilterNumber($_POST['txt_extra_time']);
	$grace_reason = FilterString($_POST['txt_extra_time_reason']);
	if ($grace_time == "0")
		$str_update = "UPDATE exam_exam_status SET grace_time=0 AND grace_reason = NULL WHERE exam_id ='$exam_id'  AND center_id = '$center_id';";
	else
		$str_update = "UPDATE exam_exam_status SET grace_time='$grace_time', grace_reason = '$grace_reason' WHERE exam_id ='$exam_id' AND center_id = '$center_id';";
	$db->query($str_update);
}

	// Attendance Sheet
  if (isset($_POST['btnAttendanceSheet']))
  {
    $random = FilterString($_POST['rand']);
    if ($random == $_SESSION['center_exam_list']['rand'])
    {
      $exam_id = FilterNumber($_POST['exam_id']);
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
          <h4 class="modal-title">Print Exam Result</h4>
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
        task:"attendance-sheet",
        user:"center",
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
    }
  }

	// FOR PRINT Student Credentials
	if(isset($_POST['btnPrintUser']))
	{
		$random = FilterString($_POST['rand']);
		if ($random == $_SESSION['center_exam_list']['rand'])
		{
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
							task:"student_login",
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
	popupWin.document.write('<html><head><link href="<?php echo $MAIN_URL;?>print.css" rel="stylesheet" type="text/css" />\n <link href="<?php echo $MAIN_URL;?>bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css" />\n	<style>.print * { visibility: visible !important; } </style>\n</head>\n<body>\n' + "<h3><strong><p align='center' style='margin:0;'>Student Credentials</p></strong></h3><br><div class='print visible-print-block'>\n" +divToPrint.innerHTML + '\n</div></body>\n</html>');
//	popupWin.window.print();
//	popupWin.window.close();
}
</script>
			<?php
		}
	}

if (isset($_POST['btnResultSubmit']))
{
	$random = FilterString($_POST['rand']);
	if ($random == $_SESSION['center_exam_list']['rand'])
	{
		$exam_id = FilterNumber($_POST['exam_id']);
		$sql_start = "UPDATE exam_exam_status SET send_for_approve = 'Y' WHERE exam_id = '$exam_id' AND center_id = '$center_id'; ";
		$db->query($sql_start);
	}
}

if (isset($_POST['btnStart']))		// Start Exam
{
	$random = FilterString($_POST['rand']);
	if ($random == $_SESSION['center_exam_list']['rand'])
	{
		$exam_id = FilterNumber($_POST['exam_id']);
		$sql_start = "UPDATE exam_exam_status SET start_time = NOW() WHERE exam_id = '$exam_id' AND center_id = '$center_id';";
		$db->query($sql_start);
	}
}

if (isset($_POST['btnPublishResult']))
{
	$random = FilterString($_POST['rand']);
	if ($random == $_SESSION['center_exam_list']['rand'])
	{
		$exam_id = FilterNumber($_POST['exam_id']);
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
        <h4 class="modal-title">Print Exam Result</h4>
      </div>
      <div class="modal-body">
        <div id="PrintStudent">
          <div align=center><span class="fa fa-spinner fa-5x fa-spin"></span><br><h3>Loading...</h3></div>
        </div>
      </div>
        <div class="modal-footer">
          <button type="button" id="btnPrintStudent" class="btn btn-primary" tabindex="-1" onClick="javascript:PrintDiv('PrintStudent')"><i class="fa fa-print"></i> Print Result</button>
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
			task:"publish-result",
			user:"center",
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
	}
}

if (isset($_POST['btnPrintResult']))
{
	$random = FilterString($_POST['rand']);
	if ($random == $_SESSION['center_exam_list']['rand'])
	{
		$exam_id = FilterNumber($_POST['exam_id']);
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
        <h4 class="modal-title">Print Exam Result</h4>
      </div>
      <div class="modal-body">
        <div id="PrintStudent">
          <div align=center><span class="fa fa-spinner fa-5x fa-spin"></span><br><h3>Loading...</h3></div>
        </div>
      </div>
        <div class="modal-footer">
          <button type="button" id="btnPrintStudent" class="btn btn-primary" tabindex="-1" onClick="javascript:PrintDiv('PrintStudent')"><i class="fa fa-print"></i> Print Result</button>
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
			task:"print-result",
			user:"center",
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
	}
}

if (isset($_POST['btnExtraTime']))		// Add Extra Time
{
	$random = FilterString($_POST['rand']);
	if ($random == $_SESSION['center_exam_list']['rand'])
	{
		$exam_id = FilterNumber($_POST['exam_id']);
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
                    <select class="form-control" name="txt_extra_time" id="txt_extra_time">
                    <?php

										$strMaxTime = "SELECT max_time_adjustment FROM exam_exam WHERE exam_id='$exam_id';";
										$query_max_time = $db->query($strMaxTime);
										$row_max_time = $db->fetch_object($query_max_time);
										$max_extra_time = $row_max_time->max_time_adjustment;
										
										if ($max_extra_time == 0) $max_extra_time = 10;
										$db->free($query_max_time);
										unset($strMaxTime, $query_max_time, $row_max_time);

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

?>
<?php
					$strTitle = "All Exam";

if (isset($_POST['isSearchFilter']))
{
	$random = FilterString($_POST['rand']);
	if ($random == $_SESSION['center_exam_list']['rand'])
	{

		if (isset($_POST['btnSearchFilter']))		//Search
		{
			$exam_status = FilterNumber($_POST['exam_type']);
			$date_type = FilterNumber($_POST['date_type']);
			$start_date = FilterDateTime($_POST['start_date']);
			$end_date = FilterDateTime($_POST['end_date']);

			if (isset($_POST['exam_type']))
			{
				$today = date("Y-m-d");
				if ($exam_status == 1) 		// All Exam
				{
					if ( (isset($_POST['start_date'])) || (isset($_POST['end_date'])) ) 
					{
						$strStatus = " AND ";
						$strTitle = "All Exam";
					}
				}
				if ($exam_status == 2)	// NOT Schedule Exam Only
				{
					$strStatus = " AND (exam_exam_center.schedule_date is NULL OR exam_exam_center.schedule_date > NOW() ) AND exam_exam_status.start_time IS NULL AND exam_exam_status.end_time IS NULL ";
					$strTitle = "Not Schedule Exam";
				}

				if ($exam_status == 3)	// Schedule Exam Only
				{
					$strStatus = " AND (exam_exam_center.schedule_date is NOT NULL AND date(exam_exam_center.schedule_date) = '$today' AND exam_exam_center.schedule_date <= NOW() ) AND exam_exam_status.start_time IS NULL AND exam_exam_status.end_time IS NULL ";
					$strTitle = "Schedule Exam";
				}

				if ($exam_status == 4)	// Schedule Expire Exam Only
				{
					$strStatus = " AND (exam_exam_center.schedule_date is NOT NULL AND date(exam_exam_center.schedule_date) < '$today' ) AND exam_exam_status.start_time IS NULL AND exam_exam_status.end_time IS NULL ";
					$strTitle = "Schedule Expire Exam";
				}

				if ($exam_status == 5)	// Running Exam Only
				{
					$strStatus = " AND exam_exam_center.schedule_date is NOT NULL AND exam_exam_status.start_time IS NOT NULL AND exam_exam_status.end_time IS NULL ";
					$strTitle = "Running Exam";
				}

				if ($exam_status == 6)	// Exam Not Sent for Approval
				{
					$strStatus = " AND exam_exam_center.schedule_date is NOT NULL AND exam_exam_status.start_time IS NOT NULL AND exam_exam_status.end_time IS NOT NULL AND exam_exam_status.full_close = 'N' AND (exam_exam_status.send_for_approve = 'N' OR exam_exam_status.send_for_approve IS NULL) ";
					$strTitle = "Exam Not Sent for Approval";
				}

				if ($exam_status == 7)	// Awaiting Approval Only
				{
					$strStatus = " AND exam_exam_center.schedule_date is NOT NULL AND exam_exam_status.start_time IS NOT NULL AND exam_exam_status.end_time IS NOT NULL AND exam_exam_status.full_close = 'N' AND exam_exam_status.send_for_approve = 'Y' ";
					$strTitle = "Awaiting Approval Exam";
				}

				if ($exam_status == 8)	// Result Published Only
				{
					$strStatus = " AND exam_exam_center.schedule_date is NOT NULL AND exam_exam_status.start_time IS NOT NULL AND exam_exam_status.end_time IS NOT NULL AND exam_exam_status.full_close = 'Y' ";
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
			unset($_SESSION['center_list_exam']['search'], $_SESSION['center_list_exam']['exam_status'], $_SESSION['center_list_exam']['title']);

		}	//Search		

		if (isset($_POST['btnSearchClear']))
		{
			unset($_SESSION['center_list_exam']['search'], $_SESSION['center_list_exam']['exam_status'], $_SESSION['center_list_exam']['title']);
		}
	}
}


$sql = 
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

$strORDER = "	ORDER BY exam_exam.exam_create_date DESC, exam_exam_status.full_close DESC, exam_exam_status.end_time DESC, exam_exam_status.start_time DESC, (exam_exam_center.schedule_date IS NULL), exam_exam_center.schedule_date DESC, exam_exam_status.final_submit_time,  exam_exam_status.exam_id, exam_exam_status.center_id ";



if (isset($_SESSION['center_list_exam']['search']))
{
	$strWHERE = $_SESSION['center_list_exam']['search'];
}
else 
{
	$strWHERE = " $strStatus $strSearchDate ";
	$_SESSION['center_list_exam']['search'] = $strWHERE;
}

if (isset($_SESSION['center_list_exam']['exam_status']))
{
	$exam_status = $_SESSION['center_list_exam']['exam_status'];	
}
else
{
	$_SESSION['center_list_exam']['exam_status'] = $exam_status ;	
}

if (isset($_SESSION['center_list_exam']['title']))
{
	$strTitle   = $_SESSION['center_list_exam']['title'];	
}
else
{
	$_SESSION['center_list_exam']['title'] = $strTitle ;	
}

$sql2 = "$sql $strWHERE $strORDER";

$sql2 = str_replace("\n","",$sql2);
$sql2 = str_replace("\n\r","",$sql2);
$sql2 = str_replace("  AND    	ORDER "," ORDER ",$sql2);

$sql2 = str_replace(" AND   AND "," AND ",$sql2);
$sql2 = str_replace(" AND  AND "," AND ",$sql2);
$sql2 = str_replace(" AND AND "," AND ",$sql2);

$sql2 = str_replace("AND   	ORDER", " ORDER",$sql2);
$sql2 = str_replace(" WHERE   ORDER "," ORDER ",$sql2);
$sql2 = str_replace(" WHERE  ORDER "," ORDER ",$sql2);
$sql2 = str_replace(" WHERE ORDER "," ORDER ",$sql2);
$sql2 = str_replace(" WHERE   	ORDER "," ORDER ",$sql2);
$sql2 = str_replace(" WHERE AND "," WHERE ",$sql2);
$sql2 = str_replace(" WHERE   AND "," WHERE ",$sql2);
$sql2 = str_replace(" WHERE  AND "," WHERE ",$sql2);

$query = $db->query($sql2);

$rand = RandomValue(20);
$_SESSION['center_exam_list']['rand'] = $rand;
?>
<h3 class="page-header"><span class="fa fa-edit"></span> Exam List</h3>

<div class="col-sm-12">
	<div class="row pull-left">
  	<strong><u><?php echo $strTitle;?></u></strong>
  </div>
	<div class="row pull-right">
      <form method="post" class="form-inline">
        <div class="form-group">
          <select class="form-control" name="exam_type" title="Select Exam Type">
            <option value="1"<?php if ($exam_status == 1) echo " selected=\"selected\"";?>>All Exam Type</option>
            <option value="2"<?php if ($exam_status == 2) echo " selected=\"selected\"";?>>Not Scheduled Exam</option>
            <option value="3"<?php if ($exam_status == 3) echo " selected=\"selected\"";?>>Scheduled Exam</option>
            <option value="4"<?php if ($exam_status == 4) echo " selected=\"selected\"";?>>Schedule Expire Exam</option>
            <option value="5"<?php if ($exam_status == 5) echo " selected=\"selected\"";?>>Running Exam</option>
            <option value="6"<?php if ($exam_status == 6) echo " selected=\"selected\"";?>>Exam Not Sent for Approval</option>
            <option value="7"<?php if ($exam_status == 7) echo " selected=\"selected\"";?>>Awaiting Approval</option>
            <option value="8"<?php if ($exam_status == 8) echo " selected=\"selected\"";?>>Result Published</option>
          </select>

          <select class="form-control" name="date_type" title="Select Date Type" id="date_type">
            <option value="1">Select Date Type</option>
            <option value="2"<?php if ($date_type == 2) echo " selected=\"selected\"";?>>Start Date</option>
            <option value="3"<?php if ($date_type == 3) echo " selected=\"selected\"";?>>End Date</option>
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

<div class="row">
  <div class="col-lg-12">
    <div class="dataTable_wrapper" id="exam_list">
      <table class="table  table-striped table-bordered table-hover tooltip-demo" id="center_exam_list">
        <thead>
          <tr>
            <th width="5%">SN</th>
            <th>Exam Code</th>
            <th width="15%">Total Time</th>
            <th width="12%">Exam Schedule Date/Time</th>
            <th width="12%">Exam Start Date/Time</th>
            <th width="12%">Exam Close Date/Time</th>
            <th width="105">&nbsp;</th>
          </tr>
        </thead>
        <tbody>
<?php
while ($row = $db->fetch_object($query))
{

	$schedule_time = strtotime($row->schedule_date);
	if ($schedule_time <= time()) $schedule = TRUE;
	else $schedule = FALSE;
	if ($row->schedule_date == NULL) $schedule = FALSE;

	if (!$schedule) $txt_schedule = " disabled=\"disabled\"";
	else $txt_schedule="";

$today = date("Y-m-d");
if ($row->schedule_date_time != NULL && $row->start_time == NULL && $row->schedule_date_time < $today) 
	$schedule_expire = TRUE;
else
	$schedule_expire = FALSE;
?>
        	<tr>
            <td width="5%"><?php echo ++$SN;?></td>
            <td>
		<?php
		$strExamCode = "SELECT * FROM exam_exam_center WHERE exam_id = '$row->exam_id';";
		$query_exam_code = $db->query($strExamCode);
		$no_of_exam_code = $db->num_rows($query_exam_code);

		if ($no_of_exam_code > 1) 
			$MultipleCenter = TRUE;
		else 
			$MultipleCenter = FALSE;
echo "<strong>";
		if ($MultipleCenter)
		{
			$strExamCode2 = "SELECT exam_code from exam_exam_center WHERE exam_id = '$row->exam_id' AND center_id = '$center_id' AND exam_code is NOT NULL";
			$query_exam_code2 = $db->query($strExamCode2);
			$no_of_exam_code2 = $db->num_rows($query_exam_code2);
			if ($no_of_exam_code2 > 0) 
			{
				$row_exam_code2 = $db->fetch_object($query_exam_code2);
				if ($row_exam_code2 != NULL)
					echo "<span title=\"Center Exam Code\">$row_exam_code2->exam_code</span>";
				else
					echo $row->exam_code;
			}
			else
				echo $row->exam_code;
		}
		else
			echo $row->exam_code;
echo "</strong>";

						if ($row->send_for_approve == "Y" && $row->full_close !='Y')
						echo "<div class=\"pull-right\"><span class=\"label label-default\" style=\"font-size:90%;\">Waiting final approval...</span></div>";
						if ($row->full_close == "Y")
            {
?>
          <div class="pull-right"><button class="btn btn-success btn-xs result-publish" id="btnPublishResult2" name="btnPublishResult2"  data-id="<?php echo $row->exam_id;?>" data-id2="<?php echo $row->center_id;?>" data-input-name2="center_id" data-button="btnPublishResult" data-class="btn btn-success" data-input-name="exam_id" data-target="#result-publish-modal" data-toggle="modal" data-url="" data-toggle-tooltip="tooltip" data-placement="top">Result Published</button></div>
<?php
            }
?>
<?php
						if ($row->start_time==NULL) 
						{
							if ($schedule && !$schedule_expire) 
              {
?>
<div class="pull-right"><button class="btn btn-default btn-xs attendance-sheet" title="Attendance-sheet" id="btnAttendanceSheet2" name="btnAttendanceSheet2"  data-id="<?php echo $row->exam_id;?>" data-id2="<?php echo $row->center_id;?>" data-input-name2="center_id" data-button="btnAttendanceSheet" data-class="btn btn-default" data-input-name="exam_id" data-target="#attendance-sheet-modal" data-toggle="modal" data-url="" data-toggle-tooltip="tooltip" data-placement="top"<?php echo $txt_schedule; echo $schedule ;?>>Attendance</button></div>
<?php   }  }  ?>

<?php
if ($row->start_time!=NULL && $row->end_time == NULL) 
{
	echo "<span class=\"label label-success pull-right\" style=\"font-size:90%; margin-top:5px;\"><i class=\"fa fa-spin fa-spinner\"></i> &nbsp; <b>On Going Exam</b></span>";
}

						if ($row->start_time==NULL) 
						{
							if ($schedule && $schedule_expire) 
								echo "<span class=\"label label-danger pull-right\" style=\"font-size:90%;\">Start Time Expire.</span>";
						}

						?></td>
            <td width="15%"><?php 
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
						echo " $row->total_time minutes";
						if (!$row->start_time==NULL) 
						{
							if ($row->grace_time > 0) echo "<br><i><small><small>+ ".$row->grace_time . " minutes ($row->grace_reason)</small></small></i>";
						}
						?> </td>
            <td width="12%"><?php 
						if (!$schedule) echo "<span class=\"label label-warning\" style=\"font-size:90%;\">Not Scheduled</span> ";
						else echo " $row->format_schedule_date";

						?></td>
            <td width="12%"><?php 
						if ($row->start_time==NULL) 
						{
							if ($schedule && !$schedule_expire) 
								echo "<span class=\"label label-inverse\" style=\"font-size:90%;\">Not Started</span>";
						}
						else
							echo $row->format_start_time;
						;?>
            
            </td>
            <td width="12%">
<?php 
if ($row->start_time!=NULL && $row->end_time == NULL) 
{
	echo "<h4 style=\"margin:0\"><span class=\"label label-warning\" style=\"margin-top:5px;\"><span id=\"elapsed$row->exam_id\"></span> remaining.</span></h4>";

	$strStart = strtotime($row->start_time);
//	$startTime = $row->start_time;
	$Total_Time = $row->total_time + $row->grace_time;
	$strEnd = strtotime("+$Total_Time minutes",$strStart);
	$endTime = date("Y-m-d H:i:s",$strEnd);

	$diff = $strEnd - time();
CountDownRemainingTime($diff,"elapsed".$row->exam_id,$row->exam_id,"list.html");
}
						else
							echo $row->format_end_time;
						;?>						</td>
            <td width="105">
            	<?php 

							if ( $schedule && $schedule_expire )
								$schedule = " disabled =\"disabled\"";

							if ($schedule && $row->end_time == NULL)
							{
							?>

              <button title="View Student Credentials" class="btn btn-info btn-circle btn-sm print_user" type="submit" id="btnPrintUser2" name="btnPrintUser2" data-id="<?php echo $row->exam_id;?>" data-button="btnPrintUser" data-class="btn btn-info" data-input-name="exam_id" data-target="#print_user_modal" data-toggle="modal" data-url="" data-toggle-tooltip="tooltip" data-placement="top"<?php echo $txt_schedule; echo $schedule ;?>><span class="fa fa-user"></span></button>
              <?php
							}
							?>

            	<?php 
							if ($row->start_time == NULL )
							{ ?>

              <button title="Start Exam" class="btn btn-success btn-circle btn-sm start-exam" type="submit" id="btnStart2" name="btnStart2"  data-id="<?php echo $row->exam_id;?>" data-button="btnStart" data-class="btn btn-success" data-input-name="exam_id" data-target="#start-exam-modal" data-toggle="modal" data-url=""  data-toggle-tooltip="tooltip" data-placement="top"<?php echo $txt_schedule; echo $schedule ;?>><span class="fa fa-play"></span></button>


              <?php } 
							if ($row->start_time!=NULL && $row->end_time == NULL) 
							{ ?>
              <button title="Extra Time" class="btn btn-warning btn-circle btn-sm extra-time" type="submit" id="btnExtraTime2" name="btnExtraTime2"  data-id="<?php echo $row->exam_id;?>" data-button="btnExtraTime" data-class="btn btn-warning" data-input-name="exam_id" data-target="#extra-time-modal" data-toggle="modal" data-url=""  data-toggle-tooltip="tooltip" data-placement="top"><span class="fa fa-clock-o"></span></button>

              <?php } ?>
              <?php
							if ($row->start_time!=NULL && $row->end_time != NULL) 
							{
								if ($row->full_close == "Y")
									$disabled = " disabled=\"disabled\"";
								else
									$disabled="";

if ($row->send_for_approve == 'Y')
	$dis_send_approve = " disabled=\"disabled\"";
else
	$dis_send_approve = "";
	
if ($row->is_approved == 'Y')
	$dis_is_approve = " disabled=\"disabled\"";
else
	$dis_is_approve = "";

								 ?>
              <button title="Enter Practical Number" class="btn btn-success btn-circle btn-sm practical-mark" type="submit" id="btnPracticalNumber2" name="btnPracticalNumber2"  data-id="<?php echo $row->exam_id;?>" data-button="btnPracticalNumber" data-class="btn btn-success" data-input-name="exam_id" data-target="#practical-mark-modal" data-toggle="modal" data-url="practical.html"  data-toggle-tooltip="tooltip" data-placement="top"<?php echo $disabled;echo $dis_send_approve; echo $dis_is_approve;?>><span class="fa fa-pencil"></span></button>&nbsp;

              <button title="Print Result" class="btn btn-primary btn-circle btn-sm print-result" type="submit" id="btnPrintResult2" name="btnPrintResult2"  data-id="<?php echo $row->exam_id;?>" data-button="btnPrintResult" data-class="btn btn-primary" data-input-name="exam_id" data-target="#print-result-modal" data-toggle="modal" data-url=""  data-toggle-tooltip="tooltip" data-placement="top"><span class="fa fa-print"></span></button>
<?php

if ($row->send_for_approve == 'Y')
	$dis_send_approve = " disabled=\"disabled\"";
else
	$dis_send_approve = "";
	
if ($row->is_approved == 'Y')
	$dis_is_approve = " disabled=\"disabled\"";
else
	$dis_is_approve = "";
	?>
              <button title="Submit Result" class="btn btn-info btn-circle btn-sm result-submit" type="submit" id="btnResultSubmit2" name="btnResultSubmit2"  data-id="<?php echo $row->exam_id;?>" data-button="btnResultSubmit" data-class="btn btn-info" data-input-name="exam_id" data-target="#result-submit-modal" data-toggle="modal" data-url=""  data-toggle-tooltip="tooltip" data-placement="top"<?php echo $dis_send_approve; echo $dis_send_approve;?>><span class="fa fa-send"></span></button>

              <?php } ?>
              
            </td>
          </tr>
<?php
}
?>
        </tbody>
      </table>
<?php
dataTable("center_exam_list");

confirm2("Assigned Exam","Do you want to <b>Start</b> This Exam?<br><br><small><font color=blue>Starting Exam will set current date and time and count-down of time will begin.</font></small>","start-exam","start-exam-modal",$rand,1);

confirm2("Assigned Exam","Do you want to <b>View/Print Attendance Sheet</b> of This Exam?","attendance-sheet","attendance-sheet-modal",$rand,"_attendance-sheet");


confirm2("Assigned Exam List","Do you want to <b>Add Extra Time to</b> This Exam?<br><br><font color=blue>Adding Extra time can be add with proper reason for this and it will be recorded and submitted.</font>","extra-time","extra-time-modal",$rand,3);

confirm2("Assigned Exam","Do you want to <b>Enter Practical Marks of students</b> of this Exam?","practical-mark","practical-mark-modal",$rand,4);

confirm2("Assigned Exam","Do you want to <b>Print Result</b> of this Exam?","print-result","print-result-modal",$rand,5);
confirm2("Assigned Exam","Do you want to <b>Sumit Result</b> of this Exam?","result-submit","result-submit-modal",$rand,6);

confirm2("Assigned Exam","Do you want to <b>View Student Credentials</b> (Username/Password)?","print_user","print_user_modal",$rand,7);

confirm2("Assigned Exam","Do you want to <b>Publish Result</b> of this Exam?","result-publish","result-publish-modal",$rand,44);

?>
    </div>
  </div>
</div>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $MAIN_URL;?>bootstrap/addon/datetimepicker/bootstrap-datetimepicker.css">
<script type="text/javascript" src="<?php echo $MAIN_URL;?>bootstrap/addon/datetimepicker/bootstrap-datetimepicker.js"></script>
<script src="<?php echo $MAIN_URL;?>bootstrap/addon/datetimepicker/date.js"></script>
<script src="<?php echo $MAIN_URL;?>bootstrap/addon/datetimepicker/respond.js"></script>

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
