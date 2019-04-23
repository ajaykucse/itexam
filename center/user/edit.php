<?php
session_start();
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>";  
?>
<?php
	if (isset($_POST['btnCancel']))
	{
		$_SESSION['editmode'] = "";
		echo "<script>window.location='list.html';</script>";
	}

$user_id = FilterNumber($_POST['user_id']);

if (!isset($user_id))
		echo "<script>window.location='list.html';</script>";
	
	if (isset($_POST['btnSubmit']))
	{
		$error = array();

		$username = addslashes(trim($_POST['username']));
		$fullname= addslashes(trim($_POST['fullname']));
		$isAdmin= FilterString(trim($_POST['isAdmin']));
		$isActive= FilterString(trim($_POST['isActive']));
		$center_id = FilterNumber(trim($_POST['center_id']));

		$isError=FALSE;

		if (strlen($username) == 0) 
		{
			$error[] = "Please enter username.";
			$isError = TRUE;
		}
		else
		{
			$strSELECT = "select UserName from exam_user where UserName = '$username' AND user_id <> '$user_id';";
			$querySELECT = $db->query($strSELECT);
			$no_of_user = $db->num_rows($querySELECT);
	
			if ($no_of_user > 0)
			{
				$isError = TRUE;
				$error[] = "User already exist!";	
			}
			$db->free($querySELECT);
			unset($no_of_user,$querySELECT,$strSELECT);
		}
		if (strlen($fullname) == 0) 
		{
			$error[] = "Please enter Full Name.";
			$isError = TRUE;
		}

		if ($isError == FALSE)
		{

			//Update Data
				$strUpdate = "
				UPDATE exam_user SET
				UserName = '$username',
				fullname = '$fullname',
				user_type = '$isAdmin',
				center_id = '$center_id',
				active = '$isActive'
				WHERE user_id = '$user_id';";

			$query_result = $db->query($strUpdate);
			
			if ($query_result)
			{
				//notify("title","message text","URL", noclose=true/false,time);
				notify("User List","User details has been changed.","list.html",TRUE,5000);
				$db->commit();
				include_once "footer.inc.php";
				exit();
			}
			else
			{
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

$strSELECT = "SELECT * FROM exam_user WHERE user_id = '$user_id';";
$query_user = $db->query($strSELECT);
$no_of_user = $db->num_rows($query_user);
if ($no_of_user > 0)
{
	$row_user = $db->fetch_object($query_user);
?>
<h3 class="page-header"><i class="fa fa-user"></i> Edit User </h3>
<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading"><i class="fa fa-user"></i> Edit User </div>
      <div class="panel-body">
        <div class="row">
          <form id="form1" name="form1" method="post" class="form-horizontal">
            <div class="col-md-12">
              <div class="form-group">
                <label class="col-md-3">Username</label>
                <div class="col-md-5">
                  <input name="username" type="text" id="username" value="<?php echo $row_user->UserName;?>" size="50" placeholder="username" class="form-control" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-3">Full Name</label>
                <div class="col-md-6">
                  <input name="fullname" type="text" id="fullname" value="<?php echo $row_user->FullName;?>" size="50" placeholder="Full Name" class="form-control" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-3">User Type</label>
                <div class="col-md-3">
                  <select name="isAdmin" class="form-control">
                    <option value="S"<?php if ($row_user->user_type== 'S') echo " selected=\"selected\"";?>>Student</option>
                    <option value="C"<?php if ($row_user->user_type == 'C') echo " selected=\"selected\"";?>>Center Admin</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-3">Status</label>
                <div class="col-md-3">
                  <select name="isActive" class="form-control">
                    <option value="Y"<?php if ($row_user->active == 'Y') echo " selected=\"selected\"";?>>Active</option>
                    <option value="N"<?php if ($row_user->active == 'N') echo " selected=\"selected\"";?>>In-active</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-3">Center</label>
                <div class="col-md-5">
                  <select name="center_id" class="form-control">
                    <option value="0">Choose one...</option>
                  <?php
                    $strCENTER = "SELECT center_id, center_name FROM exam_center WHERE center_id = " . $_SESSION['user']['center_id']. " ORDER BY center_name";
                    $query_center = $db->query($strCENTER);
                    while ($row_center = $db->fetch_object($query_center))
                    {
                      ?>
                      <option value="<?php echo $row_center->center_id;?>"<?php if ($row_center->center_id == $row_user->center_id) echo " selected=\"selected\"";?>><?php echo $row_center->center_name;?></option>
                    <?php
                    }
                    $db->free($query_center);
                    unset($row_center,$query_center, $strCENTER);
                  ?>
                  </select>
                </div>
              </div>

              <div class="col-md-9 col-md-offset-3">
                <button type="submit" class="btn btn-success" name="btnSubmit"><span class="glyphicon glyphicon-floppy-disk"></span> Edit User </button>
                <button type="submit" class="btn btn-warning" name="btnCancel"><span class="glyphicon glyphicon-repeat"></span> User List</button>
                <input type="hidden" name="user_id" value="<?php echo $user_id;?>">
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
}
else
	echo "<script>window.location='list.html';</script>";
?>