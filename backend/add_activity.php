<?php
include_once 'mysql.php';
include_once 'functions.php';
$postdata=file_get_contents("php://input");

$jsondata=json_decode($postdata);

$week=$jsondata->week;
$name=$jsondata->name;
$list=$jsondata->student_list;
$semester_id = isset($jsondata->semester) ? $jsondata->semester : null;
$openid = $jsondata->openid;
$db = getDb();
if($openid != null && $openid != ''){
    $not_admin = !is_admin($db, $openid);
}
else{
    $not_admin = True;
}
if($not_admin){
    exitJson(44, 'you do not have the privilege');
}

if($semester_id == null){
    $semester_id = get_current_semester($db);
}
elseif(gettype($semester_id) != 'integer'){
    exitJson(6, 'invalid semester');    
}

if($week == null || gettype($week)!='integer'){
    if($week == null)
        exitJson(1, 'null week');
    else
        exitJson(1, 'invalid week, type '.gettype($week));
}
if($name == null || $name == ''){
    exitJson(2, 'null name');
}
if($name == '流动'){
    exitJson(4, 'cannot add activity for flow group');
}
// check the name actually exits
$sql_check = 'select id from '.getTablePrefix()."_group where name='$name' and semester_id = '$semester_id'";
$res = mysqli_query($db, $sql_check) or die(mysqli_error($db));
$row = mysqli_fetch_assoc($res);
if($row['id'] == null){
    exitJson(9, 'group not exists');
}
$group_id = $row['id'];
if($list == null || gettype($list)!='array'){
    exitJson(3, 'invalid student_list');
}

// create the activity
if(strpos($name, '金色') == FALSE && $name != '周二下午' && $semester_id > 1){
    $location = '童伴时光';
}
else{
    $location = '金色年华';
}

$date_str = get_current_semester_date($db, $semester_id);
if($date_str == null){
	exitJson(8, 'not support semester_id provided');
}
$date = date_create($date_str);

$interval_int = 7 * ($week - 1);
date_add($date, date_interval_create_from_date_string($interval_int." days"));
$date_str = date_format($date, 'Y-m-d');
// first check whether the activity exists
$sql_check = 'select id from '.getTablePrefix()."_activity where name = '$name' and location = '$location' and time = '$date_str'";
$res = mysqli_query($db, $sql_check) or die(mysqli_error($db));
$row = mysqli_fetch_assoc($res);
if($row['id'] != null){
    exitJson(5, 'activity already exists');
}
$sql = 'insert into '.getTablePrefix()."_activity (name, location, time, institution, group_id) values ('$name', '$location', '$date_str', '$location', $group_id)";
$res = mysqli_query($db, $sql) or die(mysqli_error($db));
$activity_id = mysqli_insert_id($db);
foreach($list as $student){
    // first get student id
    $sql_s = 'select id from '.getTablePrefix()."_student where name = '$student'";
    $res_s = mysqli_query($db, $sql_s) or die(mysqli_error($db));
    $row_s = mysqli_fetch_assoc($res_s);
    if($row_s['id'] == null){
        exitJson(7, 'student '. $student. ' not exists in db');
    }    
    $student_id = $row_s['id'];
    $sql_r = 'insert into '.getTablePrefix()."_student_activity (student_id, activity_id) values ($student_id, $activity_id)";
    $res_r=mysqli_query($db, $sql_r) or die(mysqli_error($db));
}
exitJson(0, '');
?>