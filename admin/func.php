<?php
include_once(dirname(__FILE__).'/../config.php');

function dateFormat($ctime, $format='Y-m-d H:i:s') {
    $now = time();
    if($now > $ctime) {
        return '<span style="color: red">' . date($format, $ctime) . '<span>';
    } else {
        return date($format, $ctime);
    }
}

//================================
// onliine_add_data.php
//================================
function getUser($where='', $offset=30, $rows=0) {
    $ret_data = array();
    $conn = getDB();
    if(empty($where))
        $sql="select * from onliine_add_data where onadd_status>=0 order by onadd_add_date desc, onadd_sn desc limit $offset, $rows";
    else
        $sql="select * from onliine_add_data where onadd_status>=0 and ( $where ) order by onadd_add_date desc, onadd_sn desc limit $offset, $rows";

    $qresult = $conn->query($sql);
    if ($qresult->num_rows > 0) {
        while($row = $qresult->fetch_assoc()) {
            $ret_data[] = $row;
        }
        $qresult->free();
    }
    $conn->close();
    return $ret_data;
}

function getUserQty($where='') {
    $ret_data = 0;
    $conn = getDB();
    if(empty($where))
        $sql="select count(*) from onliine_add_data where onadd_status>=0";
    else
        $sql="select count(*) from onliine_add_data where onadd_status>=0 and ( $where )";

    $qresult = $conn->query($sql);
    if ($qresult->num_rows > 0) {
        while($row = $qresult->fetch_assoc()) {
            $ret_data = $row['count(*)'];
        }
        $qresult->free();
    }
    $conn->close();
    return $ret_data;
}

function getUserBySn($onadd_sn) {
    $ret_data = array();
    $conn = getDB();
    $sql="select * from onliine_add_data where onadd_sn='{$onadd_sn}'";

    $qresult = $conn->query($sql);
    if ($qresult->num_rows > 0) {
        if ($row = $qresult->fetch_assoc()) {
            $ret_data = $row;
        }
        $qresult->free();
    }
    $conn->close();
    return $ret_data;
}
function getUserByAccount($account) {
    $ret_data = array();
    $conn = getDB();
    $sql="select * from onliine_add_data where onadd_part_no='{$account}'";

    $qresult = $conn->query($sql);
    if ($qresult->num_rows > 0) {
        if ($row = $qresult->fetch_assoc()) {
            $ret_data = $row;
        }
        $qresult->free();
    }
    $conn->close();
    return $ret_data;
}

//================================
?>