<?php
include_once 'mysql.php';
include_once 'functions.php';
$postdata=file_get_contents("php://input");

$jsondata=json_decode($postdata);

$week=$jsondata->week;
$name=$jsondata->name;
$list=$jsondata->student_list;
if($week == null || gettype($week)!='integer'){
    exitJson(1, 'invalid week');
}
if($name == null || $name == ''){
    exitJson(2, 'null name');
}
if($list == null || gettype($list)!='array'){
    exitJson(3, 'invalid student_list');
}
$db = getDb();
// create the activity
if(strpos($name, '金色') == FALSE && $name != '周二下午'){
    $location = '童伴时光';
}
else{
    $location = '金色年华';
}
// convert szu calendar to 阳历
// 2019-3-4 ~ week 1
$date=date_create("2019-3-4");
$interval_int = 7 * ($week - 1);
date_add($date, date_interval_create_from_date_string($interval_int." days"));
$date_str = date_format($date, 'Y-m-d');
$sql = 'insert into '.getTablePrefix()."_activity (name, location, time) values ('$name', '$location', '$date_str')";
$res=mysqli_query($db, $sql) or die(mysqli_error($db));
$activity_id = mysqli_insert_id($db);
foreach($list as $student){
    // first get student id
    $sql_s = 'select id from '.getTablePrefix()."_student where name = '$student'";
    $res_s=mysqli_query($db, $sql_s) or die(mysqli_error($db));
    $row_s = mysqli_fetch_assoc($res_s);
    if($row_s['id'] == null){
        exitJson(4, 'student '. $student. ' not exists in db');
    }    
    $student_id = $row_s['id'];
    $sql_r = 'insert into '.getTablePrefix()."_student_activity (student_id, activity_id) values ('$student_id', '$activity_id')";
    $res_r=mysqli_query($db, $sql_r) or die(mysqli_error($db));
}
exitJson(0, '');
?>