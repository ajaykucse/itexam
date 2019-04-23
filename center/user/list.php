<?php
session_start();
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>";  
$center_id = $_SESSION['user']['center_id'];
?>
<?php

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
				$strDeleteStudent = "DELETE FROM exam_student_user WHERE user_id = '$user_id'";
				$strDeletePage="DELETE FROM exam_user WHERE user_id=$user_id";
				$db->begin();
				$query2 = $db->query($strDeleteStudent);
				$query = $db->query($strDeletePage);
				if ($query && $query2)
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
      <h3 style="margin-top:0px; margin-bottom:0px;"><span class="fa fa-users"></span> User List</h3>
    </div>
    <div class="col-md-3 pull-right">    
      <form method="post" class="pull-right form-inline" action="add.html">
        <button type="submit" name="btnAddNew" id="btnAddNew" class="btn btn-primary"><span class="fa fa-user-plus"></span> Add New User </button>
      </form>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-lg-12">
<?php
	$SQL = "SELECT exam_user.*, exam_center.center_name
  FROM exam_center exam_center
       RIGHT OUTER JOIN exam_user exam_user
          ON (exam_center.center_id = exam_user.center_id)
	WHERE exam_user.center_id = '$center_id' AND user_type <> 'A' 
	Order by user_id";
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
                <td style="width:20px"><?php echo ++$i;?></td>
                <td><?php echo $row->UserName;
                if ($row->user_type=='A') echo " <span class=\"label label-primary pull-right\">Admin</span>";
                if ($row->user_type=='C') echo " <span class=\"label label-info pull-right\">Center Admin</span>";
                if ($row->user_type=='S') echo " <span class=\"label label-warning pull-right\">Student</span>";
                ?></td>
                <td><?php echo 	$row->FullName;?></td>
                <td><?php echo 	$row->center_name;?></td>
                <td style="width:65px;"><?php if ($row->active == 'Y') echo "<span class=\"label label-primary\">Active</span>";
                else echo "<span class=\"label label-inverse\">In-active</span>";
                ?>
                <br></td>
                <td style="width:115px;">
                    <form method="post" name="edit_<?php echo $row->user_id;?>" style="float:left; margin:0px;">
                      <button title="Edit" class="btn btn-info btn-circle btn-sm edit" type="submit" id="btnEdit2" name="btnEdit2" data-id="<?php echo $row->user_id;?>" data-button="btnEdit2" data-class="btn btn-primary" data-input-name="user_id" data-target="#edit_modal" data-toggle="modal" data-url="edit.html"  data-toggle-tooltip="tooltip" data-placement="top"><span class="glyphicon glyphicon-pencil icon-white"></span></button>&nbsp;
                      <input type="hidden" name="rand" value="<?php echo $rand;?>">
                    </form>
  <?php 
  if ($_SESSION['user']['username'] == $row->UserName) $disabled = " disabled";
  else $disabled="";
  ?>                          
                    <form method="post" name="reset_pw_<?php echo $row->user_id;?>" style="float:left; margin:0px;">
                      <button class="btn btn-success btn-circle btn-sm resetpw" type="submit" id="btnResetPW2" name="btnResetPW2" data-id="<?php echo $row->user_id;?>" data-button="btnResetPW2" data-class="btn btn-success" data-input-name="user_id" data-target="#reset_pw_modal" data-toggle="modal" data-url="" title="Reset Password"<?php echo $disabled;?>  data-toggle-tooltip="tooltip" data-placement="top"><span class="fa fa-key icon-white"></span></button>&nbsp;
                      <input type="hidden" name="rand" value="<?php echo $rand;?>">
                    </form>
                    <form method="post" name="delete_<?php echo $row->user_id;?>" style="float:left; margin:0px;">
                      <button title="Delete" class="btn btn-danger btn-circle btn-sm delete" type="submit" id="btnDelete2" name="btnDelete2"  data-id="<?php echo $row->user_id;?>" data-button="btnDelete" data-class="btn btn-danger" data-input-name="user_id" data-target="#delete_modal" data-toggle="modal" data-url=""<?php echo $disabled;?>  data-toggle-tooltip="tooltip" data-placement="top"><span class="glyphicon glyphicon-trash"></span></button>
                      <input type="hidden" name="rand" value="<?php echo $rand;?>">
                    </form>
    <?php  }
    confirm2("User List","Do you want to change user details?","edit","edit_modal",$rand,1);
    confirm2("User List","Do you want to reset password of this user?","resetpw","reset_pw_modal",$rand,2);
    confirm2("User List","Do you want to delete this user detail? <br><br><font 
    color=red>Note: Deleted information can not be restore.</font>","delete","delete_modal",$rand,3);
       ?>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
  </div>
</div>
<?php
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