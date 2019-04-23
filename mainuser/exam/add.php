<?php
//error_reporting(E_ALL);
//session_start();
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>"; 

if (isset($_POST['btnAddExam']))
{	
	$rand = FilterString($_POST['rand']);
	if ($_SESSION['add_exam']['rand'] == $rand)
	{
		$exam_type_id = FilterNumber($_POST['exam_type_id']);
		$exam_code = addslashes($_POST['exam_code']);
		$center_id = $_POST['center_id'];

		if (strlen($exam_code) == 0)
		{
				$isError = TRUE;
				$error[] = "Exam Code is blank.";	
		}
		else
		{
			$select_exam_code = "SELECT exam_code FROM exam_exam WHERE exam_code = UPPER('$exam_code');";
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

			//INSERTING DATA TO exam_exam TABLE	
			$sql_exam_id = "SELECT exam_id FROM exam_exam ORDER BY exam_id DESC LIMIT 0,1;";
			$query_id = $db->query($sql_exam_id);
			$row_id = $db->fetch_object($query_id);
			$exam_id = $row_id->exam_id + 1;
			
			$sql_insert_exam = "INSERT INTO exam_exam (exam_id, exam_code, exam_type_id, exam_create_date) VALUES ('$exam_id', '$exam_code', '$exam_type_id', NOW() );";
	
			$db->begin();
			$query_insert = $db->query($sql_insert_exam);
	
			//INSERTING DATA TO exam_exam_center
			if (count($center_id) > 0)
			{
				foreach ($center_id as $KEY => $value)
				{
					$sql_center = "INSERT INTO exam_exam_center (exam_id, center_id) VALUES ('$exam_id','$KEY');";
					$query_center = $db->query($sql_center);
				}
			}
			else $query_center = TRUE;

			if ($query_insert && $query_center)
			{
				$db->commit();
				unset($_SESSION['add_exam'], $step, $complete_step);
				notify("INFORMATION", "<b>New Exam has been added.</b>","list.html",TRUE,5000); 
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

?>
<?php
$strExamType = "SELECT * FROM exam_exam_type ORDER BY exam_type_id;";
$query_exam_type = $db->query($strExamType);
$no_of_exam_type = $db->num_rows($query_exam_type);
if ($no_of_exam_type < 1)
{
	notify("INFORMATION", "<b><font color=red>Exam Type not found.</font><br> Please add it first.</b>","list.html",TRUE,5000); 
	include_once "footer.inc.php";
	exit();
}

$sql_center = " SELECT  *  FROM exam_center " ;
$query_center = $db->query($sql_center);
$no_of_center = $db->num_rows($query_center);
if ($no_of_center < 1)
{
	notify("INFORMATION", "<b><font color=red>Exam Center not found.</font><br> Please add it first.</b>","list.html",TRUE,5000); 
	include_once "footer.inc.php";
	exit();
}

?>

<div class="page-header">
  <div class="row">
  	<div class="col-md-9">
      <h3 style="margin-top:0px; margin-bottom:0px;"><i class="fa fa-edit"> </i> Add Exam</h3>
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
      <div class="panel-heading">New Exam</div>
      <form id="form1" name="form1" method="post" class="form-horizontal">
        <div class="panel-body">
            <div class="col-md-12 form-group">
              <label class="col-md-2">Exam Code</label>
              <div class="col-md-4">
                <input type="text" name="exam_code" id="exam_code" class="form-control" value="<?php 
                $exam_code = ''; 
 						if( isset( $_GET['exam_code'])){
		 					$exam_code = $_GET['exam_code'];
 		
		 				}


                echo $exam_code;?>">
              </div>
  
              <label class="col-md-2">Exam Type</label>
              <div class="col-md-4">
              	<!-- $txtUserID = isset($_GET['add_exam']['exam_type_id']) ? $_GET['add_exam']['exam_type_id'] : ''; -->
                <select name="exam_type_id" class="form-control">
  <?php
  while ($row_exam_type = $db->fetch_object($query_exam_type))
  {
  ?>
                    <option value="<?php echo $row_exam_type->exam_type_id;?>"<?php if ($row_exam_type->exam_type_id ==$_SESSION['add_exam']['exam_type_id']) echo " selected=\"selected\"";?>><?php
                    echo $row_exam_type->exam_type;?></option><?php
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
        while ($row_center = $db->fetch_object($query_center))
        {
          if (count($_SESSION['add_exam']['center_id']) > 0)
          {
            $checked = FALSE;
            foreach ($_SESSION['add_exam']['center_id'] as $key => $ID)
            {
              if ($row_center->center_id == $key) $checked=TRUE;
            }
          }
          ?>
                  <tr>
                    <td width="5%"><?php echo ++$SN;?></td>
                    <td><?php echo $row_center->center_name;?></td>
                    <td><?php echo $row_center->center_address;?></td>
                    <td width="15%" align="center"><input name="center_id[<?php echo $row_center->center_id;?>]" type="checkbox" class="student"<?php

                    $checked = ''; 
 						if( isset( $_GET['checked'])){
		 					$checked = $_GET['checked'];
 		
		 				}
                     if ($checked) echo " checked=\"checked\"";?>>
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
          <button type="submit" name="btnAddExam" class="btn btn-primary"><i class="fa fa-plus"></i> Add New Exam</button>
          <input type="hidden" name="rand" value="<?php echo $rand;?>">
        </div>
      </form>
    </div>
  </div>
</div>