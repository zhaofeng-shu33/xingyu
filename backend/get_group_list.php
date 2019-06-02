<?php
include_once 'mysql.php';
include_once 'functions.php';
$db = getDb();
if($_GET['all'] == '1'){
	$sql = 'select g.id, g.name, s.name, s.id from '.getTablePrefix().'_group as g, '.getTablePrefix().'_semester as s where s.id = g.semester_id';
	$res=mysqli_query($db, $sql) or die(mysqli_error($db));
	$row = mysqli_fetch_all($res);
	exitJson(0, '',array('group_list'=>$row));
}
$semester_id = $_GET['semester'];
if($semester_id == null){
    $semester_id = 2;
}
else{
    if(intval($semester_id) == 0){
        exitJson(1, 'invalid semester');
    }
    else{
        $semester_id = intval($semester_id);
    }
}
$sql = 'select name from '.getTablePrefix()."_group where semester_id = $semester_id";
$res=mysqli_query($db, $sql) or die(mysqli_error($db));
$row = mysqli_fetch_all($res);
exitJson(0, '',array('group_list'=>$row));

?>