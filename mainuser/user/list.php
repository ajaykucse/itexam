<?php
@session_start();
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>";  
?>
<?php

	if (isset($_POST['btnResetPW']))
	{
		$randomValue = ($_POST['rand']);
		if ($_SESSION['rand']['list_users'] == $randomValue)
		{
			$user_id = FilterNumber($_POST['user_id']);
			$strUser = "SELECT * FROM exam_user WHERE user_id='$user_id'";
			$queryUser = $db->query($strUser);
			$rowUSER = $db->fetch_object($queryUser);
			$Username = $rowUSER->UserName;
			$db->free($queryUser);
			$rand = RandomValue(6);

			$newPW = "$Username$rand";

			$pass = pacrypt($newPW,"");

			$strReset = "UPDATE exam_user SET `Password` = '$pass', LastLogin='0' WHERE `user_id` = '$user_id'";

			$db->begin();
			$queryReset = $db->query($strReset);

			if ($queryReset)
			{			
				notify("Reset Password","Password for user <strong>'$Username'</strong> has been successfully reset. <br><br>New Password is : <strong>$newPW</strong><br>&nbsp;",NULL,TRUE,0);
				$db->commit();
			}
			else 
			{
				notify("Reset Password","Can not reset Password for user <strong>'$Username'</strong>. <br>Please try later",NULL,TRUE,0);
				$db->rollback();
			}
			
		}
	}


	//Permission Save
	if (isset($_POST['SavePermission']))
	{
		$user_id = FilterNumber($_POST['user_id']);
		$txtPostPermission = $_POST['perm'];
		if (isset($txtPostPermission))
		{
			foreach ($txtPostPermission as $txtPostPerm)
			{
				$perm_calc = $perm_calc + $txtPostPerm;
			}
		}
		else
			$perm_calc = 0; 

		$strSelectPerm = "SELECT * FROM `exam_user_permission` WHERE `user_id` = '$user_id'";

		$query_perm_check = $db->query($strSelectPerm);
		$no_of_perm = $db->num_rows($query_perm_check);
		if ($no_of_perm > 0) $existing = TRUE;
		$db->free($query_perm_check);
		unset($strSelectPerm, $query_perm_check, $no_of_perm);
		$db->begin();
		if ($existing)
			$strUpdate = "UPDATE `exam_user_permission` SET `permission` = '$perm_calc' WHERE `user_id` = '$user_id';";
		else
			$strUpdate = "INSERT INTO `exam_user_permission` (`user_id`, `permission`) VALUES ('$user_id', '$perm_calc');";

		$query_update = $db->query($strUpdate);
		if ($query_update)
		{
			notify("User Permission","User Permission has been added/updated.",NULL,TRUE,5000);
			$db->commit();
		}
		else
		{
			notify("User Permission","<font color=red>User Permission can not added/updated.</font>",NULL,TRUE,5000);
			$db->rollback();
		}
	}

	//Permission Modal Box
	if(isset($_POST['btnPermission']))
	{
		$randomValue = ($_POST['rand']);
		if ($_SESSION['rand']['list_users'] == $randomValue)
		{
			$user_id = FilterNumber($_POST['user_id']);
			$isAdmin = FilterNumber($_POST['isAdmin']);
			?>
<script>
$(document).ready(function(e) {
	$('#user-permission-modal').modal('show');
	$('div#user-permission-modal .modal-body').css('max-height', $(window).height() * 0.5);
	$('div#user-permission-modal .modal-body').css('overflow-y', 'auto'); 
});
</script>
<div class="modal fade" id="user-permission-modal" tabindex="-1" role="dialog" aria-labelledby="user-permission-modal">
  <div class="modal-dialog">
    <div class="modal-content">
    	<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">User Permissions</h4>
      </div>
      <form class="form-horizontal" method="post">
      <div class="modal-body">
<?php
if ($isAdmin == 1)
{
	$strPerm = "SELECT `permission` FROM `exam_user_permission` WHERE `user_id` = '$user_id'";
	$query_perm = $db->query($strPerm);
	$row_perm = $db->fetch_object($query_perm);
	$permission = $row_perm->permission;

	$db->free($query_perm);
	unset($strPerm, $query_perm, $row_perm);

	$caption = ArrayCaption();
	$perm = ArrayPermission();
?>
              <div class="tooltip-demo pull-right" style="margin-bottom:15px;"><button title="Select All" class="btn btn-primary " type="button" name="select-all" id="select-all"  data-toggle-tooltip="tooltip" data-placement="top"><i class="fa fa-check-square-o"></i>&nbsp; Select All</button>&nbsp;
              <button class="btn btn-warning" type="button" name="deselect-all" id="deselect-all" title="De-select all"  data-toggle-tooltip="tooltip" data-placement="top"><i class="fa fa-square-o"></i>&nbsp; De-select All</button>
                 </div>
      <div class="col-md-12">At least <strong>View</strong> Permission is also needed for access Other Permission(s).</div>
<?php

	foreach ($caption as $KEY => $CAPTION)
	{
		?>

    <div class="col-md-12" style="border:1px solid #ccc; padding:5px; margin-bottom:5px;">
      <strong><?php echo $CAPTION;?></strong>
      <div class="col-md-12">
    <?php
		foreach ($perm[$KEY] as $PERM)
		{
			if ($PERM['value'] & $permission) $checked = " checked=\"checked\"";
			else $checked="";

			if ($permission == 0)
			{
				if ($PERM['text'] == "View") $checked = " checked=\"checked\"";
			}

			?>
							<div class="col-md-6">
								<label style="font-weight:normal;"><input type="checkbox"<?php echo $checked;?> value="<?php echo $PERM['value'];?>" name="perm[]">&nbsp; <?php echo $PERM['text'];?></label>
							</div>
			<?php	
		} 
		?>
      </div>
    </div>
    <?php
	}
	?>
<script>
$('#select-all').click(function(event) {   
	$(':checkbox').each(function() {
			this.checked = true;
	});
});
$('#deselect-all').click(function(event) {   
	$(':checkbox').each(function() {
			this.checked = false;			
	});
});
</script>

  <?php
}
else
{
	echo "<br><font color=red>Center User does not require any permission.</font><br>";
}
?>
      </div>
      <div class="modal-footer">
        <input type="hidden" name="user_id" value="<?php echo $user_id;?>">
        <button type="submit" class="btn btn-success" name="SavePermission">Save Permission</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>
  </div>
</div>
<?php
		}
	}

	//FOR DELETING 
	if(isset($_POST['btnDelete']))
	{
		$randomValue = ($_POST['rand']);
		if ($_SESSION['rand']['list_users'] == $randomValue)
		{
			$user_id = FilterNumber($_POST['user_id']);
			
			$strSELECT = "SELECT * FROM exam_user WHERE user_id <> '$user_id' and user_type = 'A'";
			$query_select = $db->query($strSELECT);
			$no_of_user = $db->num_rows($query_select);

			if ($no_of_user > 0)
			{
				$strDeleteUserPerm="DELETE FROM exam_user_permission WHERE user_id=$user_id";
				$strDeleteUser="DELETE FROM exam_user WHERE user_id=$user_id";
				$db->begin();
				$query_perm = $db->query($strDeleteUserPerm);
				$query = $db->query($strDeleteUser);
				if ($query && $query_perm)
				{
					notify("User List","User details has been deleted.",NULL,TRUE,5000);
					$db->commit();
				}
				else
				{
				notify("User List","<font color=red>Can not delete user details.</font>",NULL,TRUE,5000);
					$db->rollback();
				}
			}
			else
			{
				notify("User List","<font color=red><br><strong>At least one Admin user is required.</strong><br>&nbsp;</font>",NULL,TRUE,5000);
			}
		}
	}
	?>
<div class="page-header">
  <div class="row">
  	<div class="col-md-9">
      <h3 style="margin-top:0px; margin-bottom:0px;"><span class="fa fa-users"></span> Users List</h3>
    </div>
<?php
if (!$user_change)
  $disabled_add = " disabled=\"disabled\"";
?>
    <div class="col-md-3 pull-right">    
      <form method="post" class="pull-right form-inline" action="add.html">
        <button type="submit" name="btnAddNew" id="btnAddNew" class="btn btn-primary"<?php echo (!empty($disabled_add));unset($disabled_add);?>><span class="fa fa-user-plus"></span> Add New User </button>
      </form>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-lg-12">
<?php
	$SQL = "SELECT *  FROM exam_user ORDER BY user_id";
	$query_result = $db->query($SQL);

	$rand = RandomValue(20);
	$_SESSION['rand']['list_users']=$rand;
	?>
        <div class="dataTable_wrapper">
          <table width="100%" class="table table-striped table-bordered table-hover tooltip-demo" id="list_users">
            <thead>
              <tr>
                <th>SN</th>
                <th>User ID</th>
                <th>Full Name</th>
                <th>Center</th>
                <th>Status</th>
                <th>&nbsp;</th>
              </tr>
            </thead>
            <tbody>
    <?php
    while ($row = $db->fetch_object($query_result))
    {
    ?>
    
              <tr>
                <td style="width:20px"><?php echo $row->user_id;?></td>
                <td><?php echo $row->UserName;
								unset($isAdmin);
                if ($row->user_type=='A') 
								{
									echo " <span class=\"label label-primary pull-right\">Admin</span>";
									$isAdmin = "1";
								}
                if ($row->user_type=='C') 
								{
									echo " <span class=\"label label-info pull-right\">Center Admin</span>";
									$isAdmin = "0";
								}
                ?></td>
                <td><?php echo 	$row->FullName;?></td>
                <td>
                <?php
								if ($row->user_type=='C')
								{
									$strCenter = "SELECT center_name from exam_center WHERE center_id = '$row->center_id'";
									$query_center = $db->query($strCenter);
									$row_center = $db->fetch_object($query_center);
									echo (!empty($row_center->center_name));
									$db->free($query_center);
									unset($row_center, $query_center, $strCenter);
								}
								?>
                </td>
                <td style="width:65px;"><?php if ($row->active == 'Y') echo "<span class=\"label label-primary\">Active</span>";
                else echo "<span class=\"label label-inverse\">In-active</span>";
                ?>
                <br></td>
                <td style="width:115px;">
                      <button title="Edit" class="btn btn-info btn-circle btn-sm edit" type="submit" id="btnEdit2" name="btnEdit2" data-id="<?php echo $row->user_id;?>" data-button="btnEdit2" data-class="btn btn-info" data-input-name="user_id" data-target="#edit_modal" data-toggle="modal" data-url="edit.html"  data-toggle-tooltip="tooltip" data-placement="top"><span class="fa fa-pencil icon-white"></span></button>
  <?php 
  if ($_SESSION['user']['username'] == $row->UserName) $disabled = " disabled";
  else $disabled="";
  ?>                          
                      <button class="btn btn-success btn-circle btn-sm resetpw" type="submit" id="btnResetPW2" name="btnResetPW2" data-id="<?php echo $row->user_id;?>" data-button="btnResetPW" data-class="btn btn-success" data-input-name="user_id" data-target="#reset_pw_modal" data-toggle="modal" data-url="" title="Reset Password"<?php echo $disabled;?>  data-toggle-tooltip="tooltip" data-placement="top"><span class="fa fa-key icon-white"></span></button>
<?php
if ($row->user_type=='C') $dis_perm = " disabled=\"disabled\"";
else $dis_perm = "";
	?>
                  <button title="User Permission" class="btn btn-primary btn-circle btn-sm permission" type="submit" id="btnPermission2" name="btnPermission2"  data-id="<?php echo $row->user_id;?>" data-button="btnPermission" data-class="btn btn-primary" data-input-name="user_id" data-id2="<?php echo $isAdmin;?>" data-input-name2="isAdmin" data-target="#permission-modal" data-toggle="modal" data-url="" data-toggle-tooltip="tooltip" data-placement="top" <?php echo $dis_perm;?><?php echo $disabled;?>><span class="fa fa-th icon-white"></span></button>

                  <button title="Delete" class="btn btn-danger btn-circle btn-sm delete" type="submit" id="btnDelete2" name="btnDelete2"  data-id="<?php echo $row->user_id;?>" data-button="btnDelete" data-class="btn btn-danger" data-input-name="user_id" data-target="#delete_modal" data-toggle="modal" data-url=""<?php echo $disabled;?>  data-toggle-tooltip="tooltip" data-placement="top"><span class="fa fa-trash"></span></button>

<?php  
}
	 ?>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
  </div>
</div>
<?php
if ($user_change)
{
		confirm2("User List","Do you want to change user details?","edit","edit_modal",$rand,1);
		confirm2("User List","Do you want to delete this user detail? <br><br><font 
		color=red>Note: Deleted information can not be restore.</font>","delete","delete_modal",$rand,3);
}
else
{
		no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","edit","edit_modal",$rand,1);
		no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","delete","delete_modal",$rand,3);
}
if ($user_reset_pw)
	confirm2("User List","Do you want to reset password of this user?","resetpw","reset_pw_modal",$rand,2);
else
		no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","resetpw","reset_pw_modal",$rand,2);

if ($user_user_perm)		
	confirm3("User Permission","Do you want to <b>View/Edit Permissions </b> of this user?","permission","permission-modal",$rand,"_permission");
else
		no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","permission","permission-modal",$rand,"_permission");

dataTable("list_users");
?>
<script>
// tooltip demo
	$('.tooltip-demo').tooltip({
			selector: "[data-toggle-tooltip=tooltip]",
			container: "body"
	});
	$('.tooltip-demo').click(function(e) {
    	e.preventDefault();
  });
</script>
