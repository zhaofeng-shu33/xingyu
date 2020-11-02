<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

include_once 'config.php';
include_once 'mysql.php';
include_once 'functions.php';
$organization_reverse_list = array('清华' => 'thu', '北大' => 'pku',
    '哈工大' => 'hit', '南科大' => 'sust',  '深大' => 'szu');


$reader = IOFactory::createReader("Xlsx");
$spreadsheet = $reader->load("meta_data.xlsx");
$worksheet = $spreadsheet->getActiveSheet();
$highestRow = $worksheet->getHighestRow(); // e.g. 10
$highestColumn = 4;

$semester_id = 4;
$db = getDb();

for ($row = 2; $row <= $highestRow; $row++) {
    $group_name = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
    $sql = "SELECT id from ".getTablePrefix()."_group where name = '" . $group_name . "' and semester_id = " . $semester_id;
    $res = mysqli_query($db, $sql) or die(mysqli_error($db));
    $group_id = mysqli_fetch_assoc($res)["id"];
    $name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
    $student_school = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
    $school = $organization_reverse_list[$student_school];
    $sql = 'insert ignore into '.getTablePrefix()."_student (name, school) values ('$name', '$school')";
    $res = mysqli_query($db, $sql) or die(mysqli_error($db));
    $student_id =  mysqli_insert_id($db);
    if ($student_id == 0) {
        die("id is zero");
    }
    $sql_ig = 'insert ignore into '.getTablePrefix()."_student_group (group_id, student_id) values ($group_id, $student_id)";
    $res_ig = mysqli_query($db, $sql_ig) or die(mysqli_error($db));
}

?>