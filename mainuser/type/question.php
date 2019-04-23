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

if (isset($_POST['btnAddUpdateQuestion']))
{
	$random = FilterString($_POST['rand']);
	if ($_SESSION['exam_type_question']['rand']=$random)
	{
		$chapter = FilterNumber($_POST['chapter']);
		$no_of_question = FilterNumber($_POST['no_of_question']);
		$strQuestion = "SELECT * FROM exam_type_chapter_question WHERE exam_type_id = '$exam_type_id' AND chapter_no = '$chapter';";
		$query_question = $db->query($strQuestion);
		$question_no = $db->num_rows($query_question);
		if ($question_no > 0)
			$strUpdate = "UPDATE exam_type_chapter_question SET no_of_question = '$no_of_question' WHERE exam_type_id = '$exam_type_id' AND chapter_no = '$chapter';";
		else
			$strUpdate = "INSERT INTO exam_type_chapter_question (exam_type_id, chapter_no, no_of_question) VALUES ('$exam_type_id', '$chapter', '$no_of_question');";
		$db->query($strUpdate);
		
		$strDeleteZeroQuestion = "DELETE FROM exam_type_chapter_question WHERE no_of_question=0";
		$db->query($strDeleteZeroQuestion);
	}
}

$strExamType = "SELECT * FROM exam_exam_type WHERE exam_type_id = '$exam_type_id';";
$query_exam_type = $db->query($strExamType);
$row_exam_type = $db->fetch_object($query_exam_type);
$exam_type = $row_exam_type->exam_type;
$total_question = $row_exam_type->total_question;
$db->free($query_exam_type);
unset($strExamType, $query_exam_type, $row_exam_type);

$rand = RandomValue(20);
$_SESSION['exam_type_question']['rand']=$rand;	

?>
<div class="page-header">
  <div class="row">
  	<div class="col-md-9">
      <h3 style="margin-top:0px; margin-bottom:0px;"> <i class="fa fa-question-circle"></i> Chapter and No. of Question for  <strong>'<?php echo $exam_type;?>'</strong> Exam type</h3>
    </div>
    <div class="col-md-3 pull-right">    
      <button class="btn btn-warning pull-right" id="btnBack" name="btnBack"><i class="fa fa-wrench"></i> List of Exam Type</button>
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
  	<form class="form-horizontal" method="post">
      <div class="row">
        <div class="col-md-6 col-sm-6">
          <fieldset>
            <legend>Total Questions</legend>
              <label class="col-md-6"><font color="#0816B1">Total Questions to asked</font></label>
              <div class="col-md-3">
                <input type="text" id="max_question" readonly value="<?php echo $total_question;?>" class="form-control" style="text-align:center">
              </div>
          </fieldset>
  
  <?php
  $strExisting = "SELECT * FROM exam_type_chapter_question WHERE exam_type_id = '$exam_type_id';";
  $query_existing = $db->query($strExisting);
  $no_of_existing = $db->num_rows($query_existing);
  ?>
      <fieldset>
        <legend>Chapter and No. of Questions</legend>
          <table class="table table-bordered">
            <tr>
              <th>Chapter No</th>
              <th>No. of Question</th>
            </tr>
  <?php 
	$total_chapter_question = 0;
	while ($row_existing = $db->fetch_object($query_existing))
  {
		if ($row_existing->chapter_no == 1) $no_of_question = $row_existing->no_of_question;
  ?>
            <tr>
              <td align="center"><?php echo $row_existing->chapter_no;?></td>
              <td align="center"><?php echo $row_existing->no_of_question;?></td>
            </tr>
  <?php
		$total_chapter_question = $total_chapter_question + $row_existing->no_of_question;
  }
  ?>
					<tr>
          	<td align="center"><strong>Total : </strong></td>
          	<td align="center"><strong><?php echo $total_chapter_question;?></strong></td>
          </tr>
        </table>
        <input type="hidden" value="<?php echo $total_chapter_question;?>" id="total_chapter_question">
        <div class="col-md-12" id="message"></div>
      </fieldset>

      </div>
        <div class="col-md-6 col-sm-6">
          <fieldset>
            <legend>Add/Edit Chapter and No. of Questions</legend>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="col-md-4">Chapter </label>
                    <div class="col-md-5">
                      <select name="chapter" class="form-control" id="chapter">
                      <?php 
                      for ($ii=1; $ii<=50; $ii++)
                      {
                        echo "<option value=\"$ii\">Chapter $ii</option>";
                      }
                      ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="col-md-4">No. of Questions </label>
                    <div class="col-md-5">
                      <select name="no_of_question" class="form-control" id="no_of_question">
                      <?php
                        for ($j=0;$j <= 20;$j++)
                        {
                          ?>
                      <option value="<?php echo $j;?>"><?php echo $j;?></option>
                      <?php
                        }
                        ?>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-9 col-md-offset-3">
                <input type="hidden" value="<?php echo $exam_type_id;?>" name="exam_type_id">
                <input type="hidden" value="<?php echo $rand;?>" name="rand">
                <button type="submit" id="btnAddUpdateQuestion" name="btnAddUpdateQuestion" class="btn btn-success">Add / Update <br>Chapter and Question No. </button>
              </div>
          </fieldset>
        </div>
      </div>
    </form>
  </div>    
</div>
  <script>
  $('.question').change(function(e) {
    update_question_number();
  });
  
  $(document).ready(function(e) {
    update_question_number();
		update_question_no()		;
  });
  
  function update_question_number()
  {
    var max_ques = $("#max_question").val();
		var total_ques = $("#total_chapter_question").val();
    if (max_ques != total_ques)
    {
      $('#message').html("<b><font color=red>Maximum Question and Total Question does not equal.</font></b>");
    }
    else
    {
      $('#message').html("");
    }
  };
	
	$("#chapter").change(function(e) {
    update_question_no();
  });
	function update_question_no()
	{
		var chapter_no = $("#chapter :selected").val();
		$.post("<?php echo $MAIN_URL;?>ajax.html",
		{
			exam_type_id: <?php echo $exam_type_id;?>,
			chapter_no: chapter_no,
			task:"update_exam_type_question",
		},
		function(data,status){
			console.log(data);
			$("#no_of_question").val(data);	
		});
	}
  </script>