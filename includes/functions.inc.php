<?php
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";

/**
 * Encrypt a password, using the apparopriate hashing mechanism as defined in 
 * config.inc.php ($CONF['encrypt']). 
 * When wanting to compare one pw to another, it's necessary to provide the salt used - hence
 * the second parameter ($pw_db), which is the existing hash from the DB.
 *
 * @param string $pw
 * @param string $encrypted password
 * @return string encrypted password.
 */
function pacrypt ($pw, $pw_db="") {
//    global $CONF;
    $pw = stripslashes($pw);
    $password = "";
    $salt = "";
        $split_salt = preg_split ('/\$/', $pw_db);
        if (isset ($split_salt[2])) {
            $salt = $split_salt[2];
        }
        $password = md5crypt ($pw, $salt);
    return $password;
}

// md5crypt
// Action: Creates MD5 encrypted password
// Call: md5crypt (string cleartextpassword)

function md5crypt ($pw, $salt="", $magic="") {
    $MAGIC = "$1$";
    if ($magic == "") $magic = $MAGIC;
    if ($salt == "") $salt = create_salt ();
    $slist = explode ("$", $salt);
    if ($slist[0] == "1") $salt = $slist[1];
    $salt = substr ($salt, 0, 8);
    $ctx = $pw . $magic . $salt;
    $final = hex2bin (md5 ($pw . $salt . $pw));
    for ($i=strlen ($pw); $i>0; $i-=16) {
        if ($i > 16) {
            $ctx .= substr ($final,0,16);
        } else {
            $ctx .= substr ($final,0,$i);
        }
    }
    $i = strlen ($pw);
    while ($i > 0) {
        if ($i & 1) $ctx .= chr (0);
        else $ctx .= $pw[0];
        $i = $i >> 1;
    }
    $final = hex2bin (md5 ($ctx));
    for ($i=0;$i<1000;$i++) {
        $ctx1 = "";
        if ($i & 1) {
            $ctx1 .= $pw;
        } else {
            $ctx1 .= substr ($final,0,16);
        }
        if ($i % 3) $ctx1 .= $salt;
        if ($i % 7) $ctx1 .= $pw;
        if ($i & 1) {
            $ctx1 .= substr ($final,0,16);
        } else {
            $ctx1 .= $pw;
        }
        $final = hex2bin (md5 ($ctx1));
    }
    $passwd = "";
    $passwd .= to64 (((ord ($final[0]) << 16) | (ord ($final[6]) << 8) | (ord ($final[12]))), 4);
    $passwd .= to64 (((ord ($final[1]) << 16) | (ord ($final[7]) << 8) | (ord ($final[13]))), 4);
    $passwd .= to64 (((ord ($final[2]) << 16) | (ord ($final[8]) << 8) | (ord ($final[14]))), 4);
    $passwd .= to64 (((ord ($final[3]) << 16) | (ord ($final[9]) << 8) | (ord ($final[15]))), 4);
    $passwd .= to64 (((ord ($final[4]) << 16) | (ord ($final[10]) << 8) | (ord ($final[5]))), 4);
    $passwd .= to64 (ord ($final[11]), 2);
    return "$magic$salt\$$passwd";
}
function create_salt () {
    srand ((double) microtime ()*1000000);
    $salt = substr (md5 (rand (0,9999999)), 0, 8);
    return $salt;
}
/**/ if (!function_exists('hex2bin')) { # PHP around 5.3.8 includes hex2bin as native function - http://php.net/hex2bin
function hex2bin ($str) {
    $len = strlen ($str);
    $nstr = "";
    for ($i=0;$i<$len;$i+=2) {
        $num = sscanf (substr ($str,$i,2), "%x");
        $nstr.=chr ($num[0]);
    }
    return $nstr;
	}
}
/*
 * remove item $item from array $array
 */
function remove_from_array($array, $item) {
    # array_diff might be faster, but doesn't provide an easy way to know if the value was found or not
    # return array_diff($array, array($item));
    $ret = array_search($item, $array);
    if ($ret === false) {
        $found = 0;
    } else {
        $found = 1;
        unset ($array[$ret]);
    }
    return array($found, $array);
}
function to64 ($v, $n) {
    $ITOA64 = "./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    $ret = "";
    while (($n - 1) >= 0) {
        $n--;
        $ret .= $ITOA64[$v & 0x3f];
        $v = $v >> 6;
    }
    return $ret;
}

function FilterDateTime($field)
{
	$field =  strip_tags($field);
	return preg_replace("/[^0-9-: ]+/", "", html_entity_decode($field));	
	return $field;	
}

function notify($title, $text, $url, $noclose = TRUE, $timeout=0)
{
	?>
  <script>
  $(document).ready(function(e) {
		$('#notify_modal').modal('show');
  });
	</script>

      
  <div class="modal fade" id="notify_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
		  <div class="modal-content">
			  <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="notify_modal_Label"><strong><?php echo $title;?></strong></h4>
        </div>
        <div class="modal-body">
					<?php echo $text;?>
        </div>
				<?php if ($noclose)
				{
					?>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal" autofocus>Close</button>
        </div>
        <?php } ?>
      </div>
    </div>
  </div>
	<?php if (isset($url))
	{ ?>
<script>
	$('#notify_modal').on('hide.bs.modal', function (e) {
		window.location='<?php echo $url;?>'; 
	});
</script>
	<?php } ?>
	<?php if ((isset($timeout) && ($timeout > 0)))
	{
		?>
<script>
		window.setTimeout(function() { $('#notify_modal').modal('hide'); },<?php echo $timeout;?>);
</script>
		<?php } ?>
  <?php
}

function notify_big($title, $text, $url, $noclose = TRUE, $timeout=0)
{
	?>
  <script>
  $(document).ready(function(e) {
		$('#myModal').modal('show');
  });
	</script>
  <div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-vertical-centered">
		  <div class="modal-content">
			  <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel"><font size="+2"><strong><?php echo $title;?></strong></font></h4>
        </div>
        <div class="modal-body">
					<?php echo $text;?>
        </div>
				<?php if ($noclose)
				{
					?>
        <div class="modal-footer">
        <button id="close" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        <?php } ?>
      </div>
    </div>
  </div>
	<?php if (isset($url))
	{ ?>
<script>
/*	$('#close').click(function(e) {
		window.location='<?php echo $url;?>'; 
  }); */
	$('#myModal').on('hide.bs.modal', function (e) {
		window.location='<?php echo $url;?>'; 
	});
</script>
	<?php } ?>
	<?php if ((isset($timeout) && ($timeout > 0)))
	{
		?>
<script>
		window.setTimeout(function() { $('#myModal').modal('hide'); },<?php echo $timeout;?>);
</script>
		<?php } ?>
  <?php
}

function confirm($title, $text, $buttonClass,$modal_id, $form_id,$rand)
{
	?>
<div id="<?php echo $modal_id;?>" class="modal">
	<div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
            <button data-dismiss="modal" aria-hidden="true" class="close">×</button>
            <h4 class="modal-title" id="myModalLabel"><font size="+2"><strong><?php echo $title;?></strong></font></h4>
        </div>
        <div class="modal-body">
             <p><?php echo $text;?></p>
        </div>
        <div class="modal-footer">
        	<form method="post" id="modal_form<?php echo $form_id;?>">
        	<button id="btnYes<?php echo $form_id;?>" class="btn btn-danger">Ok</button>
          <button data-dismiss="modal" aria-hidden="true" class="btn btn-default" autofocus>Cancel</button>
					<input type="hidden" id="modal-input<?php echo $form_id;?>">
          <input type="hidden" id="rand" name="rand" value="<?php echo $rand;?>">
          </form>
        </div>
      </div>
    </div>
</div>
<script>
$('#btnYes<?php echo $form_id;?>').click(function() {
$('form#modal_form<?php echo $form_id;?>').submit();
$('#<?php echo $modal_id;?>').modal('hide');
});
</script>
<script>
$('.<?php echo $buttonClass;?>').click(function(e) {
	e.preventDefault();
	$('#<?php echo $modal_id;?>').removeData('bs.modal')
	$('#<?php echo $modal_id;?>').modal('show');
	var ID = $(this).data('id');
	var FORM = $(this).data('form');
	var BUTTON = $(this).data('button');
	var INPUTNAME = $(this).data('input-name');
	
	$("#btnYes<?php echo $form_id;?>").attr("class",$(this).data('class'));
	$("#btnYes<?php echo $form_id;?>").attr("name",BUTTON);
	$("#modal-input<?php echo $form_id;?>").attr("name","UserID");
	$("#modal-input<?php echo $form_id;?>").attr("name",INPUTNAME);
	$("#modal-input<?php echo $form_id;?>").val(ID);
});
</script>  
<?php
}

function FormSubmit($title, $text,$buttonClass,$modalID, $frm)
{
	?>
<div id="<?php echo $modalID;?>" class="modal fade">
	<div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
            <button data-dismiss="modal" aria-hidden="true" class="close">×</button>
            <h4 class="modal-title" id="myModalLabel"><font size="+2"><strong><?php echo $title;?></strong></font></h4>
        </div>
        <div class="modal-body">
             <p><?php echo $text;?></p>
        </div>
        <div class="modal-footer">
          <button id="btnYes<?php echo $modalID;?>" class="btn btn-danger">Yes</button>
          <button data-dismiss="modal" aria-hidden="true" class="btn btn-default" autofocus>No</button>
        </div>
      </div>
    </div>
</div>
<script>
$('button.<?php echo $buttonClass;?>').click(function(ev) {
	ev.preventDefault();
	$('#<?php echo $modalID;?>').removeData('bs.modal');
	$("#btnYes<?php echo $modalID;?>").attr("class",$(this).data('class'));
	$("#btnYes<?php echo $modalID;?>").attr("name",$(this).data('button'));
	});

$("#btnYes<?php echo $modalID;?>").click(function(e) {

	var btn_name = $(this).attr("name");
	var INPUT = '<input type="hidden" name="' + btn_name + '" />';
	$("#<?php echo $frm;?>").append(INPUT);
	$("#<?php echo $frm;?>").submit();

});
</script>
<?php
}

function no_permission($title, $text,$buttonClass,$modalID,$rand,$ID)
{
	?>
<div id="<?php echo $modalID;?>" class="modal fade">
	<div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
            <button data-dismiss="modal" aria-hidden="true" class="close">×</button>
            <h4 class="modal-title" id="myModalLabel"><font size="+2"><strong><?php echo $title;?></strong></font></h4>
        </div>
        <div class="modal-body">
             <p><?php echo $text;?></p>
        </div>
        <div class="modal-footer">
          <button data-dismiss="modal" aria-hidden="true" class="btn btn-default" autofocus>Close</button>
        </div>
      </div>
    </div>
</div>
<script>
$('button.<?php echo $buttonClass;?>').click(function(ev) {
	ev.preventDefault();
	$('#<?php echo $modalID;?>').removeData('bs.modal');

	});
</script>    
<?php
}

function url_confirm($title, $text,$buttonClass,$modalID,$ID)
{
	?>
<div id="<?php echo $modalID;?>" class="modal fade">
	<div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
            <button data-dismiss="modal" aria-hidden="true" class="close">×</button>
            <h4 class="modal-title" id="myModalLabel"><font size="+2"><strong><?php echo $title;?></strong></font></h4>
        </div>
        <div class="modal-body">
             <p><?php echo $text;?></p>
        </div>
        <div class="modal-footer">
        	<form method="post" id="FormSubmit<?php echo $ID;?>">
            <button id="btnYes<?php echo $ID;?>" class="btn btn-danger">Yes</button>
            <button data-dismiss="modal" aria-hidden="true" class="btn btn-default" autofocus>No</button>
          </form>
        </div>
      </div>
    </div>
</div>
<script>
$('.<?php echo $buttonClass;?>').click(function(ev) {
	ev.preventDefault();
	$('#<?php echo $modalID;?>').removeData('bs.modal');
	var ID = $(this).data('id');
	var INPUTNAME = $(this).data('input-name');
	var URL = $(this).data('url');
	$("#btnYes<?php echo $ID;?>").attr("class",$(this).data('class'));
	$("#FormSubmit<?php echo $ID;?>").attr("action",URL);
	});
$('#btnYes<?php echo $ID;?>').click(function(e) {
	$('#<?php echo $modalID;?>').modal('hide');  
});
</script>    
<?php
}

function confirm2($title, $text,$buttonClass,$modalID,$rand,$ID)
{
	?>
<div id="<?php echo $modalID;?>" class="modal fade">
	<div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
            <button data-dismiss="modal" aria-hidden="true" class="close">×</button>
            <h4 class="modal-title" id="myModalLabel"><font size="+2"><strong><?php echo $title;?></strong></font></h4>
        </div>
        <div class="modal-body">
             <p><?php echo $text;?></p>
        </div>
        <div class="modal-footer">
        	<form method="post" id="FormSubmit<?php echo $ID;?>">
            <button id="btnYes<?php echo $ID;?>" class="btn btn-danger">Yes</button>
            <button data-dismiss="modal" aria-hidden="true" class="btn btn-default" autofocus>No</button>
            <input type="hidden" name="rand" id="rand" value="<?php echo  $rand;?>">
            <input type="hidden" id="input<?php echo $ID;?>">
          </form>
        </div>
      </div>
    </div>
</div>
<script>
$('button.<?php echo $buttonClass;?>').click(function(ev) {
	ev.preventDefault();
	$('#<?php echo $modalID;?>').removeData('bs.modal');
	var ID = $(this).data('id');
	var INPUTNAME = $(this).data('input-name');
	var URL = $(this).data('url');

	$("#input<?php echo $ID;?>").attr("name",INPUTNAME);
	$("#input<?php echo $ID;?>").val(ID);

	$("#btnYes<?php echo $ID;?>").attr("class",$(this).data('class'));
	$("#btnYes<?php echo $ID;?>").attr("name",$(this).data('button'));
	$("#FormSubmit<?php echo $ID;?>").attr("action",URL);
	});
</script>    
<?php
}

function confirm3($title, $text,$buttonClass,$modalID,$rand,$ID)
{
	?>
<div id="<?php echo $modalID;?>" class="modal fade">
	<div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
            <button data-dismiss="modal" aria-hidden="true" class="close">×</button>
             <h4><?php echo $title;?></h4>
        </div>
        <div class="modal-body">
             <p><?php echo $text;?></p>
        </div>
        <div class="modal-footer">
        	<form method="post" id="FormSubmit<?php echo $ID;?>">
        	<button id="btnYes<?php echo $ID;?>" class="btn btn-danger">OK</button>
          <button data-dismiss="modal" aria-hidden="true" class="btn btn-default" autofocus>Cancel</button>
          <input type="hidden" name="rand" id="rand" value="<?php echo  $rand;?>">
					<input type="hidden" id="BoxInput<?php echo $ID;?>">
					<input type="hidden" id="BoxInput2<?php echo $ID;?>">
          </form>
        </div>
      </div>
    </div>
</div>
<script>
$('button.<?php echo $buttonClass;?>').click(function(ev) {
	ev.preventDefault();
	$('#<?php echo $modalID;?>').removeData('bs.modal');
	var ID = $(this).data('id');
	var INPUTNAME = $(this).data('input-name');
	var ID2 = $(this).data('id2');
	var INPUTNAME2 = $(this).data('input-name2');
	var URL = $(this).data('url');

	$("#BoxInput<?php echo $ID;?>").attr("name",INPUTNAME);
	$("#BoxInput<?php echo $ID;?>").val(ID);

	$("#BoxInput2<?php echo $ID;?>").attr("name",INPUTNAME2);
	$("#BoxInput2<?php echo $ID;?>").val(ID2);

	$("#btnYes<?php echo $ID;?>").attr("class",$(this).data('class'));
	$("#btnYes<?php echo $ID;?>").attr("name",$(this).data('button'));
	$("#FormSubmit<?php echo $ID;?>").attr("action",URL);
	});
</script>

<?php
}

function confirm4($title, $text,$buttonClass,$modalID,$rand,$ID)
{
	?>
<div id="<?php echo $modalID;?>" class="modal fade">
	<div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
            <button data-dismiss="modal" aria-hidden="true" class="close">×</button>
             <h4><?php echo $title;?></h4>
        </div>
        <div class="modal-body">
             <p><?php echo $text;?></p>
        </div>
        <div class="modal-footer">
        	<form method="post" id="FormSubmit<?php echo $ID;?>">
        	<button id="btnYes<?php echo $ID;?>" class="btn btn-danger">OK</button>
          <button data-dismiss="modal" aria-hidden="true" class="btn btn-default" autofocus>Cancel</button>
          <input type="hidden" name="rand" id="rand" value="<?php echo  $rand;?>">
					<input type="hidden" id="BoxInput<?php echo $ID;?>">
					<input type="hidden" id="BoxInput2<?php echo $ID;?>">
  				<input type="hidden" id="BoxInput3<?php echo $ID;?>">
          </form>
        </div>
      </div>
    </div>
</div>
<script>
$('button.<?php echo $buttonClass;?>').click(function(ev) {
	ev.preventDefault();
	$('#<?php echo $modalID;?>').removeData('bs.modal');
	var ID = $(this).data('id');
	var INPUTNAME = $(this).data('input-name');
	var ID2 = $(this).data('id2');
	var INPUTNAME2 = $(this).data('input-name2');
	var ID3 = $(this).data('id3');
	var INPUTNAME3 = $(this).data('input-name3');
	var URL = $(this).data('url');

	$("#BoxInput<?php echo $ID;?>").attr("name",INPUTNAME);
	$("#BoxInput<?php echo $ID;?>").val(ID);

	$("#BoxInput2<?php echo $ID;?>").attr("name",INPUTNAME2);
	$("#BoxInput2<?php echo $ID;?>").val(ID2);

	$("#BoxInput3<?php echo $ID;?>").attr("name",INPUTNAME3);
	$("#BoxInput3<?php echo $ID;?>").val(ID3);

	$("#btnYes<?php echo $ID;?>").attr("class",$(this).data('class'));
	$("#btnYes<?php echo $ID;?>").attr("name",$(this).data('button'));
	$("#FormSubmit<?php echo $ID;?>").attr("action",URL);
	});
</script>

<?php
}


function RandomString($length=6)
{

	srand ((double) microtime() * 1000000);
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $charactersS = '!@#$%&*=+';

    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length -1; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    $randomS = $charactersS[rand(0,strlen($charactersS) -1)];

    return str_shuffle($randomString . $randomS);

/*
	$_rand_src = array(
		array(48,57) //digits
		, 
		array(97,122) //lowercase chars
		, array(64,90) //@ uppercase chars
	);
	srand ((double) microtime() * 1000000);
	$random_string = "";
	for($i=0;$i<$length;$i++){
		$i1=rand(0,sizeof($_rand_src)-1);
		$random_string .= chr(rand($_rand_src[$i1][0],$_rand_src[$i1][1]));
	}
  $random_string = str_suffle($random_string."!#$%&*+=");
	return $random_string;
  */
}

function RandomValue($length=6)
{
	$_rand_src = array(
		array(48,57) //digits
		, array(97,122) //lowercase chars
		, array(65,90) //uppercase chars
	);
	srand ((double) microtime() * 1000000);
	$random_string = "";
	for($i=0;$i<$length;$i++){
		$i1=rand(0,sizeof($_rand_src)-1);
		$random_string .= chr(rand($_rand_src[$i1][0],$_rand_src[$i1][1]));
	}
	return $random_string;
}

function spamcheck($field)
{
	if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$field))
	{
		return FALSE;
	}
	else
	{
			return TRUE;
	}
}

function LogGenerateArray($LogTableName,$field, $value,$db,$fields) 
//LogTable, Where Field, Where Value, $DB, Fields for Fetch * for all
{
		$strLog = "SELECT $fields FROM `$LogTableName` WHERE `$field` = '$value';";
		$queryL = $db->query($strLog);
		$row = $db->fetch_array_assoc($queryL);
		return $row;
		$db->free($queryL);
}


function LogGenerate($LogTableName,$field, $value,$db,$fields) 
//LogTable, Where Field, Where Value, $DB, Fields for Fetch * for all
{
		$strLog = "SELECT $fields FROM `$LogTableName` WHERE `$field` = '$value';";
		$queryL = $db->query($strLog);
		$row = $db->fetch_array_assoc($queryL);
		return $row;
		$db->free($queryL);
}

function AddLog($LogAction, $LogTable, $OldValue, $NewValue, $db)
{
	if (is_array($OldValue))
	{
			foreach($OldValue as $key => $value) {
				$i++;
					$OldTxt .= "$key=>$value";
					if ($i < count($OldValue)) 
					{
						$OldTxt .= "{br} \n";
					}
					else "\n";
			}
	}
	if (is_array($NewValue))
	{
			foreach($NewValue as $key => $value) {
				$j++;
					$NewTxt .= "$key=>$value";
					if ($j < count($NewValue)) 
					{
						$NewTxt .= "{br} \n";
					}
					else "\n";
			}
	}

	if (strlen($NewTxt) == 0) $NewTxt = NULL;
	if (strlen($OldTxt) == 0) $OldTxt = NULL;
	
		if ($NewTxt <> $OldTxt)
		{
			$LoginUser = $_SESSION['user']['username'];
	
			$rowLog = $db->fetch_array_assoc($db->query("select `ID` from `portal_activity_log` ORDER BY ID DESC LIMIT 0,1"));
			$LogID = $rowLog['ID'] +1;

			$strLog = "INSERT INTO `portal_activity_log` (ID, ActionDate, LogAction, LogTable, OldValue, CurrentValue,RemoteIP,ActionUser)
			VALUES ('$LogID', NOW(), '$LogAction', '$LogTable', '$OldTxt', '$NewTxt','" . $_SERVER['REMOTE_ADDR'] . "', '$LoginUser')
			";
			$db->query($strLog);
		}
}

function Old_LogGenerate($LogTableName,$field, $value,$db,$fields) //LogTable, Where Field, Where Value, $DB, Fields for Fetch * for all
	{
		$strLog = "SELECT $fields FROM `$LogTableName` WHERE `$field` = '$value';";
		$queryLog = $db->query($strLog);
		$no_of_rows = $db->num_rows($queryLog);
		$fields = $db->fetch_fields($queryLog);
		if ($no_of_rows > 0)
		{
			while ($row = $db->fetch_array($queryLog))
			{
				$j=0;
				$field=array();
				foreach ($fields as $val)
				{
					$field[] = $val->name;
					$j++;
				}
				for ($i=0; $i < $j; $i++)
				{
					$txtLog .=  "[".$field[$i]. "] => " .addslashes($row[$i])  ;
					if ($i <= count($j)) $txtLog .= ", \n";
					else $txtLog .= "\n";
				}
			}
			return $txtLog;
		}
	}

	function getPrimary($table,$db)
	{
			$query = $db->query("SHOW INDEX FROM $table WHERE `Key_name` = 'PRIMARY'");
			$row = $db->fetch_object($query);
			return $row->Column_name;
	}
        
        /*
         * AddLog Required 6 parameter
         * LogAction -> Add/Edit/Update
         * LogTable -> Table Name
         * OldValue
         * NewValue
         * $db -> Database
         * UniqueField -> Unique ID of Table
         */
	function Old_AddLog($LogAction, $LogTable, $OldValue, $NewValue, $db)
	{
		$LoginUser = $_SESSION['user']['username'];

		$rowLog = $db->fetch_array_assoc($db->query("select `ID` from `portal_activity_log` ORDER BY ID DESC LIMIT 0,1"));
		$LogID = $rowLog['ID'] +1;

	
		$strLog = "INSERT INTO `portal_activity_log` (ID, ActionDate, LogAction, LogTable, OldValue, CurrentValue,RemoteIP,ActionUser)
		VALUES ('$LogID', NOW(), '$LogAction', '$LogTable', '$OldValue','$NewValue','" . $_SERVER['REMOTE_ADDR'] . "', '$LoginUser')
		";
		$db->query($strLog);
	}

	function ShowError($heading, $txtError)
	{
?>
                            <div class="alert alert-danger alert-dismissable" id="MyAlert">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4 class="panel-title"><?php echo $heading;?></h4>
                                    <?php echo $txtError;?>
                            </div>
<script>
	
	window.setTimeout(function() {
	$("#MyAlert").fadeTo(500, 0).slideUp(500, function(){
	$(this).remove(); 
	});
	}, 10000);

</script>
<?php		
	}


/*
parameter (Software Name, Database Connection $db)

*/
function checkPermission($software,$db)
{
		$strPerm = "
		SELECT mailbox.username,
				 portal_permission.Software,
				 portal_permission.Permission,
				 portal_user_section.SectionID
		FROM (portal_user_section
					INNER JOIN portal_permission
						 ON (portal_user_section.SectionID = portal_permission.SectionID))
				 INNER JOIN mailbox ON (mailbox.ID = portal_user_section.UserID)
		WHERE mailbox.ID = '" . $_SESSION['user']['ID'] . "' AND portal_permission.Software = '$software' AND portal_permission.Permission='1' 
				 ";
		$query= $db->query($strPerm);
		$no_of_rows = $db->num_rows($query);
		$db->free($query);
		if ($no_of_rows ==1) return true;
		else return false;

}
function create_session($db)
{
	$session_id = session_id();
	$username = $_SESSION['user']['username'];
	$browser = $_SERVER['HTTP_USER_AGENT'];
	$remote_ip = $_SERVER['REMOTE_ADDR'];
	$strINSERT = "INSERT INTO `exam_session` (`session_id`, `username`, `browser`,`remote_ip`) VALUES ('$session_id','$username','$browser','$remote_ip')";
	$db->query($strINSERT);
}
function check_session($db)
{
	$session_id = session_id();
	$username = $_SESSION['user']['username'];
	$browser = $_SERVER['HTTP_USER_AGENT'];
	$remote_ip = $_SERVER['REMOTE_ADDR'];
	$strSELECT = "SELECT * FROM `exam_session` WHERE `session_id` = '$session_id' AND  `username` = '$username' AND `browser` = '$browser' AND `remote_ip` = '$remote_ip'";
	$query = $db->query($strSELECT);
	$session_no = $db->num_rows($query);
	if ($session_no > 0)
		return true;
	else
		return false;
	$db->free($query);
}
function update_session($db)
{
	$session_id = session_id();
	$username = $_SESSION['user']['username'];
	$browser = $_SERVER['HTTP_USER_AGENT'];
	$remote_ip = $_SERVER['REMOTE_ADDR'];
	$now = time();
	$session_time = $_SESSION['user']['session_time'] ;	

	$expire_time = $now - ($session_time * 2) ;
 if (check_session($db))
 {
		$strUPDATE = "UPDATE `exam_session` SET `last_activity` = '$now' 
			WHERE `session_id` = '$session_id' 
				AND  `username` = '$username' 
				AND `browser` = '$browser' 
				AND `remote_ip` = '$remote_ip'";
		$db->query($strUPDATE);
		
		$strDELETE = "DELETE FROM `exam_session` WHERE last_activity < $expire_time";
		$db->query($strDELETE);
 }
}
function destroy_session($db)
{
	$session_id = session_id();
	$username = $_SESSION['user']['username'];
	$browser = $_SERVER['HTTP_USER_AGENT'];
	$remote_ip = $_SERVER['REMOTE_ADDR'];
	$strDELETE = "DELETE FROM `exam_session` WHERE `session_id` = '$session_id' AND  `username` = '$username' AND `browser` = '$browser' AND `remote_ip` = '$remote_ip'";

	$db->query($strDELETE);
}

function UpdateEndTime($db)
{
	$str = "
SELECT 
			 exam_exam_status.exam_id,
			 exam_exam_status.center_id,
       exam_exam_status.start_time,
       exam_exam_status.end_time,
       exam_exam_status.full_close,
       exam_exam_status.grace_time,
       exam_exam_status.grace_reason,
       exam_exam_status.final_submit_time,
       DATE_ADD(exam_exam_status.start_time, INTERVAL exam_exam_type.total_time + exam_exam_status.grace_time MINUTE) as ENDTIME,
       exam_exam_type.total_time
FROM (exam_exam exam_exam
        INNER JOIN exam_exam_type exam_exam_type
           ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id))
       INNER JOIN exam_exam_status exam_exam_status
          ON (exam_exam_status.exam_id = exam_exam.exam_id)

	WHERE exam_exam_status.start_time IS NOT NULL AND exam_exam_status.end_time IS NULL;";

	$query = $db->query($str);
	while ($row = $db->fetch_object($query))
	{
		if ($row->end_time == NULL AND $row->start_time != NULL)
			$isStarted = TRUE;
	
		if ($isStarted)
		{	
			$strStart = strtotime($row->start_time);
			$startTime = $row->start_time;
			$Total_Time = $row->total_time + $row->grace_time;
			$strEnd = strtotime("+$Total_Time minutes",$strStart);

			if ($strEnd < time())
			{
				$endTime = date("Y-m-d H:i:s",$strEnd);
				$strUpdate = "UPDATE exam_exam_status SET end_time = '$endTime' WHERE exam_id = '$row->exam_id' AND center_id = '$row->center_id';";
				$db->begin();
				$queryUpdate = $db->query($strUpdate);
									
						$select_student = "SELECT student_id FROM exam_exam_student WHERE exam_id = '$row->exam_id' AND center_id = '$row->center_id'";
						$query_student = $db->query($select_student);
						while ($row_student = $db->fetch_object($query_student))
						{
							
							//No of correct Answer by student
							$strCorrect = "
							SELECT * FROM exam_student_answer
							WHERE exam_student_answer.exam_id = '$row->exam_id' 
							AND exam_student_answer.center_id = '$row->center_id' 
							AND exam_student_answer.student_id = '$row_student->student_id' 
							AND exam_student_answer.choosen_answer = exam_student_answer.correct_answer; ";
	
							$query_correct = $db->query($strCorrect);
							$No_of_Correct_Question = $db->num_rows($query_correct);
							$db->free($query_correct);
	
							// no of max question
							$strTotalQuestion = "
	SELECT exam_exam_type.total_question, exam_exam_type.full_mark, exam_exam_type.mcq_mark
		FROM exam_exam exam_exam
				 INNER JOIN exam_exam_type exam_exam_type
						ON (exam_exam.exam_type_id = exam_exam_type.exam_type_id)
		WHERE exam_exam.exam_id = '$row->exam_id';					
							 ";
							 $query_total_question = $db->query($strTotalQuestion);
							 $row_total = $db->fetch_object($query_total_question);
							 $total_question = $row_total->total_question;
							 $full_mark= $row_total->full_mark;
							 $mcq_mark = $row_total->mcq_mark;
	
							$mcq_number = ($mcq_mark / $total_question) * $No_of_Correct_Question;
							 
							 $db->free($query_total_question);
							 unset($query_total_question, $row_total,$strTotalQuestion);
	
							$select_student_exam = "SELECT student_id FROM exam_student_exam WHERE exam_id = '$row->exam_id' AND center_id = '$row->center_id' AND student_id = '$row_student->student_id'";
							$query_student_exam = $db->query($select_student_exam);
							$no_of_student = $db->num_rows($query_student_exam);
	
							if ($no_of_student > 0) $studentExist = TRUE;
							else $studentExist = FALSE;
	
							$db->free($query_student_exam);
							unset($no_of_student, $query_student_exam, $select_student_exam);
	
	
							if ($studentExist)		// Student Exists
								$strInsert = "UPDATE exam_student_exam SET max_question = '$total_question', mcq_mark = '$mcq_number', is_closed='Y' 
								WHERE exam_id = '$row->exam_id' AND center_id = '$row->center_id' AND student_id = '$row_student->student_id'
								;";
							else							
								$strInsert = "INSERT INTO exam_student_exam (student_id, exam_id, center_id, max_question, mcq_mark, is_closed) VALUES ('$row_student->student_id', '$row->exam_id', '$row->center_id', $total_question, $mcq_number, 'Y'); ";
	
							$query_exam_student_exam = $db->query($strInsert);
						} // While
						if ($query_exam_student_exam) $success = TRUE;
						else $success = FALSE;
						
						if ($queryUpdate  && $success)
						{
							$db->commit();
							$success = TRUE;
						}
						else
						{
							$success = FALSE;
							$db->rollback();
						}
				}
		}
		return (isset($queryUpdate));
	}
}

function ExcelUpload($file)
{
	require_once 'reader.php';
	$data = new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('UTF-8');
	$data->read($file);
	for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++)
	{
		for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++)
		{
			$abc[$i][$j] = $data->sheets[0]['cells'][$i][$j];
		}
	}
	return $abc;
}
function wysiwyg_script($MAIN_URL)
{
?>	
	<script type="text/javascript" src="<?php echo $MAIN_URL;?>bootstrap/addon/wysiwyg/summernote.min.js"></script>
  <link rel="stylesheet" type="text/css" href="<?php echo $MAIN_URL;?>bootstrap/addon/wysiwyg/summernote.css">
<?php
}
function wysiwyg($txtAreaName, $txtValue = NULL, $class)
{
?>
  <textarea name="<?php echo $txtAreaName;?>" class="<?php echo $class;?>"><?php echo $txtValue;?></textarea>
<?php
}
function wysiwyg_ajax($MAIN_URL, $height,$class)
{
?>	
<script>
$('.<?php echo $class;?>').summernote({
		height: <?php echo $height;?>,
		toolbar: [
			['style', ['bold', 'italic', 'underline', 'clear']],
			['color', ['color']],
			['para', ['ul', 'ol', 'paragraph']],
			['table', ['table']],
			['insert', ['link', 'picture']],
			['view', ['fullscreen','codeview']],
		],				
		onImageUpload : function( files ) { 
		 // get current editable container 
			 var $note = $(this);  
				data = new FormData();
				data.append("file", files[0]);
				data.append("task", "image_upload");
				$.ajax({
						data: data,
						type: "POST",
						 url: "<?php echo $MAIN_URL;?>ajax.html",
						cache: false,
						contentType: false,
						processData: false,
						success: function(imageFile) {
							$note.summernote('insertImage', imageFile);
						}
				});
			}
	});

  </script>
<?php
}

function TextEditorRemove($text)
{
	
	$text = str_replace("<p><br></p>","",$text);
	$text = str_replace("<br><p></p>","",$text);
	$text = str_replace("<p><br>","<p>",$text);
	$text = str_replace("<br></p>","</p>",$text);
	if (substr($text,-4) == "<br>")
		$text = substr($text,0,-4);
	return $text;
}
?>
<?php
function CheckExam($exam_id, $student_id, $center_id, $db, $MAIN_URL, $software)
{

/*	$strExamCheck = "

SELECT exam_exam_status.exam_id,
       exam_exam_status.center_id,
       exam_exam_status.start_time,
       exam_exam_status.end_time,
       exam_exam_status.full_close,
       exam_exam.exam_code,
       exam_student_exam.is_closed,
       exam_exam_student.student_id
  FROM ((exam_exam_status exam_exam_status
         INNER JOIN exam_exam exam_exam
            ON (exam_exam_status.exam_id = exam_exam.exam_id))
        INNER JOIN exam_exam_student exam_exam_student
           ON     (exam_exam_status.exam_id = exam_exam_student.exam_id)
              AND (exam_exam_status.center_id = exam_exam_student.center_id))
       LEFT OUTER JOIN exam_student_exam exam_student_exam
          ON     (exam_exam_status.exam_id = exam_student_exam.exam_id)
             AND (exam_exam_status.center_id = exam_student_exam.center_id)

		WHERE exam_exam_student.student_id = '$student_id' AND exam_exam_status.exam_id = '$exam_id'
	AND exam_exam_status.center_id = '$center_id'
	AND exam_student_exam.student_id = '$student_id' 
	";

	$querycheck = $db->query($strExamCheck);
	$no_of_exam = $db->num_rows($querycheck);

	if ($no_of_exam > 0)
	{
		$row_exam = $db->fetch_object($querycheck);
		if ($row_exam->start_time != NULL)
		{
			$db_start_time = strtotime($row_exam->start_time);
			if ($db_start_time < time()) $isStarted = TRUE;
		}
		
		if ($row_exam->end_time != NULL)
		{
			$db_end_time = strtotime($row_exam->end_time);
			if ($db_end_time < time()) $isEnd = TRUE;
		}
		
		if ($row_exam->full_close == 'Y')
			$isClosed = TRUE;
		
		if ($isClosed == FALSE)
		{
			if ($isStarted)
			{
				if ($isEnd == FALSE) 
					$StartedNotFinished = TRUE;
				else 
					$Finished = TRUE;
			}
		}
	
		if ($row_exam->is_closed == 'Y')
		{
			notify("Exam List","Assigned Exam has been fully closed.<br><br>Please contact to Administration.",$MAIN_URL."start.html",TRUE,10000);
			include_once "footer.inc.php";
			exit();
		}
	}
*/

		$strExamCheck = "
	
	SELECT exam_exam_status.exam_id,
				 exam_exam_status.center_id,
				 exam_exam_status.start_time,
				 exam_exam_status.end_time,
				 exam_exam_status.full_close,
				 exam_exam.exam_code,
				 exam_exam_student.student_id
		FROM (exam_exam_status exam_exam_status
					INNER JOIN exam_exam_student exam_exam_student
						 ON     (exam_exam_status.exam_id = exam_exam_student.exam_id)
								AND (exam_exam_status.center_id = exam_exam_student.center_id))
				 INNER JOIN exam_exam exam_exam
						ON (exam_exam_status.exam_id = exam_exam.exam_id)
						
			WHERE exam_exam_student.student_id = '$student_id' AND exam_exam_status.exam_id = '$exam_id'
		AND exam_exam_status.center_id = '$center_id'
		";
	
	$querycheck = $db->query($strExamCheck);
	$no_of_exam = $db->num_rows($querycheck);
	
	$strFullClose = "SELECT * from exam_student_exam WHERE exam_id = '$exam_id' AND center_id = '$center_id' AND student_id = '$student_id';";
	$query_full_close = $db->query($strFullClose);
	$no_of_full_close = $db->num_rows($query_full_close);
	
	if ($no_of_full_close > 0)
	{
		$row_full = $db->fetch_object($query_full_close);
		if ($row_full->is_closed=='Y') $StudentExamFullClose = TRUE;
		else $StudentExamFullClose = FALSE;
	}
		else $StudentExamFullClose = FALSE;
	
	if ($StudentExamFullClose )
	{
			notify("Exam List","Assigned Exam has been fully closed.<br><br>Please contact to Administration.",$MAIN_URL."start.html",TRUE,10000);
			include_once "footer.inc.php";
			exit();
	}
	
	if ($no_of_exam > 0)
	{
		$row_exam = $db->fetch_object($querycheck);
		if ($row_exam->start_time != NULL)
		{
			$db_start_time = strtotime($row_exam->start_time);
			if ($db_start_time < time()) $isStarted = TRUE;
		}
		
		if ($row_exam->end_time != NULL)
		{
			$db_end_time = strtotime($row_exam->end_time);
			if ($db_end_time < time()) $isEnd = TRUE;
		}
		
		if ($row_exam->full_close == 'Y')
			$isClosed = TRUE;
	
		if ($isClosed == FALSE)
		{
			if ($isStarted)
			{
				if ($isEnd == FALSE) 
					$StartedNotFinished = TRUE;
				else 
					$Finished = TRUE;
			}
		}
	
		if ($row_exam->is_closed == 'Y')
		{
			notify("Exam List","Assigned Exam has been fully closed.<br><br>Please contact to Administration.",$MAIN_URL."start.html",TRUE,10000);
			include_once "footer.inc.php";
			exit();
		}
	}

	else
	{
	?>
			<div class="alert alert-danger alert-dismissible" role="alert">
				No Exam has been assigned for you. <br>Please contract to Administration.
			</div>
	<?php
			notify($software,"<b><font color=red>No exam has been assigned to you.</font></b><br><font color=blue>Please login later. <br> OR <br>contact to Administration.</font>",$MAIN_URL ."logout.html", TRUE,10000);
			include_once "footer.inc.php";
			exit();
	}
	?>
	<?php
	if ($Finished)
	{
	?>
			<div class="alert alert-danger alert-dismissible" role="alert">
				Assigned Exam has already been Closed. <br>Please contract to Administration.
			</div>
	<?php
			notify($software,"<b><font color=red>Assigned Exam has already been Closed.</font></b><br><font color=blue>Please login later. <br> OR <br>contact to Administration.</font>",$MAIN_URL ."logout.html", TRUE,10000);
			include_once "footer.inc.php";
			exit();
	}

}

function PermissionArray()
{
	$perm[1]['text'] = "View, Add and Edit Question.";
	$perm[1]['value'] = 1;
	
	$perm[2]['text'] = "View, Add and Edit Exam Type.";
	$perm[2]['value'] = 2;
	
	$perm[3]['text'] = "View, Add and Edit Exam Center.";
	$perm[3]['value'] = 4;
	
	$perm[4]['text'] = "View, Add and Edit Exam.";
	$perm[4]['value'] = 8;
	
	$perm[5]['text'] = "View, Add and Edit Student.";
	$perm[5]['value'] = 16;
	
	$perm[6]['text'] = "View, Add and Edit Users.";
	$perm[6]['value'] = 32;

return $perm;
}
function ArrayCaption()
{
	$perm['exam_type']='Exam Type';
	$perm['question']='Question';
	$perm['exam_center']='Exam Center';
	$perm['exam']='Exam';
	$perm['student']='Student';
	$perm['user']='User';
	$perm['db']='Database';
	
	return $perm;	
}
function ArrayPermission()
{

  $perm['exam_type'][1]['text']= 'View';
  $perm['exam_type'][2]['text']= 'Change';
  $perm['exam_type'][3]['text']= 'Change Question Per Chapter';
  $perm['question'][4]['text']= 'View';
  $perm['question'][5]['text']= 'Change';
  $perm['exam_center'][6]['text']= 'View';
  $perm['exam_center'][7]['text']= 'Change';
  $perm['exam'][8]['text']= 'View';
  $perm['exam'][9]['text']= 'Start Exam';
  $perm['exam'][10]['text']= 'View Student Credentials';
  $perm['exam'][11]['text']= 'Change';
  $perm['exam'][12]['text']= 'Question Selection';
  $perm['exam'][13]['text']= 'Student Selection';
  $perm['exam'][14]['text']= 'Schedule Exam';
  $perm['exam'][15]['text']= 'Time Adjustment';
  $perm['exam'][16]['text']= 'Result of Student';
  $perm['student'][17]['text']= 'View';
  $perm['student'][18]['text']= 'Change';
  $perm['user'][19]['text']= 'View';
  $perm['user'][20]['text']= 'Change';
  $perm['user'][21]['text']= 'Reset Password';
  $perm['user'][22]['text']= 'User Permission';
  $perm['db'][23]['text']= 'Database Backup';

  $perm['exam_type'][1]['value']= 1;
  $perm['exam_type'][2]['value']= 2;
  $perm['exam_type'][3]['value']= 4;
  $perm['question'][4]['value']= 8;
  $perm['question'][5]['value']= 16;
  $perm['exam_center'][6]['value']= 32;
  $perm['exam_center'][7]['value']= 64;
  $perm['exam'][8]['value']= 128;
  $perm['exam'][9]['value']= 256;
  $perm['exam'][10]['value']= 512;
  $perm['exam'][11]['value']= 1024;
  $perm['exam'][12]['value']= 2048;
  $perm['exam'][13]['value']= 4096;
  $perm['exam'][14]['value']= 8192;
  $perm['exam'][15]['value']= 16384;
  $perm['exam'][16]['value']= 32768;
  $perm['student'][17]['value']= 65536;
  $perm['student'][18]['value']= 131072;
  $perm['user'][19]['value']= 262144;
  $perm['user'][20]['value']= 524288;
  $perm['user'][21]['value']= 1048576;
  $perm['user'][22]['value']= 2097152;
  $perm['db'][23]['value']= 4194304;

	return $perm;

}
?>
<?php
function CountDownRemainingTime($TotalSeconds,$DisplayIDofDiv, $ID, $RedirectPageAfterTimeOut = NULL) // yyyy-mm-dd hh:mm:ss format and Require jquery 1.11 or above
{
	if ($TotalSeconds > 0)
	{
	?>
	<script type="text/jscript">
  function elapsedTime_<?php echo $ID;?>() {
    var startTime<?php echo $ID;?> = new Date();
    var timeDiff<?php echo $ID;?> = endTime<?php echo $ID;?> - startTime<?php echo $ID;?>;

    if (timeDiff<?php echo $ID;?> >=0)
    {	
      // strip the miliseconds
      timeDiff<?php echo $ID;?> /= 1000;
      // get seconds
      var seconds<?php echo $ID;?> = Math.round(timeDiff<?php echo $ID;?> % 60);
      // remove seconds from the date
      timeDiff<?php echo $ID;?> = Math.floor(timeDiff<?php echo $ID;?> / 60);
      // get minutes
      var minutes<?php echo $ID;?> = Math.round(timeDiff<?php echo $ID;?> % 60);
      // remove minutes from the date
      timeDiff<?php echo $ID;?> = Math.floor(timeDiff<?php echo $ID;?> / 60);
      // get hours
      var hours<?php echo $ID;?> = Math.round(timeDiff<?php echo $ID;?> % 24);
      // remove hours from the date
      timeDiff<?php echo $ID;?> = Math.floor(timeDiff<?php echo $ID;?> / 24);
      // the rest of timeDiff is number of days
      var days<?php echo $ID;?> = timeDiff<?php echo $ID;?>;
      if ( hours<?php echo $ID;?> < 10) hours<?php echo $ID;?> = "0" + hours<?php echo $ID;?>;
      if ( minutes<?php echo $ID;?> < 10) minutes<?php echo $ID;?> = "0" + minutes<?php echo $ID;?>;
      if ( seconds<?php echo $ID;?> < 10) seconds<?php echo $ID;?>= "0" + seconds<?php echo $ID;?>;
    
      if (days<?php echo $ID;?> < 1)
        $("#<?php echo $DisplayIDofDiv;?>").text(hours<?php echo $ID;?> + ":" + minutes<?php echo $ID;?> + ":" + seconds<?php echo $ID;?>);
      else
        $("#<?php echo $DisplayIDofDiv;?>").text(days<?php echo $ID;?> + " days, " + hours<?php echo $ID;?> + ":" + minutes<?php echo $ID;?> + ":" + seconds<?php echo $ID;?>);
      setTimeout(elapsedTime_<?php echo $ID;?>, 1000);
    }
  
  <?php
      if ($RedirectPageAfterTimeOut != NULL)
      {
        ?>
    else
    {
        window.location='<?php echo $RedirectPageAfterTimeOut;?>';
    }
  <?php	}	?>
  }
      // when the document is loaded, make an ajax call to load the timestamp
  
      $(document).ready(function(e) {
        endTime<?php echo $ID;?> = new Date();
        endTime<?php echo $ID;?>.setSeconds(endTime<?php echo $ID;?>.getSeconds() + (<?php echo $TotalSeconds;?>) );
        elapsedTime_<?php echo $ID;?>();
      });
  </script>
  <?php
  }
	else
	{
		echo "<script>window.location='".$RedirectPageAfterTimeOut . "';</script>";
	}
}
?>
<?php
function CurrentDateTime($ID)
{
	?>
<script>
	function date_time_<?php echo $ID;?>()
	{
		var date = new Date() ;
		date.setTime(date.getTime() -(Diff) );

		year = date.getFullYear();
		month = date.getMonth();
		months = new Array('Jan.', 'Feb.', 'Mar.', 'Apr.', 'May', 'Jun.', 'Jul.', 'Aug.', 'Sep.', 'Oct.', 'Nov.', 'Dec.');
		d = date.getDate();
		day = date.getDay();
		h = date.getHours();
		if(h<10)
		{
						h = "0"+h;
		}
		m = date.getMinutes();
		if(m<10)
		{
						m = "0"+m;
		}
		s = date.getSeconds();
		if(s<10)
		{
						s = "0"+s;
		}
		 result = ''+year+' '+months[month]+' '+d+', '+h+':'+m+':'+s;
		$("#<?php echo $ID;?>").html(result);
		setTimeout(date_time_<?php echo $ID;?>,1000);

	}
	$(document).ready(function(e) {
		var t = "<?php echo date("Y-m-d H:i:s");?>".split(/[- :]/);
		ServerDate = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
		CurrTime = new Date();
		Diff = CurrTime - ServerDate;
		date_time_<?php echo $ID;?>()
	});

</script>
<?php
}
?>
<?php
function wysiwyg_ajax_answer($MAIN_URL, $height,$class)
{
?>	
<script>
$('.<?php echo $class;?>').summernote({
		height: <?php echo $height;?>,
		toolbar: [
			['style', ['bold', 'italic', 'underline', 'clear']],
			['color', ['color']],
			['para', ['ul', 'ol', 'paragraph']],
			['table', ['table']],
			['insert', ['link', 'picture']],
			['view', ['fullscreen','codeview']],
		],				
		onImageUpload : function( files ) { 
		 // get current editable container 
			 var $note = $(this);  
				data = new FormData();
				data.append("file", files[0]);
				data.append("task", "image_upload");
				$.ajax({
						data: data,
						type: "POST",
						 url: "<?php echo $MAIN_URL;?>ajax.html",
						cache: false,
						contentType: false,
						processData: false,
						success: function(imageFile) {
							$note.summernote('insertImage', imageFile);
						}
				});
			}
	});

$('.<?php echo $class;?>').on('summernote.change', function(we, contents, $editable) {
	var $a = $(this).data("id");
	$.ajax({
			data: {"id": $a, "question-answer": contents,"task": "question-answer-add" },
			type: "POST",
			 url: "<?php echo $MAIN_URL;?>ajax.html"
	});
});
  </script>
<?php
}

function wysiwyg_answer($txtAreaName, $txtValue = NULL, $class, $id)
{
?>
  <textarea data-id="<?php echo $id;?>" name="<?php echo $txtAreaName;?>" class="<?php echo $class;?>"><?php echo $txtValue;?></textarea>
<?php
}

?>