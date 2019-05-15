<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include_once 'mysql.php';
$school=$_GET['student_school'];
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$valid_school_list = array('thu', 'pku', 'hit', 'sust', 'szu');
if($school == null || array_search($school, $valid_school_list) === false){
	exit();
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Hello World !');

$writer = new Xlsx($spreadsheet);
$writer->save('hello world.xlsx');

$date_str = date('Y-m-d-');
header('Content-Disposition: attachment; filename="' . $date_str . $school .'.xlsx"');

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', '姓名');
$sheet->setCellValue('B1', '活动名称');
$sheet->setCellValue('C1', '活动时间');
$sheet->setCellValue('D1', '活动地点');

$sql = "SELECT s.name, a.name, a.time, a.location from ".getTablePrefix()."_activity as a, ".getTablePrefix()."_student as s, ".getTablePrefix()."_student_activity as sa where sa.student_id = s.id and sa.activity_id = a.id and s.school = '" . $school . "' and a.time > '2019-01-01'";
$db = getDb();
$res=mysqli_query($db, $sql) or die(mysqli_error($db));
$rows = mysqli_fetch_all($res);
for($i=0; $i<count($rows); $i++){
	$sheet->setCellValue('A' . ($i+2), $rows[$i][0]);
	$sheet->setCellValue('B' . ($i+2), $rows[$i][1]);
	$sheet->setCellValue('C' . ($i+2), $rows[$i][2]);
	$sheet->setCellValue('D' . ($i+2), $rows[$i][3]);
}
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
?>