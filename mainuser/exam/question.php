<?php
session_start();
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>"; 

if (!isset($_POST['exam_id']))
	echo "<script>window.location='list.html';</script>"; 

$exam_id = FilterNumber($_POST['exam_id']);
$center_id = FilterNumber($_POST['center_id']);

if (isset($_POST['btnUpdate']))
{
	$rand = FilterString($_POST['rand']);
	if ($rand == $_SESSION['rand']['exam_question_selection'])
	{
		$chapter_no = FilterNumber($_POST['chapter_no']);
		$question_id = $_POST['question_id'];

		$strDelete = "DELETE FROM exam_exam_question WHERE exam_id = '$exam_id' AND chapter_no = '$chapter_no';";
		$db->query($strDelete);
		if (count($question_id) > 0)
		{
			foreach ($question_id as $KEY => $value)
			{
				$strInsert = "INSERT INTO exam_exam_question (exam_id, chapter_no, question_id) VALUES ('$exam_id','$chapter_no', '$KEY');";
				$db->query($strInsert);
			}
		}
	}
}

$rand = RandomValue(20);
$_SESSION['rand']['exam_question_selection'] = $rand;
$strExamCode = "
SELECT exam_exam.exam_code, exam_exam_type.exam_type
  FROM exam_exam exam_exam
       INNER JOIN exam_exam_type exam_exam_type
          ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id)
WHERE exam_exam.exam_id = '$exam_id';";
$query = $db->query($strExamCode);
$row = $db->fetch_object($query);
$exam_code = $row->exam_code;
$exam_type = $row->exam_type;
$db->free($query);
unset($strExamCode, $query, $row);
?>
<div class="page-header">
  <div class="row">
  	<div class="col-md-9">
      <h3 style="margin-top:0px; margin-bottom:0px;"><i class="fa fa-edit"> </i> Select Questions for Displaying for Exam</h3>
    </div>
    <div class="col-md-3 pull-right">    
      <button class="btn btn-warning pull-right" id="btnBack" name="btnBack"><i class="fa fa-rotate-left"></i> Exam List</button>
      <script>
      $("#btnBack").click(function(e) {
        window.location='list.html';      
      });
      </script>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="row">
      <div class="col-md-5"><strong>Exam Code : <u><?php 

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
					echo "<span title=\"Center Exam Code\">$row_exam_code2->exam_code</span>";
				else
					echo $exam_code;
			}
			else
				echo $exam_code;
		}
			else
				echo $exam_code;
			
			?></u></strong></div>
      <div class="col-md-7"><strong>Exam Type : <u><?php echo $exam_type;?></u></strong></div>
    </div>
  </div>
</div>
<br>
<div class="row">
	<div class="col-md-5">
    <fieldset>
      <legend>Minimum Question for Chapter</legend>
      <div class="col-md-12" id="minimum-question">
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
      </div>
      <style>
			#total_question td, #total_question th
			{
				text-align:center;
			}
			#total_question tfoot
			{
				text-align:center;
				font-weight:bold;
				font-size:130%;
			}
			</style>
    </fieldset>
	</div>
  <div class="col-md-7">
  	<form class="form-horizontal" method="post" id="frm_question">
    	<fieldset>
      	<legend>Chapter No., No. of Questions And Question</legend>
          <div class="col-md-12">
            <div class="col-md-12 form-group">
            	<label class="col-md-4">Chapter </label>
              <div class="col-md-4">
                <select class="form-control" name="chapter_no" id="chapter_no">
<?php 
$strChapter = "
SELECT exam_exam_type.exam_type_id,
       exam_exam_type.total_question,
       exam_exam.exam_id,
       exam_type_chapter_question.chapter_no,
       exam_type_chapter_question.no_of_question
  FROM (exam_exam exam_exam
        INNER JOIN exam_exam_type exam_exam_type
           ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id))
       INNER JOIN exam_type_chapter_question exam_type_chapter_question
          ON (exam_type_chapter_question.exam_type_id =
                 exam_exam_type.exam_type_id)
	WHERE exam_exam.exam_id = '$exam_id' 
		AND exam_type_chapter_question.no_of_question > 0 ;
";

$query_chap_no = $db->query($strChapter);
while ($row_chap = $db->fetch_object($query_chap_no))
{
	$exam_type_id = $row_chap->exam_type_id;
?>
                  <option value="<?php echo $row_chap->chapter_no;?>"<?php if ($row_chap->chapter_no == $chapter_no) echo " selected=\"selected\"";?>>Chapter <?php echo $row_chap->chapter_no;?></option>
  <?php
$str = "SELECT count(chapter_no) as `cnt` FROM exam_exam_question WHERE exam_id = '$exam_id' AND chapter_no = $row_chap->chapter_no;";
$query_str = $db->query($str);
$row2 = $db->fetch_object($query_str);
unset($cnt);
$cnt = $row2->cnt;
$db->free($query_str);
unset($str, $query_str, $row2);

if ($cnt >= $row_chap->no_of_question)	$isShown = TRUE;
else $isShown = FALSE;
	} ?>                
                </select>
              </div>
            </div>
              <strong>NOTE: Minimum number of question(s) per Chapter must be selected. </strong>
						<div class="col-md-12">
            	<div class="row">
                <div id="UpdateQuestion"></div>
              </div>
            </div>
						<script>
            $(document).ready(function(e) {
              update_question_list();
						});
            $("#chapter_no").change(function(e) {
              update_question_list();
            });
            
            var delay = (function(){
              var timer = 0;
              return function(callback, ms){
                clearTimeout (timer);
                timer = setTimeout(callback, ms);
              };
            })();
            
            function update_question_list()
            {
              $("div#UpdateQuestion").html("<div align=center><span class=\"fa fa-spinner fa-5x fa-spin\"></span><br><h3>Loading Questions...</h3></div>");


              var chapter_no = $("#chapter_no :selected").val();
							var no_of_question = $("#chapter_no_"+chapter_no+"").data("id");

              delay(function() {
                $.post("<?php echo $MAIN_URL;?>ajax.html",
                {
                  chapter_no: chapter_no,
                  exam_id: "<?php echo $exam_id;?>",
                  exam_type_id: "<?php echo $exam_type_id;?>",
                  task:"update_no_of_question",
                },
                function(data,status){
                  $("div#UpdateQuestion").html(data);
                });
              }, 200);

							AutoSaveQuestion();

            };

			function AutoSaveQuestion()
			{
				var chapter_no = $("#chapter_no :selected").val();
				var total_question = $("#chapter_no_"+chapter_no+"").data("id");

				var question_no = $(".question:checked").size();
				if (question_no >= total_question) 
				{
						$.ajax({
								data: $( "#frm_question" ).serializeArray(),
								type: "POST",
									 url: "<?php echo $MAIN_URL;?>ajax2.php",
								success: function(data){
									UpdateMinimumQuestion();
									$("div#ajax_test").html(data);
								}
						});
				}
			};
			
			function UpdateMinimumQuestion()
			{
console.log("A");
				$.post("<?php echo $MAIN_URL;?>ajax.html",
				{
					exam_id: <?php echo $exam_id;?>,
					task:"UpdateMinimumQuestion",
				},
				function(data,status){
					$("div#minimum-question").html(data); 		
				});
			};

            </script>
        </div>
        <div class="col-md-12" align="center">
          <input type="hidden" name="rand" value="<?php echo $rand;?>">
          <input type="hidden" name="exam_id" value="<?php echo $exam_id;?>">
        </div>
      </fieldset>
    </form>
	</div>
</div>
<style>
table img
{
	max-width:250px;
}
</style>