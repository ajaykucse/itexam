<?php
session_start();
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>";  
?>
<?php

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
	
			$strCheck = "SELECT center_name from exam_center where center_name = '". ($center_name) . "'";
			$query_check = $db->query($strCheck);
			$no_of_rows = $db->num_rows($query_check);

			if ($no_of_rows == 0)
			{
				$str =  "SELECT `center_id` from exam_center ORDER BY `center_id` DESC LIMIT 0,1";
				$row = $db->fetch_array_assoc($db->query($str));
				
				$center_id = $row['center_id'] +1;

				//Inserting New Data
				$strInsert = "INSERT INTO exam_center (center_id, center_name, center_address, center_phone, center_fax, center_email) VALUES ('$center_id','$center_name', '$center_address', '$center_phone', '$center_fax', '$center_email')";
				$query_result = $db->query($strInsert);
				
				if ($query_result)
				{
					//notify("title","message text","URL", noclose=true/false,time);
					notify("Center List","New Center has been added.","list.html",TRUE,5000);
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
?>
<h3 class="page-header">Add New Center</h3>
<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading">New Center</div>
      <div class="panel-body">
        <form id="form1" name="form1" method="post" class="form-horizontal">
          <div class="col-md-12">
  
            <div class="form-group">
              <label class="col-md-3">Center Name</label>
              <div class="col-md-9">
                <input name="center_name" type="text" id="center_name" value="<?php if (isset($center_name)) echo $center_name;?>" size="50" placeholder="Center Name" class="form-control" />                      
              </div>
            </div>
  
            <div class="form-group">
              <label class="col-md-3">Center Address</label>
              <div class="col-md-9">
                <input name="center_address" type="text" id="center_address" value="<?php if (isset($center_address)) echo $center_address;?>" size="50" placeholder="Center Address" class="form-control" />                      
              </div>
            </div>
  
            <div class="form-group">
              <label class="col-md-3">Center Phone</label>
              <div class="col-md-9">
                <input name="center_phone" type="text" id="center_phone" value="<?php if (isset($center_phone)) echo $center_phone;?>" size="50" placeholder="Center Phone" class="form-control" />                      
              </div>
            </div>
  
            <div class="form-group">
              <label class="col-md-3">Center Fax</label>
              <div class="col-md-9">
                <input name="center_fax" type="text" id="center_fax" value="<?php if (isset($center_fax)) echo $center_fax;?>" size="50" placeholder="Center Fax" class="form-control" />                      
              </div>
            </div>
  
            <div class="form-group">
              <label class="col-md-3">Center Email</label>
              <div class="col-md-9">
                <input name="center_email" type="text" id="center_email" value="<?php if (isset($center_email)) echo $center_email;?>" size="50" placeholder="Center Email" class="form-control" />                      
              </div>
            </div>
  
        </div>
            <div class="col-md-9 col-md-offset-3">
              <button type="submit" class="btn btn-primary" name="btnSubmit"><span class="glyphicon glyphicon-plus"></span> Add New Center</button>
              <button type="submit" class="btn btn-warning" name="btnCancel"><span class="glyphicon glyphicon-repeat"></span> Center List</button>
            </div>
        </form>
      </div>
    </div>
  </div>  
</div>