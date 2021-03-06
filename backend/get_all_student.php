<?php
include_once 'config.php';
include_once 'mysql.php';
include_once 'functions.php';

$name = $_GET['student_group'];
$week = $_GET['week'];
$db = getDb();

if($name == null && $week == null){
    // general activity query
    $activity_name = $_GET['name'];
    $activity_time = $_GET['time'];
    $activity_location = $_GET['location'];

    if($activity_name == null || $activity_name == ''){
        exitJson(3, 'null name');
    }
    if($activity_location == null || $activity_location == ''){
        exitJson(4, 'invalid location');
    }
    if($activity_time == null || $activity_time == ''){
        exitJson(5, 'invalid time');
    }
    $sql = 'select id from '.getTablePrefix()."_activity where location = '$activity_location' and time = '$activity_time' and name= '$activity_name'";
}
else{
	$semester = $_GET['semester'];
	if($semester == null || $semester == ''){
		$semester = get_current_semester($db);
	}
	else{
		$semester =  intval($semester);
	}

    if($name == null || $name == ''){
        exitJson(1, 'null group');
    }
    if($week == null || intval($week) == 0){
        exitJson(2, 'invalid week');
    }
    if($name == $temp_group_name)
        exitJson(0, 'no result for flow',[]);
    
    // convert szu calendar to solar calendar
    $start_time = get_semester_start_date($db, $semester);

    $date=date_create($start_time);
    $interval_int = 7 * (intval($week) - 1);
    date_add($date, date_interval_create_from_date_string($interval_int." days"));
    $date_str = date_format($date, 'Y-m-d');
    $sql = 'select id from '.getTablePrefix()."_activity where time = '$date_str' and name= '$name'";
}
$res = mysqli_query($db, $sql) or die(mysqli_error($db));
$row = mysqli_fetch_assoc($res);
if($row['id'] == null){
    exitJson(0, 'activity not exist',[]);
}
$activity_id = $row['id'];
$sql_s = 'select s.name, s.school from '.getTablePrefix().'_student as s, '.getTablePrefix()."_student_activity as sa where s.id = sa.student_id and sa.activity_id = '$activity_id'";
$res_s=mysqli_query($db, $sql_s) or die(mysqli_error($db));
$row_s = mysqli_fetch_all($res_s);
exitJson(0, '', array('student_list'=>$row_s));
?>