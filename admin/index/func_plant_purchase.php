<?php
include_once(dirname(__FILE__).'/../config.php');
$onchba_cycle = 0;
$GLOBALS['size'] = array(
		1=>"1.7",
		2=>"2.5",
		3=>"2.8",
		4=>"3.0",
		5=>"3.5",
		6=>"3.6",
		7=>"其他",
		8=>"瓶苗開瓶",
		9=>"出貨"
		// 1.7, 2.5, 2.8, 3.0, 3.5, 3.6 其他
);
//function--------------------------------------------------------------------------------------
function consolelog($php) {
	echo '<script>console.log('.$php.');</script>';
}

function getAllSupplierData() {
	$ret_data = array();
	$conn = getDB();

	$sql="SELECT * FROM `onliine_supplier_data` WHERE onsd_status >= 1";

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
function getUserByAccount() {
	$ret_data = array();
	$conn = getDB();
	$sql="select * from onliine_add_data where onadd_status>=0";

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

function getDetails($onadd_growing) {
	$ret_data = 0;
	$conn = getDB();
	if(empty($where))
		$sql="select SUM(onadd_quantity) from onliine_add_data where onadd_cur_size='$onadd_growing' GROUP BY onadd_growing";
	
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data += (int)$row['SUM(onadd_quantity)'];
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getSellQuantity($year) {
	$firday = strtotime($year."/01/1");
	$endday = strtotime($year."/12/31");
	$ret_data = array();
	$conn = getDB();
	
	$sql="SELECT FROM_UNIXTIME(onshda_add_date,'%m') as months,sum(onshda_quantity) as quantity FROM `online_shipment_data` WHERE onshda_add_date IN (SELECT onshda_add_date FROM `online_shipment_data` WHERE onshda_add_date >= {$firday} AND onshda_add_date <= {$endday})  group by months order by months";
	// echo $sql;

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$row['months'] = intval($row['months']);
			$ret_data[] = $row;
		}
		$qresult->free();
	}
	$all_months = array();

	for($i=1;$i<=12;$i++){		
		$all_months[$i]['months'] = $i;
		$all_months[$i]['quantity'] = 0;				
	}
	for($i=0;$i<count($ret_data);$i++){	
		$n = $ret_data[$i]['months'];
		$all_months[$n]['quantity'] = $ret_data[$i]['quantity'];	
	}

	$conn->close();
	return $all_months;
}

function getEliminationQuantity($year) {
	$firday = strtotime($year."/01/1");
	$endday = strtotime($year."/12/31");
	$ret_data = array();
	$conn = getDB();
	
	$sql="SELECT FROM_UNIXTIME(onelda_mod_date,'%m') as months,sum(onelda_quantity) as quantity FROM `online_elimination_data` WHERE onelda_mod_date IN (SELECT onelda_mod_date FROM `online_elimination_data` WHERE onelda_mod_date >= {$firday} AND onelda_mod_date <= {$endday})  group by months order by months";
	// echo $sql;

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$row['months'] = intval($row['months']);
			$ret_data[] = $row;
		}
		$qresult->free();
	}

	$all_months = array();

	for($i=1;$i<=12;$i++){		
		$all_months[$i]['months'] = $i;
		$all_months[$i]['quantity'] = 0;				
	}
	for($i=0;$i<count($ret_data);$i++){	
		$n = $ret_data[$i]['months'];
		$all_months[$n]['quantity'] = $ret_data[$i]['quantity'];	
	}

	$conn->close();
	return $all_months;
}

function getQuantity_Day($day='') {
	if($day != ''){
		$conn = getDB();
		$ret_data = array();
		$firday = strtotime("-7 days",strtotime($day." 00:00:00"));
		$endday = strtotime("-7 days",strtotime($day." 24:00:00"));
	
		for($i=0;$i<=7;$i++){
			$firday = strtotime("+".$i." days",strtotime($day." 00:00:00"));
			$endday = strtotime("+".$i." days",strtotime($day." 24:00:00"));
			$sql="SELECT sum(onadd_quantity) as add_quantity FROM `onliine_add_data` WHERE onadd_mod_date Between {$firday} AND {$endday}";
			$sql2="SELECT sum(onelda_quantity) as elda_quantity FROM `online_elimination_data` WHERE onelda_mod_date Between {$firday} AND {$endday}";
			$sql3="SELECT sum(onshda_quantity) as ship_quantity FROM `online_shipment_data` WHERE onshda_add_date Between {$firday} AND {$endday}";
		
			$qresult = $conn->query($sql);
			if ($qresult->num_rows > 0) {
				while($row = $qresult->fetch_assoc()) {
					if($row['add_quantity'] != ''){
						$ret_data[$i][0] = $row['add_quantity'];
					}
					else{
						$ret_data[$i][0] = '0';
					}				
					$ret_data[$i]['date1'] = date("Y-m-d",strtotime("-".$i." days",strtotime($day." 00:00:00")));
					$ret_data[$i]['date2'] = date("Ymd",strtotime("-".$i." days",strtotime($day." 00:00:00")));
				}
				$qresult->free();
			}
	
			$qresult = $conn->query($sql2);
			if ($qresult->num_rows > 0) {
				while($row = $qresult->fetch_assoc()) {
					if($row['elda_quantity'] != ''){
						$ret_data[$i][1] = $row['elda_quantity'];
					}
					else{
						$ret_data[$i][1] = '0';
					}
					$ret_data[$i]['date1'] = date("Y-m-d",strtotime("-".$i." days",strtotime($day." 00:00:00")));
					$ret_data[$i]['date2'] = date("Ymd",strtotime("-".$i." days",strtotime($day." 00:00:00")));
				}
				$qresult->free();
			}
	
			$qresult = $conn->query($sql3);
			if ($qresult->num_rows > 0) {
				while($row = $qresult->fetch_assoc()) {
					if($row['ship_quantity'] != ''){
						$ret_data[$i][2] = $row['ship_quantity'];
					}
					else{
						$ret_data[$i][2] = '0';
					}
					$ret_data[$i]['date1'] = date("Y-m-d",strtotime("-".$i." days",strtotime($day." 00:00:00")));
					$ret_data[$i]['date2'] = date("Ymd",strtotime("-".$i." days",strtotime($day." 00:00:00")));
				}
				$qresult->free();
			}
		}
	
		$conn->close();
	}
	return $ret_data;
}

function getHistogram($day) {
	$conn = getDB();
	// $firday = strtotime(date("Y/m/d",time())." 00:00:00");
	// $endday = strtotime(date("Y/m/d",time())." 24:00:00");
	$ret_data = array();
	$firday = strtotime("-7 days",strtotime($day." 00:00:00"));
	$endday = strtotime("-7 days",strtotime($day." 24:00:00"));

	for($i=0;$i<=7;$i++){
		$firday = strtotime("+".$i." days",strtotime($day." 00:00:00"));
		$endday = strtotime("+".$i." days",strtotime($day." 24:00:00"));
		$sql="SELECT sum(onadd_quantity) as add_quantity FROM `onliine_add_data` WHERE onadd_mod_date Between {$firday} AND {$endday}";
		$sql2="SELECT sum(onelda_quantity) as elda_quantity FROM `online_elimination_data` WHERE onelda_mod_date Between {$firday} AND {$endday}";
		$sql3="SELECT sum(onshda_quantity) as ship_quantity FROM `online_shipment_data` WHERE onshda_mod_date Between {$firday} AND {$endday}";
	
		$qresult = $conn->query($sql);
		if ($qresult->num_rows > 0) {
			while($row = $qresult->fetch_assoc()) {
				if($row['add_quantity'] != ''){
					$ret_data[$i][0] = $row['add_quantity'];
				}
				else{
					$ret_data[$i][0] = '0';
				}				
				$ret_data[$i]['date1'] = date("Y-m-d",strtotime("-".$i." days",strtotime($day." 00:00:00")));
				$ret_data[$i]['date2'] = date("Ymd",strtotime("-".$i." days",strtotime($day." 00:00:00")));
			}
			$qresult->free();
		}

		$qresult = $conn->query($sql2);
		if ($qresult->num_rows > 0) {
			while($row = $qresult->fetch_assoc()) {
				if($row['elda_quantity'] != ''){
					$ret_data[$i][1] = $row['elda_quantity'];
				}
				else{
					$ret_data[$i][1] = '0';
				}
				$ret_data[$i]['date1'] = date("Y-m-d",strtotime("-".$i." days",strtotime($day." 00:00:00")));
				$ret_data[$i]['date2'] = date("Ymd",strtotime("-".$i." days",strtotime($day." 00:00:00")));
			}
			$qresult->free();
		}

		$qresult = $conn->query($sql3);
		if ($qresult->num_rows > 0) {
			while($row = $qresult->fetch_assoc()) {
				if($row['ship_quantity'] != ''){
					$ret_data[$i][2] = $row['ship_quantity'];
				}
				else{
					$ret_data[$i][2] = '0';
				}
				$ret_data[$i]['date1'] = date("Y-m-d",strtotime("-".$i." days",strtotime($day." 00:00:00")));
				$ret_data[$i]['date2'] = date("Ymd",strtotime("-".$i." days",strtotime($day." 00:00:00")));
			}
			$qresult->free();
		}
	}

 
    for($i=0;$i<count($ret_data);$i++){
    	if($i<count($ret_data)-1){
    		$str1 .= "'".$ret_data[$i]['date1']."',";
    		$str2 .= "'".$ret_data[$i]['date2']."',";
    		$str3 .= "'".$ret_data[$i][0]."',";
    		$str4 .= "'".$ret_data[$i][2]."',";
    		$str5 .= "'".$ret_data[$i][1]."',";
	   	}
    	else{
    		$str1 .= "'".$ret_data[$i]['date1']."'";
    		$str2 .= "'".$ret_data[$i]['date2']."'";
    		$str3 .= "'".$ret_data[$i][0]."'";
    		$str4 .= "'".$ret_data[$i][2]."'";
    		$str5 .= "'".$ret_data[$i][1]."'";
    	}
    }
 //    echo "['x',".$str1."],";
	// echo "['x',".$str2."],";
	// echo "['下種',".$str3."],";
	// echo "['出貨',".$str4."],";
	// echo "['耗損',".$str5."],";
	$chart = "['x', $str1],['x', $str2],['下種',$str3],['出貨',$str4],['耗損',$str5]";

	$conn->close();
	return $chart;
}

function getUsedQuantity() {
	$conn = getDB();
	
	$sql="SELECT sum(onadd_quantity) as add_quantity FROM `onliine_add_data`";//園區全部的下種數量
	$sql2="SELECT sum(onelda_quantity) as elda_quantity FROM `online_elimination_data`";//園區全部的汰除數量
	$sql3="SELECT sum(onshda_quantity) as ship_quantity FROM `online_shipment_data`";//園區全部的出貨數量
	// echo $sql;

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data[0] = $row;
		}
		$qresult->free();
	}
	$qresult = $conn->query($sql2);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data[1] = $row;
		}
		$qresult->free();
	}
	$qresult = $conn->query($sql3);
		if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data[2] = $row;
		}
		$qresult->free();
	}

	$conn->close();
	return $ret_data;
}


function getSpace() {
	$ret_data = array();
	$conn = getDB();
	$sql="SELECT * FROM `online_space`";

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

function getSettingBySn($onchba_size,$onchba_tsize) {
	$ret_data = array();
	$conn = getDB();
	$sql="select * from online_change_basin where onchba_size='{$onchba_size}' and onchba_tsize='{$onchba_tsize}'";
	// echo $sql."<br>";
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

function getScheduleData() {
	$ret_data = array();
	$conn = getDB();
	$sql="SELECT count(*) as sum FROM `onliine_add_data` WHERE onadd_status >= 0 AND onadd_schedule = 1";
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while ($row = $qresult->fetch_assoc()) {
			$ret_data = $row;
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getWorkListByMonth() {
	$flag = false;
	$ret_data = array();
	$conn = getDB();
	$sql="select onadd_quantity, onadd_sn, onadd_part_no, onadd_part_name, onadd_cur_size, onadd_growing, onadd_planting_date  from onliine_add_data where onadd_status = 1";
	$mapping = getMapping_size();
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
				if($row['onadd_growing'] == '出貨') $row['onadd_growing']=9;
				$GLOBALS['onchba_cycle'] = getSettingBySn($GLOBALS['size'][$row['onadd_cur_size']],$GLOBALS['size'][$row['onadd_growing']])['onchba_cycle'];
	       		$row['onchba_cycle'] = $GLOBALS['onchba_cycle'];
	        	$test = date("Y/m/d", strtotime("+".$GLOBALS['onchba_cycle']." days", $row['onadd_planting_date']));
	        	$o_y = intval(date('Y',strtotime($test)));        	
	        	$c_y = intval(date('Y'));
	        	$o_m = intval(date('m',strtotime($test)));
	        	$c_m = intval(date('m'));
	        	$row['o_y'] = $o_y;
	        	$row['c_y'] = $c_y;
	        	$row['o_m'] = $o_m;
	        	$row['c_m'] = $c_m;
	        	if($row['onadd_growing'] == '出貨')
	        		$row['onadd_growing'] = 9;

	        	$row['isSell'] = $row['onadd_growing'];
	        	$row['onadd_growing'] = $mapping[$row['onadd_growing']];
	        	$row['onadd_sellsize'] = $GLOBALS['size'][$row['onadd_sellsize']];
	        	$days=strtotime(date('Y-m-d')." 23:59:59")-strtotime($test);

	        	if($days > 0 && $days < 604800){// 0:尚未達標 1:即將到達預估出貨日期(成熟期前7天開始提醒)  2:已超過可出貨日期
	        		$row['practicesell_status'] = 1;
	        	}
	        	else if($days < 86400){
	        		$row['practicesell_status'] = 2;
	        		$flag = true;
	        	}
	        	else{
	        		$row['practicesell_status'] = 0;
	        	}

	        	if($row['onadd_schedule'] > 0 || $flag){
		        	if($o_y <= $c_y && !empty($row['onchba_cycle'])){
		        		if($o_y == $c_y && $o_m <= $c_m){
		        			$row['onadd_planting_date'] = date("Y/m/d",$row['onadd_planting_date']);
		        			$row['expected_date'] = $test;
		        			$row['onadd_planting_date_unix'] = strtotime(date( "Y-m-d", strtotime("now")));
		        			$row['expected_date_unix'] = strtotime(date( "Y-m-d", strtotime($test)));
		        			$ret_data[] = $row;
		        		}
		        		else if($o_y < $c_y){
		        			$row['onadd_planting_date'] = date("Y/m/d",$row['onadd_planting_date']);
		        			$row['expected_date'] = $test;
		        			$row['onadd_planting_date_unix'] = strtotime(date( "Y-m-d", strtotime("now")));
		        			$row['expected_date_unix'] = strtotime(date( "Y-m-d", strtotime($test)));
		        			$ret_data[] = $row;
		        		}        		
		        	}
		        }
		}
		$qresult->free();
	}
	$conn->close();
	$ret_data[] = getScheduleData();
	return $ret_data;
}

function getMapping_size() {
	$ret_data = array();
	$conn = getDB();
	$sql="select onchba_sn,onchba_tsize from online_change_basin";
	// echo $sql;
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data[$row['onchba_sn']] = $row['onchba_tsize'];
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getTotalQty(){
	$location = array('A1','A2','A3','A4','A5','B1','B2','B3','B4','B5');
	$onadd_cur_size = array('1','2','3','4','5','6');
	$ret_data = array();
	$conn = getDB();

	for($i=0;$i<count($location);$i++){
		for($j=0;$j<count($onadd_cur_size);$j++){
			$sql="select SUM(onadd_quantity) from onliine_add_data where onadd_location like '".$location[$i]."' and onadd_cur_size = ".$onadd_cur_size[$j];
			$qresult = $conn->query($sql);
			if ($qresult->num_rows > 0) {
				if ($row = $qresult->fetch_assoc()) {
					$ret_data[$i]['location'] = $location[$i];
					if($row['SUM(onadd_quantity)'] != '')
						$ret_data[$i][$onadd_cur_size[$j]] = $row['SUM(onadd_quantity)'];
					else
						$ret_data[$i][$onadd_cur_size[$j]] = 0;
				}
				$qresult->free();
			}
		}
	}	
	$conn->close();
	return $ret_data;
}
//function--------------------------------------------------------------------------------------
?>