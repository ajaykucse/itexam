<?php
session_start();

define ("WEB-PROGRAM","Online Exam");
define ("CODER","Sunil Kumar K. C.");
if (file_exists("security.php")) include_once "security.php";

date_default_timezone_set("Asia/Katmandu"); 
set_time_limit(600);
	require_once "main_url.inc.php";
	$URL = main_url();
	$MAIN_URL = $URL;

if (!$_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER'])
	echo "<script>window.location='" .$URL."login.html';</script>";  

if ($_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER'])
{


	$files =  getDirSQLFile("/upload");
	$dir = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF']) .'/upload/';

	foreach ($files as $FILES)
	{
		unlink ($dir.$FILES);
	}

	$file_time = date("YmdHis");

	$file = $dir.'backup_'.$file_time.'.sql';

	require_once "includes/functions.inc.php";

	$config = new config();
	$dbname = $config->dbname;
	$dbuser = $config->dbuser;
	$dbhost = $config->dbhost;
	$dbpass = $config->dbpass;
	unset($config);

	include_once "includes/db.inc.php";
	$db = new Database($dbhost,$dbuser,$dbpass,$dbname) ;

	//Permission Check
	$strPerm = "SELECT * FROM `exam_user_permission` WHERE `user_id` = '". $_SESSION['user']['user_id'] ."';";
	$query_perm = $db->query($strPerm);
	$no_of_perm = $db->num_rows($query_perm);
	$row_perm = $db->fetch_object($query_perm);
	$permission = $row_perm->permission;
	$db->free($query_perm);
	unset($strPerm, $query_perm, $row_perm);

	if ($permission & 4194304) $db_backup = TRUE;

	if (!$db_backup)
	{
		?>
  <link href="<?php echo $MAIN_URL;?>bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!--[if lt IE 9]>
  <script src="<?php echo $MAIN_URL;?>bootstrap/js/html5shiv.js"></script>
  <script src="<?php echo $MAIN_URL;?>bootstrap/js/respond.min.js"></script>
  <![endif]-->
  <script src="<?php echo $MAIN_URL;?>bootstrap/js/jquery-1.11.3.min.js"></script>
  <script src="<?php echo $MAIN_URL;?>bootstrap/js/bootstrap.min.js"></script>
  <style>	.modal-sm {     width: 400px;		}	</style>
    <?php
		notify("User Permission","<br><font color=red>You don't have permission to view this page.</font><br>&nbsp;",$URL."mainuser/dashboard.html",TRUE,5000);
		include_once "footer.inc.php";
		exit();
	}
	?>
	<?php

	mysqli_backup($dbhost,$dbname, $dbuser, $dbpass, $file);

}
else
{
	echo "<script>window.location='" .$URL."login.html';</script>";  
}

?>
<?php
	function mysqli_backup($server,$database,$user,$password, $file) //Database backup system written by Sunil Kumar K. C.
	{
		/* Connecting to mySQL Server */
		$db = mysqli_connect($server, $user, $password);
		if(!$db)
		{
			print mysqli_error();
			exit;
		}
	
		/* List of Database */
		$result = mysqli_query($db,"SHOW TABLES FROM $database");
		if(!$result)  
		{
			print mysqli_error($db);
			exit;
		}
	
		$TextToWrite = "\n--  \n-- ";
		$TextToWrite .= "\n--  \n-- ";
		$TextToWrite .="\n-- This backup script is written by Sunil Kumar K. C.\n";
		$TextToWrite .= "\n--  Email: it@sunil.com.np\n-- ";
		$TextToWrite .= "\n--  \n-- ";
		$TextToWrite .= "\n--  \n-- ";
		$TextToWrite .= "\n-- \n--  Backup Created on ". date("Y/m/d H:m:s")."\n-- \n";
		$TextToWrite .= "\n--  \n-- ";
		$TextToWrite .= "\n--  \n-- ";
		$TextToWrite .= "SET FOREIGN_KEY_CHECKS=0;\n";
		$TextToWrite .= "\n--  \n-- ";

		mysqli_select_db($db,$database);

		while ($row = mysqli_fetch_row($result))
		{
			$TextToWrite .= "--\n-- Table `".$row[0]."`\n--\n";
			$TextToWrite .= "DROP TABLE IF EXISTS `$row[0]`;\n";
			$req = mysqli_query($db,"SHOW CREATE TABLE `".$row[0]. "`");
			$res = mysqli_fetch_array($req);
			$TextToWrite .= $res[1].";\n";
	
			mysqli_query($db,"SET NAMES 'utf8'");
			mysqli_query($db,"SET CHARACTER SET utf8");
			mysqli_query($db,"SET COLLATION_CONNECTION = 'utf8_general_ci'");
			$requete = mysqli_query($db,"SELECT * FROM `".$row[0]."`;");
			$nb = mysqli_num_fields($requete);
			
			$reqcomplete = "";
			$j = 0;
			unset($field);
			while ($property = mysqli_fetch_field($requete))
			{
				$field[] = $property->name;	
			}

			$reqcomplete = ' (';
			for($i = 0; $i < $nb; $i++)
			{
				$reqcomplete .= '`'.$field[$i].'`';
				if($i == ($nb - 1))	$reqcomplete .= ')';
				else			$reqcomplete .= ', ';
			}
	
			$TextToWrite .= "--\n-- Data for `".$row[0]."`\n--\n";
			while($res = mysqli_fetch_array($requete))
			{
				$i = 0;
				$TextToWrite .= "INSERT INTO `$row[0]`".$reqcomplete." VALUES (";
				while($i<$nb)
				{
					$ligne = mysqli_real_escape_string($db,$res[$i]);
	
					if(is_numeric($ligne)) 	$TextToWrite .= $ligne;
					else 			$TextToWrite .= "'".$ligne."'";
	
					if($i == ($nb - 1))	$TextToWrite .= ')';
					else			$TextToWrite .= ', ';
					$i++;
				}
	
				$TextToWrite .= ";\n";
			}
		}

		$TextToWrite .= "\n--  \n-- ";
		$TextToWrite .= "SET FOREIGN_KEY_CHECKS=1;";
		$TextToWrite .= "\n--  \n-- ";
		$TextToWrite .= "\n--  \n-- ";
		$TextToWrite .= "\n-- \n--  End of Backup ";
		$TextToWrite .= "\n--  \n-- ";
		$TextToWrite .= "\n--  \n-- ";
		$TextToWrite .="\n--  This backup script is written by Sunil Kumar K. C.";
		$TextToWrite .= "\n--  Email: it@sunil.com.np\n-- ";
		$TextToWrite .= "\n--  \n-- ";

		$TextToWrite = str_replace("\n--  \n-- ","",$TextToWrite);

//		$TextToWrite = str_replace("\n","<br>",$TextToWrite);
		mysqli_close($db);
writeUTF8File($file,$TextToWrite);	

header('Content-Encoding: UTF-8');
header('Content-Type: text/html; charset=utf-8');
header("Content-Description: File Transfer"); 
header("Content-Type: application/octet-stream"); 
header('Content-Disposition: attachment; filename="'.basename($file).'"');
readfile($file);
		exit;
	}
?>
<?php
function writeUTF8File($filename,$content) { 
	$f=fopen($filename,"w"); 
	# Now UTF-8 - Add byte order mark 
	fwrite($f, pack("CCC",0xef,0xbb,0xbf)); 
	fwrite($f,$content); 
	fclose($f); 
}

function getDirSQLFile($directory)
{
	$dir    = dirname(__FILE__);
	$DS=DIRECTORY_SEPARATOR;
	if ($DS=="\\") $DS = "/";

	$dir3 = $dir . $DS . $directory;

	$dir3=str_replace("\\",$DS,$dir3);

	$dir3 = str_replace("//","/",$dir3);

	$dirFile = scandir($dir3);
	natcasesort($dirFile);
	foreach ($dirFile as $DIRFILE)
	{
		$ext = pathinfo($DIRFILE,PATHINFO_EXTENSION);
		if ($ext == "sql")
		{
			$dir_file[] = $DIRFILE;
		}
	}
	return $dir_file;
}

?>
