<?php
// api: https://developers.weixin.qq.com/miniprogram/dev/api/network/upload/wx.uploadFile.html
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

include_once 'config.php';
include_once 'mysql.php';
include_once 'functions.php';
$file_key = 'volunteer';
$file_obj = $_FILES[$file_key];
$open_id = isset($_POST["openid"]) ? $_POST["openid"]: null;
$semester_time = isset($_POST["time"]) ? $_POST["time"]: null;
$db = getDb();
ensure_admin($db, $open_id);
function check_date($db, $date_str) {
    // return False if the date is invalid
    $sql = 'select start_time from '.getTablePrefix()."_semester order by start_time desc limit 1";
    $res = mysqli_query($db, $sql) or die(mysqli_error($db));
    $res = mysqli_fetch_assoc($res)["start_time"];
    return $res < $date_str;
}
if ($semester_time != null) {
    if (check_date($db, $date_str) == False) {
        exitJson(5, "invalid time");
    }
    $date = date_create($semester_time);
    $name = $date->format('Y');
    if ($date->format('m') <= '06') {
        $name = $name . $season_spring;
    } else {
        $name = $name . $season_autumn;
    }
    $sql_s = 'select id from '.getTablePrefix()."_semester where name = '$name'";
    $res = mysqli_query($db, $sql_s) or die(mysqli_error($db));
    $row_id = mysqli_fetch_assoc($res)['id'];
    if ($row_id == null) { // semester not exist
        $sql = 'insert into '.getTablePrefix()."_semester (name, start_time) values ('$name', '$semester_time')";
        $res = mysqli_query($db, $sql) or die(mysqli_error($db)); 
    } else { // semester exists, update the start time only
        $sql = 'update '.getTablePrefix()."_semester set start_time = '$semester_time' where id = $row_id";
        $res = mysqli_query($db, $sql) or die(mysqli_error($db)); 
    }
}

$organization_reverse_list = array('清华' => 'thu', '北大' => 'pku',
    '哈工大' => 'hit', '南科大' => 'sust',  '深大' => 'szu');


$reader = IOFactory::createReader("Xlsx");
try {
    $spreadsheet = $reader->load($file_obj["tmp_name"]);
    $worksheet = $spreadsheet->getActiveSheet();
    $highestRow = $worksheet->getHighestRow(); // e.g. 10    
} catch (Exception $e) {
    exitJson(5, $e->getMessage());
}
$highestColumn = 4;


$semester_id = get_current_semester($db);


function add_student_and_group($db, $name, $school, $nickname, $group_id) {
    $sql_s = 'select id from '.getTablePrefix()."_student where name = '$name'";
    $res = mysqli_query($db, $sql_s) or die(mysqli_error($db));
    $row = mysqli_fetch_assoc($res);
    if ($row['id'] == null) { // student not exist
        $sql = '';
        if ($nickname == '') {
            $sql = 'insert into '.getTablePrefix()."_student (name, school) values ('$name', '$school')";
        } else {
            $sql = 'insert into '.getTablePrefix()."_student (name, school, wechat_nickname) values ('$name', '$school', '$nickname')";
        }
        $res = mysqli_query($db, $sql) or die(mysqli_error($db));
        $student_id =  mysqli_insert_id($db);
    }
    else {// if student exists, don't trigger any error
        $student_id = $row['id'];
        if ($nickname != '') {
            $sql = 'update '.getTablePrefix()."_student set wechat_nickname = '$nickname' where id = $student_id";
            mysqli_query($db, $sql) or die(mysqli_error($db));
        }
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
    $group_name_valid = False;
    foreach($institution_list as $val) {
        if (strpos($group_name, $val) != FALSE) {
            $group_name_valid = True;
            break;            
        }
    }
    if ($group_name_valid == False) {
        exitJson(5, 'invalid group name at row ' . $row);
    }
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
    $nickname = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
    $school =  isset($organization_reverse_list[$student_school]) ? $organization_reverse_list[$student_school] : null;
    if ($school == null) {
        exitJson(5, 'invalid school name at row ' . $row);
    }
    add_student_and_group($db, $name, $school, $nickname, $group_id);
}
exitJson(0, '');
?>