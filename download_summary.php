<?php
include_once 'mysql.php';
$school=$_GET['student_school'];
header('Content-Type: text/csv');
$valid_school_list = array('thu', 'pku', 'hit', 'sust', 'szu');
if($school == null || array_search($school, $valid_school_list) === false){
	exit();
}
$date_str = date('Y-m-d-');
header('Content-Disposition: attachment; filename="' . $date_str . $school .'.csv"');
echo("姓名,活动名称,活动时间,活动地点\n");
$sql = "SELECT s.name, a.name, a.time, a.location from ".getTablePrefix()."_activity as a, ".getTablePrefix()."_student as s, ".getTablePrefix()."_student_activity as sa where sa.student_id = s.id and sa.activity_id = a.id and s.school = '" . $school . "' and a.time > '2019-01-01'";
$db = getDb();
$res=mysqli_query($db, $sql) or die(mysqli_error($db));
$rows = mysqli_fetch_all($res);
for($i=0; $i<count($rows); $i++){
	echo ($rows[$i][0] . "," . $rows[$i][1] . "," . $rows[$i][2] . "," . $rows[$i][3] . "\n");
}

?>