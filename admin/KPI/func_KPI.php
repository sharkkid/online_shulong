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
// kpi_planting_performance.php
//================================
function get_Staffs() {
	$ret_data = array();
	$conn = getDB();
	$sql="SELECT * FROM js_user WHERE jsuser_status = 1 AND jsuser_admin_permit > 0";
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

function get_Staff_name($onpp_plant_staff){
	$conn = getDB();
	$sql="SELECT jsuser_name FROM `js_user` WHERE `jsuser_sn` = $onpp_plant_staff ";
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data = $row['jsuser_name'];
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
	
}

function get_ppData($where="",$start_year="",$end_year=""){
	$ret_data = array();
	$ALL_data = array(1=>"", 2=>"", 3=>"", 4=>"", 5=>"", 6=>"", 7=>"", 8=>"", 9=>"", 10=>"", 11=>"", 12=>"");
	$conn = getDB();
	$thisYear = date("yy",time());
	$year_data = array();
	if (!empty($where)) {
		$where = $where;		
	}else{
		$where = 'b.onpp_year = '.$thisYear;
	}
	
	$sql = "SELECT b.*,a.onadd_sn,a.onadd_quantity,a.onadd_cur_size, FROM_UNIXTIME(a.onadd_planting_date, '%Y/%m') AS onadd_planting_date
		FROM
		   onliine_add_data a
		LEFT JOIN  online_planting_performance b ON
		    b.onpp_plant_staff = a.onadd_plant_staff
		    and FROM_UNIXTIME(a.onadd_planting_date, '%m') = b.onpp_month
		    and FROM_UNIXTIME(a.onadd_planting_date, '%Y') = b.onpp_year
		WHERE
		    $where
		ORDER BY
		    a.onadd_planting_date";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data[] = $row;
		}
		$qresult->free();
	}	
	
	if (!empty($start_year)) {
		$Ywhere .= " onpp_year BETWEEN ".$start_year;
	}
	if (!empty($end_year)) {
		$Ywhere .= " and ".$end_year;
	}
	$sql_years = "SELECT distinct onpp_year FROM `online_planting_performance` WHERE $Ywhere GROUP by onpp_year";	
	$qresult = $conn->query($sql_years);
	$years = array();
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$years[] = $row['onpp_year'];
		}
		$qresult->free();
	}
	foreach ($years as $y_key => $y_value) {
		$year_data[$y_value] = $ALL_data;

		foreach ($ALL_data as $ALL_key => $ALL_value) { 
			foreach ($ret_data as $key => $value) {	
				if ($ALL_key == $value['onpp_month'] &&  $value['onpp_year'] == $y_value) {
					$year_data[$y_value][$ALL_key][] = $value;
				}else{
					// $year_data[$y_value][$i] = "";
				}
			}
		}
	}
	$conn->close();

	// printr($sql);printr($year_data);
	// exit;
	return $year_data;
}

// kpi_cultivation_performance.php
function getCPstaff($plant_staff,$start_year,$start_month,$end_year,$end_month,$oncp_up_to_standard){
	$ret_data = array();
	$conn = getDB();
	$search_where['oncp_date'] = " oncp_date BETWEEN '".strtotime($start_year."-".$start_month)."' and '" .strtotime($end_year."-".$end_month)."'";
	// $search_where['oncp_month'] = " oncp_month BETWEEN ".$start_month." and ".$end_month;
	if($plant_staff != -1)
		$search_where['oncp_plant_staff'] = " oncp_plant_staff IN ( ".$plant_staff.") ";
	if ($oncp_up_to_standard != -1) {
		$search_where['oncp_up_to_standard'] = " oncp_up_to_standard = ".$oncp_up_to_standard;
	}	
	$search_where = isset($search_where) ? implode(' and ', $search_where) : '';
	$sql = "SELECT * FROM `online_cultivation_performance` WHERE $search_where";
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data[] = $row;
		}
		$qresult->free();
	}	
	return $ret_data;
}

function get_cpData($staff,$oncp_location,$start_year,$end_year,$start_month,$end_month){
	$ret_data = array();
	$ALL_data = array(1=>"", 2=>"", 3=>"", 4=>"", 5=>"", 6=>"", 7=>"", 8=>"", 9=>"", 10=>"", 11=>"", 12=>"");
	$conn = getDB();
	$thisYear = date("Y",time());
	$year_data = array();
	$timewhere_oncp = " oncp_date BETWEEN '".strtotime($start_year."-".$start_month)."' and '" .strtotime($end_year."-".$end_month)."'";
	$timewhere_onadd =  " and onadd_planting_date  BETWEEN '".strtotime($start_year."-".$start_month)."' and '" .strtotime($end_year."-".$end_month)."'";
	
	$sql = "SELECT
    			a.*,
			    sum(b.onadd_quantity) as onadd_quantity,
			    sum(c.onelda_quantity) as onelda_quantity
			FROM
			    `online_cultivation_performance` a
			LEFT JOIN onliine_add_data b ON
			    b.onadd_plant_staff = a.oncp_plant_staff AND b.onadd_location = a.oncp_location
			LEFT JOIN online_elimination_data c ON
			    b.onadd_sn = c.onadd_sn
			WHERE
			    a.oncp_plant_staff = $staff AND
			    a.oncp_location = '{$oncp_location}' AND
			    $timewhere_oncp 
			    $timewhere_onadd
			GROUP BY
			    a.oncp_location,
			    a.oncp_plant_staff;";
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data[] = $row;
		}
		$qresult->free();
	}	

	
	$sql_years = "SELECT distinct oncp_year FROM `online_cultivation_performance` WHERE $timewhere_oncp GROUP by oncp_year";

	$qresult = $conn->query($sql_years);

	$years = array();

	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$years[$row['oncp_year']] = "";
		}
		$qresult->free();
	}
	foreach ($years as $years_key => $years_value) {
		for ($i=1; $i <= 12; $i++) { 
			foreach ($ret_data as $key => $value) {	
				if ($years_key == $value['oncp_year'] && $i == $value['oncp_month']) {
					$year_data[$years_key][$i] = $value;
				}
			}
		}
	}

	$conn->close();
	return $year_data;
}

// kpi_sales_performance.php

function get_spData($where="",$start_year="",$end_year=""){
	$ret_data = array();
	$ALL_data = array(1=>"", 2=>"", 3=>"", 4=>"", 5=>"", 6=>"", 7=>"", 8=>"", 9=>"", 10=>"", 11=>"", 12=>"");
	$conn = getDB();
	$thisYear = date("yy",time());
	$year_data = array();
	if (!empty($where)) {
		$where = $where;		
	}else{
		$where = 'b.onsp_year = '.$thisYear;
	}
	
	$sql = "SELECT b.*,a.onbd_sn,a.onbd_quantity,a.onbd_sell_price, FROM_UNIXTIME(a.onbd_sell_date, '%Y/%m') AS onbd_sell_date
		FROM
		   onliine_bill_data a
		LEFT JOIN  online_sales_performance b ON
		    b.onsp_sales_staff = a.onbd_seller
		    and FROM_UNIXTIME(a.onbd_sell_date, '%m') = b.onsp_month
		    and FROM_UNIXTIME(a.onbd_sell_date, '%Y') = b.onsp_year
		WHERE
		    $where
		ORDER BY
		    a.onbd_sell_date,onsp_sales_staff";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data[] = $row;
		}
		$qresult->free();
	}	
	
	if (!empty($start_year)) {
		$Ywhere .= " onsp_year BETWEEN ".$start_year;
	}
	if (!empty($end_year)) {
		$Ywhere .= " and ".$end_year;
	}

	$sql_years = "SELECT distinct onsp_year FROM `online_sales_performance` WHERE $Ywhere GROUP by onsp_year";

	
	$qresult = $conn->query($sql_years);
	$years = array();
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$years[] = $row['onsp_year'];
		}
		$qresult->free();
	}
	$Staffs = get_Staffs();
	foreach ($years as $y_key => $y_value) {
		$year_data[$y_value] = $ALL_data;
		foreach ($ALL_data as $ALL_key => $ALL_value) { 
			foreach ($ret_data as $key => $value) {	
				if ($ALL_key == $value['onsp_month'] &&  $value['onsp_year'] == $y_value) {
					foreach ($Staffs as $Staffs_key => $Staffs_value) {
						if ($Staffs_value['jsuser_sn'] == $value['onsp_sales_staff']) {
							$year_data[$y_value][$ALL_key][$value['onsp_sales_staff']][] = $value;
						}
					}
				}
			}
		}
	}
	$new_yaer_data = array();
	foreach ($year_data as $year_key => $year_value) {
		foreach ($year_value as $year_staff_key => $year_staff_value) {
			if (!empty($year_staff_value)){
				foreach ($year_staff_value as $year_month_key => $year_month_value) {
					$new_yaer_data[$year_key][$year_staff_key][$year_month_key]['actually_order'] = count($year_month_value);
					$status = array();
					foreach ($year_month_value as $year_each_key => $year_each_value) {
						$new_yaer_data[$year_key][$year_staff_key][$year_month_key]['actually_unmber'] += $year_each_value['onbd_quantity']*$year_each_value['onbd_sell_price'];
					}
					$new_yaer_data[$year_key][$year_staff_key][$year_month_key]['onsp_target_number'] = $year_each_value['onsp_target_number'];
					$new_yaer_data[$year_key][$year_staff_key][$year_month_key]['onsp_target_order'] = $year_each_value['onsp_target_order'];
					$actually_percent = round(100*(count($year_month_value)/$year_each_value['onsp_target_order']),2);
					$new_yaer_data[$year_key][$year_staff_key][$year_month_key]['actually_percent'] = $actually_percent."％";
					if ($actually_percent >= 100) {
						$sales_status = 1;
					}else{
						$sales_status = 2;
					}
					$new_yaer_data[$year_key][$year_staff_key][$year_month_key]['status'] = $sales_status;
					
				}
			}
		}
	}
	$conn->close();
	// printr($sql);printr($new_yaer_data);
	// exit;
	return $new_yaer_data;
}

?>