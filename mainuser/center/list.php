<?php
session_start();
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";
if (file_exists("../../security.php")) include_once "../../security.php";

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>";  
?>
<?php

	//FOR DELETING 
	if(isset($_POST['btnDelete']))
	{
		$randomValue = ($_POST['rand']);
		if ($_SESSION['rand']['list_center'] == $randomValue)
		{
			$center_id = FilterNumber($_POST['center_id']);
			$strDeletePage="DELETE FROM exam_center WHERE center_id=$center_id";
			$db->begin();
			$query = $db->query($strDeletePage);
			if ($query)
			{
				notify("Center List","Center details has been deleted.",NULL,TRUE,5000);
				$db->commit();
			}
			else
			{
			notify("Center List","<font color=red>Can not delete center details. <br><br>Child Records may exists.</font>",NULL,TRUE,5000);
				$db->rollback();
			}
		}
	}
	?>
<div class="page-header">
  <div class="row">
  	<div class="col-md-9">
      <h3 style="margin-top:0px; margin-bottom:0px;"><span class="fa fa-university"></span> Center List</h3>
    </div>
    <div class="col-md-3 pull-right">    
<?php
if (!$exam_center_change)
  $disabled_add = " disabled=\"disabled\"";
?>

      <form method="post" class="pull-right form-inline" action="add.html">
        <button type="submit" name="btnAddNew" id="btnAddNew" class="btn btn-primary"<?php echo $disabled_add;unset($disabled_add);?>><span class="fa fa-plus-circle"></span> Add New Center </button>
      </form>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-lg-12">
<?php
	$SQL = "SELECT * FROM  exam_center Order by center_id";
	$query_result = $db->query($SQL);
	$rand = RandomValue(20);
	$_SESSION['rand']['list_center']=$rand;
	?>

      <div class="dataTable_wrapper">
        <table class="table table-striped table-bordered table-hover tooltip-demo" id="list_center">
          <thead>
            <tr>
              <th>SN</th>
              <th>Center Name</th>
              <th>Address</th>
              <th>Telephone</th>
              <th>Fax</th>
              <th>Email</th>
              <th>&nbsp;</th>
            </tr>
          </thead>
          <tbody>
<?php
while ($row = $db->fetch_object($query_result))
{
?>

                <tr>
                  <td style="width:20px"><?php echo $row->center_id;?></td>
                  <td><?php echo $row->center_name ;?></td>
                  <td><?php echo $row->center_address ;?></td>
                  <td><?php echo $row->center_phone ;?></td>
                  <td><?php echo 	$row->center_fax;?></td>
                  <td><?php echo $row->center_email;?></td>
                  <td style="width:65px;">
                      <form method="post" name="edit_<?php echo $row->center_id;?>" style="float:left; margin:0px;">
                        <button title="Edit" class="btn btn-info btn-circle btn-sm edit" type="submit" id="btnEdit2" name="btnEdit2" data-id="<?php echo $row->center_id;?>" data-button="btnEdit2" data-class="btn btn-info" data-input-name="center_id" data-target="#edit_modal" data-toggle="modal" data-url="edit.html"  data-toggle-tooltip="tooltip" data-placement="top"><span class="glyphicon glyphicon-pencil icon-white"></span></button>&nbsp;
                        <input type="hidden" name="rand" value="<?php echo $rand;?>">
                      </form>
                      <form method="post" name="delete_<?php echo $row->center_id;?>" style="float:left; margin:0px;">
                        <button title="Delete" class="btn btn-danger btn-circle btn-sm delete" type="submit" id="btnDelete2" name="btnDelete2"  data-id="<?php echo $row->center_id;?>" data-button="btnDelete" data-class="btn btn-danger" data-input-name="center_id" data-target="#delete_modal" data-toggle="modal" data-url="" data-toggle-tooltip="tooltip" data-placement="top"><span class="glyphicon glyphicon-trash"></span></button>
                        <input type="hidden" name="rand" value="<?php echo $rand;?>">
                      </form>
                  </td>
                </tr>
<?php  }
if ($exam_center_change)
{
	confirm2("Center List","Do you want to change Center details?","edit","edit_modal",$rand,1);
	confirm2("Center List","Do you want to delete this Center detail? <br><br><font 
	color=red>Note: Deleted information can not be restore.</font>","delete","delete_modal",$rand,2);
}
else
{
	no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","edit","edit_modal",$rand,1);
	no_permission("User Permission","<br><font color=red>You don't have permission to do this action.</font><br>&nbsp;","delete","delete_modal",$rand,2);

}
   ?>
              </tbody>
            </table>
      </div>
<?php
	dataTable("list_center");
?>
    </div>
  </div>
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