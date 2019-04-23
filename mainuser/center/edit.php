<?php
session_start();
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>";  

	$center_id = FilterNumber($_POST['center_id']);

	if (!isset($_POST['center_id']))
		echo "<script>window.location='list.html';</script>";
	
	if (isset($_POST['btnCancel']))
	{
		$_SESSION['editmode'] = "";
		echo "<script>window.location='list.html';</script>";
	}
	
	if (isset($_POST['btnSubmit']))
	{
		$error = array();
		$center_name = addslashes(trim($_POST['center_name']));
		$center_address = addslashes(trim($_POST['center_address']));
		$center_phone = addslashes(trim($_POST['center_phone']));
		$center_fax = addslashes(trim($_POST['center_fax']));
		$center_email = FilterEmail($_POST['center_email']);
		$isError=FALSE;

		if (strlen($center_name) == 0) 
		{
			$error[] = "Please enter Center name.";
			$isError = TRUE;
		}

		if (strlen($center_address) == 0) 
		{
			$error[] = "Please enter Center Address.";
			$isError = TRUE;
		}

		if ($isError == FALSE)
		{
	
			$strCheck = "SELECT center_name from exam_center where center_name = '". ($center_name) . "' AND center_id <> '$center_id';";
			$query_check = $db->query($strCheck);
			$no_of_rows = $db->num_rows($query_check);

			if ($no_of_rows == 0)
			{
				//Updating Data
				$strUpdate = "UPDATE exam_center SET 
				center_name = '$center_name',
				center_address = '$center_address',
				center_phone = '$center_phone',
				center_fax = '$center_fax',
				center_email = '$center_email'
				WHERE center_id = '$center_id';";

				$query_result = $db->query($strUpdate);
				
				if ($query_result)
				{
					//notify("title","message text","URL", noclose=true/false,time);
					notify("Center List","Center Information has been changed.","list.html",TRUE,5000);
					$db->commit();
					include_once "footer.inc.php";
					exit();
				}
				else
				{
					$isError = TRUE;
				}
			}
			else
			{
					$error[]= "Center is already exist.";
					$isError = TRUE;
			}
		}
		
	}
?>
<?php 
if ($isError)
{
	$CountERROR = count($error);
	$text = "<ul>";
	for($i=0;$i<$CountERROR;$i++)
	{
		$text .= "<li>".$error[$i] . "</li>";
	}
	$text .= "</ul>";
	notify("<font color=red><b>E r r o r</b></font>", $text,NULL,TRUE,0);
}

$strSELECT = "SELECT * FROM exam_center WHERE center_id = '$center_id';";
$query_select = $db->query($strSELECT);
$no_of_center = $db->num_rows($query_select);
if ($no_of_center > 0)
{
	$row_center = $db->fetch_object($query_select);
?>
<h3 class="page-header">Edit Center</h3>

<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading">Edit Center</div>
      <div class="panel-body">
        <form id="form1" name="form1" method="post" class="form-horizontal">
          <div class="col-md-12">
        
            <div class="form-group">
              <label class="col-md-3">Center Name</label>
              <div class="col-md-9">
                <input name="center_name" type="text" id="center_name" value="<?php echo $row_center->center_name;?>" size="50" placeholder="Center Name" class="form-control" />                      
              </div>
            </div>
        
            <div class="form-group">
              <label class="col-md-3">Center Address</label>
              <div class="col-md-9">
                <input name="center_address" type="text" id="center_address" value="<?php echo $row_center->center_address;?>" size="50" placeholder="Center Address" class="form-control" />                      
              </div>
            </div>
        
            <div class="form-group">
              <label class="col-md-3">Center Phone</label>
              <div class="col-md-9">
                <input name="center_phone" type="text" id="center_phone" value="<?php echo $row_center->center_phone;?>" size="50" placeholder="Center Phone" class="form-control" />                      
              </div>
            </div>
        
            <div class="form-group">
              <label class="col-md-3">Center Fax</label>
              <div class="col-md-9">
                <input name="center_fax" type="text" id="center_fax" value="<?php echo $row_center->center_fax;?>" size="50" placeholder="Center Fax" class="form-control" />                      
              </div>
            </div>
        
            <div class="form-group">
              <label class="col-md-3">Center Email</label>
              <div class="col-md-9">
                <input name="center_email" type="text" id="center_email" value="<?php echo $row_center->center_email;?>" size="50" placeholder="Center Email" class="form-control" />                      
              </div>
            </div>
        
        </div>
            <div class="col-md-9 col-md-offset-3">
              <button type="submit" class="btn btn-primary" name="btnSubmit"><span class="glyphicon glyphicon-floppy-disk"></span> Edit Center</button>
              <button type="submit" class="btn btn-warning" name="btnCancel"><span class="glyphicon glyphicon-repeat"></span> Center List</button>
              <input type="hidden" name="center_id" value="<?php echo $center_id;?>">
            </div>
        </form>
      </div>
  </div>
</div>
<?php
}
else
		echo "<script>window.location='list.html';</script>";
?>