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
	
	if (isset($_POST['btnSubmit']))
	{
		$error = array();
		$username = addslashes(trim($_POST['username']));
		$password= addslashes(trim($_POST['password']));
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
			$strSELECT = "select UserName from exam_user where UserName = '$username'";
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
		if (strlen($password) == 0) 
		{
			$error[] = "Please enter password.";
			$isError = TRUE;
		}

		if (strlen($fullname) == 0) 
		{
			$error[] = "Please enter Full Name.";
			$isError = TRUE;
		}

		if ($isError == FALSE)
		{
			$str =  "SELECT `user_id` from exam_user ORDER BY `user_id` DESC LIMIT 0,1";
			$row = $db->fetch_array_assoc($db->query($str));
			$user_id = $row['user_id'] +1;

			//Inserting New Data
			$pass = pacrypt($password,"");
			
			$strInsert = "INSERT INTO exam_user (user_id, UserName, Password, fullname, user_type, center_id, active, LastLogin) VALUES ('$user_id','$username', '$pass', '$fullname', '$isAdmin', '$center_id', '$isActive','0')";

			$query_result = $db->query($strInsert);
			
			if ($query_result)
			{
				//notify("title","message text","URL", noclose=true/false,time);
				notify("User List","New user has been added.","list.html",TRUE,5000);
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
?>
<h3 class="page-header"><i class="fa fa-user-plus"></i> Add User </h3>
<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading">Add User </div>
      <div class="panel-body">
        <div class="row">
          <form id="form1" name="form1" method="post" class="form-horizontal">
            <div class="col-md-12">
              <div class="form-group">
                <label class="col-md-3">Username</label>
                <div class="col-md-5">
                  <input name="username" type="text" id="username" value="<?php if (isset($username)) echo $username;?>" size="50" placeholder="username" class="form-control" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-3">Password</label>
                <div class="col-md-5">
                  <input name="password" type="password" id="password" value="<?php if (isset($password)) echo $password;?>" size="50" placeholder="Password" class="form-control" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-3">Full Name</label>
                <div class="col-md-6">
                  <input name="fullname" type="text" id="fullname" value="<?php if (isset($fullname)) echo $fullname;?>" size="50" placeholder="Full Name" class="form-control" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-3">User Type</label>
                <div class="col-md-3">
                  <select name="isAdmin" class="form-control">
<!--                    <option value="S"<?php if ($user_type== 'S') echo " selected=\"selected\"";?>>Student</option> -->
                    <option value="C"<?php if ($isAdmin == 'C') echo " selected=\"selected\"";?>>Center Admin</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-3">Status</label>
                <div class="col-md-3">
                  <select name="isActive" class="form-control">
                    <option value="Y"<?php if ($isActive == 'Y') echo " selected=\"selected\"";?>>Active</option>
                    <option value="N"<?php if ($isActive == 'N') echo " selected=\"selected\"";?>>In-active</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-3">Center</label>
                <div class="col-md-5">
                  <select name="center_id" class="form-control">
                  <?php
                    $strCENTER = "SELECT center_id, center_name FROM exam_center WHERE center_id = ". $_SESSION['user']['center_id'] ." ORDER BY center_name";
                    $query_center = $db->query($strCENTER);
                    while ($row_center = $db->fetch_object($query_center))
                    {
                      ?>
                      <option value="<?php echo $row_center->center_id;?>"<?php if ($row_center->center_id == $center_id) echo " selected=\"selected\"";?>><?php echo $row_center->center_name;?></option>
                    <?php
                    }
                    $db->free($query_center);
                    unset($row_center,$query_center, $strCENTER);
                  ?>
                  </select>
                </div>
              </div>

              <div class="col-md-9 col-md-offset-3">
                <button type="submit" class="btn btn-primary" name="btnSubmit"><span class="fa fa-user-plus"></span> Add New User</button>
                <button type="submit" class="btn btn-warning" name="btnCancel"><span class="glyphicon glyphicon-repeat"></span> User List</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>