<?php
@session_start();
if (file_exists("security.php")) include_once "security.php";
if (isset($_SESSION['ONLINE-EXAM-SIMULATOR']) || isset($_SESSION['FirstTime']))
{
		$randomValue = ($_POST['password_random']);

	if (isset($_POST['btnchangepw']))
	{
		$randomValue = ($_POST['password_random']);

		if ($_SESSION['password_rand'] == $randomValue)
		{

			$currpw = addslashes($_POST['currpw']);
			$pw1 = addslashes($_POST['pw1']);
			$pw2 = addslashes($_POST['pw2']);
			unset($isPasswordError, $pw_error);

			//Error Checking
			$isPasswordError = FALSE;
	
			if (strlen($currpw) == 0)
			{
				$isPasswordError = TRUE;
				$pw_error[] = "Enter current password.";	
			}
			else
			{
				$user = $_SESSION['user']['username'];
				$strSQL = "select * from exam_user where `UserName` = '$user'; ";

				$queryPW = $db->query($strSQL);
				$rowPW = $db->fetch_object($queryPW);
				$pw = $rowPW->Password;
				$db->free($queryPW);
				$crypt_password = pacrypt ($currpw, $pw);
				$newPW = pacrypt($pw1);
				if($pw != $crypt_password)
				{
					$isPasswordError = TRUE;
					$pw_error[] = "Current password mis-matched.";
				}
			}
			if (strlen($pw1) == 0)
			{
				$isPasswordError = TRUE;
				$pw_error[] = "Enter new password.";	
			}
			if (strlen($pw2) == 0)
			{
				$isPasswordError = TRUE;
				$pw_error[] = "Verify new password.";	
			}
			
			if ($pw1 != $pw2)
			{
				$isPasswordError = TRUE;
				$pw_error[] = "Verify new password.";
			}
				
			if ($isPasswordError == FALSE)
			{
				if ($_SESSION['FirstTime'])
				{
					$LastLogin = time();
					$strReset = ("UPDATE `exam_user` SET `Password` = '$newPW', LastLogin = '$LastLogin'  WHERE `UserName` = '" . $_SESSION['user']['username'] . "';");
					unset($_SESSION['FirstTime']);
				}
				else
				{
					$strReset = ("UPDATE `exam_user` SET `Password` = '$newPW' WHERE `UserName` = '" . $_SESSION['user']['username'] . "';");
					
				}
				$queryReset = $db->query($strReset);

				if ($queryReset)
				{
					unset($_SESSION['FirstTime']);
					unset($_SESSION['ONLINE-EXAM-SIMULATOR']);
					session_destroy();
					notify("Password Change","Your password has been successfully changed.<br> Please login with new password.","login.html",TRUE,10000);
					include_once "footer.inc.php";
					exit();
				}
			}		// NO ERROR
		}		//random value check
	}

?>
<?php 
if ($isPasswordError)
{
	$CountERROR = count($pw_error);
	$text = "<ul>";
	for($i=0;$i<$CountERROR;$i++)
	{
		$text .= "<li>" . $pw_error[$i] . "</li>";
	}
	$text .= "</ul></b></font>";

	notify("समस्या","<font color=\"red\">$text</font>",NULL,TRUE, 10000);
	?>
<?php    
}
?>
<?php
$password_rand = RandomValue(15);
$_SESSION['password_rand']=$password_rand;
?>
    <!-- Change Password -->
    <div style="display: none;" class="modal" id="changepw" tabindex="-1" role="dialog" aria-labelledby="mychangepwlabel" aria-hidden="true">
      <div class="modal-dialog" style="margin-top:7%;">
        <div class="modal-content">
          <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="mychangepwlabel"><strong><span class="fa fa-lock"></span> Change Password</strong></h4>
          </div>
          <div class="modal-body">
            <form  method="post" role="form" name="form-change-pw" id="form-change-pw" class="form-horizontal">
              <div class="form-group">
                <label class="col-sm-4">Current Password</label>
                <div class="col-sm-8">
                  <input type="password" name="currpw" id="currpw" class="form-control" placeholder="Current Password" />
                </div>
              </div>
    
              <div class="form-group">
                <label class="col-sm-4">New Password</label>
                <div class="col-sm-8">
                  <input type="password" name="pw1" id="pw1" class="form-control" placeholder="New Password" />
                </div>
              </div>
    
              <div class="form-group">
                <label class="col-sm-4">Verify New Password </label>
                <div class="col-sm-8">
                  <input type="password" name="pw2" id="pw2" class="form-control" placeholder="Verify New Password" />
                </div>
              </div>
    
          <div class="modal-footer">
            <button id ="btnchangepw" name="btnchangepw" type="submit" class="btn btn-success"><i class="glyphicon glyphicon-lock"></i> Change Password</button>
            <input type="hidden" name="password_random" value="<?php echo $password_rand;?>">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
            </form>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /.change password -->
<?php 
 }
?>