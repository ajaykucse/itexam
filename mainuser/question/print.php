<?php 
@session_start();
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>";  

if (isset($_POST['btnBack']))
	echo "<script>window.location='list.html';</script>";  


	$SQL = "SELECT exam_question.*
	  FROM exam_question
	ORDER BY exam_question.question_id";
	$query_result = $db->query($SQL);
?>
	<div class="pull-right">
    <form method="post" class="form-inline pull-right">
      <button type="submit" name="btnBack" class="btn btn-warning" style="margin-top:20px;"><span class="fa fa-th-list"></span> List of Question / Answer(s)</button>&nbsp;
    <button type="button" onClick="javascript:PrintDiv('ListQuestion')" class="btn btn-primary btn-circle pull-right" style="margin-top:20px;"><span class="fa fa-print"></span></button>
    </form>
  </div>
  <div id="ListQuestion">
    <div class="page-header" style="border-bottom:none; padding-bottom:0px;">
    	<h3>List of All Questions</h3>
    </div>
    <div class="panel-body">
    	<div class="row">
        <table width="100%" border="0" cellpadding="10" cellspacing="10" class="table table-responsive">
      <?php
      $i='0';
        while ($row = $db->fetch_object($query_result))
        {
        ?>
            <tr>
              <td width="60" valign="top"><strong><?php echo ++$i;?>.</strong></td>
              <td valign="top">
              <?php
									echo "<strong>$row->question</strong>";
                  $strAnswer = "SELECT * FROM exam_answer WHERE question_id = '$row->question_id' ORDER BY answer_id";
                  $query_answer = $db->query($strAnswer);
                  $no_of_answer = $db->num_rows($query_answer);
                  if ($no_of_answer > 0)
                  {
                    while ($row_answer = $db->fetch_object($query_answer))
                    {
                      if ($row_answer->is_correct == "Y") $checked = " checked = \"checked\"";
                      else $checked="";
                      
                      $answer = str_replace("\n","<br>",$row_answer->answer);
                      if ($row->answer_type=="S")
									echo "<table  class='table-checkbox' width=\"100%\" style=\"font-size:90%\"><tr><td valign=\"top\" width=\"20px\"><input type=\"radio\" disabled $checked></td><td>$answer</td></tr></table>";

                      if ($row->answer_type=="M") 
									echo "<table class='table-checkbox' width=\"100%\" style=\"font-size:100%\"><tr><td valign=\"top\"  width=\"20px\"><input type=\"checkbox\" disabled $checked></td><td>$answer</td></tr></table>";

                    }
                  }
      
                  if ($row->is_active != "Y") echo "<span style=\"font-weight:normal\" class=\"label label-info\">Not Active</span>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;";
                  if ($row->rand_answer != 'Y')
                    echo "<span class=\"label label-danger\" style=\"font-weight:normal\"><i>Do not Shuffle Answer</i></span> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;";
                  if ($row->answer_type == 'M') echo "<span style=\"font-weight:normal\" class=\"label label-warning\"><i>Multiple Answer</i></span>";
            
            ?>
              </td>
            </tr>
        <?php } ?>        	
        </table>
      </div>
    </div>
  </div>
  <style>
	.table-checkbox p
	{
		margin:0px;
		padding:0px;
	}
	</style>
<script type="text/javascript">
function PrintDiv(div)
{
	var divToPrint = document.getElementById(div);
	var popupWin = window.open('','_blank','width=800,height=600');
	popupWin.document.open();
	popupWin.document.write('<html><head><title>Question Bank :: List of All Questions</title>\n<link href="<?php echo $MAIN_URL;?>print.css" rel="stylesheet" type="text/css">\n <style>.print * { visibility: visible !important; } </style> \n	</head>\n<body>\n' + "<h3><strong><p align='center' style='margin:0;'><?php echo $software;?></p></strong></h3><br><div class='print visible-print-block'>\n" +divToPrint.innerHTML + '\n</div></body>\n</html>');
//	popupWin.window.print();
//	popupWin.window.close();
}
</script>