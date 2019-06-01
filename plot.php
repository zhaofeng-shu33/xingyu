<?php
// plot bar chart or line chart for xingyu program
require 'vendor/autoload.php';
JpGraph\JpGraph::load();
JpGraph\JpGraph::module('bar');

include_once 'mysql.php';

$plot_type = $_GET['type'];
if($plot_type != 'bar' && $plot_type != 'line'){
    exit();
}
$graph = new Graph(400, 250);
$graph->SetScale('textlin');
$graph->xaxis->SetTickLabels(array('1', '2', '3', '4', '5', '6', '7', '8-9', '10-14', '15+'));
$db = getDb();
if($plot_type == 'bar'){
	$graph->title->SetFont(FF_CHINESE, FS_NORMAL, 16);
    $graph->title->Set('星语志愿者参与活动次数统计图');
	$graph->xaxis->title->SetFont(FF_CHINESE, FS_NORMAL, 10);
    $graph->xaxis->title->Set('次');
	$graph->yaxis->title->SetFont(FF_CHINESE, FS_NORMAL, 10);
    $graph->yaxis->title->Set('人');
	$sql = 'select times, count(times) from (select s.name, count(s.id) as times from '.getTablePrefix().'_student as s, '.getTablePrefix().'_activity as a, '.getTablePrefix().'_student_activity as sa where s.id = sa.student_id and a.id = sa.activity_id group by s.name order by count(s.id) desc) as old group by times';
	$res = mysqli_query($db, $sql) or die(mysqli_error($db));
    $rows = mysqli_fetch_all($res);
    $data = array();
    for($i=0; $i<7;$i++){
        array_push($data, intval($rows[$i][1]));
    }
    array_push($data, intval($rows[7][1]) + intval($rows[8][1])); // 8-9
    $cnt = 0;
    $offset = 14;
    for($i=9; $i<14; $i++){
        if($rows[$i][0]>14){
            $offset = $i;
            break;
        }
        $cnt += intval($rows[$i][1]);
    }
    array_push($data, $cnt); // 10-14
    $cnt = 0;
    for($i=$offset; $i<count($rows); $i++){
        $cnt += intval($rows[$i][1]);
    }
    array_push($data, $cnt); // 15+
    
	$barplot = new BarPlot($data);
	$graph->Add($barplot);
}
$graph->Stroke();
?>
