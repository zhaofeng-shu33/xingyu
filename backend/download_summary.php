<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

include_once 'config.php';
include_once 'mysql.php';
include_once 'functions.php';

$school=$_GET['student_school'];
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

$valid_school_list = array();
foreach($organization_list as $key => $val){
	array_push($valid_school_list, $key);
}
foreach($institution_list as $val){
	array_push($valid_school_list, $val);
}

if($school == null || array_search($school, $valid_school_list) === false){
	http_response_code(400);
	exit();
}
$date_str = date('Y-m-d-');
header('Content-Disposition: attachment; filename="' . $date_str . $school .'.xlsx"');
$spreadsheet = new Spreadsheet();
$db = getDb();

if(array_search($school, $institution_list) !== FALSE){
	$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A1', '志愿者姓名')
		->setCellValue('B1', '所在学校')
		->setCellValue('C1', '活动名称')
		->setCellValue('D1', '活动时间')
		->setCellValue('E1', '活动地点');
	$school_map = $organization_list;
	$sql = "SELECT s.name, s.school, a.name, a.time, a.location from ".getTablePrefix()."_activity as a, ".getTablePrefix()."_student as s, ".getTablePrefix()."_student_activity as sa where sa.student_id = s.id and sa.activity_id = a.id and a.institution = '$school'";
	$res=mysqli_query($db, $sql) or die(mysqli_error($db));
	$rows = mysqli_fetch_all($res);
	for($i=0; $i<count($rows); $i++){
		$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A' . strval($i+2), $rows[$i][0])
		->setCellValue('B' . strval($i+2), $school_map[$rows[$i][1]])
		->setCellValue('C' . strval($i+2), $rows[$i][2])
		->setCellValue('D' . strval($i+2), $rows[$i][3])
		->setCellValue('E' . strval($i+2), $rows[$i][4]);
	}
}
else{
	$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A1', '姓名')
		->setCellValue('B1', '活动名称')
		->setCellValue('C1', '活动时间')
		->setCellValue('D1', '活动地点');
	$semester_id = get_current_semester($db);	
    $start_time = get_semester_start_date($db, $semester_id);
	$sql = "SELECT s.name, a.name, a.time, a.location from ".getTablePrefix()."_activity as a, ".getTablePrefix()."_student as s, ".getTablePrefix()."_student_activity as sa where sa.student_id = s.id and sa.activity_id = a.id and s.school = '" . $school . "' and a.time > '$start_time'";
	$res=mysqli_query($db, $sql) or die(mysqli_error($db));
	$rows = mysqli_fetch_all($res);
	for($i=0; $i<count($rows); $i++){
		$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A' . strval($i+2), $rows[$i][0])
		->setCellValue('B' . strval($i+2), $rows[$i][1])
		->setCellValue('C' . strval($i+2), $rows[$i][2])
		->setCellValue('D' . strval($i+2), $rows[$i][3]);
	}
}
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
?>