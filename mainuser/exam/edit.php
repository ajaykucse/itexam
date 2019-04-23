<?php
@session_start();
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>"; 

if (!isset($_POST['exam_id'])) 
	echo "<script>window.location='list.html';</script>"; 

if (isset($_POST['btnBack']))
	echo "<script>window.location='list.html';</script>"; 

$exam_id = FilterNumber($_POST['exam_id']);

if (isset($_POST['btnEditExam']))
{	
	$rand = FilterString($_POST['rand']);
	if ($_SESSION['add_exam']['rand'] == $rand)
	{
		$exam_type_id = FilterNumber($_POST['exam_type_id']);
		$exam_code = addslashes($_POST['exam_code']);
		$center_id = $_POST['center_id'];
		$center_exam_code = $_POST['center_exam_code'];

		if (strlen($exam_code) == 0)
		{
				$isError = TRUE;
				$error[] = "Exam Code is blank.";	
		}
		else
		{
			$select_exam_code = "SELECT exam_code FROM exam_exam WHERE exam_code = UPPER('$exam_code') AND exam_id <> $exam_id;";
			$query_exam_code = $db->query($select_exam_code);
			$no_of_exam_code = $db->num_rows($query_exam_code);
			if ($no_of_exam_code > 0)
			{
				$isError = TRUE;
				$error[] = "Exam code is already exist.<br>Please enter new Exam code.";	
			}
		}
		if (strlen($exam_type_id) == 0)
		{
			$isError = TRUE;
			$error[] = "Please choose Exam Type.";	
		}

		if (count($center_id) < 1)
		{
			$isError = TRUE;
			$error[] = "Please choose Exam Center.";	
		}

		if (!$isError)
		{
		
			$sql_update_exam = "UPDATE exam_exam SET exam_code = '$exam_code', exam_type_id = '$exam_type_id'
			WHERE exam_id = '$exam_id'";

			$db->begin();
			$query_update = $db->query($sql_update_exam);
	
			//Update DATA TO exam_exam_center
			if (count($center_id) > 0)
			{
				$db->query("DELETE FROM exam_exam_center WHERE exam_id='$exam_id';");
				foreach ($center_id as $KEY => $value)
				{
					$examcode = FilterString($center_exam_code[$KEY]);
					if (strlen($examcode)>0)
						$sql_center = "INSERT INTO exam_exam_center (exam_id, center_id,exam_code) VALUES ('$exam_id','$KEY','$center_exam_code[$KEY]');";
					else
						$sql_center = "INSERT INTO exam_exam_center (exam_id, center_id,exam_code) VALUES ('$exam_id','$KEY',NULL);";
					$query_center = $db->query($sql_center);
				}
			}
			else $query_center = TRUE;

			if ($query_update && $query_center)
			{
				$db->commit();
				notify("INFORMATION", "<b>Exam has been edited successfully.</b>","list.html",TRUE,5000); 
				include_once "footer.inc.php";
				exit();
			}
			else
			{
				$db->rollback();
				$isError = TRUE;
				$error[] = "There is some problem while Creating Exam.";
			}
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
}

$rand = RandomValue(20);
$_SESSION['add_exam']['rand']=$rand;	

$sql_exam = "SELECT * FROM exam_exam WHERE exam_id = '$exam_id';";
$query_exam = $db->query($sql_exam);
$row = $db->fetch_object($query_exam);

?>
<div class="page-header">
  <div class="row">
  	<div class="col-md-9">
      <h3 style="margin-top:0px; margin-bottom:0px;"><i class="fa fa-edit"> </i> Edit Exam</h3>
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
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading">Edit Exam</div>
      <form id="form1" name="form1" method="post" class="form-horizontal">
        <div class="panel-body">
            <div class="col-md-12 form-group">
              <label class="col-md-2">Exam Code</label>
              <div class="col-md-5">
                <input type="text" name="exam_code" id="exam_code" class="form-control" value="<?php echo $row->exam_code;?>">
<?php
		$strExamCode = "SELECT * FROM exam_exam_center WHERE exam_id = '$row->exam_id';";
		$query_exam_code = $db->query($strExamCode);
		$no_of_exam_code = $db->num_rows($query_exam_code);

		if ($no_of_exam_code > 1) echo "<small><small>Exam Code may different from this as <strong>Center Exam Code</strong> can be changed.</small></small>";

?>
              </div>
  
              <label class="col-md-2">Exam Type</label>
              <div class="col-md-3">
                <select name="exam_type_id" class="form-control">
  <?php
  $strExamType = "SELECT * FROM exam_exam_type ORDER BY exam_type_id;";
  $query_exam_type = $db->query($strExamType);
  while ($row_exam_type = $db->fetch_object($query_exam_type))
  {
  ?>
                    <option value="<?php echo $row_exam_type->exam_type_id;?>"<?php if ($row_exam_type->exam_type_id ==$row->exam_type_id ) echo " selected=\"selected\"";?>><?php echo $row_exam_type->exam_type;?></option>
  <?php
  }
  ?>
              </select>
            </div>
          </div>
        </div>
        <div class="panel-body">
          <fieldset>
            <legend>Select Exam Center</legend>
            <div class="form-group">
              <div class="col-md-12">
                <table width="100%" class="table table-bordered">
                  <thead>
                    <tr>
                      <th width="5%">SN</th>
                      <th>Exam Center Name</th>
                      <th>Exam Address</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    
        <?php
        $sql_center = "
	SELECT * from exam_center
	ORDER BY exam_center.center_id				
				" ;
        $query_center = $db->query($sql_center);
        $SN=0;
        while ($row_center = $db->fetch_object($query_center))
        {
            $checked = FALSE;
						$strexamcenter = "SELECT * FROM exam_exam_center WHERE exam_id='$exam_id' and center_id = '$row_center->center_id';";
						$queryexamcenter = $db->query($strexamcenter);
						$rowexamcenter = $db->fetch_object($queryexamcenter);
						if ($row_center->center_id == $rowexamcenter->center_id) $checked=TRUE;
						$db->free($queryexamcenter);
						unset($strexamcenter, $queryexamcenter);
          ?>
                  <tr>
                    <td width="5%"><?php echo ++$SN;?></td>
                    <td><?php echo $row_center->center_name;?></td>
                    <td><?php echo $row_center->center_address;?></td>
                    <td width="15%" align="center"><input name="center_id[<?php echo $row_center->center_id;?>]" type="checkbox" class="student"<?php if ($checked) echo " checked=\"checked\"";?>>
                    <input name="center_exam_code[<?php echo $row_center->center_id;?>]" type="hidden" value="<?php echo $rowexamcenter->exam_code;?>">
                    </td>
                  </tr>
          <?php
        }
        ?>
                </tbody>
              </table>
            </div>
          </div>
          </fieldset>
        </div>      
        <div class="panel-body" style="text-align:center">
          <button type="submit" name="btnEditExam" class="btn btn-success"><i class="fa fa-save"></i> Update Exam</button>
          <input type="hidden" name="rand" value="<?php echo $rand;?>">
          <input type="hidden" name="exam_id" value="<?php echo $exam_id;?>">
        </div>
      </form>
    </div>
  </div>
</div>