<?php 
session_start();
define ("WEB-PROGRAM","Online Exam");
define ("CODER","Sunil Kumar K. C.");
if (file_exists("security.php")) include_once "security.php";

require_once "main_url.inc.php";
$URL = main_url();
$MAIN_URL = $URL;

if (isset($_SESSION['ONLINE-EXAM-SIMULATOR-STUDENT']))
{
	if ( ($_POST['task'] == "exam-started-or-not") && (FilterNumber($_POST['center_id']) > 0) && (FilterNumber($_POST['exam_id']) > 0) )
	{

		require_once "includes/functions.inc.php";
	
		require_once "main_url.inc.php";

		$exam_id = FilterNumber($_POST['exam_id']);
		$center_id = FilterNumber($_POST['center_id']);

		$config = new config();
		$dbname = $config->dbname;
		$dbuser = $config->dbuser;
		$dbhost = $config->dbhost;
		$dbpass = $config->dbpass;
		unset($config);
	
		include_once "includes/db.inc.php";
		$db = new Database($dbhost,$dbuser,$dbpass,$dbname) ;

		$strExam = "SELECT * FROM exam_exam_status WHERE exam_id = '$exam_id' AND center_id = '$center_id';";
		$query_exam = $db->query($strExam);
		$row_exam = $db->fetch_object($query_exam);
		$start_time = $row_exam->start_time;

		$db->free($query_exam);
		$db->close();
		unset($db,$strExam, $query_exam, $row_exam);
		if ($start_time != NULL) 
		{
			$_SESSION['user']['exam']['started'] = TRUE;
			echo "<script>window.location='" .$URL."student/exam.html';</script>";
		}
		
	}
}
else if (isset($_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER']) OR ($_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER']) )
{
	require_once "includes/functions.inc.php";

	require_once "main_url.inc.php";
	$URL = main_url();

	if ( ($_POST['task'] == "UpdateMinimumQuestion") && (FilterNumber($_POST['exam_id']) > 0) )
	{
		$config = new config();
		$dbname = $config->dbname;
		$dbuser = $config->dbuser;
		$dbhost = $config->dbhost;
		$dbpass = $config->dbpass;
		unset($config);
	
		include_once "includes/db.inc.php";
		$db = new Database($dbhost,$dbuser,$dbpass,$dbname) ;

		?>
<table class="table table-bordered" id="total_question">
          <thead>
            <tr>
              <th>SN</th>
              <th>Chapter No.</th>
              <th>Minimum Question No.</th>
              <th>Selected Question No.</th>
            </tr>
          </thead>
          <tbody>
<?php
$exam_id = FilterNumber($_POST['exam_id']);
$strChapter = "SELECT exam_exam.exam_id,
       exam_type_chapter_question.chapter_no,
       exam_type_chapter_question.no_of_question
  FROM (exam_exam exam_exam
        INNER JOIN exam_exam_type exam_exam_type
           ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id))
       INNER JOIN exam_type_chapter_question exam_type_chapter_question
          ON (exam_type_chapter_question.exam_type_id =
                 exam_exam_type.exam_type_id AND exam_type_chapter_question.no_of_question > 0)
	WHERE exam_exam.exam_id = '$exam_id'
;";
$query_chapter = $db->query($strChapter);

while ($row_chapter = $db->fetch_object($query_chapter))
{
$strTotal = "SELECT count(chapter_no) as `total_question` FROM exam_exam_question WHERE exam_id= '$exam_id' AND chapter_no = '$row_chapter->chapter_no';";
$queryTotal = $db->query($strTotal);
$rowTotal = $db->fetch_object($queryTotal);
$total_question = $rowTotal->total_question;

$total_total_question = $total_total_question + $total_question;

if ($total_question >= $row_chapter->no_of_question) $isMax = TRUE;
else $isMax = FALSE;

if ($isMax)	$font = "green";
else $font="red";

$str_total_question = "<span style=\"font-size:130%; color:$font\">$total_question</span>";
$total_no_of_question = $total_no_of_question + $row_chapter->no_of_question;
$db->free($queryTotal);
unset($query_total, $strTotal, $rowTotal);
?>
            <tr<?php if ($isMax == FALSE) echo " bgcolor=#fbcccc style=font-size:110%;color:red";?>>
              <td width="5%"><?php echo ++$SN;?></td>
              <td id="chapter_no_<?php echo $row_chapter->chapter_no;?>" data-id="<?php echo $row_chapter->no_of_question;?>"><?php echo $row_chapter->chapter_no;?></td>
              <td><?php echo $row_chapter->no_of_question;?></td>
              <td><?php echo $str_total_question ;?></td>
            </tr>
<?php
}
?>
          </tbody>
          <tfoot>
            <tr>
              <td width="5%"></td>
              <td><strong>Total</strong></td>
              <td><?php echo $total_no_of_question;?></td>
              <td><?php echo $total_total_question ;?></td>
            </tr>
          </tfoot>
        </table>    
        <?php if ($total_no_of_question > $total_total_question)
				echo "<h4 align=\"center\"><font color=red>Total Question is less than Minimum no. of question</font></h4>";
				?>
    <?php
	}


	if ( ($_POST['task'] == "update_exam_type_question") && (FilterNumber($_POST['exam_type_id']) > 0) && (FilterNumber($_POST['chapter_no']) > 0) )
	{
		require_once "includes/functions.inc.php";

		$exam_type_id = FilterNumber($_POST['exam_type_id']);
		$chapter_no = FilterNumber($_POST['chapter_no']);
	
		$config = new config();
		$dbname = $config->dbname;
		$dbuser = $config->dbuser;
		$dbhost = $config->dbhost;
		$dbpass = $config->dbpass;
		unset($config);
	
		include_once "includes/db.inc.php";
		$db = new Database($dbhost,$dbuser,$dbpass,$dbname) ;
	
		$strChapter = "SELECT * FROM exam_type_chapter_question WHERE exam_type_id = '$exam_type_id' AND chapter_no = '$chapter_no';";
		$query_chapter = $db->query($strChapter);
		$no = $db->num_rows($query_chapter);
		if ($no > 0)
		{
			$row_chapter = $db->fetch_object($query_chapter);
			$no_question = $row_chapter->no_of_question;
		}
		else $no_question = 0;
			$db->free($query_chapter);
		unset($no, $strChapter, $query_chapter, $row_chapter);
		echo $no_question;
	}

	if (isset($_FILES['file']) && FilterString($_POST['task']="image_upload") )
	{
		if ($_FILES['file']['name']) {
			if (!$_FILES['file']['error']) {		
					$FILE = $_FILES['file']['name'];
					$getfileName = explode(".",$FILE);
					$txtExt= $getfileName[count($getfileName)-1];
		
					$filename = $getfileName[0] . time() . ".".$txtExt;
					$filename = str_replace(" ","_",$filename);
		
					$ROOT = getLocationDir();
		
					$destination = $ROOT.'upload/' . $filename; //change this directory

					$location = $_FILES["file"]["tmp_name"];
					move_uploaded_file($location, $destination);
					echo $URL ."upload/" . $filename ;//change this URL
			}
			else
			{
				echo  $message = 'Ooops!  Your upload triggered the following error:  '.$_FILES['file']['error'];
			}
		}
		
	}

	if ( (FilterString($_POST['task']) == "excel_upload") && ($_FILES['excel_file']) && ($_POST['exam_id']) && ($_POST['center_id']) )
	{
		$exam_id = FilterNumber($_POST['exam_id']);
		$center_id = FilterNumber($_POST['center_id']);

		$config = new config();
		$dbname = $config->dbname;
		$dbuser = $config->dbuser;
		$dbhost = $config->dbhost;
		$dbpass = $config->dbpass;
		unset($config);
	
		include_once "includes/db.inc.php";
		$db = new Database($dbhost,$dbuser,$dbpass,$dbname) ;

		include_once "includes/functions.inc.php";
		if (strlen($_FILES['excel_file']['tmp_name']) > 0) 
		{
			$FileName = $_FILES['excel_file']['tmp_name'];
			$fileSize = $_FILES['excel_file']['size'];

			if ($fileSize < 1)
			{
				$_SESSION["fileSize"] = ''; 

				$error[]="Blank File Uploaded.";	
				$isError = TRUE;
			}
			$data = ExcelUpload($_FILES['excel_file']['tmp_name']);

	$field = count($data[1]);
	if ($field == 3)
	{
?>
			<div class="col-md-12" style="margin-bottom:15px;">
        <fieldset>
          <legend>Select Student</legend>
          <div class="col-md-12">
            <table class="table table-bordered">
<?php 
	if ($field > 0);
  $caption = array("","IT Serial No.", "Reg. No.", "Student Name");
		echo "<tr><td><label>SN</label></td>";

	for ($i=1; $i<=$field; $i++)
	{
			echo "<td><label>". $caption[$i]."<br><input type=\"checkbox\" name=\"include[$i]\" value=\"1\" checked=\"checked\"> Include This </label></td>";
	}
	if ($field > 0);
		echo "</tr>";
		$SN=0;
		foreach ($data as $K => $val)
		{
			
	?>            
								<tr>
                  <td><?php echo $SN++;?></td>
		<?php 
		foreach ($data[$K] as $KEY => $value)
		{
			?>            
									<td><div class="pull-left"><input type="text" value="<?php echo $value;?>" name="field[<?php echo $KEY;?>][]"></div></td>
	
		<?php } ?>
			<td width="150px;">
			<?php 
			if ($K == 1) 
			{
				?>
				<label style="font-weight:normal;"><input type="checkbox" name="show" value="HEADING" checked="checked"> Column Heading</label>
			<?php } ?>
			</td>
								</tr>
	<?php
		}
		?>
							</table>
						</div>

						<div class="col-md-12" align="center" style="margin-top:15px;">
							<button class="btn btn-success" name="btnUpdateStudent" type="submit"><i class="fa fa-save"></i> Assign Student to Exam and Center</button>
							<input type="hidden" name="exam_id" value="<?php echo $exam_id;?>">
							<input type="hidden" name="center_id" value="<?php echo $center_id;?>">
							<input type="hidden" name="rand" value="<?php echo $_SESSION['rand']['exam_student_selection'];?>">
						</div>
					</fieldset>
				</div>
	<?php
			} // FIELD 3 ONLY
		else
		 echo "<div class=\"col-md-12\" style=\"margin-top:15px;\"><fieldset><div class=\"col-md-6 col-md-offset-3\" style=\"margin:15px;\"><font color=red>There should be only 3 field in excel sheet. <br><br> Column 1 = IT Serial No. <br> Column 2 = Reg No <br>Column 3 = Student Name.</h4></font></div></fieldset></div>";
		}
	}


	if (isset($_POST['chapter_no']) && ($_POST['task'] == "update_no_of_question") && ($_POST['exam_type_id']) && ($_POST['exam_id']) )
	{
		$chapter_no = FilterNumber($_POST['chapter_no']);
		$exam_type_id = FilterNumber($_POST['exam_type_id']);
		$exam_id = FilterNumber($_POST['exam_id']);
		$config = new config();
		$dbname = $config->dbname;
		$dbuser = $config->dbuser;
		$dbhost = $config->dbhost;
		$dbpass = $config->dbpass;
		unset($config);
	
		include_once "includes/db.inc.php";
		$db = new Database($dbhost,$dbuser,$dbpass,$dbname) ;
	  $strChapter = "SELECT no_of_question FROM exam_type_chapter_question WHERE exam_type_id = '$exam_type_id' AND chapter_no = '$chapter_no'";

		$query_chap_no = $db->query($strChapter);
		$row = $db->fetch_object($query_chap_no);
		$question_no= $row->no_of_question;
		$db->free($query_chap_no);
		unset($row, $query_chap_no, $strChapter);
		?>
<fieldset>
	<legend>No of question for selected Chapter <?php echo $chapter_no;?></legend>
      <div class="form-group">
        <label class="col-md-4">No. of Question : </label>
				<div class="col-md-4"><input id="question_no" type="text" data-id="<?php echo $question_no;?>" readonly value="<?php echo $question_no ;?> Questions" class="form-control"></div>
      </div>

  </fieldset>
  <fieldset style="margin-bottom:15px;">
  	<legend>Select Question for Display</legend>
	<div class="row">
    <div class="col-md-12">
<?php
		$strSelectQuestion = "
SELECT exam_question.*,
       exam_exam_question.question_id AS `QUES_ID`,
       exam_exam_question.chapter_no,
       exam_exam_question.exam_id
  FROM exam_exam_question exam_exam_question
       RIGHT OUTER JOIN exam_question exam_question
          ON (exam_exam_question.question_id = exam_question.question_id AND exam_exam_question.exam_id = '$exam_id')
 WHERE (exam_question.chapter_no = '$chapter_no' AND exam_question.is_active = 'Y' AND exam_question.exam_type_id = '$exam_type_id');
 ";

		$query_question = $db->query($strSelectQuestion);
		$no_of_question = $db->num_rows($query_question);
		if ($no_of_question > 0)
		{
?>    
    	<table class="question table" width="100%" border="0" cellpadding="5" cellspacing="5" style="border: 1px solid #CCCCCC; margin-bottom:5px;">
      	<tr>
        	<th></th>
          <th>ID</th>
          <th>Question</th>
        </tr>
    <?php
		while ($row_ques = $db->fetch_object($query_question))
		{
			?>
      	<tr>
        	<td valign="center" align="center" width="25"><input type="checkbox" id="question_id_<?php echo $row_ques->question_id;?>" class="question" name="question_id[<?php echo $row_ques->question_id;?>]" value="1"<?php if ($row_ques->QUES_ID == $row_ques->question_id) echo " checked=\"checked\"";?>></td>
          <td valign="top" align="center" width="50"><label for="question_id_<?php echo $row_ques->question_id;?>"><?php echo $row_ques->question_id; ?></label></td>
        	<td><label for="question_id_<?php echo $row_ques->question_id;?>"><?php echo stripslashes($row_ques->question);?></label></td>
        </tr>
<?php			
		}
		$db->free($query_question);
		unset($row_ques,$query_question,$strSelectQuestion);

		$str = "SELECT count(*) as `CNT` FROM exam_exam_question WHERE exam_id = '$exam_id' AND chapter_no = '$chapter_no';";
		$query_str = $db->query($str);
		$row3 = $db->fetch_object($query_str);
		$cnt = $row3->CNT;
		$db->free($query_str);
		unset($row3,$query_str,$str);
		
		$db->close();
if ($question_no > $cnt) $disabled = " disabled";
else $disabled="";
	?>
      </table>
<?php
		}
		else 
		{
			echo "<div align=center><strong><font color=red>No question available for this exam-type.</font></strong></div>";
			$disabled = " disabled";
		}
		?>
    </div>
  </div>

  <div align="center" style="margin-top:15px;">
      <button class="btn btn-primary<?php echo $disabled;?>" name="btnUpdate" id="btnUpdate"><i class="fa fa-save"></i> Save </button>
  </div>
	</fieldset>
  <div id="ajax_test"></div>
<style>
.question label
{
	font-weight:normal;
}
</style>
<script>
$(".question").click(function() {
//	var total_question = $("#question_no").data("id");

	var total_question = $("#chapter_no_<?php echo $chapter_no;?>").data("id");

	var question_no = $(".question:checked").size();
	if (question_no >= total_question) 
		$('#btnUpdate').removeClass("disabled");
	else
			$('#btnUpdate').addClass("disabled");

	AutoSaveQuestion();
	
			function AutoSaveQuestion()
			{
				var total_question = $("#question_no").data("id");
				var question_no = $(".question:checked").size();
//				if (question_no >= total_question) 
	//			{
						$.ajax({
								data: $( "#frm_question" ).serializeArray(),
								type: "POST",
									 url: "<?php echo $MAIN_URL;?>ajax2.html",
								success: function(data){
									UpdateMinimumQuestion();
									$("div#ajax_test").html(data);
								}
						});
		//		}
			};
			
			function UpdateMinimumQuestion()
			{
				$.post("<?php echo $MAIN_URL;?>ajax.html",
				{
					exam_id: <?php echo $exam_id;?>,
					task:"UpdateMinimumQuestion",
				},
				function(data,status){
					$("div#minimum-question").html(data); 		
				});
			};
});

</script>
	<?php
	}

	if (isset($_POST['exam_type_id']) && ($_POST['task'] == "view_exam_type") )
	{
		$exam_type_id = FilterNumber($_POST['exam_type_id']);
		$config = new config();
		$dbname = $config->dbname;
		$dbuser = $config->dbuser;
		$dbhost = $config->dbhost;
		$dbpass = $config->dbpass;
		unset($config);
	
		include_once "includes/db.inc.php";
		$db = new Database($dbhost,$dbuser,$dbpass,$dbname) ;
		
		$select = " SELECT * FROM exam_exam_type WHERE exam_exam_type.exam_type_id = '$exam_type_id ';";
		$query = $db->query($select);
		$row = $db->fetch_object($query);
		?>
	<h4 class="margin-top-10">Exam Type Details</h4>
		<table class="table table-bordered">
				<tr>
					<th width="50%">Exam Type</th>
					<td width="50%"><?php echo $row->exam_type;?></td>
				</tr>
				<tr>
					<th width="50%">Exam Start Instruction</th>
					<td width="50%"><?php echo $row->start_message;?></td>
				</tr>
				<tr>
					<th width="50%">Exam Ending Message</th>
					<td width="50%"><?php echo $row->end_message;?></td>
				</tr>
	
				<tr>
					<th width="50%">Full Mark</th>
					<td width="50%"><?php echo $row->full_mark;?></td>
				</tr>
	
				<tr>
					<th width="50%">Pass Mark</th>
					<td width="50%"><?php echo $row->pass_mark;?></td>
				</tr>
	
				<tr>
					<th width="50%">MCQ Total Mark</th>
					<td width="50%"><?php echo $row->mcq_mark;?></td>
				</tr>
	
				<tr>
					<th width="50%">Total Practical Mark</th>
					<td width="50%"><?php echo $row->practical_mark;?></td>
				</tr>
		</table>

		<h4 class="margin-top-10">Total Question </h4>
		<table class="table table-bordered margin-top-10">
    	<tr>
					<th width="50%">Total Question </th>
					<td width="50%"><?php echo $row->total_question;?></td>
      </tr>
    </table>
	
		<h4 class="margin-top-10">Chapter and No. of Question per Chapter</h4>
	
		<table class="table table-bordered margin-top-10">
      
			<?php
			$sql_chapter = "SELECT * FROM exam_type_chapter_question WHERE exam_type_id = '$exam_type_id' AND no_of_question > 0;";
			$query_chapter = $db->query($sql_chapter);
			$no_of_chapter = $db->num_rows($query_chapter);
			if ($no_of_chapter > 0)
			{
				while ($row_chapter = $db->fetch_object($query_chapter))
				{
			?>
				<tr>
					<th width="50%">Chapter <?php echo $row_chapter->chapter_no;?></th>
					<td width="50%"><?php echo $row_chapter->no_of_question;?></td>
				</tr>
			<?php 
				}
			}
			else
			{
				?>
				<tr>
					<th colspan="2" width="100%"><font color=red>Chapter and No. of Question not Specified.</th>
				</tr>
		<?php
			}
			?>          
		</table>
	
	<style>
	.margin-top-10
	{
		margin-top:10px;
	}
	img
	{
		width:100%;
		height:100%;
	}
	</style>
	<?php	
		$db->close();
	}

	if (isset($_POST['exam_id']) && ($_POST['task'] == "attendance-sheet") && ($_POST['user']) )
	{
		$exam_id = FilterNumber($_POST['exam_id']);
		$user = FilterString($_POST['user']);

	if ($user != "admin")
		$center_id = FilterNumber($_SESSION['user']['center_id']);
	if ($user == "admin")
		$center_id = FilterNumber($_POST['center_id']);
	
		$config = new config();
		$dbname = $config->dbname;
		$dbuser = $config->dbuser;
		$dbhost = $config->dbhost;
		$dbpass = $config->dbpass;
		unset($config);
	
		include_once "includes/db.inc.php";
		$db = new Database($dbhost,$dbuser,$dbpass,$dbname) ;

		$TXT_CENTER= " AND exam_exam_student.center_id = '$center_id'";
		
/*
		$strSelectStudent = "
SELECT exam_exam.exam_code,
       exam_center.center_name,
       exam_student.it_serial,
       exam_student.reg_no,
       exam_student.name,
       exam_student_exam.mcq_mark,
       exam_student_exam.practical_mark,
       exam_student_exam.exam_id,
       exam_student_exam.student_id,
       exam_student_exam.center_id,
       exam_exam_type.full_mark,
       exam_exam_type.pass_mark
  FROM (((exam_student_exam exam_student_exam
          INNER JOIN exam_exam exam_exam
             ON (exam_student_exam.exam_id = exam_exam.exam_id))
         INNER JOIN exam_student exam_student
            ON (exam_student_exam.student_id = exam_student.student_id))
        INNER JOIN exam_center exam_center
           ON (exam_student_exam.center_id = exam_center.center_id))
       INNER JOIN exam_exam_type exam_exam_type
          ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id)
	WHERE exam_student_exam.exam_id = '$exam_id' $TXT_CENTER;
	";
*/

		$strSelectStudent = "

SELECT exam_student.it_serial,
       exam_student.reg_no,
       exam_student.name,
       exam_center.center_name
FROM (exam_exam_student    exam_exam_student
      INNER JOIN exam_center exam_center
         ON (exam_exam_student.center_id = exam_center.center_id))
     INNER JOIN exam_student exam_student
        ON (exam_exam_student.student_id = exam_student.student_id)
	WHERE exam_exam_student.exam_id = '$exam_id' $TXT_CENTER;
";        
		$query_student = $db->query($strSelectStudent);
    $row_std = $db->fetch_object($query_student);
    $full_mark = (!empty($row_std->full_mark));
    $pass_mark = (!empty($row_std->pass_mark));
    $db->free($query_student);
    unset($row_std);
    
		$query_student = $db->query($strSelectStudent);
		$no_of_student = $db->num_rows($query_student);
	
		if ($no_of_student > 0)
		{
		?>
    <div align="center">
      <b>The Institute of Chartered Accountants of Nepal</b><br>
      Satdobato, Lalitpur<br>
        <?php

      $exam3 = $_SESSION["exam_code"];
					 

					$exam_typeSELECT = $db->query("SELECT exam_exam_type.exam_type FROM (exam_exam_type exam_exam_type  LEFT OUTER JOIN exam_exam exam_exam ON (exam_exam_type.exam_type_id=exam_exam.exam_type_id)) WHERE exam_exam.exam_code = '$exam3';");

					$exam_type = $exam_typeSELECT->fetch_assoc();

					 
					$result=($exam_type['exam_type']);
					$getresult = substr($result, 0,2);

					 echo "$getresult"; ?> Hours  IT Training Course<br>
    <strong>Exam Score Sheet</strong> </div>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tbody>
        <tr>
          <td width="25%"><label><strong>Batch No:</strong></label></td>
          <td width="25%"><?php 
          $str_center = "SELECT exam_code FROM exam_exam WHERE exam_id= '$exam_id';";
          $query_center = $db->query($str_center);
          $row_center = $db->fetch_object($query_center);
          $exam3 = $_SESSION["exam_code"] = $row_center->exam_code;


		$strExamCode = "SELECT * FROM exam_exam_center WHERE exam_id = '$exam_id';";
		$query_exam_code = $db->query($strExamCode);
		$no_of_exam_code = $db->num_rows($query_exam_code);



		if ($no_of_exam_code > 1) 
			$MultipleCenter = TRUE;
		else{
			$exam3 = $_SESSION["exam_code"] = $row_center->exam_code;
			$MultipleCenter = FALSE;
		}

		if ($MultipleCenter)
		{
			$strExamCode2 = "SELECT exam_code from exam_exam_center WHERE exam_id = '$exam_id' AND center_id = '$center_id' AND exam_code is NOT NULL";

			$query_exam_code2 = $db->query($strExamCode2);
			$no_of_exam_code2 = $db->num_rows($query_exam_code2);
			if ($no_of_exam_code2 > 0) 
			{
				$row_exam_code2 = $db->fetch_object($query_exam_code2);
				if ($row_exam_code2 != NULL){
					$exam_code2 = "<span title=\"Center Exam Code\">$row_exam_code2->exam_code</span>";
					$exam3 = $_SESSION["exam_code"] = $row_exam_code2->exam_code;
				}
				else{
					$exam = $row_center->exam_code;
					$exam3 = $_SESSION["exam_code"] = $exam_code2;
				}
			}
			else{
				$exam_code2 = $row_center->exam_code;
				$exam3 = $_SESSION["exam_code"] = $exam_code2;
			}
		}
		else{
			$exam_code2 = $row_center->exam_code;
			$exam3 = $_SESSION["exam_code"] = $exam_code2;
          }
          echo "<u>$exam_code2</u>";
          unset($row_center, $str_center, $query_center);
           ;?></td>
          <td width="25%"><strong>Exam Date</strong></td>
          <td width="25%"><?php 
            $str_center = "SELECT schedule_date FROM exam_exam_center WHERE exam_id= '$exam_id' and center_id='$center_id';";
            $query_center = $db->query($str_center);
            $row_center = $db->fetch_object($query_center);
            
            echo "<u>$row_center->schedule_date</u>";
            unset($row_center, $str_center, $query_center);
             ;?></td>
        </tr>
        <tr>
          <td width="25%"><strong>Center Name:</strong></td>
          <td width="25%"><?php
            $str_center = "

SELECT exam_center.center_name
  FROM (exam_exam_center exam_exam_center
        LEFT OUTER JOIN exam_center exam_center
           ON (exam_exam_center.center_id = exam_center.center_id))
       LEFT OUTER JOIN exam_exam exam_exam
          ON (exam_exam_center.exam_id = exam_exam.exam_id)
 WHERE exam_exam_center.center_id = '$center_id' AND exam_exam_center.exam_id = '$exam_id';";
					 
            $query_center = $db->query($str_center);
            $row_center = $db->fetch_object($query_center);
						$center_name = $row_center->center_name;
						unset($str_center, $query_center, $row_center);
?>
          <?php echo $center_name;?></td>
        	<td width="25%">&nbsp;</td>
        	<td width="25%">&nbsp;</td>
        </tr>
      </tbody>
    </table>
<br>
<table width="100%" border="1" cellpadding="0" cellspacing="0" class="table  table-striped table-bordered">
        <thead>
          <tr>
            <th width="5%">SN</th>
            <th width="5%">IT Serial No</th>
            <th width="5%">Reg. No</th>
            <th width="10%">Student Name</th>
            <th width="10%">Signature</th>
            <th width="10%">Remraks</th>
          </tr>
        </thead>
        <tbody>
		<?php
			$SN=0;
			while ($row_student = $db->fetch_object($query_student))
			{
				$mcq = (!empty($row_student->mcq_mark));
				$practical = (!empty($row_student->practical_mark));
				$total_mark = (!empty($mcq + $practical));
				$center_id = (!empty($row_student->center_id));
				?>
          <tr style="height:50px;">
            <td width="5%"><?php echo ++$SN;?></td>
            <td width="10%"><?php echo $row_student->it_serial;?></td>
            <td width="10%"><?php echo $row_student->reg_no;?></td>
            <td width="10%"><?php echo $row_student->name;?></td>
            <td width="20%">&nbsp;</td>
            <td width="10%">&nbsp;</td>
          </tr>
			<?php				
      }
			?>
        </tbody>
      </table>
			<?php
		}
		else
		{
			echo	"<div class=\"col-md-12\">No Result.</div>";
			echo "<script>
			$(\"#btnPrintStudent\").addClass(\"disabled\");
			$(\"#btnPrintStudent\").attr(\"disabled\",\"disabled\");
			</script>";
		}
	}


	if (isset($_POST['exam_id']) && ($_POST['task'] == "publish-result") && ($_POST['user']) )
	{
		$exam_id = FilterNumber($_POST['exam_id']);
		$user = FilterString($_POST['user']);

	if ($user != "admin")
		$center_id = FilterNumber($_SESSION['user']['center_id']);
	if ($user == "admin")
		$center_id = FilterNumber($_POST['center_id']);
	
		$config = new config();
		$dbname = $config->dbname;
		$dbuser = $config->dbuser;
		$dbhost = $config->dbhost;
		$dbpass = $config->dbpass;
		unset($config);
	
		include_once "includes/db.inc.php";
		$db = new Database($dbhost,$dbuser,$dbpass,$dbname) ;

		$TXT_CENTER= " AND exam_student_exam.center_id = '$center_id'";
		

		$strSelectStudent = "
SELECT exam_exam.exam_code,
       exam_center.center_name,
       exam_student.it_serial,
       exam_student.reg_no,
       exam_student.name,
       exam_student_exam.mcq_mark,
       exam_student_exam.practical_mark,
       exam_student_exam.exam_id,
       exam_student_exam.student_id,
       exam_student_exam.center_id,
       exam_exam_type.full_mark,
       exam_exam_type.pass_mark
  FROM (((exam_student_exam exam_student_exam
          INNER JOIN exam_exam exam_exam
             ON (exam_student_exam.exam_id = exam_exam.exam_id))
         INNER JOIN exam_student exam_student
            ON (exam_student_exam.student_id = exam_student.student_id))
        INNER JOIN exam_center exam_center
           ON (exam_student_exam.center_id = exam_center.center_id))
       INNER JOIN exam_exam_type exam_exam_type
          ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id)
	WHERE exam_student_exam.exam_id = '$exam_id' $TXT_CENTER;
	";

		$query_student = $db->query($strSelectStudent);
    $row_std = $db->fetch_object($query_student);
    $full_mark = $row_std->full_mark;
    $pass_mark = $row_std->pass_mark;
    $db->free($query_student);
    unset($row_std);
    
		$query_student = $db->query($strSelectStudent);
		$no_of_student = $db->num_rows($query_student);
	
		if ($no_of_student > 0)
		{
		?>
    <div align="center">
      <b>The Institute of Chartered Accountants of Nepal</b><br>
      Satdobato, Lalitpur<br>
      <?php

      $exam2 = $_SESSION["exam_code"];
					 

					$exam_typeSELECT = $db->query("SELECT exam_exam_type.exam_type FROM (exam_exam_type exam_exam_type  LEFT OUTER JOIN exam_exam exam_exam ON (exam_exam_type.exam_type_id=exam_exam.exam_type_id)) WHERE exam_exam.exam_code = '$exam2';");

					$exam_type = $exam_typeSELECT->fetch_assoc();

					$no_of_student = $db->num_rows($exam_type);

					$result=($exam_type['exam_type']);
					$getresult = substr($result, 0,2);

					 echo "$getresult";?> Hours  IT Training Course<br>
<strong>Exam Score Sheet</strong> </div>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tbody>
        <tr>
          <td width="16%"><label><strong>Batch No:</strong></label></td>
          <td width="16%"><?php 
          $str_center = "SELECT exam_code FROM exam_exam WHERE exam_id= '$exam_id';";
          $query_center = $db->query($str_center);
          $row_center = $db->fetch_object($query_center);
          $exam2 = $_SESSION["exam_code"] = $row_center->exam_code;

		$strExamCode = "SELECT * FROM exam_exam_center WHERE exam_id = '$exam_id';";
		$query_exam_code = $db->query($strExamCode);
		$no_of_exam_code = $db->num_rows($query_exam_code);

		if ($no_of_exam_code > 1) 
			$MultipleCenter = TRUE;
		else {
			$exam2 = $_SESSION["exam_code"] =$row_center->exam_code;
			$MultipleCenter = FALSE;
		}

		if ($MultipleCenter)
		{
			$strExamCode2 = "SELECT exam_code from exam_exam_center WHERE exam_id = '$exam_id' AND center_id = '$center_id' AND exam_code is NOT NULL";

			$query_exam_code2 = $db->query($strExamCode2);
			$no_of_exam_code2 = $db->num_rows($query_exam_code2);
			if ($no_of_exam_code2 > 0) 
			{
				$row_exam_code2 = $db->fetch_object($query_exam_code2);
				if ($row_exam_code2 != NULL){
					$exam_code2 = "<span title=\"Center Exam Code\">$row_exam_code2->exam_code</span>";
					$exam2 = $_SESSION["exam_code"] = $row_exam_code2->exam_code;
				}
				else{
					$exam_code2 = $row_center->exam_code;
					$exam2 = $_SESSION["exam_code"] = $exam_code2;
				}
			}
			else{
				$exam_code2 = $row_center->exam_code;
				$exam2 = $_SESSION["exam_code"] = $exam_code2;
			}
		}
		else{
			$exam_code2 = $row_center->exam_code;
			$exam2 = $_SESSION["exam_code"] = $exam_code2;
          }
          echo "<u>$exam_code2</u>";
          unset($row_center, $str_center, $query_center);
           ;?></td>
          <td width="16%"><strong>Exam Date/Time:</strong></td>
          <td colspan="3"><?php 
            $str_center = "SELECT start_time FROM exam_exam_status WHERE exam_id= '$exam_id';";
            $query_center = $db->query($str_center);
            $row_center = $db->fetch_object($query_center);
            
            echo "<u>$row_center->start_time</u>";
            unset($row_center, $str_center, $query_center);
             ;?></td>
        </tr>
        <tr>
          <td width="16%"><strong>Center Name:</strong></td>
          <td width="16%"><?php
            $str_center = "

SELECT exam_center.center_name
  FROM (exam_exam_center exam_exam_center
        LEFT OUTER JOIN exam_center exam_center
           ON (exam_exam_center.center_id = exam_center.center_id))
       LEFT OUTER JOIN exam_exam exam_exam
          ON (exam_exam_center.exam_id = exam_exam.exam_id)
 WHERE exam_exam_center.center_id = '$center_id' AND exam_exam_center.exam_id = '$exam_id';";
					 
            $query_center = $db->query($str_center);
            $row_center = $db->fetch_object($query_center);
						$center_name = $row_center->center_name;
						unset($str_center, $query_center, $row_center);
?>
          <?php echo $center_name;?></td>
        	<td width="16%"><strong>Full Mark</strong></td>
        	<td width="16%"><?php echo $full_mark;?></td>
          <td width="16%"><strong>Pass Mark</strong></td>
          <td width="16%"><?php echo $pass_mark;?></td>
        </tr>
      </tbody>
</table>
<br>
<table width="100%" border="1" cellpadding="0" cellspacing="0" class="table  table-striped table-bordered">
  <thead>
          <tr>
            <th width="5%">SN</th>
            <th width="5%">Reg. No</th>
            <th width="10%">MCQ Number</th>
            <th width="10%">Practical Number</th>
            <th width="10%">Total Number</th>
            <th width="10%">Result</th>
            <th width="10%">Remraks</th>
          </tr>
  </thead>
        <tbody>
		<?php
			while ($row_student = $db->fetch_object($query_student))
			{
				$mcq = $row_student->mcq_mark;
				$practical = $row_student->practical_mark;
				$total_mark = $mcq + $practical;
				$center_id = $row_student->center_id;
				?>
          <tr>
            <td width="5%"><?php echo ++$SN;?></td>
            <td width="15%"><?php echo $row_student->reg_no;?></td>
  <?php
// Checking Absent of User
$str_abs = "SELECT * from exam_student_answer WHERE exam_id = '$exam_id' and student_id = '$row_student->student_id' AND center_id = '$center_id'";

$query_abs = $db->query($str_abs);
$no_of_abs = $db->num_rows($query_abs);
$db->free($query_abs);
unset($str_abs,$query_abs,$txtAbs);
if ($no_of_abs < 1) $txtAbs = TRUE;

?>            
            <td width="10%"><?php if ($txtAbs) echo "-"; else echo $mcq;?></td>
            <td width="10%"><?php if ($txtAbs) echo "-"; else echo $practical;?></td>
            <td width="10%"><?php if ($txtAbs) echo "-"; else echo $total_mark;?></td>
            <td width="10%"><b>
            <?php 
							if ($total_mark >= $row_student->pass_mark) echo "<font color=green>Pass !</font>";
							else if ( $txtAbs ) echo "<font color=blue>Absent</font>";
              else echo "<font color=red>Fail !</font>";
              ?>
            </b></td>
            <td width="10%">&nbsp;</td>
          </tr>
			<?php				
      }
			?>
        </tbody>
</table>
			<?php
		}
		else
		{
			echo	"<div class=\"col-md-12\">No Result.</div>";
			echo "<script>
			$(\"#btnPrintStudent\").addClass(\"disabled\");
			$(\"#btnPrintStudent\").attr(\"disabled\",\"disabled\");
			</script>";
		}
	}


	if (isset($_POST['exam_id']) && ($_POST['task'] == "print-result") && ($_POST['user']) )
	{
		$exam_id = FilterNumber($_POST['exam_id']);
		$user = FilterString($_POST['user']);

	if ($user != "admin")
		$center_id = FilterNumber($_SESSION['user']['center_id']);
	if ($user == "admin")
		$center_id = FilterNumber($_POST['center_id']);
	
		$config = new config();
		$dbname = $config->dbname;
		$dbuser = $config->dbuser;
		$dbhost = $config->dbhost;
		$dbpass = $config->dbpass;
		unset($config);
	
		include_once "includes/db.inc.php";
		$db = new Database($dbhost,$dbuser,$dbpass,$dbname) ;

		$TXT_CENTER= " AND exam_student_exam.center_id = '$center_id'";
		

		$strSelectStudent = "
SELECT exam_exam.exam_code,
       exam_center.center_name,
       exam_student.it_serial,
       exam_student.reg_no,
       exam_student.name,
       exam_student_exam.mcq_mark,
       exam_student_exam.practical_mark,
       exam_student_exam.exam_id,
       exam_student_exam.student_id,
       exam_student_exam.center_id,
       exam_exam_type.full_mark,
       exam_exam_type.pass_mark
  FROM (((exam_student_exam exam_student_exam
          INNER JOIN exam_exam exam_exam
             ON (exam_student_exam.exam_id = exam_exam.exam_id))
         INNER JOIN exam_student exam_student
            ON (exam_student_exam.student_id = exam_student.student_id))
        INNER JOIN exam_center exam_center
           ON (exam_student_exam.center_id = exam_center.center_id))
       INNER JOIN exam_exam_type exam_exam_type
          ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id)
	WHERE exam_student_exam.exam_id = '$exam_id' $TXT_CENTER;
	";

		$query_student = $db->query($strSelectStudent);
    $row_std = $db->fetch_object($query_student);
    $full_mark = $row_std->full_mark;
    $pass_mark = $row_std->pass_mark;
    $db->free($query_student);
    unset($row_std);
    
		$query_student = $db->query($strSelectStudent);
		$no_of_student = $db->num_rows($query_student);
	
		if ($no_of_student > 0)
		{
		?>
    <div align="center">
      <b>The Institute of Chartered Accountants of Nepal</b><br>
      Satdobato, Lalitpur<br>
        <?php

      $exam1 = $_SESSION["exam_code"];
					 

					$exam_typeSELECT = $db->query("SELECT exam_exam_type.exam_type FROM (exam_exam_type exam_exam_type  LEFT OUTER JOIN exam_exam exam_exam ON (exam_exam_type.exam_type_id=exam_exam.exam_type_id)) WHERE exam_exam.exam_code = '$exam1';");

					$exam_type = $exam_typeSELECT->fetch_assoc();

					 
					$result=($exam_type['exam_type']);
					$getresult = substr($result, 0,2);

					 echo "$getresult"; ?> Hours  IT Training Course<br>
    <strong>Exam Score Sheet</strong> </div>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tbody>
        <tr>
          <td width="16%"><label><strong>Batch No:</strong></label></td>
          <td width="16%"><?php 
          $str_center = "SELECT exam_code FROM exam_exam WHERE exam_id= '$exam_id';";
          $query_center = $db->query($str_center);
          $row_center = $db->fetch_object($query_center);
          $exam1 = $_SESSION["exam_code"] = $row_center->exam_code;

		$strExamCode = "SELECT * FROM exam_exam_center WHERE exam_id = '$exam_id';";
		$query_exam_code = $db->query($strExamCode);
		$no_of_exam_code = $db->num_rows($query_exam_code);

		if ($no_of_exam_code > 1) 
			$MultipleCenter = TRUE;
		else 
			$MultipleCenter = FALSE;

		if ($MultipleCenter)
		{
			$strExamCode2 = "SELECT exam_code from exam_exam_center WHERE exam_id = '$exam_id' AND center_id = '$center_id' AND exam_code is NOT NULL";

			$query_exam_code2 = $db->query($strExamCode2);
			$no_of_exam_code2 = $db->num_rows($query_exam_code2);
			if ($no_of_exam_code2 > 0) 
			{
				$row_exam_code2 = $db->fetch_object($query_exam_code2);
				if ($row_exam_code2 != NULL)
					$exam_code2 = "<span title=\"Center Exam Code\">$row_exam_code2->exam_code</span>";
				else
					$exam_code2 = $row_center->exam_code;
			}
			else
				$exam_code2 = $row_center->exam_code;
		}
		else
			$exam_code2 = $row_center->exam_code;
          
          echo "<u>$exam_code2</u>";
          unset($row_center, $str_center, $query_center);
           ;?></td>
          <td width="16%"><strong>Exam Date/Time:</strong></td>
          <td colspan="3"><?php 
            $str_center = "SELECT start_time FROM exam_exam_status WHERE exam_id= '$exam_id';";
            $query_center = $db->query($str_center);
            $row_center = $db->fetch_object($query_center);
            
            echo "<u>$row_center->start_time</u>";
            unset($row_center, $str_center, $query_center);
             ;?></td>
        </tr>
        <tr>
          <td width="16%"><strong>Center Name:</strong></td>
          <td width="16%"><?php
            $str_center = "

SELECT exam_center.center_name
  FROM (exam_exam_center exam_exam_center
        LEFT OUTER JOIN exam_center exam_center
           ON (exam_exam_center.center_id = exam_center.center_id))
       LEFT OUTER JOIN exam_exam exam_exam
          ON (exam_exam_center.exam_id = exam_exam.exam_id)
 WHERE exam_exam_center.center_id = '$center_id' AND exam_exam_center.exam_id = '$exam_id';";
					 
            $query_center = $db->query($str_center);
            $row_center = $db->fetch_object($query_center);
						$center_name = $row_center->center_name;
						unset($str_center, $query_center, $row_center);
?>
          <?php echo $center_name;?></td>
        	<td width="16%"><strong>Full Mark</strong></td>
        	<td width="16%"><?php echo $full_mark;?></td>
          <td width="16%"><strong>Pass Mark</strong></td>
          <td width="16%"><?php echo $pass_mark;?></td>
        </tr>
      </tbody>
    </table>
<br>
      <table width="100%" border="1" cellpadding="0" cellspacing="0" class="table  table-striped table-bordered">
        <thead>
          <tr>
            <th width="5%">SN</th>
            <th width="5%">IT Serial No</th>
            <th width="5%">Reg. No</th>
            <th width="20%">Student Name</th>
            <th width="10%">MCQ Number</th>
            <th width="10%">Practical Number</th>
            <th width="10%">Total Number</th>
            <th width="10%">Result</th>
            <th width="10%">Remraks</th>
          </tr>
        </thead>
        <tbody>
		<?php
			while ($row_student = $db->fetch_object($query_student))
			{
				$mcq = $row_student->mcq_mark;
				$practical = $row_student->practical_mark;
				$total_mark = $mcq + $practical;
				$center_id = $row_student->center_id;
				?>
          <tr>
            <td width="5%"><?php echo ++$SN;?></td>
            <td width="15%"><?php echo $row_student->it_serial;?></td>
            <td width="15%"><?php echo $row_student->reg_no;?></td>
            <td><?php echo $row_student->name;?>
  <?php
// Checking Absent of User
$str_abs = "SELECT * from exam_student_answer WHERE exam_id = '$exam_id' and student_id = '$row_student->student_id' AND center_id = '$center_id'";

$query_abs = $db->query($str_abs);
$no_of_abs = $db->num_rows($query_abs);
$db->free($query_abs);
unset($str_abs,$query_abs,$txtAbs);
if ($no_of_abs < 1) $txtAbs = TRUE;

?>            
            </td>
            <td width="10%"><?php if ($txtAbs) echo "-"; else echo $mcq;?></td>
            <td width="10%"><?php if ($txtAbs) echo "-"; else echo $practical;?></td>
            <td width="10%"><?php if ($txtAbs) echo "-"; else echo $total_mark;?></td>
            <td width="10%"><b>
            <?php 
							if ($total_mark >= $row_student->pass_mark) echo "<font color=green>Pass !</font>";
							else if ( $txtAbs ) echo "<font color=blue>Absent</font>";
              else echo "<font color=red>Fail !</font>";
              ?>
            </b></td>
            <td width="10%">&nbsp;</td>
          </tr>
			<?php				
      }
			?>
        </tbody>
      </table>
      <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top:50px;">
      	<tr>
        	<td valign="top" align="center" width="33%">
            <span>_______________________</span><br>Verified By
          </td>
        	<td valign="top" align="center" width="33%">
          </td>
        	<td valign="top" align="center" width="33%">
            <span>_______________________</span><br>Authorized Signature
          </td>
        </tr>
      </table>
			<?php
		}
		else
		{
			echo	"<div class=\"col-md-12\">No Result.</div>";
			echo "<script>
			$(\"#btnPrintStudent\").addClass(\"disabled\");
			$(\"#btnPrintStudent\").attr(\"disabled\",\"disabled\");
			</script>";
		}
	}

	
	if (isset($_POST['center_id']) &&  ($_POST['exam_id']) && ($_POST['task'] == "student_login_admin") )
	{
		$exam_id = FilterNumber($_POST['exam_id']);
		$center_id = FilterNumber($_POST['center_id']);
		$config = new config();
		$dbname = $config->dbname;
		$dbuser = $config->dbuser;
		$dbhost = $config->dbhost;
		$dbpass = $config->dbpass;
		unset($config);
	
		include_once "includes/db.inc.php";
		$db = new Database($dbhost,$dbuser,$dbpass,$dbname) ;
	
		$strSelectStudent = "
		SELECT exam_student.reg_no, exam_exam_student.`Password`
		FROM exam_exam_student exam_exam_student
				 INNER JOIN exam_student exam_student
						ON (exam_exam_student.student_id = exam_student.student_id)
		WHERE exam_id = '$exam_id' AND center_id = '$center_id';";

		$query_student = $db->query($strSelectStudent);
		$no_of_student = $db->num_rows($query_student);
	
		if ($no_of_student > 0)
		{
			$str_exam_code = "SELECT exam_code FROM exam_exam WHERE exam_id = '$exam_id';";

			$query_exam_code = $db->query($str_exam_code);
			$row_code = $db->fetch_object($query_exam_code);
			$exam_code = $row_code->exam_code;

		$strExamCode = "SELECT * FROM exam_exam_center WHERE exam_id = '$exam_id';";
		$query_exam_code = $db->query($strExamCode);
		$no_of_exam_code = $db->num_rows($query_exam_code);

		if ($no_of_exam_code > 1) 
			$MultipleCenter = TRUE;
		else 
			$MultipleCenter = FALSE;

		if ($MultipleCenter)
		{
			$strExamCode2 = "SELECT exam_code from exam_exam_center WHERE exam_id = '$exam_id' AND center_id = '$center_id' AND exam_code is NOT NULL";

			$query_exam_code2 = $db->query($strExamCode2);
			$no_of_exam_code2 = $db->num_rows($query_exam_code2);
			if ($no_of_exam_code2 > 0) 
			{
				$row_exam_code2 = $db->fetch_object($query_exam_code2);
				if ($row_exam_code2 != NULL)
					$exam_code = "<span title=\"Center Exam Code\">$row_exam_code2->exam_code</span>";
			}
		}

			echo "<div style=\"margin-left:15px; margin-right:15px;padding-bottom:5px; float:left\"><b>Exam Code:</b> &nbsp;&nbsp;&nbsp;&nbsp; <u>$exam_code</u></div>";


			$str_exam_center = "SELECT center_name FROM exam_center WHERE center_id = '$center_id';";
			$query_exam_center = $db->query($str_exam_center);
			$row_center = $db->fetch_object($query_exam_center);
			$exam_center = $row_center->center_name;
			echo "<div style=\"margin-left:15px; margin-right:15px;padding-bottom:5px;float:right\"><b>Exam Center:</b> &nbsp;&nbsp;&nbsp;&nbsp; <u>$exam_center </u></div>";


		?>
		<style>
		@font-face {
			font-family: Ubuntu;
			src: url(<?php echo $URL;?>bootstrap/fonts/ubuntumono-webfont.woff);
			src: url('<?php echo $URL;?>bootstrap/fonts/ubuntumono-webfont.eot');
			src: url('<?php echo $URL;?>bootstrap/fonts/ubuntumono-webfont.eot?#iefix') format('embedded-opentype'), url('<?php echo $URL;?>bootstrap/fonts/ubuntumono-webfont.woff2') format('woff2'), url('<?php echo $URL;?>bootstrap/fonts/ubuntumono-webfont.woff') format('woff'), url('<?php echo $URL;?>bootstrap/fonts/ubuntumono-webfont.ttf') format('truetype'), url('<?php echo $URL;?>bootstrap/fonts/ubuntumono-webfont.svg#glyphicons_halflingsregular') format('svg');
		}
		.ubuntu
		{
			font-family:"Ubuntu";
			font-size:120%;
		}
		.border
		{
			border:solid 1px #ccc;
			min-width:30px;
			padding:5px;
		}
		td
		{
			padding:15px;
		}
		</style>
    <table border="0" cellspacing="3" cellpadding="0" width="98%" align="center">
		<?php
			$no_of_td = 3;
			$i = 1;
			$tr = $no_of_student / $no_of_td;
			while ($row_student = $db->fetch_object($query_student))
			{
				if ($i > $no_of_td) 
				{
					if ($tr > 0) echo "<tr>";
				}
				?>
        <td>	
          <div class="border">
            <span><strong>User : &nbsp;&nbsp;&nbsp;&nbsp; </strong></span>
              <span class="ubuntu"><?php echo $row_student->reg_no;?></span>
            <br>

            <span><strong>Pass : &nbsp;&nbsp;&nbsp;&nbsp;</strong></span>
              <span class="ubuntu"><?php echo base64_decode($row_student->Password);?></span>
          </div>
        </td>
			<?php
				if ($i >= $no_of_td) 
				{
					if ($tr > 0) echo "</tr>";
					$tr - 1;
					$i = 1;
				}
				else
					$i++;
			}
			?>
    </table>
			<?php
		}
		else
		{
			echo	"<div class=\"col-md-12\">No User Defined.</div>";
			echo "<script>
			$(\"#btnPrintStudent\").addClass(\"disabled\");
			$(\"#btnPrintStudent\").attr(\"disabled\",\"disabled\");
			</script>";
		}
	}

	if (isset($_POST['exam_id']) && ($_POST['task'] == "student_login") )
	{
		$exam_id = FilterNumber($_POST['exam_id']);
		$center_id = FilterNumber($_SESSION['user']['center_id']);
		$config = new config();
		$dbname = $config->dbname;
		$dbuser = $config->dbuser;
		$dbhost = $config->dbhost;
		$dbpass = $config->dbpass;
		unset($config);
	
		include_once "includes/db.inc.php";
		$db = new Database($dbhost,$dbuser,$dbpass,$dbname) ;
	
		$strSelectStudent = "
		SELECT exam_student.reg_no, exam_exam_student.`Password`
		FROM exam_exam_student exam_exam_student
				 INNER JOIN exam_student exam_student
						ON (exam_exam_student.student_id = exam_student.student_id)
		WHERE exam_id = '$exam_id' AND center_id = '$center_id';";

		$query_student = $db->query($strSelectStudent);
		$no_of_student = $db->num_rows($query_student);
	
		if ($no_of_student > 0)
		{
			$str_exam_code = "SELECT exam_code FROM exam_exam WHERE exam_id = '$exam_id';";

			$query_exam_code = $db->query($str_exam_code);
			$row_code = $db->fetch_object($query_exam_code);
			$exam_code = $row_code->exam_code;

		$strExamCode = "SELECT * FROM exam_exam_center WHERE exam_id = '$exam_id';";
		$query_exam_code = $db->query($strExamCode);
		$no_of_exam_code = $db->num_rows($query_exam_code);

		if ($no_of_exam_code > 1) 
			$MultipleCenter = TRUE;
		else 
			$MultipleCenter = FALSE;

		if ($MultipleCenter)
		{
			$strExamCode2 = "SELECT exam_code from exam_exam_center WHERE exam_id = '$exam_id' AND center_id = '$center_id' AND exam_code is NOT NULL";
			$query_exam_code2 = $db->query($strExamCode2);
			$no_of_exam_code2 = $db->num_rows($query_exam_code2);
			if ($no_of_exam_code2 > 0) 
			{
				$row_exam_code2 = $db->fetch_object($query_exam_code2);
				if ($row_exam_code2 != NULL)
					$exam_code = "<span title=\"Center Exam Code\">$row_exam_code2->exam_code</span>";
			}
		}

			echo "<p style=\"margin-left:15px; margin-right:15px;padding-bottom:5px;\"><b>Exam Code:</b> &nbsp;&nbsp;&nbsp;&nbsp; <u>$exam_code</u></p>";
		?>
		<style>
		@font-face {
			font-family: Ubuntu;
			src: url(<?php echo $URL;?>bootstrap/fonts/ubuntumono-webfont.woff);
			src: url('<?php echo $URL;?>bootstrap/fonts/ubuntumono-webfont.eot');
			src: url('<?php echo $URL;?>bootstrap/fonts/ubuntumono-webfont.eot?#iefix') format('embedded-opentype'), url('<?php echo $URL;?>bootstrap/fonts/ubuntumono-webfont.woff2') format('woff2'), url('<?php echo $URL;?>bootstrap/fonts/ubuntumono-webfont.woff') format('woff'), url('<?php echo $URL;?>bootstrap/fonts/ubuntumono-webfont.ttf') format('truetype'), url('<?php echo $URL;?>bootstrap/fonts/ubuntumono-webfont.svg#glyphicons_halflingsregular') format('svg');
		}
		.ubuntu
		{
			font-family:"Ubuntu";
			font-size:120%;
		}
		.border
		{
			border:solid 1px #ccc;
			min-width:30px;
			padding:5px;
		}
		td
		{
			padding:15px;
		}
		</style>
    <table border="0" cellspacing="3" cellpadding="0" width="98%" align="center">
		<?php
			$no_of_td = 3;
			$i = 1;
			$tr = $no_of_student / $no_of_td;
			while ($row_student = $db->fetch_object($query_student))
			{
				if ($i > $no_of_td) 
				{
					if ($tr > 0) echo "<tr>";
				}
				?>
        <td>	
          <div class="border">
            <span><strong>User : &nbsp;&nbsp;&nbsp;&nbsp; </strong></span>
              <span class="ubuntu"><?php echo $row_student->reg_no;?></span>
            <br>

            <span><strong>Pass : &nbsp;&nbsp;&nbsp;&nbsp;</strong></span>
              <span class="ubuntu"><?php echo base64_decode($row_student->Password);?></span>
          </div>
        </td>
			<?php
				if ($i >= $no_of_td) 
				{
					if ($tr > 0) echo "</tr>";
					$tr - 1;
					$i = 1;
				}
				else
					$i++;
			}
			?>
    </table>
			<?php
		}
		else
		{
			echo	"<div class=\"col-md-12\">No User Defined.<br>Please contact to Administration.</div>";
			echo "<script>
			$(\"#btnPrintStudent\").addClass(\"disabled\");
			$(\"#btnPrintStudent\").attr(\"disabled\",\"disabled\");
			</script>";
		}
	}
	
	if (isset($_POST['exam_id']) && ($_POST['task'] == "view_exam") )
	{
		$exam_id = FilterNumber($_POST['exam_id']);
		$center_id = FilterNumber($_POST['center_id']);
		$config = new config();
		$dbname = $config->dbname;
		$dbuser = $config->dbuser;
		$dbhost = $config->dbhost;
		$dbpass = $config->dbpass;
		unset($config);
	
		include_once "includes/db.inc.php";
		$db = new Database($dbhost,$dbuser,$dbpass,$dbname) ;
		
		$select = "

SELECT exam_exam.exam_id,
       exam_exam.exam_code,
       exam_exam_type.full_mark,
       exam_exam_type.pass_mark,
       exam_exam_type.total_time,
       exam_exam_type.mcq_mark,
       exam_exam_type.practical_mark,
       exam_exam_type.total_question,
       exam_exam_type.exam_type,
       exam_exam_message.start_message AS exam_start_message,
       exam_exam_message.end_message AS exam_end_message,
       exam_exam_type.start_message,
       exam_exam_type.end_message
  FROM (exam_exam exam_exam
        INNER JOIN exam_exam_type exam_exam_type
           ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id))
       LEFT OUTER JOIN exam_exam_message exam_exam_message
          ON (exam_exam_message.exam_id = exam_exam.exam_id)

			WHERE exam_exam.exam_id = '$exam_id';";

		$query = $db->query($select);
		$row = $db->fetch_object($query);
		?>
	<h4 class="margin-top-10">Exam Details</h4>
		<table class="table table-bordered">
				<tr>
					<th width="50%">Exam Code</th>
					<td width="50%"><?php 
		$strExamCode = "SELECT * FROM exam_exam_center WHERE exam_id = '$row->exam_id';";
		$query_exam_code = $db->query($strExamCode);
		$no_of_exam_code = $db->num_rows($query_exam_code);

		if ($no_of_exam_code > 1) 
			$MultipleCenter = TRUE;
		else 
			$MultipleCenter = FALSE;

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
					?></td>
				</tr>
				<tr>
					<th width="50%">Exam Start Instruction</th>
					<td width="50%"><?php 
					if ($row->exam_start_message != NULL) echo $row->exam_start_message;
					else echo $row->start_message;?></td>
				</tr>
				<tr>
					<th width="50%">Exam Ending Message</th>
					<td width="50%"><?php 
					if ($row->exam_end_message != NULL) echo $row->exam_end_message;
					else echo $row->end_message;?></td>
				</tr>
				<tr>
					<th width="50%">Full Mark</th>
					<td width="50%"><?php echo $row->full_mark;?></td>
				</tr>
	
				<tr>
					<th width="50%">Pass Mark</th>
					<td width="50%"><?php echo $row->pass_mark;?></td>
				</tr>
	
				<tr>
					<th width="50%">MCQ Total Mark</th>
					<td width="50%"><?php echo $row->mcq_mark;?></td>
				</tr>
	
				<tr>
					<th width="50%">Total Practical Mark</th>
					<td width="50%"><?php echo $row->practical_mark;?></td>
				</tr>
		</table>
	
  	<h4 class="margin-top-10">Exam Center</h4>
		<table class="table table-bordered margin-top-10">
      <tr>
        <th width="50%">Exam Center</th>
        <td width="50%">
<?php 
								$strCenter = "SELECT exam_center.center_name
  FROM exam_exam_center exam_exam_center
       INNER JOIN exam_center exam_center
          ON (exam_exam_center.center_id = exam_center.center_id)
					 WHERE exam_exam_center.exam_id = '$row->exam_id' AND exam_exam_center.center_id = '$center_id'";

								$query_center = $db->query($strCenter);
								$row_center = $db->fetch_object($query_center);
								echo $row_center->center_name;
								$db->free($query_center);
								unset($query_center, $strCenter, $row_center);

?></td>
      </tr>
    </table>
  
		<h4 class="margin-top-10">Chapter and Maximum Question per Chapter</h4>
	
		<table class="table table-bordered margin-top-10">
			<?php
			$sql_chapter = "
SELECT exam_exam_question.exam_id,
       exam_exam_question.chapter_no,
       COUNT(exam_exam_question.question_id) as no_of_question
  FROM exam_exam_question exam_exam_question
 WHERE exam_exam_question.exam_id = '$exam_id'
GROUP BY exam_exam_question.exam_id, exam_exam_question.chapter_no;
";

			$query_chapter = $db->query($sql_chapter);
			$no_of_chapter = $db->num_rows($query_chapter);
			if ($no_of_chapter > 0)
			{
				while ($row_chapter = $db->fetch_object($query_chapter))
				{
			?>
				<tr>
					<th width="50%">Chapter <?php echo $row_chapter->chapter_no;?></th>
					<td width="50%"><?php echo $row_chapter->no_of_question;?></td>
				</tr>
			<?php 
				}
			}
			else
			{
				?>
				<tr>
					<th colspan="2" width="100%"><font color=red>No Chapter Specified.</th>
				</tr>
		<?php
			}
			?>          
		</table>
	
		<h4 class="margin-top-10">Students</h4>
	
		<table class="table table-bordered margin-top-10">
			<thead>
				<tr>
					<th width="5%">SN</th>
					<th width="20%">Registration Number</th>
					<th>Student Name</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$sql_student = "SELECT exam_student.name, exam_student.reg_no
			FROM exam_exam_student exam_exam_student
				 INNER JOIN exam_student exam_student
						ON (exam_exam_student.student_id = exam_student.student_id)
			WHERE exam_exam_student.exam_id = '$exam_id' AND exam_exam_student.center_id = '$center_id';";
			$query_student = $db->query($sql_student);
			$no_of_student = $db->num_rows($query_student);
			if ($no_of_student > 0)
			{
				$SN=0;
				while ($row_student = $db->fetch_object($query_student))
				{
			?>
				<tr>
					<td width="5%"><?php echo ++$SN;?></td>
					<td width="20%"><?php echo $row_student->reg_no;?></td>
					<td><?php echo $row_student->name;?></td>
				</tr>
			<?php 
				}
			}
			else
			{
				?>
				<tr>
					<th colspan="3" width="100%"><font color=red>Student not selected.</th>
				</tr>
		<?php
			}
			?>          
			</tbody>
		</table>
	
	
	<style>
	.margin-top-10
	{
		margin-top:10px;
	}
	img
	{
		width:100%;
		height:100%;
	}
	</style>
	<?php	
		$db->close();
	}
	
	if (isset($_POST['no_of_answer']) && ($_POST['answer_type']) && ($_POST['question_id']) && ($_POST['type']=="edit")  )
	{
	
		$config = new config();
		$dbname = $config->dbname;
		$dbuser = $config->dbuser;
		$dbhost = $config->dbhost;
		$dbpass = $config->dbpass;
		unset($config);
	
		include_once "includes/db.inc.php";
		$db = new Database($dbhost,$dbuser,$dbpass,$dbname) ;
	
	
		$no_of_answer = FilterNumber($_POST['no_of_answer']);
		$answer_type = FilterString($_POST['answer_type']);
		$question_id = FilterNumber($_POST['question_id']);
	
		$strSelectAnswer = "SELECT * FROM exam_answer WHERE question_id = '$question_id' ORDER BY answer_id LIMIT 0,$no_of_answer;";
	
		$query_answer2 = $db->query($strSelectAnswer);
		$no_of_answer2 = $db->num_rows($query_answer2);
	
		if ($answer_type== "S") $single=TRUE;
		if ($single) $checkbox = "radio"; else $checkbox = "checkbox";
	
		if ($no_of_answer2 > 0)
		{
			unset($i);
			while ($row_answer = $db->fetch_object($query_answer2))
			{
				$answer[] = $row_answer->answer;
				$is_correct[] = $row_answer->is_correct;
				$answer_id[] = $row_answer->answer_id;
			}
		}
		for ($i=0; $i<$no_of_answer; $i++)
		{
		?>
						<div class="form-group">
							<label class="col-md-1"><input type="<?php echo $checkbox;?>" name="correct[]" value="<?php echo $i;?>"<?php if ($is_correct[$i] == "Y") echo " checked";?>> &nbsp;<?php echo NumberToAlpha($i+1);?></label>
							<label class="col-md-11">
<?php
							wysiwyg_answer("answer[]", $_SESSION['question-answer'][$i+1], 'exam-answer',$i+1); 

//								wysiwyg("answer[]",stripslashes($answer[$i]), "exam-answer". $i); 
?>                
							</label>
								<input type="hidden" name="answer_id[]" value="<?php echo $answer_id[$i];?>">
						</div>
		<?php } 
		$db->close();
	}

	if(isset($_POST['no_of_answer']) && ($_POST['answer_type']) && ($_POST['type']=="add" ))
	{
		$no_of_answer = FilterNumber($_POST['no_of_answer']);
		$answer_type = FilterString($_POST['answer_type']);
		
		if ($answer_type == "S") $single=TRUE;
		
		if ($single) $checkbox = "radio"; else $checkbox = "checkbox";
	
		for ($i=0; $i<$no_of_answer; $i++)
		{
		?>
							<div class="form-group">
								<label class="col-md-1"><input type="<?php echo $checkbox;?>" name="correct[]" value="<?php echo $i;?>"<?php if ($correct[$i] == $i) echo " checked";?> style="padding-top:5px;"> &nbsp;<?php echo NumberToAlpha($i+1);?></label>
								<div class="col-md-11">
<?php
//  wysiwyg_answer("answer[$i]", $_SESSION['question-answer'][$i+1], 'exam-answer',$i+1); 

?>
          <textarea data-id="<?php echo $i+1;?>" name="answer[<?php echo $i;?>]" class="form-control"><?php echo stripslashes($_SESSION['question-answer'][$i+1]);?></textarea>

								</div>
						</div>
	<?php } 
wysiwyg_ajax_answer($MAIN_URL,40,'exam-answer');
//	wysiwyg_ajax($MAIN_URL,50,"exam-answer");

	}

	if(isset($_POST['no_of_answer']) && ($_POST['answer_type']) && ($_POST['type']) == "edit_question" && ($_POST['question_id']))
	{

		$no_of_answer = FilterNumber($_POST['no_of_answer']);
		$question_id = FilterNumber($_POST['question_id']);
		$answer_type = FilterString($_POST['answer_type']);
		
		if ($answer_type == "S") $single=TRUE;
		
		if ($single) $checkbox = "radio"; else $checkbox = "checkbox";
	
		$ans = $_SESSION['rand']['answer']['ans'];
		$is_cor = $_SESSION['rand']['answer']['is_cor'];
	
		for ($i=0; $i<$no_of_answer; $i++)
		{
		?>
							<div class="form-group">
								<label class="col-md-1"><input type="<?php echo $checkbox;?>" name="correct[]" value="<?php echo $i;?>"<?php 
							if ($is_cor[$i] == 'Y') echo " checked";?>> &nbsp;<?php echo NumberToAlpha($i+1);?></label>
								
								<div class="col-md-11">
<?php
//  wysiwyg_answer("answer[$i]", $_SESSION['question-answer'][$i+1], 'exam-answer',$i+1); 
//								wysiwyg("answer[$i]", stripslashes($ans[$i]), 'exam-answer'); 
?>

          <textarea data-id="<?php echo $i+1;?>" name="answer[<?php echo $i;?>]" class="form-control"><?php echo stripslashes($_SESSION['question-answer'][$i+1]);?></textarea>

								</div>
							</div>
	<?php } 

wysiwyg_ajax_answer($MAIN_URL,40,'exam-answer');
//		wysiwyg_ajax($MAIN_URL,50,"exam-answer");

	}

 //edit 

	if (isset($_POST['id']) && ($_POST['question-answer']) && ($_POST['task']) == "question-answer-add" )
	{
		$id = FilterNumber($_POST['id']);
		$_SESSION['question-answer'][$id] = $_POST['question-answer'];
	}
}
else
	echo "<script>window.location='" .$URL."start.html';</script>";  
?>