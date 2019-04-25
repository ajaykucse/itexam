<?php
@session_start();
if (file_exists("security.php")) include_once "security.php";
	header("Location:".$MAIN_URL . "start.html");
exit();
include_once "changepw.php";

if (isset($_POST['btnHome']))
{
	session_destroy();
	header("Location:".$MAIN_URL . "start.html");
}

if (isset($_POST['btnLogin']))
{

	$randomValue = ($_POST['rand']);
	if ($_SESSION['list_user'] == $randomValue)
	{
		$username = addslashes($_POST['user']);
		$password = addslashes($_POST['password']);

		$query = "
		SELECT * FROM exam_user WHERE UserName='$username'";

		$query_result=$db->query($query);
		$no_of_rows = $db->num_rows($query_result);
	
		$row= $db->fetch_object($query_result);
		$crypt_password = pacrypt ($password, $row->Password);
		if($row->Password == $crypt_password)
			$isAuthenticated=TRUE;
		else
			$isAuthenticated=FALSE;
	
		if ($no_of_rows == 0 || $isAuthenticated == FALSE)
		{
			unset ($_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER']);
			$isError = TRUE;
			$txtError = "Username/Password not matched.";
		}
		if ($isAuthenticated)
		{
			$user_id = $row->user_id;
			$username = $row->UserName;
			$fullname = $row->FullName;
			$Status = $row->active;
			$isAdmin = $row->isAdmin;
			$center_id = $row->center_id;
			$LastLogin = $row->LastLogin;
			$Status = $row->active;

			if ($Status != "Y")	//if not active a/c
			{
				unset ($_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER']);
				unset ($_SESSION['user']);
				$isError = TRUE;
				$txtError = "Your account is not active. <br>Please contact to System Administrator.";
			}
			else
			{
				session_regenerate_id();
				$_SESSION['user']['user_id'] = $user_id;
				$_SESSION['user']['username'] = $username;
				$_SESSION['user']['fullname'] = $fullname;
				$_SESSION['user']['isAdmin'] = $isAdmin;
				$_SESSION['user']['center_id'] = $center_id;
				$_SESSION['expire'] = time() + $session_time ;

					if ($LastLogin <= 0)
					{
						$txtError = "Please change your password.<br>Administrator Enforced.";
						$isError = TRUE;
						$_SESSION['FirstTime'] = TRUE;
						?>
					
					<script type="text/javascript">
					$(window).load(function(ev) {
						$('#changepw').modal('show');  
					});
					</script>
						
						<?php
					}
					else
					{

				session_regenerate_id();
				$_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER']= true;

						create_session($db);			
						notify($software,"<b>Welcome to $software</b>", $URL ."dashboard.html", FALSE,2000);
						include_once "footer.inc.php";
						die();
					}
			}
		}
	}
}

	$rand = RandomValue(10);
	$_SESSION['list_user']=$rand;

?>

<div class="col-md-4 col-sm-6 col-md-offset-4 col-sm-offset-3" style="margin-top:10%;" >
	<div class="animated bounceInDown" align="center">
		<h3><?php echo $software;?></h3>
  </div>
	<div class="login-box animated flipInY">
  <div class="panel panel-primary login">
    <div class="panel-heading">
      <h1 class="panel-title "><i class="fa fa-user-secret fa-2x"></i> &nbsp;Admin User Login</h1>
		</div>
<?php 
if ($isError)
{
?>
	<script>
    window.setTimeout(function()
    {
      $("#alert").fadeTo(100, 0).slideUp(100, function(){
      $(this).remove(); 
      });
    }, 10000);
  </script>
<?php 
}
if ($isError) 
{
	?>
  <div class="row" style="margin-left:3px; margin-right:3px;"  id="alert">
    <div class="col-md-12" style="padding-top:15px; padding-bottom:0px;">
      <div class="alert alert-danger alert-dismissable" style="margin-bottom:0px;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <?php echo "<b>$txtError</b>";?>
      </div>
    </div>
    </div>

<?php } ?>
<style>
.input-group-addon
{
	 min-width:40px;
}
</style>
    <div class="panel-body">
        <form role="form" method="post">
            <fieldset>
                <div class="form-group input-group">
                  <span class="input-group-addon" id="sizing-addon"><span class="fa fa-user"></span></span>
                     <input name="user" type="text" id="user" placeholder="Username" title="Username" class="form-control">
                </div>
                <div class="form-group input-group">
                  <span class="input-group-addon" id="sizing-addon"><span class="fa fa-key"></span></span>
                    <input class="form-control" placeholder="Password" name="password" type="password" value="">
                </div>
                <input type="hidden" name="rand" value="<?php echo $rand;?>">
                <div class="row">
                  <div class="col-md-6">
                    <button class="btn btn-success btn-block active" name="btnLogin"> <span class="fa fa-unlock"></span> Login</button>
                  </div>
                  <div class="col-md-6">
                    <button class="btn btn-primary btn-block" name="btnHome"> <span class="fa fa-home"></span> <?php echo $software;?> </button>
                  </div>
                </div>
                  
            </fieldset>
        </form>
    </div>
  </div>
</div>
</div>

<link href="../animated.css" rel="stylesheet" type="text/css">
<style>
.login
{
	border: 1px solid rgb(153, 200, 250);
	box-shadow: rgba(0, 0, 0, 0.1) 0px 9px 9px 9px;
	background: rgba(153, 200, 250, 0.1);
}
</style>