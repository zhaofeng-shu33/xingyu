<?php
// plot bar chart or line chart for xingyu program
require 'vendor/autoload.php';
JpGraph\JpGraph::load();
JpGraph\JpGraph::module('bar');
JpGraph\JpGraph::module('line');
include_once 'mysql.php';
include_once 'functions.php';


$plot_type = $_GET['type'];
if($plot_type != 'bar' && $plot_type != 'line'){
    exit();
}
$graph = new Graph(400, 250);
$graph->SetScale('textlin');
$db = getDb();
$graph->title->SetFont(FF_CHINESE, FS_NORMAL, 16);
$graph->xaxis->title->SetFont(FF_CHINESE, FS_NORMAL, 10);
$graph->yaxis->title->SetFont(FF_CHINESE, FS_NORMAL, 10);
          
if($plot_type == 'bar'){
    $graph->xaxis->SetTickLabels(array('1', '2', '3', '4', '5', '6', '7', '8-9', '10-14', '15+'));
    $graph->title->Set('星语志愿者参与活动次数统计图');
    $graph->xaxis->title->Set('次');
    $graph->yaxis->title->Set('人');
	$sql = 'select times, count(times) from (select s.name, count(s.id) as times from '.getTablePrefix().'_student as s, '.getTablePrefix().'_activity as a, '.getTablePrefix().'_student_activity as sa where s.id = sa.student_id and a.id = sa.activity_id group by s.name order by count(s.id) desc) as old group by times asc';
	$res = mysqli_query($db, $sql) or die(mysqli_error($db));
    $rows = mysqli_fetch_all($res);
    $data = array();
    for($i = 1; $i <= 7; $i++){
        array_push($data, get_rows_value($rows, $i));
    }
    array_push($data, get_rows_value($rows, 8) + get_rows_value($rows, 9)); // 8-9
    $cnt = 0;
    for($i = 10; $i <= 14; $i++){
        $cnt += get_rows_value($rows, $i);
    }
    array_push($data, $cnt); // 10-14
    $cnt = 0;
    if(count($rows) > 0){
        for($i = 15; $i < $rows[count($rows)-1][0]; $i++){
            $cnt += get_rows_value($rows, $i);
        }
    }
    array_push($data, $cnt); // 15+
    
	$barplot = new BarPlot($data);
	$graph->Add($barplot);
}
else{
    $semester = $_GET['semester'];
    if($semester == null || intval($semester) == 0){
        $semester = get_current_semester($db);
    }
    else{
        $semester = intval($semester);
    }
    // select start_time to offset
    $start_time = get_current_semester_date($db, $semester);
    $start_time_obj = date_create($start_time);
    $end_time = date_create($start_time)->add(new DateInterval('P5M'))->format('Y-m-d');
    $sql = 'SELECT a.time, count(sa.id) FROM '.getTablePrefix().'_activity as a, '.getTablePrefix().'_student_activity as sa where sa.activity_id = a.id and a.special = 0 and a.time >="'. $start_time . '" and a.time <"' . $end_time . '" group by a.time';
    $res = mysqli_query($db, $sql) or die(mysqli_error($db));
    $rows = mysqli_fetch_all($res);
    $array_x = array();
    $array_y = array();
    
    for($i = 0; $i < count($rows); $i++){
        $activity_time = $rows[$i][0];
        $interval = date_diff(date_create($activity_time), $start_time_obj);
        array_push($array_x, 1 + intval($interval->format('%a'))/7);
        array_push($array_y, intval($rows[$i][1]));
    }
    $interval_offset = 1;
    if(count($array_x) > 0)
        $interval_offset = 1 + $array_x[count($array_x) - 1];

    for($i = count($rows); $i < 8; $i++){
          array_push($array_x, $interval_offset + $i - count($rows));
          array_push($array_y, 0);        
    }
    $graph->xaxis->SetTickLabels($array_x);

    $graph->title->Set('星语志愿者参与活动人数变化图');
    $graph->xaxis->title->Set('周');
    $graph->yaxis->SetTitle('人', 'high');
    $lineplot = new LinePlot($array_y);
    $graph->Add($lineplot);	
}
$graph->Stroke();
?>
