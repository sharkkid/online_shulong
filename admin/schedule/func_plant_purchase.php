<?php
include_once(dirname(__FILE__).'/../config.php');
$onchba_cycle = '0';

function dateFormat($ctime, $format='Y-m-d H:i:s') {
	$now = time();
	if($now > $ctime) {
		return '<span style="color: red">' . date($format, $ctime) . '<span>';
	} else {
		return date($format, $ctime);
	}
}

//===============================
//plant_re_schedule.php
//===============================
function getUser_re($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select * from onliine_add_data where onadd_status>=0 and onadd_schedule=1 order by onadd_planting_date,onadd_growing, onadd_sn desc limit $offset, $rows";
	else
		$sql="select * from onliine_add_data where onadd_status>=0 and onadd_schedule=1 and ( $where ) order by onadd_planting_date,onadd_growing, onadd_sn desc limit $offset, $rows";

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

function getProductFirstQty($onadd_sn) {
	$ret_data = 0;
	$conn = getDB();	
	$sql="select onfp_plant_amount from onliine_firstplant_data where onfp_status>=1 and onadd_sn like '$onadd_sn'";
	// echo $sql;
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data = $row['onfp_plant_amount'];
		}
		$qresult->free();
	}
	else{
		$ret_data = 1;
	}
	$conn->close();
	return $ret_data;
}

function getUserQty_re($where='') {
	$ret_data = 0;
	$conn = getDB();
	if(empty($where))
		$sql="select count(*) from onliine_add_data where onadd_status>=0 and onadd_schedule=1";
	else
		$sql="select count(*) from onliine_add_data where onadd_status>=0 and onadd_schedule=1 and ( $where )";

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

//================================
// onliine_add_data.php
//================================
function getUser($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select * from onliine_add_data where onadd_status>=0 and onadd_schedule!=1 order by onadd_planting_date,onadd_growing, onadd_sn desc limit $offset, $rows";
	else
		$sql="select * from onliine_add_data where onadd_status>=0 and onadd_schedule!=1 and ( $where ) order by onadd_planting_date,onadd_growing, onadd_sn desc limit $offset, $rows";

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

function getWorkListByMonth($where='', $offset=30, $rows=0) {
	$DEVICE_SYSTEM = array(
		0=>"其它",
		1=>"1.7",
		2=>"2.5",
		3=>"2.8",
		4=>"3.0",
		5=>"3.5",
		6=>"3.6",
		7=>"其他",
		8=>"瓶苗開瓶",
		9=>"出貨"
	);
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select onadd_supplier,onadd_sn,onadd_plant_st,onadd_quantity,onadd_cur_size,onadd_part_no,onadd_part_name,onadd_planting_date,onadd_growing,onadd_planting_date,onadd_delay_date from onliine_add_data where onadd_status>=0 and onadd_schedule!=1";
	else
		$sql="select onadd_supplier,onadd_sn,onadd_plant_st,onadd_quantity,onadd_cur_size,onadd_part_no,onadd_part_name,onadd_planting_date,onadd_growing,onadd_planting_date,onadd_delay_date from onliine_add_data where onadd_status>=0 and onadd_schedule!=1 and ( $where )";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			// $delay_days = getWorkDelayDays($row['onadd_sn']);
			$cur_size = $DEVICE_SYSTEM[$row['onadd_cur_size']];
			if($row['onadd_growing'] == "出貨")
				$row['onadd_growing'] = 9;
			$growing_size = $DEVICE_SYSTEM[$row['onadd_growing']];
			$row['onchba_cycle'] = getSettingBySn($cur_size,$growing_size)['onchba_cycle'];
			
			if($row['onchba_cycle'] > 0){
	        	$pre_date = date("Y/m/d", strtotime("+".$row['onchba_cycle']." days", $row['onadd_planting_date']));
	        	// $show_day = date("Y/m/d", strtotime("+".$row['onchba_cycle']+($delay_days-7)." days", $row['onadd_planting_date']));
	        	$nowdays = time();
	        	$tdays = strtotime($pre_date." 00:00:00");
	        	if(time() > $row['onadd_delay_date']){
		        	if($tdays < ($nowdays + 604800)){
		        		$row['onadd_planting_date'] = date('Y/m/d',$row['onadd_planting_date']);        		
		        		$row['expected_date'] = date('Y/m/d',strtotime($pre_date));
		        		// $row['show_day'] = date('Y/m/d',strtotime($show_day));
						$ret_data[] = $row;
		        	}
		        }
		    }
		}
		$qresult->free();
	}
	$conn->close();
	$data = array();
	$rows += $offset;
	for($i=$offset;$i<$rows;$i++){
		if(!empty($ret_data[$i]))
			$data[$i] = $ret_data[$i];
	}

	return $data;
}

function getDaysBetweenTwoDays($start,$end){
	$n=0;  
	$date_from = $start;   
	$date_from = strtotime($date_from); // Convert date to a UNIX timestamp  
	  
	// Specify the end date. This date can be any English textual format  
	$date_to = $end;  
	$date_to = strtotime($date_to); // Convert date to a UNIX timestamp  
	  
	// Loop from the start date to end date and output all dates inbetween  
	for ($i=$date_from; $i<=$date_to; $i+=86400) {  
	    $n++;
	}  
	return $n;
}

function getUseradd($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select * from onliine_add_data where onadd_status>=0 GROUP BY onadd_part_no";
	else
		$sql="select * from onliine_add_data where onadd_status>=0 GROUP BY onadd_part_no";

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

function getWorkListByMonthQty($where='') {
	$DEVICE_SYSTEM = array(
		0=>"其它",
		1=>"1.7",
		2=>"2.5",
		3=>"2.8",
		4=>"3.0",
		5=>"3.5",
		6=>"3.6",
		7=>"其他",
		8=>"瓶苗開瓶",
		9=>"出貨"
	);
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select onadd_supplier,onadd_sn,onadd_plant_st,onadd_quantity,onadd_cur_size,onadd_part_no,onadd_part_name,onadd_planting_date,onadd_growing,onadd_planting_date,onadd_delay_date from onliine_add_data where onadd_status>=0 and onadd_schedule!=1";
	else
		$sql="select onadd_supplier,onadd_sn,onadd_plant_st,onadd_quantity,onadd_cur_size,onadd_part_no,onadd_part_name,onadd_planting_date,onadd_growing,onadd_planting_date,onadd_delay_date from onliine_add_data where onadd_status>=0 and onadd_schedule!=1 and ( $where )";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			// $delay_days = getWorkDelayDays($row['onadd_sn']);
			$cur_size = $DEVICE_SYSTEM[$row['onadd_cur_size']];
			if($row['onadd_growing'] == "出貨")
				$row['onadd_growing'] = 9;
			$growing_size = $DEVICE_SYSTEM[$row['onadd_growing']];


			$row['onchba_cycle'] = getSettingBySn($cur_size,$growing_size)['onchba_cycle'];
			
			if($row['onchba_cycle'] > 0){
	        	$pre_date = date("Y/m/d", strtotime("+".$row['onchba_cycle']." days", $row['onadd_planting_date']));
	        	// $show_day = date("Y/m/d", strtotime("+".$row['onchba_cycle']+($delay_days-7)." days", $row['onadd_planting_date']));
	        	$nowdays = time();
	        	$tdays = strtotime($pre_date." 00:00:00");
	        	if(time() > $row['onadd_delay_date']){
		        	if($tdays < ($nowdays + 604800)){
		        		$row['onadd_planting_date'] = date('Y/m/d',$row['onadd_planting_date']);        		
		        		$row['expected_date'] = date('Y/m/d',strtotime($pre_date));
		        		// $row['show_day'] = date('Y/m/d',strtotime($show_day));
						$ret_data[] = $row;
		        	}
		        }
		    }

		}
		$qresult->free();
	}
	$conn->close();

	return $ret_data;
}
function getUserQtyadd($where='') {
	$ret_data = 0;
	$conn = getDB();
	if(empty($where))
		$sql="select count(*) from onliine_add_data where onadd_status>=0 GROUP BY onadd_part_no";
	else
		$sql="select count(*) from onliine_add_data where onadd_status>=0 GROUP BY onadd_part_no";

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
function getSettingBySn($onchba_size,$onchba_tsize) {
	$ret_data = array();
	$conn = getDB();
	$sql="select * from online_change_basin where onchba_size like '{$onchba_size}' and onchba_tsize like '{$onchba_tsize}'";

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

function getsetting() {
	$ret_data = array();
	$conn = getDB();
		$sql="select * from online_change_basin where onchba_status>=0";

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

//================================
// onliine_details.php
//================================
// SELECT onadd_part_no, SUM(onadd_quantity) FROM onliine_add_data WHERE onadd_part_no='PP-0052' AND onadd_growing='1' GROUP BY onadd_quantity_shi
function getDetails($onadd_part_no,$onadd_growing,$onadd_quantity_del) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select * , SUM(onadd_quantity) from onliine_add_data where onadd_part_no='$onadd_part_no' AND onadd_growing='$onadd_growing' AND onadd_quantity_del='$onadd_quantity_del' GROUP BY onadd_quantity_shi";
	else
		$sql="select * , SUM(onadd_quantity) from onliine_add_data where onadd_part_no='$onadd_part_no' AND onadd_growing='$onadd_growing' AND onadd_quantity_del='$onadd_quantity_del' GROUP BY onadd_quantity_shi";

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

function getDetailsQty($where='') {
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

function getDetailsBySn($onadd_sn) {
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
function getDetailsByAccount($account) {
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

//================================
// sys_history.php
//================================
function getHistory($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select * from js_history a left join device_manage b on a.jshist_user=b.dema_sn order by jshist_add_date desc, jshist_sn desc limit $offset, $rows";
	else
		$sql="select * from js_history a left join device_manage b on a.jshist_user=b.dema_sn where 1=1 and ( $where ) order by jshist_add_date desc, jshist_sn desc limit $offset, $rows";

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

function getHistoryQty($where='') {
	$ret_data = 0;
	$conn = getDB();
	if(empty($where))
		$sql="select count(*) from js_history a left join device_manage b on a.jshist_user=b.dema_sn";
	else
		$sql="select count(*) from js_history a left join device_manage b on a.jshist_user=b.dema_sn where 1=1 and ( $where )";

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

function getWorkDelayDays($onadd_sn) {
	$ret_data = 0;
	$conn = getDB();
	$sql="SELECT onwd_sn FROM `online_work_delay` WHERE onadd_sn = '{$onadd_sn}'";
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data = $row['onwd_sn'];
		}
		$qresult->free();
	}
	$conn->close();
	return ($ret_data*7);
}

//================================
// sys_history_online.php
//================================
function getHistoryOnline($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select a.dema_sn, dema_device_name, sum(jsol_count) as count from js_online a left join device_manage b on a.dema_sn=b.dema_sn group by a.dema_sn order by count desc limit $offset, $rows";
	else
		$sql="select a.dema_sn, dema_device_name, sum(jsol_count) as count from js_online a left join device_manage b on a.dema_sn=b.dema_sn where 1=1 and ( $where ) group by a.dema_sn order by count desc limit $offset, $rows";

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

function getHistoryOnlineQty($where='') {
	$ret_data = 0;
	$conn = getDB();
	if(empty($where))
		$sql="select a.dema_sn, dema_device_name, sum(jsol_count) as count from js_online a left join device_manage b on a.dema_sn=b.dema_sn group by a.dema_sn";
	else
		$sql="select a.dema_sn, dema_device_name, sum(jsol_count) as count from js_online a left join device_manage b on a.dema_sn=b.dema_sn where 1=1 and ( $where ) group by a.dema_sn";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		$ret_data = $qresult->num_rows;
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

//================================
// sys_history_edit.php
//================================
function getHistoryEdit($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select dema_sn, dema_device_name, count(*) as count from js_history a left join device_manage b on a.jshist_user=b.dema_sn where jshist_op_type=2 group by dema_sn order by count desc limit $offset, $rows";
	else
		$sql="select dema_sn, dema_device_name, count(*) as count from js_history a left join device_manage b on a.jshist_user=b.dema_sn where jshist_op_type=2 and ( $where ) group by dema_sn order by count desc limit $offset, $rows";

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

function getHistoryEditQty($where='') {
	$ret_data = 0;
	$conn = getDB();
	if(empty($where))
		$sql="select dema_sn, dema_device_name, count(*) as count from js_history a left join device_manage b on a.jshist_user=b.dema_sn where jshist_op_type=2 group by dema_sn order by dema_sn";
	else
		$sql="select dema_sn, dema_device_name, count(*) as count from js_history a left join device_manage b on a.jshist_user=b.dema_sn where jshist_op_type=2 and ( $where ) group by dema_sn order by dema_sn";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		$ret_data = $qresult->num_rows;
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getDelayRecord() {
	$ret_data = array();
	$conn = getDB();

	$sql="SELECT * FROM `online_work_delay` WHERE onwd_status >= 1";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$d = getUserBySn($row['onadd_sn']);
			if($d['onadd_newpot_sn'] == 0){
				if($d['onadd_ml'] == 0){
					$row['onadd_sn'] = $d['onadd_sn'];
				}else{											
	        		$row['onadd_sn'] = $d['onadd_ml'];
	        	}
	        }
	        else{
	        	$row['onadd_sn'] = $d['onadd_newpot_sn'];
	        }
	        
			$row['onadd_part_no'] = $d['onadd_part_no'];
			$row['onadd_part_name'] = $d['onadd_part_name'];
			if($d['onadd_isbought'] == 0)
				$row['onadd_isbought'] = date("Y",$d['onadd_planting_date']);
			elseif($d['onadd_isbought'] == 1)
				$row['onadd_isbought'] = "P".date("Y",$d['onadd_planting_date']);
			
			$row['onwd_date'] = date("Y-m-d H:i:s",$row['onwd_date']);
			$ret_data[] = $row;
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

?>