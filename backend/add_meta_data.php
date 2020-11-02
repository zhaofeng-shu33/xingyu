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

$db = getDb();
$semester_id = get_current_semester($db);


function add_student_and_group($db, $name, $school, $group_id) {
    $sql_s = 'select id from '.getTablePrefix()."_student where name = '$name'";
    $res = mysqli_query($db, $sql_s) or die(mysqli_error($db));
    $row = mysqli_fetch_assoc($res);
    if($row['id'] == null) { // student not exist
        $sql = 'insert into '.getTablePrefix()."_student (name, school) values ('$name', '$school')";
        $res = mysqli_query($db, $sql) or die(mysqli_error($db));    
        $student_id =  mysqli_insert_id($db);
    }
    else {// if student exists, don't trigger any error
        $student_id = $row['id'];
        $sql_sg = 'select id from '.getTablePrefix()."_student_group where student_id = $student_id and group_id = $group_id";
        $res_sg = mysqli_query($db, $sql_sg) or die(mysqli_error($db));    
        $row_sg = mysqli_fetch_assoc($res_sg);
        if($row_sg['id'] != null){
            return;
             // 'student already has a group'
        }
    }
    $sql_ig = 'insert into '.getTablePrefix()."_student_group (group_id, student_id) values ($group_id, $student_id)";
    $res_ig = mysqli_query($db, $sql_ig) or die(mysqli_error($db));   
}
for ($row = 2; $row <= $highestRow; $row++) {
    $group_name = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
    $sql = "select id from ".getTablePrefix()."_group where name = '" . $group_name . "' and semester_id = " . $semester_id;
    $res = mysqli_query($db, $sql) or die(mysqli_error($db));
    $group_id = mysqli_fetch_assoc($res)["id"];
    // if group not exist, create the group
    if ($group_id == null) {
        $sql = 'insert into '.getTablePrefix()."_group (name, semester_id) values ('$group_name', $semester_id)";        
        $res = mysqli_query($db, $sql) or die(mysqli_error($db));
        $group_id =  mysqli_insert_id($db);
    }
    $name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
    $student_school = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
    $school = $organization_reverse_list[$student_school];
    add_student_and_group($db, $name, $school, $group_id);
    // Todo: add group leader nickname if exists
}

?>