<?php
include_once 'mysql.php';
$school=$_GET['student_school'];
header('Content-Type: text/csv');
$valid_school_list = array('thu', 'pku', 'hit', 'sust', 'szu');
if($school == null || array_search($school, $valid_school_list) === false){
	exit();
}
$date = date_default_timezone_get();
$date_str = date_format($date, 'Y-m-d-');
header('Content-Disposition: attachment; filename="' + $date_str + $school +'.csv"');
echo("姓名\t活动名称\t活动时间\t活动地点\n");
$sql = "SELECT s.name, a.name, a.time, a.location from ".getTablePrefix()."_activity as a, ".getTablePrefix()."_student as s, ".getTablePrefix()."_student_activity as sa where sa.student_id = s.id and sa.activity_id = a.id and s.school = '" + $school + "'";
$db = getDb();
$res=mysqli_query($db, $sql) or die(mysqli_error($db));
$row = mysqli_fetch_all($res);
for($i=0; $i<count($row); $i++){
	echo ($row[0] + '\t' + $row[1] + '\t' + $row[2] + '\t' + $row[3] + '\n');
}

?>