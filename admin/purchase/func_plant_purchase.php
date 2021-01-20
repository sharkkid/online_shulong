<?php
include_once(dirname(__FILE__).'/../config.php');
include_once("../../app/line/func.php");

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
function getPlantData($where = '', $offset = 30, $rows = 0) {
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
	if(empty($where)){
		$sql="select * from onliine_add_data where onadd_status > 0 and onadd_plant_st=1 order by onadd_add_date desc, onadd_sn desc limit $offset, $rows";
	}
	else{
		$sql="select * from onliine_add_data where onadd_status > 0 and onadd_plant_st=1 and ( $where ) order by onadd_add_date desc, onadd_sn desc limit $offset, $rows";
	}
	$qresult = $conn->query($sql);
	$AllQuantityBySize = getAllQuantityBySize();
	if ($qresult->num_rows > 0) {
		$a = 0;
		while($row = $qresult->fetch_assoc()) {
			$ret_data[$a] = $row;
			if ($row['onadd_cur_size'] == 1) {
				$ret_data[$a]['HowManyWeeksPlant'] = HowManyWeeksPlant(Date('m/d/Y',GetFirstPlantDay($row['onadd_sn'])),Date('m/d/Y',time()));
			}else{
				$ret_data[$a]['HowManyWeeksPlant'] = HowManyWeeksPlant(Date('m/d/Y',$row['onadd_planting_date']),Date('m/d/Y',time()));
			}
			$costdata = getCost($row['onadd_sn'],$row['onadd_cur_size']);
			$ret_data[$a]['CostBase'] = $ret_data[$a]['HowManyWeeksPlant']*$costdata['onadd_cost_weeks']+$costdata['onadd_cost_base']+$row['onadd_basin_cost'];

			$cur_size = $DEVICE_SYSTEM[$row['onadd_cur_size']];
        	if($row['onadd_next_status'] == 1){
	        	$growing_size = $DEVICE_SYSTEM[$row['onadd_growing']];
	        }
	        else if($row['onadd_next_status'] == 2){
	        	$growing_size = "催花";
	        }
	        else if($row['onadd_next_status'] == 3 || strpos($row['onadd_growing'],'出貨') !== false){
	        	$growing_size = "出貨";
	        }
	        
        	$onchba_cycle = getSettingBySn($cur_size,$growing_size)['onchba_cycle'];
        	$ret_data[$a]['pre_days'] = date("Y/m/d", strtotime("+$onchba_cycle days", $row['onadd_planting_date']));

			$ret_data[$a]['total_month_list'] = getMonthBetweenTwoDate(Date('m/d/Y',$row['onadd_planting_date']),Date('m/d/Y',time()));
			$ret_data[$a]['total_cost'] = getCostBySizeWeight($row['onadd_cur_size'],$ret_data[$a]['total_month_list'],$AllQuantityBySize);
			// $ret_data[$a]['total_month_list_start'] = Date('m/d/Y',$row['onadd_planting_date']);
			// $ret_data[$a]['total_month_list_end'] = Date('m/d/Y',time());

			$a++;
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

//計算每月各尺寸成本權重
function getCostBySizeWeight($onadd_cur_size,$total_month_list,$AllQuantityBySize){
	$month_cost = getMonthCost();
	$amount_cost = 0;
	$weight = 0;
	if(!isset($ret_data[$a]['total_month_list'])){
		foreach ($total_month_list as $key => $value) {
			$amount_cost += $month_cost[$value];
		}
		switch ($onadd_cur_size) {
			case 1:
				$weight = 0.2;				
				break;
			
			case 2:
				$weight = 0.35;
				break;

			case 6:
				$weight = 0.45;
				break;	
		}
	}
	if($AllQuantityBySize[$onadd_cur_size] == 0)
		$AllQuantityBySize[$onadd_cur_size] = 1;
	return round(($amount_cost*$weight)/$AllQuantityBySize[$onadd_cur_size],2);
}


// 計算自種成本
function getCost($onadd_sn="",$onadd_cur_size='') {
	$ret_data = array();
	$conn = getDB();	
	$sql_onadd_cost_float = "SELECT b.oncoda_cost AS cost_month FROM onliine_add_data a LEFT JOIN online_cost_data b ON a.onadd_cur_size = b.oncoda_cost_size WHERE b.oncoda_cost_status = 1 AND a.onadd_status = 1 AND a.onadd_sn = {$onadd_sn} ORDER BY a.`onadd_add_date` DESC";
	$sql_onadd_cost_base = "SELECT SUM(b.oncoda_cost) AS onadd_cost_base FROM onliine_add_data a LEFT JOIN online_cost_data b ON a.onadd_cur_size = b.oncoda_cost_size WHERE b.oncoda_cost_status = 0 AND a.onadd_status = 1 AND a.onadd_sn = {$onadd_sn} ORDER BY a.`onadd_add_date` DESC";
	$qresult1 = $conn->query($sql_onadd_cost_float);
	$qresult2 = $conn->query($sql_onadd_cost_base);
	$onadd_cost_weeks = round($qresult1->fetch_assoc()['cost_month']/4,2);
	$onadd_cost_base = round($qresult2->fetch_assoc()['onadd_cost_base'],2);
	$ret_data['onadd_cost_weeks'] = $onadd_cost_weeks;
	$ret_data['onadd_cost_base'] = $onadd_cost_base;

	return $ret_data; 	
}

function getUseradd($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select * from onliine_add_data where onadd_status>=0 and onadd_plant_st=1 GROUP BY onadd_part_no";
	else
		$sql="select * from onliine_add_data where onadd_status>=0 and onadd_plant_st=1 GROUP BY onadd_part_no";

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

function getLatestOnaddSn($onadd_part_no,$planting_n) {
	$ret_data = "";
	$conn = getDB();

	$sql="select onadd_sn from onliine_add_data where onadd_status>=0 and onadd_plant_st=1 GROUP BY onadd_sn DESC limit 0,1";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data = $row['onadd_sn'];
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function IsfirtPlant($onadd_sn) {
	$ret_data = "0";
	$conn = getDB();
	$sql="select onadd_sn from onliine_firstplant_data  where onadd_sn = {$onadd_sn} and onfp_status >= 1";
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		$ret_data = "1";
	}
	$conn->close();
	return $ret_data;
}

function getProducts($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select * from  onliine_product_data where onproduct_status>=0 and onproduct_plant_st = 1 GROUP BY onproduct_part_no limit $offset, $rows";
	else
		$sql="select * from  onliine_product_data where onproduct_status>=0 and onproduct_plant_st = 1 and $where GROUP BY onproduct_part_no limit $offset, $rows";
	// echo $sql;
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

function getProductsQty($where='') {
	$ret_data = 0;
	$conn = getDB();
	if(empty($where))
		$sql="select count(DISTINCT onproduct_part_no) as count from onliine_product_data where onproduct_status>=0 AND onproduct_plant_st = 1";
	else
		$sql="select count(DISTINCT onproduct_part_no) as count from onliine_product_data where onproduct_status>=0 AND onproduct_plant_st = 1 AND $where";
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data = $row['count'];
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getAllProductsNo() {
	$ret_data = array();
	$conn = getDB();

	$sql="select onproduct_part_no,onproduct_part_name from  onliine_product_data where onproduct_status>=0";
	// echo $sql;
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data[0][] = $row['onproduct_part_no'];
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

//flag 0:下種 1:出貨 2:汰除 3:換盆
function getHistory_List($onadd_sn) {
	$ret_data = array();
	$conn = getDB();

	$sql="select onadd_add_date as add_date,onadd_planting_date as mod_date,onadd_quantity as quantity,onadd_quantity_cha from onliine_add_data where onadd_status>=0 and onadd_sn like '$onadd_sn' or onadd_newpot_sn like '$onadd_sn' order by onadd_add_date";
	// echo $sql;
	$qresult = $conn->query($sql);

	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			if(!empty($row['onadd_quantity_cha'])){
				$row['flag'] = 3;	
				$row['flag'] = 0;			
			}
			$ret_data[] = $row;
		}
		$qresult->free();
	}

	$sql="select onshda_add_date as add_date,onshda_mod_date as mod_date,onshda_quantity as quantity from online_shipment_data where onshda_status>=0 and onadd_sn like '$onadd_sn' or onadd_newpot_sn like '$onadd_sn'";

	$qresult = $conn->query($sql);
	
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$row['flag'] = 1;
			$ret_data[] = $row;
		}
		$qresult->free();
	}

	$sql="select onelda_add_date as add_date,onelda_mod_date as mod_date,onelda_quantity as quantity from online_elimination_data where onelda_status>=0 and onadd_sn like '$onadd_sn'";

		$qresult = $conn->query($sql);
		
		if ($qresult->num_rows > 0) {
			while($row = $qresult->fetch_assoc()) {
				$row['flag'] = 2;
				$ret_data[] = $row;
			}
			$qresult->free();
		}

	//排序日期
	$num = count($ret_data);
    //只是做迴圈
    for($i = 0 ; $i < $num ; $i++){
        //從最後一個數字往上比較，如果比較小就交換
        for($j = $num-1 ; $j > $k ; $j--){
            if($ret_data[$j]['add_date'] < $ret_data[$j-1]['add_date']){
                //交換兩個數值的小技巧，用list+each
	            list($ret_data[$j]['add_date'] , $ret_data[$j-1]['add_date']) = array($ret_data[$j-1]['add_date'] , $ret_data[$j]['add_date']);
	            list($ret_data[$j]['mod_date'] , $ret_data[$j-1]['mod_date']) = array($ret_data[$j-1]['mod_date'] , $ret_data[$j]['mod_date']);
	            list($ret_data[$j]['quantity'] , $ret_data[$j-1]['quantity']) = array($ret_data[$j-1]['quantity'] , $ret_data[$j]['quantity']);
	            list($ret_data[$j]['flag'] , $ret_data[$j-1]['flag']) = array($ret_data[$j-1]['flag'] , $ret_data[$j]['flag']);
            }
        }
    }
    for($i = 0 ; $i < $num ; $i++){
        //從最後一個數字往上比較，如果比較小就交換    
        $ret_data[$i]['add_date'] = date('Y-m-d',$ret_data[$i]['add_date']);
        $ret_data[$i]['mod_date'] = date('Y-m-d',$ret_data[$i]['mod_date']);
    }


	$conn->close();
	return $ret_data;
}

function getProductImg($onadd_part_no,$onadd_part_name) {
	$ret_data = 0;
	$conn = getDB();
	$sql="select DISTINCT(onproduct_pic_url) from onliine_product_data where onproduct_status>=0 and onproduct_part_no like '$onadd_part_no' and onproduct_part_name like '$onadd_part_name'";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data = $row['onproduct_pic_url'];
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getPlantDataQty($where='') {
	$ret_data = 0;
	$conn = getDB();
	if(empty($where))
		$sql="select count(*) from onliine_add_data where onadd_plant_st=1 and onadd_status>=0";
	else
		$sql="select count(*) from onliine_add_data where onadd_plant_st=1 and onadd_status>=0 and ( $where )";

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
function getUserQtyadd($where='') {
	$ret_data = 0;
	$conn = getDB();
	if(empty($where))
		$sql="select count(*) from onliine_add_data where onadd_status>=0 and onadd_plant_st=1 GROUP BY onadd_part_no";
	else
		$sql="select count(*) from onliine_add_data where onadd_status>=0 and onadd_plant_st=1 GROUP BY onadd_part_no";

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

function getEliQtyBySn($onadd_sn) {
	$ret_data = 0;
	$conn = getDB();	
	$sql="SELECT sum(onelda_quantity) as qty FROM `online_elimination_data` where onelda_status>=1 and onadd_sn like '$onadd_sn'";
	// echo $sql;
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data = $row['qty'];
		}
		$qresult->free();
	}
	else{
		$ret_data = 0;
	}
	$conn->close();
	return $ret_data;
}

function getSupplierSn($onsd_name) {
	$ret_data = "";
	$conn = getDB();	
	$sql="SELECT onsd_sn FROM `onliine_supplier_data` where onsd_name like '$onsd_name'";
	// echo $sql;
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data = $row['onsd_sn'];
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getPlantDataSn($onadd_sn) {
	$ret_data = array();
	$conn = getDB();
	$sql="select * from onliine_add_data where onadd_sn='{$onadd_sn}' and onadd_plant_st = 1";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		if ($row = $qresult->fetch_assoc()) {
			$ret_data = $row;
			$ret_data['onadd_planting_date'] = date('Y-m-d',$ret_data['onadd_planting_date']);
			$ret_data['onadd_planting_date_unix'] = $row['onadd_planting_date'];
			$ret_data['onsd_sn'] = getSupplierSn($row['onadd_supplier']);
		}
		$qresult->free();
	}

	// 若是代工則算出總代工費用
	if ($ret_data['onadd_type'] == 2) {
		// $Day1 = strftime("%U", str2time($ret_data['onadd_planting_date']));
		// $Day2 = strftime("%U", time());
		// $weekday = ($Day2 - $Day1)+1;
		// $ret_data['onadd_weekday'] = $weekday;
		// $ret_data['onadd_total'] = $ret_data['onadd_buy_price'] * $weekday;

	}else{		
		if ($row['onadd_cur_size'] == 1) {
			$ret_data['onadd_weekday'] = HowManyWeeksPlant(Date('m/d/Y',GetFirstPlantDay($row['onadd_sn'])),Date('m/d/Y',time()));
		}else{
			$ret_data['onadd_weekday'] = HowManyWeeksPlant(Date('m/d/Y',$row['onadd_planting_date']),Date('m/d/Y',time()));
		}
		$costdata = getCost($row['onadd_sn'],$row['onadd_cur_size']);
		$ret_data['onadd_total'] = $ret_data['onadd_weekday']*$costdata['onadd_cost_weeks']+$costdata['onadd_cost_base']+$row['onadd_buy_price'];
	}
	$sql="SELECT * FROM `onliine_product_data` where onproduct_part_no='".$ret_data['onadd_part_no']."' and onproduct_part_name='".$ret_data['onadd_part_name']."'";
	$ret_data['sql'] = $sql;
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data['img_url'] = $row['onproduct_pic_url'];
			if($row['onproduct_pic_url'] == "")
				$ret_data['img_url'] = "./images/nopic.png";
		}
		$qresult->free();
	}
	// $ret_data['img_url'] = "123";
	$conn->close();
	return $ret_data;
}

function getSecondMonday(){
    $today = date("Y-m-d");
    $_dYear =  date("Y");
    $_dMonth =  date("m");
    // 取得這個月的 1 號
    $iThisMonthFirst = strtotime("{$_dYear}-{$_dMonth}-01");
    $dThisDay = date("w", $iThisMonthFirst);
    if ($dThisDay == '1') {
        $dSecondMonday = date("Y-m-d",  strtotime("+7 days",$iThisMonthFirst));
    } else {
        //不是星期一先回到星期日
        $iDays = 86400 * ($dThisDay * 1);
        //找出星期日的日期
        $iSunday = $iThisMonthFirst - $iDays;
        //星期日 + 8 天就是星期一
        $iThisMonthFirstMonday = $iSunday + (86400 * 8);
        $dSecondMonday = date("Y-m-d", $iThisMonthFirstMonday);
    }

    if($today == $dSecondMonday){
    	return 1;
    }else{
    	return 0;
    }    
}

function qr_download($onadd_sn) {
	$ret_data = array();
	$conn = getDB();
	$sql="select * from onliine_add_data where onadd_sn='{$onadd_sn}'";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		if ($row = $qresult->fetch_assoc()) {
			$ret_data = $row;
			$ret_data['onadd_planting_date'] = date('Y-m-d',$ret_data['onadd_planting_date']);
		}
		$qresult->free();
	}
	$conn->close();
	$img_data = getProductImg($ret_data['onadd_part_no'],$ret_data['onadd_part_name']);
	if(!empty($img_data)){
		if($ret_data['onadd_plant_st'] == "1")
			$ret_data['img_url'] = $img_data;
		else
			$ret_data['img_url'] = "./../../admin/purchase/".substr($img_data, 2);
	}
	else{
		if($ret_data['onadd_plant_st'] == "1")
			$ret_data['img_url'] = "./images/nopic.png";
		else
			$ret_data['img_url'] = "./../../admin/flask/images/nopic.png";
	}
	

	return $ret_data;
}

function getProductBySn($onproduct_sn) {
	$ret_data = array();
	$conn = getDB();
	$sql="select * from onliine_product_data where onproduct_sn='{$onproduct_sn}'";

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

function getProductByPartNo($onproduct_part_no) {
	$ret_data = array();
	$conn = getDB();
	$sql="select a.*,b.* from onliine_product_data as a
	left join onliine_add_data as b on a.onproduct_part_no =b.onadd_part_no
	WHERE onproduct_part_no='{$onproduct_part_no}'
	order by b.onadd_sn desc LIMIT 0,1";

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

function IsProductExit($onproduct_part_no,$onproduct_part_name) {
	$ret_data = "0";
	$conn = getDB();
	$sql="select * from onliine_product_data where onproduct_part_no='{$onproduct_part_no}' and onproduct_part_name='{$onproduct_part_name}'";
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		$ret_data = "1";
	}
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

function getTargetSize($onchba_size,$onchba_tsize) {
	$ret_data = "";
	$conn = getDB();

	$sql="SELECT onchba_sn FROM `online_change_basin` WHERE onchba_size like '{$onchba_size}' and onchba_tsize like '{$onchba_tsize}'";
	// echo $sql;
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		if ($row = $qresult->fetch_assoc()) {
			$ret_data = $row['onchba_sn'];
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}
function getPlantDataByAccount($account) {
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

function getBusinessData($onbuda_part_no,$onbuda_year) {
	$ret_data = array();
	$conn = getDB();
	$data_array = array();

	$sql="select * from onliine_business_data where onbuda_part_no='$onbuda_part_no' AND onbuda_year='$onbuda_year' ";
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data[] = $row;
			switch($row['onbuda_size']){
				case "1":
					$data_array[1][$row['onbuda_day']] += intval($row['onbuda_quantity']);
					$data_array[1]['size'] = 1;
					break;
				case "2":
					$data_array[2][$row['onbuda_day']] += intval($row['onbuda_quantity']);
					$data_array[2]['size'] = 2;
					break;
				case "3":
					$data_array[3][$row['onbuda_day']] += intval($row['onbuda_quantity']);
					$data_array[3]['size'] = 3;
					break;
				case "4":
					$data_array[4][$row['onbuda_day']] += intval($row['onbuda_quantity']);
					$data_array[4]['size'] = 4;
					break;
				case "5":
					$data_array[5][$row['onbuda_day']] += intval($row['onbuda_quantity']);
					$data_array[5]['size'] = 5;
					break;
				case "6":
					$data_array[6][$row['onbuda_day']] += intval($row['onbuda_quantity']);
					$data_array[6]['size'] = 6;
					break;
			}
		}
		$qresult->free();
	}
	$conn->close();
	return $data_array;
}

function getProductData($onproduct_sn) {
	$ret_data = array();
	$conn = getDB();
	
	$sql="select * from onliine_product_data where onproduct_sn='$onproduct_sn'";	
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

function getDataDetails($onproduct_part_no,$onproduct_part_name) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select * from onliine_product_data where onproduct_part_no='$onproduct_part_no' AND onproduct_part_name='$onproduct_part_name'";
	else
		$sql="select * from onliine_product_data where onproduct_part_no='$onproduct_part_no' AND onproduct_part_name='$onproduct_part_name'";

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

function getExpectedList($onbuda_part_no,$year,$month,$size) {
	$DEVICE_SYSTEM = array(
		1=>"1.7",
		2=>"2.5",
		3=>"2.8",
		4=>"3.0",
		5=>"3.5",
		6=>"3.6",
		7=>"其他"
	);
	$ret_data = array();
	$conn = getDB();
	$sql="SELECT * FROM `onliine_business_data` WHERE onbuda_part_no = '$onbuda_part_no' AND onbuda_day = $month AND onbuda_year = $year AND onbuda_size = $size";
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$row['onbuda_date'] = date('Y-m-d',$row['onbuda_date']);
			$row['onbuda_add_date'] = date('Y-m-d',$row['onbuda_add_date']);
			$row['onbuda_size'] = $DEVICE_SYSTEM[$row['onbuda_size']];
			$ret_data[] = $row;
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getExpectedListBySn($onbuda_sn) {
	$DEVICE_SYSTEM = array(
		1=>"1.7",
		2=>"2.5",
		3=>"2.8",
		4=>"3.0",
		5=>"3.5",
		6=>"3.6",
		7=>"其他"
	);

	$ret_data = array();
	$conn = getDB();
	$sql="SELECT * FROM `onliine_business_data` WHERE onbuda_sn = '{$onbuda_sn}'";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		if($row = $qresult->fetch_assoc()) {
			$row['onbuda_date'] = date('Y-m-d',$row['onbuda_date']);
			$row['onbuda_add_date'] = date('Y-m-d',$row['onbuda_add_date']);
			// $row['onbuda_size'] = $DEVICE_SYSTEM[$row['onbuda_size']];
			$ret_data = $row;
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getWorkListByMonth() {
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
			// 1.7, 2.5, 2.8, 3.0, 3.5, 3.6 其他
	);
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select * from onliine_add_data where onadd_status>=0 and onadd_schedule!=1";
	else
		$sql="select * from onliine_add_data where onadd_status>=0 and onadd_schedule!=1 and ( $where )";
	
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$cur_size = $DEVICE_SYSTEM[$row['onadd_cur_size']];
			$growing_size = $DEVICE_SYSTEM[$row['onadd_growing']];

			$row['onchba_cycle'] = getSettingBySn($cur_size,$growing_size)['onchba_cycle'];

        	$test = date("Y/m/d", strtotime("+".$row['onchba_cycle']." days", $row['onadd_planting_date']));
        	$o_y = date('Y',strtotime($test));        	
        	$c_y = date('Y');
        	$o_m = date('m',strtotime($test));
        	$c_m = date('m');
        	$row['o_y'] = $o_y;
        	$row['c_y'] = $c_y;
        	$row['o_m'] = $o_m;
        	$row['c_m'] = $c_m;
        	if($o_y <= $c_y){
        		if($o_y == $c_y && $o_m <= $c_m){
					$row['onadd_planting_date'] = date('Y/m/d',$row['onadd_planting_date']);        		
        			$row['expected_date'] = date('Y/m/d',strtotime($test));
					$ret_data[] = $row;
        		}
        		else if($o_y < $c_y){
					$row['onadd_planting_date'] = date('Y/m/d',$row['onadd_planting_date']);        		
        			$row['expected_date'] = date('Y/m/d',strtotime($test));
					$ret_data[] = $row;
        		} 
        	}
		}
		$qresult->free();
	}
	$conn->close();

	return $ret_data;
}

function getExpectedShipByMonth($year,$onadd_part_no,$onadd_growing,$onadd_type) {
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
		9=>"出貨",
		10=>"2.0"
			// 1.7, 2.5, 2.8, 3.0, 3.5, 3.6 其他
	);
	$year_start = strtotime($year."/1/1");
    $year_end = strtotime(($year)."/12/31");
	$ret_data = array();
	$conn = getDB();
	$sql="select * from onliine_add_data where onadd_part_no='$onadd_part_no' AND onadd_plant_st = 1 and onadd_type in ({$onadd_type})";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			if($row['onadd_growing'] == "出貨"){
				$row['onadd_growing'] = 9;
			}		
			$onchba_cycle = getSettingBySn($DEVICE_SYSTEM[$row['onadd_cur_size']],$DEVICE_SYSTEM[$row['onadd_growing']])['onchba_cycle'];
			
        	if($row['onadd_plant_st']==2){
        		$onchba_cycle=1;
        		$test = date("Y/m/d", strtotime("+$onchba_cycle days", $row['onadd_planting_date']));
        	}else{
        		$test = date("Y/m/d", strtotime("+$onchba_cycle days", $row['onadd_planting_date']));
        	}
			
        	if(strtotime($test) > $year_start && strtotime($test) < $year_end){
        		$row['month'] = intval(date("m", strtotime("+$onchba_cycle days", $row['onadd_planting_date'])));
        		$row['count'] = $row['onadd_quantity'];
        		$sn[$row['month']] .= $row['onadd_sn'].",";
        		$ret_data[] = $row;
        	}
		}
		$qresult->free();
	}

	$conn->close();

	$expected_number = array();
	for($i=0;$i<8;$i++)
		for($j=1;$j<=12;$j++)
		$expected_number[$i][$j] = 0;

	for($i=0;$i<count($ret_data);$i++){
		$n = 0;
		switch ($ret_data[$i]['month']) {
			case '01':
				$expected_number[$ret_data[$i]['onadd_sellsize']]['1'] += $ret_data[$i]['count'];
				break;
			case '02':
				$expected_number[$ret_data[$i]['onadd_sellsize']]['2'] += $ret_data[$i]['count'];
				break;
			case '03':
				$expected_number[$ret_data[$i]['onadd_sellsize']]['3'] += $ret_data[$i]['count'];
				break;
			case '04':
				$expected_number[$ret_data[$i]['onadd_sellsize']]['4'] += $ret_data[$i]['count'];
				break;
			case '05':
				$expected_number[$ret_data[$i]['onadd_sellsize']]['5'] += $ret_data[$i]['count'];
				break;
			case '06':
				$expected_number[$ret_data[$i]['onadd_sellsize']]['6'] += $ret_data[$i]['count'];
				break;
			case '07':
				$expected_number[$ret_data[$i]['onadd_sellsize']]['7'] += $ret_data[$i]['count'];
				break;
			case '08':
				$expected_number[$ret_data[$i]['onadd_sellsize']]['8'] += $ret_data[$i]['count'];
				break;
			case '09':
				$expected_number[$ret_data[$i]['onadd_sellsize']]['9'] += $ret_data[$i]['count'];
				break;
			case '10':
				$expected_number[$ret_data[$i]['onadd_sellsize']]['10'] += $ret_data[$i]['count'];
				break;
			case '11':
				$expected_number[$ret_data[$i]['onadd_sellsize']]['11'] += $ret_data[$i]['count'];
				break;
			case '12':
				$expected_number[$ret_data[$i]['onadd_sellsize']]['12'] += $ret_data[$i]['count'];
				break;
		}
	}
	$expected_number['sn'] = $sn;
	$ret_data = $expected_number;
	return $ret_data;
}

function getPicQty($onproduct_sn) {
	$ret_data = '';
	$conn = getDB();

	$sql="SELECT COUNT(*) FROM `onliine_pic_data` WHERE onproduct_sn = '{$onproduct_sn}'";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data = $row['COUNT(*)'];
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getSizeQtyBySn($onadd_sn) {
	$ret_data = '';
	$conn = getDB();

	$sql="SELECT COUNT(*) as total FROM `onliine_add_data` WHERE onadd_sn = '{$onadd_sn}' and onadd_status >= 1 group by onadd_growing";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data = $row['total'];
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getPic($onproduct_sn) {
	$ret_data = array();
	$conn = getDB();

	$sql="SELECT * FROM `onliine_product_data` WHERE onproduct_sn = '{$onproduct_sn}'";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$row['onpic_img_path'] = $row['onproduct_pic_url'];
			$ret_data[] = $row;
		}
		$qresult->free();
	}

	$sql2="SELECT * FROM `onliine_pic_data` WHERE onproduct_sn = '{$onproduct_sn}'";

	$qresult2 = $conn->query($sql2);
	if ($qresult2->num_rows > 0) {
		while($row2 = $qresult2->fetch_assoc()) {
			$ret_data[] = $row2;
		}
		$qresult2->free();
	}
	$conn->close();
	return $ret_data;
}

function IsNewProduct($onproduct_part_no,$onproduct_part_name) {
	$ret_data = "0";
	$conn = getDB();

	$sql="SELECT * FROM `onliine_product_data` WHERE onproduct_part_no like '{$onproduct_part_no}' and onproduct_part_name like '{$onproduct_part_name}'";
	// echo $sql;
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data = "1";
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}
function IsNewProduct2($onproduct_part_no,$onproduct_part_name) {
	$ret_data = "0";
	$conn = getDB();

	$sql="SELECT * FROM `onliine_product_data` WHERE onproduct_part_no like '{$onproduct_part_no}' and onproduct_part_name like '{$onproduct_part_name}'";
	$ret_data = $sql;
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data = $sql;
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getQuantityForseller($part_no,$part_name) {
	$ret_data = array();
	$conn = getDB();

	$sql="select onadd_sn,onadd_cur_size,onadd_level,onadd_AB_sn,onadd_planting_date,onadd_ml,onadd_newpot_sn,onadd_type,onadd_prebill_quantity,SUM(onadd_quantity) from onliine_add_data
	where onadd_status>=0 and onadd_part_no like '{$part_no}' and onadd_part_name like '{$part_name}' and onadd_status = 1 and onadd_cur_size not in(0,8) and onadd_plant_st = 1 group by onadd_sn,onadd_cur_size order by onadd_planting_date";
	// printr($sql);
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

function getAlreadyPrebillQuantityBySn($onadd_sn) {
	$ret_data = 0;
	$conn = getDB();

	$sql="SELECT SUM(onbuda_quantity) as total FROM `onliine_business_data` WHERE onadd_sn = {$onadd_sn}";
	// printr($sql);
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data = $row['total'];
		}
		$qresult->free();
	}
	$conn->close();
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

function getABdataByDateAndLevel($onadd_sn) {
	$ret_data = array();
	$conn = getDB();
	$sql="select * from onliine_add_data WHERE onadd_sn = '{$onadd_sn}' AND onadd_status >= 1";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data = $row;
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getPrebillList($onbd_part_no,$onbd_part_name,$year) {
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
		9=>"出貨",
		10=>"2.0"
	);

	$start = strtotime($year."/1/1 00:00");
	$end = strtotime($year."/12/31 23:59");
	$ret_data = array();
	$conn = getDB();
	$sql="SELECT * FROM `onliine_bill_data` WHERE onbd_part_no like '{$onbd_part_no}' AND onbd_part_name like '{$onbd_part_name}' AND onbd_status = 1 AND onbd_sell_date BETWEEN $start AND $end";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$row['month'] = intval(date("m", $row['onbd_sell_date']));
        	$row['count'] = $row['onbd_quantity'];
			$ret_data[] = $row;
		}
		$qresult->free();
	}

	$expected_number = array();
	for($i=0;$i<8;$i++)
		for($j=1;$j<=12;$j++)
		$expected_number[$i][$j] = 0;

	for($i=0;$i<count($ret_data);$i++){
		$n = 0;
		switch ($ret_data[$i]['month']) {
			case '01':
				$expected_number[$ret_data[$i]['onbd_size']]['1'] += $ret_data[$i]['count'];
				break;
			case '02':
				$expected_number[$ret_data[$i]['onbd_size']]['2'] += $ret_data[$i]['count'];
				break;
			case '03':
				$expected_number[$ret_data[$i]['onbd_size']]['3'] += $ret_data[$i]['count'];
				break;
			case '04':
				$expected_number[$ret_data[$i]['onbd_size']]['4'] += $ret_data[$i]['count'];
				break;
			case '05':
				$expected_number[$ret_data[$i]['onbd_size']]['5'] += $ret_data[$i]['count'];
				break;
			case '06':
				$expected_number[$ret_data[$i]['onbd_size']]['6'] += $ret_data[$i]['count'];
				break;
			case '07':
				$expected_number[$ret_data[$i]['onbd_size']]['7'] += $ret_data[$i]['count'];
				break;
			case '08':
				$expected_number[$ret_data[$i]['onbd_size']]['8'] += $ret_data[$i]['count'];
				break;
			case '09':
				$expected_number[$ret_data[$i]['onbd_size']]['9'] += $ret_data[$i]['count'];
				break;
			case '10':
				$expected_number[$ret_data[$i]['onbd_size']]['10'] += $ret_data[$i]['count'];
				break;
			case '11':
				$expected_number[$ret_data[$i]['onbd_size']]['11'] += $ret_data[$i]['count'];
				break;
			case '12':
				$expected_number[$ret_data[$i]['onbd_size']]['12'] += $ret_data[$i]['count'];
				break;
		}
	}

	$ret_data = $expected_number;
	return $ret_data;
}

function getPrebillDetailList($onbd_part_no,$onbd_part_name,$year,$month,$size) {
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
		9=>"出貨",
		10=>"2.0"
	);

	$start = strtotime($year."/".$month."/1 00:00");
	$end = strtotime(Date('Y-m-t', strtotime(Date("Y-m-d",$start))));
	$ret_data = array();
	$conn = getDB();
	$sql="SELECT * FROM `onliine_bill_data` WHERE onbd_part_no like '{$onbd_part_no}' AND onbd_part_name like '{$onbd_part_name}' AND onbd_status = 1 AND onbd_sell_date BETWEEN $start AND $end AND onbd_size = '{$size}'";
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$row['onbd_add_date'] = Date('Y-m-d',$row['onbd_add_date']);
			$row['month'] = intval(date("m", $row['onbd_sell_date']));
        	$row['count'] = $row['onbd_quantity'];
        	$row['onbd_size'] = $DEVICE_SYSTEM[$row['onbd_size']];
        	$row['onbd_sell_date'] = Date('Y-m-d',$row['onbd_sell_date']);
			$ret_data[] = $row;
		}
		$qresult->free();
	}

	return $ret_data;
}

function getPrebillListBySn($onbd_sn) {
	$ret_data = array();
	$conn = getDB();
	$sql="SELECT * FROM `onliine_bill_data` WHERE onbd_sn = '{$onbd_sn}'";
	$ret_data['sql'] = $sql;
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		if($row = $qresult->fetch_assoc()) {
			$row['onbd_sell_date'] = Date('Y-m-d',$row['onbd_sell_date']);
			$ret_data = $row;
		}
		$qresult->free();
	}
	return $ret_data;
}

function SnProcessor($onadd_sn, $onadd_type, $onadd_newpot_sn, $onadd_ml, $onadd_planting_date, $onadd_AB_sn, $onadd_level){
	$sn = 0;
	$mark = "";
	$level = "";
	if($onadd_type == 0){
		$mark = "";
	}
	else if($onadd_type == 1){
		$mark = "F";
	}
	else{
		$mark = "O";
	}

	if($onadd_AB_sn == 0){
		if($onadd_newpot_sn == 0){
			if($onadd_ml == 0){			
				$sn = $onadd_sn;
			}
			else{
				$sn = $onadd_ml;
			}
		}
		else{
			$sn = $onadd_newpot_sn;
		}
	}
	else{
		$sn = $onadd_AB_sn;
	}

	if($onadd_level == "1"){
		$level = "_B";
	}
	else{
		$level = "";
	}

	$sn = str_pad($sn,5,"0",STR_PAD_LEFT);
	$sn = $mark.date('Y',$onadd_planting_date).'-'.$sn.$level;
	return $sn;
}

//育成率 (公式 (數量-汰除)/數量)     
function getLivability($onadd_AB_sn,$onadd_newpot_sn,$onadd_ml,$onadd_sn,$onadd_level){
	if($onadd_AB_sn == 0){
		if($onadd_newpot_sn == 0){
	    	if($onadd_ml == 0){
	    		$sn = $onadd_sn;
			}
			else{
				$sn = $onadd_ml;
			}
		}
		else{
			$sn = $onadd_newpot_sn;
		}	
	}
	else{
		$sn = $onadd_AB_sn;
	}
	$first_plant_amount = (getProductFirstQty($sn) != 0 ? getProductFirstQty($sn) : 1);//第一次下種時間
	$incubation_rate = getProductAllNowQty($sn,$onadd_level)/$first_plant_amount;
	// printr("first_plant_amount=".$first_plant_amount);
	// printr("incubation_rate=".$incubation_rate);
	return $incubation_rate;
}

//確認是否已有AB苗存在
function IsABLevelExist($onadd_AB_sn){
	$ret_data = array();
	$conn = getDB();
	$sql="SELECT * FROM `onliine_add_data` WHERE onadd_AB_sn = '{$onadd_AB_sn}' AND onadd_status >= 1";
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		return true;
	}
	else{
		return false;
	}
}

//換盆費用計算
function computing_costs($sn,$onadd_sn,$onadd_planting_date){
 $conn = getDB();
 // 換盆前or出貨前成本計算----------------strat
 $total_cost = "";
 // 換盆前所有成本：每月（水草、軟杯...）
 $sql_cost = "SELECT SUM(b.oncoda_cost) as oncoda_cost,ceil(($onadd_planting_date- a.onadd_planting_date)/60/60/24/30) as date_month 
  FROM onliine_add_data a
  left join online_cost_data b on a.onadd_cur_size = b.oncoda_cost_size
  where b.oncoda_cost_status = 0 and a.onadd_sn='{$sn}'";
 $onadd_cost = $conn->query($sql_cost)->fetch_assoc();
 // 每月固定成本
 $sql_onadd_cost_month = "SELECT c.oncoda_cost as onadd_cost_month FROM onliine_add_data b  left join online_cost_data c on b.onadd_cur_size = c.oncoda_cost_size where c.oncoda_cost_status = 1 and b.onadd_status = 1 and b.onadd_sn = $onadd_sn";
 $onadd_cost_month = $conn->query($sql_onadd_cost_month)->fetch_assoc()['onadd_cost_month'];
 $total_cost = $onadd_cost_month*$onadd_cost['date_month']+$onadd_cost['oncoda_cost'];
 
 return $total_cost;
}

//換盆紀錄
function sql_onliine_basin_log($onadd_sn='',$onadd_cur_size='',$onadd_sellsize='',$onadd_sn_basin='',$onadd_sn_after_basin="",$act){ 
	$conn = getDB();
	switch ($act) {
		case 'add':
		 	$sql_onliine_basin_log = "INSERT INTO `onliine_basin_log`(`onadd_sn`, `onb_size`, `onb_tsize`, `onadd_sn_before_basin`, `onadd_sn_after_basin`, `onb_status`)  VALUES ('{$onadd_sn}','{$onadd_cur_size}','{$onadd_sellsize}','{$onadd_sn_basin}','{$onadd_sn_after_basin}',1)";
		  	break;  
		case 'update':
		 	 $sql_onliine_basin_log = "UPDATE onliine_basin_log SET `onb_size`='{$onadd_cur_size}',`onb_tsize`='{$onadd_sellsize}',`onb_status`=1 WHERE `onadd_sn`='{$onadd_sn}' AND onadd_sn_before_basin = '{$onadd_sn_basin}'";
		 break;
	}
	$conn->query($sql_onliine_basin_log);
}

//取得兩日期之間共幾周
function HowManyWeeksPlant($date1, $date2){ 
	if($date1 > $date2) return HowManyWeeksPlant($date2, $date1);
    $first = DateTime::createFromFormat('m/d/Y', $date1);
    $second = DateTime::createFromFormat('m/d/Y', $date2);
    return floor($first->diff($second)->days/7);

	return $ret_data;
}

//至今目前共種幾周
function GetFirstPlantDay($onadd_sn){ 
	$conn = getDB();
	$sql="SELECT onfp_plant_date FROM `onliine_firstplant_data` WHERE onadd_sn = '{$onadd_sn}'";
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		if($row = $qresult->fetch_assoc()) {
			$ret_data = $row['onfp_plant_date'];
		}
		$qresult->free();
	}	
	return $ret_data;
}

//出貨規格總周數
function GetSellSizeWeeks($sell_size){ 
	$SellSize_number = array(
		1=>"1.7",
		10=>"2.0",
		2=>"2.5",
		3=>"2.8",
		4=>"3.0",
		5=>"3.5",
		6=>"3.6",
	);
	$ret_data = 0;
	$sell_size = (double)filter_var($SellSize_number[$sell_size], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	if($sell_size >= 1 && $sell_size < 2){
		$ret_data = 4;
	}
	else if($sell_size >= 2 && $sell_size < 3){
		$ret_data = 8;
	}
	else if($sell_size >= 3){
		$ret_data = 12;
	}
	return $ret_data*4;
}

//取得原始批號
function GetOriginalSn($onadd_sn, $onadd_type, $onadd_newpot_sn, $onadd_ml, $onadd_planting_date, $onadd_AB_sn, $onadd_level){
	$sn = 0;
	$mark = "";
	$level = "";
	if($onadd_type == 0){
		$mark = "";
	}
	else if($onadd_type == 1){
		$mark = "F";
	}
	else{
		$mark = "O";
	}

	if($onadd_AB_sn == 0){
		if($onadd_newpot_sn == 0){
			if($onadd_ml == 0){			
				$sn = $onadd_sn;
			}
			else{
				$sn = $onadd_ml;
			}
		}
		else{
			$sn = $onadd_newpot_sn;
		}
	}
	else{
		$sn = $onadd_AB_sn;
	}

	if($onadd_level == "1"){
		$level = "_B";
	}
	else{
		$level = "";
	}
	return $sn;
}

//取得兩日期間之月數
function getMonthBetweenTwoDate($date1,$date2){
	$ts1 = strtotime($date1);
	$ts2 = strtotime($date2);

	if($ts1 > $ts2) return getMonthBetweenTwoDate($date2, $date1);

	$start    = (new DateTime($date1))->modify('first day of this month');
	$end      = (new DateTime($date2))->modify('first day of next month');
	$interval = DateInterval::createFromDateString('1 month');
	$period   = new DatePeriod($start, $interval, $end);
	$month_list = array();
	foreach ($period as $dt) {
	    $month_list[] = intval($dt->format("m"));
	}

	return $month_list;
}

//取得成本總表的各月數成本總額
function getMonthCost(){
	$conn = getDB();
	$ret_data = array();
	$sql="SELECT oncoda_cost_size,SUM(oncoda_cost) AS total_cost FROM `online_cost_sum_data` GROUP BY oncoda_cost_size";
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data[$row['oncoda_cost_size']] = $row['total_cost'];
		}
		$qresult->free();
	}	
	return $ret_data;
}

//取得全部園區各尺寸數量
function getAllQuantityBySize(){
	$conn = getDB();
	$ret_data = array();
	$sql="SELECT onadd_cur_size,SUM(onadd_quantity) AS total_quantity FROM `onliine_add_data` WHERE onadd_type = 1 GROUP BY onadd_cur_size";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data[$row['onadd_cur_size']] = $row['total_quantity'];
		}
		$qresult->free();
	}	
	return $ret_data;
}
// 取得種植人員
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

?>