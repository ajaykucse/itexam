<?php 
session_start();
define ("WEB-PROGRAM","Online Exam");
define ("CODER","Sunil Kumar K. C.");
if (file_exists("security.php")) include_once "security.php";

require_once "main_url.inc.php";
$URL = main_url();
$MAIN_URL = $URL;

if (count($_POST) == 0)
	echo "<script>window.location='" .$URL."start.html';</script>";  


if (($_SESSION['ONLINE-EXAM-SIMULATOR-CENTER-USER']) OR ($_SESSION['ONLINE-EXAM-SIMULATOR-ADMIN-USER']) )
{
	require_once "includes/functions.inc.php";
	
	$value = $_POST;

		$config = new config();
		$dbname = $config->dbname;
		$dbuser = $config->dbuser;
		$dbhost = $config->dbhost;
		$dbpass = $config->dbpass;
		unset($config);
	
		include_once "includes/db.inc.php";
		$db = new Database($dbhost,$dbuser,$dbpass,$dbname) ;


	foreach ($value as $KEY => $VALUE)
	{
		if ($KEY == 'exam_id') $exam_id2 = $VALUE;
		if ($KEY == 'chapter_no') $chapter_no = $VALUE;

		if ($KEY == 'question_id') 			$question = $VALUE;

	}

	$db->begin();
	$sqlQuestionDelete = "DELETE FROM exam_exam_question WHERE exam_id = '$exam_id2' AND chapter_no = '$chapter_no'";
	$query_delete = $db->query($sqlQuestionDelete);

	if (count($question) > 0)
	{
		foreach ($question as $question_id2 => $QUESTION)
		{
			$sqlQuestionInsert = "INSERT INTO exam_exam_question (exam_id, chapter_no, question_id) VALUES ('$exam_id2','$chapter_no', '$question_id2');";
			$query_insert = $db->query($sqlQuestionInsert);

		}
	}
	else $query_insert = TRUE;
	
	if ($query_delete && $query_insert)
		$db->commit();
	else
		$db->rollback();
	
}
else
	echo "<script>window.location='" .$URL."start.html';</script>";  

?>