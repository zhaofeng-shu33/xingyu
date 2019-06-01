<?php
// plot bar chart or line chart for xingyu program
require 'vendor/autoload.php';
JpGraph\JpGraph::load();
JpGraph\JpGraph::module('bar');
$plot_type = $_GET['type'];
if($plot_type != 'bar' && $plot_type != 'line'){
    exit();
}
$graph = new Graph(400, 250);
$graph->SetScale('textlin');
$graph->xaxis->SetTickLabels(array('1', '2', '3', '4', '5', '6', '7', '8-9', '10-14', '15+'));
if($plot_type == 'bar'){
	$graph->title->SetFont(FF_CHINESE, FS_NORMAL, 16);
    $graph->title->Set('星语志愿者参与活动次数统计图');
	$graph->xaxis->title->SetFont(FF_CHINESE, FS_NORMAL, 10);
    $graph->xaxis->title->Set('次');
	$graph->yaxis->title->SetFont(FF_CHINESE, FS_NORMAL, 10);
    $graph->yaxis->title->Set('人');
	$sql = 'select times, count(times) from (select s.name, count(s.id) as times from xingyu_student as s, xingyu_activity as a, xingyu_student_activity as sa where s.id = sa.student_id and a.id = sa.activity_id group by s.name order by count(s.id) desc) as old group by times';
	$data = array(92, 31, 16, 10, 8, 13, 6, 6, 5, 4);
	$barplot = new BarPlot($data);
	$graph->Add($barplot);
}
$graph->Stroke();
?>
