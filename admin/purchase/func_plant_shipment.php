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
// online_shipment_data.php
//================================
function getShipList($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
	 	$sql="select * from online_shipment_data a left join onliine_add_data b on a.onadd_sn = b.onadd_sn where onshda_status>=0 order by onshda_add_date desc, onshda_sn desc limit $offset, $rows";
	else
	 	$sql="select * from online_shipment_data a left join onliine_add_data b on a.onadd_sn = b.onadd_sn where onshda_status>=0 and ( $where ) order by onshda_add_date desc, onshda_sn desc limit $offset, $rows";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
	 	while($row = $qresult->fetch_assoc()) {
	  		$sql_getdata = "select distinct onproduct_isbought from onliine_product_data where onproduct_status>=0 and onproduct_part_no like '".$row['onadd_part_no']."'";
	  		$qresult2 = $conn->query($sql_getdata);
	  
	  		$row['onadd_plant_st'] = $qresult2->fetch_assoc()['onproduct_isbought'];
	  		$ret_data[] = $row;
	 	}
	}
	$conn->close();
	return $ret_data;
}

function getShipListBySn($onshda_sn) {
	$ret_data = array();
	$conn = getDB();

	$sql="SELECT b.onadd_buy_price,b.onadd_cur_size,b.onadd_sn,onshda_sn,onshda_add_date,onshda_real_price,b.onadd_part_no,b.onadd_part_name, a.onshda_real_revenue from online_shipment_data a left join onliine_add_data b on a.onadd_data_sn = b.onadd_sn where onshda_sn = '{$onshda_sn}' AND onshda_status>=0 order by onshda_add_date desc, onshda_sn desc";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
	 	while($row = $qresult->fetch_assoc()) {
	 		$row['onshda_add_date_formated'] = Date('m/d/Y',$row['onshda_add_date']);
	 		$sql2="SELECT onadd_planting_date FROM onliine_add_data 
	 			  WHERE onadd_sn = ".$row['onadd_sn'];

			$qresult2 = $conn->query($sql2);
			if ($qresult2->num_rows > 0) {
			 	while($row2 = $qresult2->fetch_assoc()) {
			 		$row['onadd_planting_date_formated'] = Date('m/d/Y',$row2['onadd_planting_date']);
			 		$row['TotalWeeks'] = datediffInWeeks(Date('m/d/Y',$row2['onadd_planting_date']), Date('m/d/Y',$row['onshda_add_date']));
			 		if ($row['TotalWeeks'] == 0) {
			 			$row['TotalWeeks'] = 1;
			 		}
			 	}
			 	$costdata = getCost($row['onadd_sn'],$row['onadd_cur_size']);
				// $row['CostBase'] = $row['TotalWeeks']*$costdata['onadd_cost_weeks']+$costdata['onadd_cost_base']+$row['onadd_buy_price'];
			 	$row['CostBase'] = $row['TotalWeeks']*$costdata['onadd_cost_weeks']+$costdata['onadd_cost_base'];
			}

	  		$ret_data = $row;
	 	}
	}
	$conn->close();
	return $ret_data;
}

// 計算自種成本
function getCost($onadd_sn="",$onadd_cur_size='') {
	$ret_data = array();
	$conn = getDB();	
	$sql_onadd_cost_float = "SELECT b.oncoda_cost AS cost_month FROM onliine_add_data a LEFT JOIN online_cost_data b ON a.onadd_cur_size = b.oncoda_cost_size WHERE b.oncoda_cost_status = 1  AND a.onadd_sn = {$onadd_sn} ORDER BY a.`onadd_add_date` DESC";
	$sql_onadd_cost_base = "SELECT SUM(b.oncoda_cost) AS onadd_cost_base FROM onliine_add_data a LEFT JOIN online_cost_data b ON a.onadd_cur_size = b.oncoda_cost_size WHERE b.oncoda_cost_status = 0  AND a.onadd_sn = {$onadd_sn} ORDER BY a.`onadd_add_date` DESC";
	$qresult1 = $conn->query($sql_onadd_cost_float);
	$qresult2 = $conn->query($sql_onadd_cost_base);
	$onadd_cost_weeks = round($qresult1->fetch_assoc()['cost_month']/4,2);
	$onadd_cost_base = round($qresult2->fetch_assoc()['onadd_cost_base'],2);
	$ret_data['onadd_cost_weeks'] = $onadd_cost_weeks;
	$ret_data['onadd_cost_base'] = $onadd_cost_base;

	return $ret_data;
}

function datediffInWeeks($date1, $date2)
{
    if($date1 > $date2) return datediffInWeeks($date2, $date1);
    $first = DateTime::createFromFormat('m/d/Y', $date1);
    $second = DateTime::createFromFormat('m/d/Y', $date2);
    return floor($first->diff($second)->days/7);
}

function getUserQty($where='') {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
	 	$sql="select * from online_shipment_data a where onshda_status>=0 order by onshda_add_date desc, onshda_sn desc";
	else
	 	$sql="select * from online_shipment_data a where onshda_status>=0 and ( $where ) order by onshda_add_date desc, onshda_sn desc";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
	 	while($row = $qresult->fetch_assoc()) {
	  		$sql_getdata = "select distinct onproduct_isbought from onliine_product_data where onproduct_status>=0 and onproduct_part_no like '".$row['onadd_part_no']."'";
	  		$qresult2 = $conn->query($sql_getdata);
	  
	  		$row['onadd_plant_st'] = $qresult2->fetch_assoc()['onproduct_isbought'];
	  		$ret_data[] = $row;
	 	}
	}
	$conn->close();
	return count($ret_data);
}

function getUserBySn($onshda_sn) {
	$ret_data = array();
	$conn = getDB();
	$sql="select * from online_shipment_data where onshda_sn='{$onshda_sn}'";

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
	$sql="select * from online_shipment_data where onadd_part_no='{$account}'";

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

function getShipList_forExcel2($where='', $offset=30, $rows=0) {
 $ret_data = array();
 $conn = getDB();
 if(empty($where))
  $sql="SELECT a.onshda_sn,a.onshda_real_price,b.onadd_level,b.onadd_newpot_sn,b.onadd_type,b.onadd_ml ,a.onadd_sn,a.onadd_data_sn,a.onadd_part_no,a.onadd_part_name,a.onshda_add_date,a.onshda_quantity,a.onshda_price,a.onshda_client,b.onadd_cur_size,b.onadd_cost_month,a.total_cost_shipment,a.onadd_other_price,b.onadd_buy_price,b.onadd_planting_date,a.onshda_real_revenue
   FROM online_shipment_data a 
   left join onliine_add_data b on a.onadd_data_sn = b.onadd_sn
   where a.onshda_status = 1 and b.onadd_type = 0 GROUP by a.onshda_sn order by a.onshda_add_date desc";
 else
  $sql="SELECT a.onshda_sn,a.onshda_real_price,b.onadd_level,b.onadd_newpot_sn,b.onadd_type,b.onadd_ml ,a.onadd_sn,a.onadd_data_sn,a.onadd_part_no,a.onadd_part_name,a.onshda_add_date,a.onshda_quantity,a.onshda_price,a.onshda_client,b.onadd_cur_size,b.onadd_cost_month,a.total_cost_shipment,a.onadd_other_price,b.onadd_buy_price ,b.onadd_planting_date,a.onshda_real_revenue
   FROM online_shipment_data a 
   left join onliine_add_data b on a.onadd_data_sn = b.onadd_sn
   where a.onshda_status = 1 and b.onadd_type = 0 and ( $where ) GROUP by a.onshda_sn order by a.onshda_add_date desc";

 $qresult = $conn->query($sql);
 if ($qresult->num_rows > 0) {
  while($row = $qresult->fetch_assoc()) {
   $sql_getdata = "select distinct onproduct_isbought from onliine_product_data where onproduct_status>=0 and onproduct_part_no like '".$row['onadd_part_no']."'";
   $sql_onadd_cost_month = "SELECT c.oncoda_cost as onadd_cost_month FROM online_shipment_data a left join onliine_add_data b on a.onadd_data_sn = b.onadd_sn left join online_cost_data c on b.onadd_cur_size = c.oncoda_cost_size where c.oncoda_cost_status = 1 and a.onshda_status = 1 GROUP by a.onshda_sn order by a.onshda_add_date desc";
   $qresult2 = $conn->query($sql_getdata);
   $row['onadd_plant_st'] = $qresult2->fetch_assoc()['onproduct_isbought'];   
   $qresult3 = $conn->query($sql_onadd_cost_month); 
   $row['onadd_cost_month'] = $qresult3->fetch_assoc()['onadd_cost_month'];
   $row['date_month'] = ($row['a.onshda_add_date'] - $row['a.onadd_planting_date'])/60/60/24/30+1;   
   $get_real_price = "SELECT a.onshda_real_price
    FROM online_shipment_data a 
    left join onliine_add_data b on a.onadd_sn = b.onadd_sn
    where  a.onshda_status = 1 and b.onadd_sn = {$row['onadd_newpot_sn']}";
   $row['real_price'] = $conn->query($get_real_price)->fetch_assoc()['onshda_real_price'];
   $ret_data[] = $row;
  }
 }

	foreach ($ret_data as $key => $value) {
		$sql_onadd_planting_date = "SELECT onadd_planting_date FROM onliine_add_data where onadd_sn='{$value['onadd_newpot_sn']}'";
		$onadd_planting_date = $conn->query($sql_onadd_planting_date)->fetch_assoc()['onadd_planting_date'];
		$until_now = ceil(($value['onshda_add_date']-$onadd_planting_date)/60/60/24/30);
		$ret_data[$key]['until_now_m'] = $until_now;
	}

 !isset($ret_data) ? $ret_data = array() : null;
  foreach ($ret_data as $key => $value) { 	
 	$ret_data[$key]['cost_money'] = $value['onadd_buy_price']; 	
 }
 $conn->close();
 return $ret_data;
}

function getShipList_forExcel($where='', $offset=30, $rows=0) {
 $ret_data = array();
 $conn = getDB();
 if(empty($where)){
  $sql="SELECT a.onshda_sn,a.onshda_real_price,b.onadd_cost_plant,b.onadd_price_per_plant, b.onadd_level,b.onadd_newpot_sn,b.onadd_type,b.onadd_ml ,a.onadd_sn,a.onadd_data_sn,a.onadd_part_no,a.onadd_part_name,a.onshda_add_date,a.onshda_quantity,a.onshda_price,a.onshda_client,b.onadd_cur_size,b.onadd_cost_month,a.total_cost_shipment,a.onadd_other_price,b.onadd_buy_price,b.onadd_planting_date,a.onshda_real_revenue
   FROM online_shipment_data a 
   left join onliine_add_data b on a.onadd_data_sn = b.onadd_sn
   left join online_cost_data c on b.onadd_cur_size = c.oncoda_cost_size 
   where c.oncoda_cost_status = 0 and a.onshda_status = 1 GROUP by a.onshda_sn order by a.onshda_add_date";
 }else{
  $sql="SELECT a.onshda_sn,a.onshda_real_price,b.onadd_cost_plant,b.onadd_price_per_plant, b.onadd_level,b.onadd_newpot_sn,b.onadd_type,b.onadd_ml ,a.onadd_sn,a.onadd_data_sn,a.onadd_part_no,a.onadd_part_name,a.onshda_add_date,a.onshda_quantity,a.onshda_price,a.onshda_client,b.onadd_cur_size,b.onadd_cost_month,a.total_cost_shipment,a.onadd_other_price,b.onadd_buy_price,b.onadd_planting_date,a.onshda_real_revenue	
   FROM online_shipment_data a 
   left join onliine_add_data b on a.onadd_data_sn = b.onadd_sn
   left join online_cost_data c on b.onadd_cur_size = c.oncoda_cost_size 
   where c.oncoda_cost_status = 0 and a.onshda_status = 1  and ( $where ) GROUP by a.onshda_sn order by a.onshda_add_date";
 }
 $qresult = $conn->query($sql);
 if ($qresult->num_rows > 0) {
  while($row = $qresult->fetch_assoc()) {
   $sql_getdata = "select distinct onproduct_isbought from onliine_product_data where onproduct_status>=0 and onproduct_part_no like '".$row['onadd_part_no']."'";
   if($row['onadd_newpot_sn'] == "0")
   	$row['onadd_newpot_sn'] = $row['onadd_sn'];
   $qresult2 = $conn->query($sql_getdata);
   $row['onadd_plant_st'] = $qresult2->fetch_assoc()['onproduct_isbought'];   
   $sql_get_first_data = "SELECT b.onadd_planting_date , a.onshda_real_price
    FROM online_shipment_data a 
    left join onliine_add_data b on a.onadd_sn = b.onadd_sn
    where  a.onshda_status = 1 and b.onadd_sn = {$row['onadd_newpot_sn']}";
   $get_first_data = $conn->query($sql_get_first_data)->fetch_assoc()['onadd_planting_date']; 
   $row['real_price'] = $conn->query($sql_get_first_data)->fetch_assoc()['onshda_real_price']; 
   $TotalWeeks = datediffInWeeks(Date('m/d/Y',$row['onshda_add_date']), Date('m/d/Y',$get_first_data));
   $row['TotalWeeks'] = $TotalWeeks;

   $ret_data[] = $row;
  }
 }
 $DEVICE_SYSTEM = array(
  1=>"1.7",
  10=>"2.0",
  2=>"2.5",
  3=>"2.8",
  4=>"3.0",
  5=>"3.5",
  6=>"3.6",
  7=>"其他"
  // 1.7, 2.5, 2.8, 3.0, 3.5, 3.6 其他
 );
 $basin_log_data = array();
 foreach ($ret_data as $key => $value) { 
  if ($value['onadd_type'] == 1) {   
   // 代工
   $sql_basin  =  "SELECT * FROM onliine_basin_log WHERE onadd_sn_after_basin = {$value['onadd_data_sn']} and onadd_sn = {$value['onadd_newpot_sn']}";
   $sql_basin_data = $conn->query($sql_basin);
   if ($sql_basin_data->num_rows > 0) {
    while($row_2 = $sql_basin_data->fetch_assoc()) {
     $basin_log_data[] = $row_2;
    }
    $sql_basin_data->free();
   }  
   if (!empty($basin_log_data)) {
    foreach ($basin_log_data as $key2 => $value2) {    
     $sql_basin2  =  "SELECT * FROM onliine_basin_log WHERE onadd_sn_after_basin = {$value2['onadd_sn_before_basin']} and onadd_sn = {$value['onadd_newpot_sn']}";
     $sql_basin_data = $conn->query($sql_basin2);
     if ($sql_basin_data->num_rows > 0) {
      while($row_3 = $sql_basin_data->fetch_assoc()) {
       $basin_log_data[] = $row_3;
      }
      $sql_basin_data->free();
     }
    }
   }
  }
 }

 $total_day = 0;
 foreach ($ret_data as $key => $value) {
  // 成本計算，代工
    $sql_onadd_planting_date = "SELECT onadd_planting_date FROM onliine_add_data where onadd_sn='{$value['onadd_newpot_sn']}'";    
    $onadd_planting_date = $conn->query($sql_onadd_planting_date)->fetch_assoc()['onadd_planting_date'];
    foreach ($basin_log_data as $key_2 => $value_2) {

     $sql_onadd_cur_size = "SELECT onadd_cur_size,onadd_growing FROM onliine_add_data where onadd_sn='{$value_2['onadd_sn_before_basin']}'";

     $qresult_size = $conn->query($sql_onadd_cur_size);
     if ($qresult_size->num_rows > 0) {
       while($row_size = $qresult_size->fetch_assoc()) {        
         $cur_size = $DEVICE_SYSTEM[$row_size['onadd_cur_size']];
         $growing_size =  $DEVICE_SYSTEM[$row_size['onadd_growing']];

       }
       $qresult_size->free();
      $onchba_cycle = getSettingBySn($cur_size,$growing_size)['onchba_cycle'];
      
      // 總種植區間天數
      $total_day += $onchba_cycle;
      // 總種植區間週數
      $total_week = ceil($total_day/7);
      // 迄今種植週數
      $until_now = ceil(($value['onshda_add_date']-$onadd_planting_date)/60/60/24/7);
      // 平均種植區間內的代工費
      $avg_foundry_price = round($value['onadd_buy_price']/ceil($total_day/7),2);

      $ret_data[$key]['total_day'] = $total_day;
      $ret_data[$key]['total_week'] = $total_week;
      $ret_data[$key]['avg_foundry_price'] = $avg_foundry_price;
      $ret_data[$key]['until_now'] = $until_now;
      // 若超出種植區間所產生的費用
      if ($total_week-$until_now) {
       $ret_data[$key]['over_cost'] = abs(($total_week-$until_now)*$avg_foundry_price);
      }else{
       $ret_data[$key]['over_cost'] = "0";
      }     
     }
    }    
 }
 !isset($ret_data) ? $ret_data = array() : null;
 // $data = array_merge($ret_data, $ret_data2);
 // printr($value['onadd_cost_plant']);
 // exit;
 if(round($value['onadd_cost_plant'],2) != 0)
 	$ret_data[$key]['cost_money'] = round($value['onadd_cost_plant'],2);
 	
 $conn->close();
 return $ret_data;
}

function getSettingBySn($onchba_size,$onchba_tsize) {
	$ret_data = array();
	$conn = getDB();
	$sql="select * from online_change_basin where onchba_size like '{$onchba_size}' and onchba_tsize like '{$onchba_tsize}'";
	// echo "sql=".$sql."<br>";
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

//取得所有已出貨客戶
function getAlreadySoldCustomer() {
	$ret_data = array();
	$conn = getDB();
	$sql="SELECT onshda_client FROM `online_shipment_data` GROUP BY onshda_client";
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while ($row = $qresult->fetch_assoc()) {
			$ret_data[] = $row['onshda_client'];
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

//客戶管理紀錄
function getCustomerSoldLog($where) {
	$ret_data = array();
	$conn = getDB();
	if($where != ''){
		$sql="SELECT * FROM `online_shipment_data` WHERE {$where}";
	}
	else{
		$sql="SELECT * FROM `online_shipment_data`";
	}
	// printr($sql);
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while ($row = $qresult->fetch_assoc()) {
			$row['livability'] = getLivability($row['onadd_data_sn']);
			$ret_data[$row['onshda_client']][] = $row;
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

//取得所有供應商
function getAllSupplier() {
	$ret_data = array();
	$conn = getDB();
	$sql="SELECT onadd_supplier FROM `onliine_add_data` GROUP BY onadd_supplier";
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while ($row = $qresult->fetch_assoc()) {
			if(!empty($row['onadd_supplier']))
				$ret_data[] = $row['onadd_supplier'];
			else
				$ret_data[] = '未輸入';
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

//供應商管理紀錄
function getSupplierBoughtLog($where) {
	$DEVICE_SYSTEM = array(
		1=>"1.7",
		10=>"2.0",
		2=>"2.5",
		3=>"2.8",
		4=>"3.0",
		5=>"3.5",
		6=>"3.6",
		7=>"其他"
		// 1.7, 2.5, 2.8, 3.0, 3.5, 3.6 其他
	);
	$ret_data = array();
	$conn = getDB();
	if($where != ''){
		$sql="SELECT onadd_add_date,onadd_part_no,onadd_part_name,onadd_quantity,onadd_sn,onadd_supplier,onadd_type,onadd_sellsize FROM `onliine_add_data` WHERE onadd_plant_st = 1 AND {$where}";
	}
	else{
		$sql="SELECT onadd_add_date,onadd_part_no,onadd_part_name,onadd_quantity,onadd_sn,onadd_supplier,onadd_type,onadd_sellsize FROM `onliine_add_data` WHERE onadd_plant_st = 1";
	}
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while ($row = $qresult->fetch_assoc()) {
			if(!empty($row['onadd_supplier'])){
				$row['livability'] = getLivability($row['onadd_sn']);
				switch ($row['onadd_type']) {
					case '0':
						$row['onadd_type'] = "自種";
						break;
					case '1':
						$row['onadd_type'] = "代工";
						break;
					case '2':
						$row['onadd_type'] = "委外代工";
						break;
				}
				$row['onadd_type'] .= "(".$DEVICE_SYSTEM[$row['onadd_sellsize']]."寸出貨)";
				$ret_data[$row['onadd_supplier']][] = $row;
			}
			else
				$ret_data['未輸入'][] = $row;
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getOnAddDataBySn($onadd_sn) {
	$ret_data = array();
	$conn = getDB();
	$sql="select onadd_level,onadd_AB_sn,onadd_sn,onadd_newpot_sn,onadd_ml from onliine_add_data where onadd_sn='{$onadd_sn}'";
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

function getProductAllNowQty($onadd_sn,$onadd_level) {
	$ret_data = array();
	$conn = getDB();	
	$sql="select onadd_level,SUM(onadd_quantity) as now_total from onliine_add_data where onadd_status>=1 and onadd_sn like '$onadd_sn' or onadd_newpot_sn like '$onadd_sn' or onadd_ml like '$onadd_sn' or onadd_AB_sn like '$onadd_sn' GROUP BY onadd_level";
	$sql2="select onadd_level,onshda_quantity as ship_total from online_shipment_data a left join onliine_add_data b on a.onadd_data_sn = b.onadd_sn where onshda_status>=1 and a.onadd_sn like '$onadd_sn'";
	// printr($sql);
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data[] = $row['now_total'];
		}
		$qresult->free();

		$temp = array();
		$qresult2 = $conn->query($sql2);
		if ($qresult2->num_rows > 0) {
			while($row2 = $qresult2->fetch_assoc()) {
				$temp[] = $row2;
			}
			$qresult2->free();			
		}

		$ship_total = array();
		foreach ($temp as $key => $value) {
			$ship_total[$value['onadd_level']] += $value['ship_total'];
		}
		
		$ret_data[0] += $ship_total[0];
		$ret_data[1] += $ship_total[1];
	}
	else{
		$ret_data = 1;
	}


	$conn->close();
	return $ret_data[$onadd_level];
}

//育成率 (公式 (數量-汰除)/數量)     
function getLivability($onadd_sn){
	$onadd_data = getOnAddDataBySn($onadd_sn);	
	if($onadd_data['onadd_AB_sn'] == 0){
		if($onadd_data['onadd_newpot_sn'] == 0){
	    	if($onadd_data['onadd_ml'] == 0){
	    		$sn = $onadd_data['onadd_sn'];
			}
			else{
				$sn = $onadd_data['onadd_ml'];
			}
		}
		else{
			$sn = $onadd_data['onadd_newpot_sn'];
		}	
	}
	else{
		$sn = $onadd_data['onadd_AB_sn'];
	}
	$first_plant_amount = (getProductFirstQty($sn) != 0 ? getProductFirstQty($sn) : 1);//第一次下種時間
	$incubation_rate = getProductAllNowQty($sn,$onadd_data['onadd_level'])/$first_plant_amount;
	// printr("first_plant_amount=".$first_plant_amount);
	// printr("incubation_rate=".$incubation_rate);
	return number_format(($incubation_rate*100),2)."%";
}

?>