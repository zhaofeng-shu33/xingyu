<?php
include_once 'config.php';
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

if($week == null || gettype($week)!='integer'){
    if($week == null)
        exitJson(1, 'null week');
    else
        exitJson(1, 'invalid week, type '.gettype($week));
}
if($name == null || $name == ''){
    exitJson(2, 'null name');
}
if($list == null || gettype($list)!='array'){
    exitJson(3, 'invalid student_list');
}
if($semester_id == null){
    $semester_id = get_current_semester($db);
}
elseif(gettype($semester_id) != 'integer'){
    exitJson(6, 'invalid semester');    
}

// set the location of the activity
$location = '';
foreach($target_organization_list as $val){
    if(strpos($name, $val) != FALSE){
        $location = $val;
        break;
    }
}
if($locatition == ''){
    exitJson(10, 'group name does not contain location information');
}

$date_str = get_semester_start_date($db, $semester_id);
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
if($row['id'] == null){
    exitJson(5, 'activity not exists, with location = '.$location.' and time = '.$date_str);
}
$activity_id = $row['id'];
foreach($list as $student){
    // first get student id
    $sql_s = 'select id from '.getTablePrefix()."_student where name = '$student'";
    $res_s = mysqli_query($db, $sql_s) or die(mysqli_error($db));
    $row_s = mysqli_fetch_assoc($res_s);
    if($row_s['id'] == null){
        exitJson(4, 'student '. $student. ' not exists in db');
    }    
    $student_id = $row_s['id'];
    $sql_s_a = 'select id from '.getTablePrefix()."_student_activity where student_id = $student_id and activity_id = $activity_id";
    $res_s_a = mysqli_query($db, $sql_s_a) or die(mysqli_error($db));
    $row_s_a = mysqli_fetch_assoc($res_s_a);
    if($row_s_a['id'] == null){ // student has already joined this activity
        continue;
    }    
    $sql_r = 'delete from '.getTablePrefix()."_student_activity where student_id = $student_id and activity_id = $activity_id";
    $res_r = mysqli_query($db, $sql_r) or die(mysqli_error($db));
}
// if the activity has no students, delete it
    $sql = 'select count(id) as a from '.getTablePrefix()."_student_activity where activity_id = $activity_id";
    $res = mysqli_query($db, $sql) or die(mysqli_error($db));
    $row = mysqli_fetch_assoc($res);
    if(intval($row['a']) == 0){
        // delete the activity
		$sql_d = 'delete from '.getTablePrefix()."_activity where id = $activity_id";
		$res = mysqli_query($db, $sql_d) or die(mysqli_error($db));
    }    

exitJson(0, '');
?>