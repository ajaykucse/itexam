<?php
if (file_exists("security.php")) include_once "security.php";
$software = "Online Exam";


class config
{
	public $dbname = 'examdb';
	public $dbuser = 'root';
	public $dbpass = '';
	public $dbhost = 'localhost';
}


function main_url()
{
	$ROOT_DIR= $_SERVER['DOCUMENT_ROOT'];
	$DS=DIRECTORY_SEPARATOR;
	if ($DS=="\\") $DS = "/";
	$filedir = dirname(__FILE__);
	$filedir=str_replace("\\",$DS,$filedir);
	$filedir=str_replace($ROOT_DIR,"",$filedir);
	$basename = $DS . $filedir;
	
	$basename = $basename."/";
	$basename = str_replace("//","/",$basename);
	return $basename;
}


function removeExtraCharacter($field)
{
	return preg_replace("/[^a-zA-Z0-9_-]+/", "", htmlspecialchars(html_entity_decode(strip_tags($field))));
}
function FilterString($field)
{
	$field =  strip_tags($field);
	$field = preg_replace("/[;]+/", "", html_entity_decode($field));	
	$field = htmlspecialchars($field,ENT_QUOTES,'UTF-8');
	$field = htmlentities($field, ENT_QUOTES,'UTF-8');
	$field =  str_replace("--","",($field));
	return $field;
}
function FilterTextBox($field)
{
	$field =  strip_tags($field);
	$field = preg_replace("/[;]+/", "", html_entity_decode($field));	
	$field =  str_replace("--","",($field));
	return $field;
}

function FilterAnswerOrder($field)
{
	return preg_replace("/[^0-9,]+/", "", htmlspecialchars(html_entity_decode(strip_tags($field))));	
}


function FilterNumber($field)
{
	return preg_replace("/[^0-9]+/", "", htmlspecialchars(html_entity_decode(strip_tags($field))));	
}
function FilterDate($field)
{
	return preg_replace("/[^0-9-]+/", "", htmlspecialchars(html_entity_decode(strip_tags($field))));		
}	

function FilterEmail($field)
{
    if (preg_match("/^([A-Z0-9._%+-]+@[a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $field) //valid chars check
            && preg_match("/^.{1,253}$/", $field) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $field)   ); //length of each label
	return preg_replace("/[^a-z@A-Z0-9._-]+/", "", htmlspecialchars(html_entity_decode(strip_tags($field))));
}

function NumberToAlpha($string)
{
	$num = array (
	"1" => "A", 
	"2" => "B", 
	"3" => "C", 
	"4" => "D",
	"5" => "E",
	"6" => "F",
	"7" => "G",
	"8" => "H",
	"9" => "I",
	"10" => "K"
	);
	return strtr($string, $num); //corrected 
} 


function validDomain($domain)
{
    return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain) //valid chars check
            && preg_match("/^.{1,253}$/", $domain) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain)   ); //length of each label
}

function getDir()
{
	$dir    = dirname(__FILE__);
	$directories = scandir($dir);
	for ($i=0;$i<count($directories);$i++)
	{
			if (is_dir($directories[$i])) 
			{
				if(! preg_match("/_/i",$directories[$i])
				&& $directories[$i] != "."
				&& $directories[$i] != ".."
				) 
				$dir2[] = $directories[$i];
			}
	}
	return $dir2;
}
function getLocationDir()
{

	$DS=DIRECTORY_SEPARATOR;
	if ($DS=="\\") $DS = "/";
	$filedir = dirname(__FILE__);
	$filedir=str_replace("\\",$DS,$filedir);
	
	$basename = $filedir."/";
	$basename = str_replace("//","/",$basename);
	return $basename;
}
function getDirFile($directory)
{
	$dir    = dirname(__FILE__);
	$DS=DIRECTORY_SEPARATOR;
	if ($DS=="\\") $DS = "/";

	$dir3 = $dir . $DS . $directory;

	$dirFile = scandir($dir3);
	natcasesort($dirFile);
	foreach ($dirFile as $DIRFILE)
	{
		$ext = pathinfo($DIRFILE,PATHINFO_EXTENSION);
		if (($ext == "php") || ($ext =="html") || ($ext == "htm"))
		{
			$dir_file[] = $DIRFILE;
		}
	}
	return $dir_file;
}

function dataTable($table)
{
	$URL = main_url();
?>
	<script>
$(document).ready( function () {
	DataTable = $('#<?php echo $table;?>').dataTable( {
			"paging":   true,
			"ordering": false, 
			"info":     true,
			"pageLength": 25,
		 "responsive": true
			
	} );
} );
</script>
    <!-- PAGE LEVEL SCRIPTS -->
    <link href="<?php echo $URL;?>bootstrap/addon/dataTables/css/dataTables.bootstrap.css" rel="stylesheet">
    <link href="<?php echo $URL;?>bootstrap/addon/dataTables/css/dataTable.responsive.css" rel="stylesheet">
    <script src="<?php echo $URL;?>bootstrap/addon/dataTables/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo $URL;?>bootstrap/addon/dataTables/js/dataTables.bootstrap.js"></script>
     <!-- END PAGE LEVEL SCRIPTS -->
<?php
}
?>