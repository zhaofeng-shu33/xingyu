<?php
include_once 'config.php';
include_once 'mysql.php';

function exitJson($err, $msg , $result='')
{
    echo json_encode(array('err'=>$err, 'msg'=>$msg , 'result'=>$result));
    exit();
}

//! check the user with $openid is admin
function is_admin($db, $openid)
{
    $sql_p = 'select id from '.getTablePrefix()."_student where wechat_openid = '$openid'";
    $res_p = mysqli_query($db, $sql_p);
    $row_p = mysqli_fetch_assoc($res_p);
    return ($row_p['id'] != null);
}

function ensure_admin($db, $openid) {
    if ($openid != null && $openid != '') {
        $not_admin = !is_admin($db, $openid);
    } else {
        $not_admin = True;
    }
    if ($not_admin) {
        exitJson(44, 'you do not have the privilege');
    }
}

function get_current_semester($db) {
    $sql = 'select id from '.getTablePrefix()."_semester order by start_time desc limit 1";
    $res = mysqli_query($db, $sql) or die(mysqli_error($db));
    $res = mysqli_fetch_assoc($res);
    return $res['id'];
}

function get_current_semester_group_id($db, $group_name) {
    $semester_id = get_current_semester($db);
    $sql = 'select id from '.getTablePrefix()."_group where name = '$group_name' and semester_id = $semester_id";
    $res = mysqli_query($db, $sql) or die(mysqli_error($db));
    $res = mysqli_fetch_assoc($res);
    return $res['id'];
}

// return string date-obj
function get_semester_start_date($db, $semester_id){
    $sql = 'select start_time from '.getTablePrefix()."_semester where id = $semester_id";
    $res = mysqli_query($db, $sql) or die(mysqli_error($db));
    $res = mysqli_fetch_assoc($res);
    return $res['start_time'];
}

function get_group_id($db, $group_name, $semester_id) {
    $sql = 'select id from '.getTablePrefix()."_group where name = '$group_name' and semester_id = $semester_id";
    $res = mysqli_query($db, $sql) or die(mysqli_error($db));
    $res = mysqli_fetch_assoc($res);
    return $res['id'];
}

//! each element of $rows is (times, count) pair, and $rows are sorted based on times
//! this function get the count corresponding to $index = times
function get_rows_value($rows, $index)
{
    for($i = 0; $i < count($rows); $i++){
        $current = array(intval($rows[$i][0]), intval($rows[$i][1]));
        if($current[0] == $index)
            return $current[1];
        else if($current[0] > $index)
            break;
    }
    return 0;
}

function get_location($group_name) {
    global $target_organization_list;
    $location = '';
    foreach($target_organization_list as $val){
        if(strpos($group_name, $val) != FALSE){
            $location = $val;
            break;
        }
    }
    return $location;
}
?>
