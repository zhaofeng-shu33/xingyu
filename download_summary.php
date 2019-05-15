<?php
include_once 'mysql.php';
$school=$_GET['student_school'];
header('Content-Type: text/csv');
$valid_school_list = array('thu', 'pku', 'hit', 'sust', 'szu');
if($school == null || array_search($school, $valid_school_list) == false){
	exit();
}
echo ("a\tb");
?>