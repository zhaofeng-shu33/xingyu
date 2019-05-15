<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

include_once 'mysql.php';
$school=$_GET['student_school'];
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$valid_school_list = array('thu', 'pku', 'hit', 'sust', 'szu');
if($school == null || array_search($school, $valid_school_list) === false){
	exit();
}

$date_str = date('Y-m-d-');
header('Content-Disposition: attachment; filename="' . $date_str . $school .'.xlsx"');

$spreadsheet = new Spreadsheet();
$spreadsheet->setActiveSheetIndex(0)
	->setCellValue('A1', '姓名')
	->setCellValue('B1', '活动名称')
	->setCellValue('C1', '活动时间')
	->setCellValue('D1', '活动地点');

$sql = "SELECT s.name, a.name, a.time, a.location from ".getTablePrefix()."_activity as a, ".getTablePrefix()."_student as s, ".getTablePrefix()."_student_activity as sa where sa.student_id = s.id and sa.activity_id = a.id and s.school = '" . $school . "' and a.time > '2019-01-01'";
$db = getDb();
$res=mysqli_query($db, $sql) or die(mysqli_error($db));
$rows = mysqli_fetch_all($res);
for($i=0; $i<count($rows); $i++){
	$spreadsheet->setActiveSheetIndex(0)
	->setCellValue('A' . strval($i+2), $rows[$i][0])
	->setCellValue('B' . strval($i+2), $rows[$i][1])
	->setCellValue('C' . strval($i+2), $rows[$i][2])
	->setCellValue('D' . strval($i+2), $rows[$i][3]);
}
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
?>