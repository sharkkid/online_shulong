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
function getPlantData($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select * from onliine_add_data where onadd_status > 0 and onadd_plant_st=1 order by onadd_add_date desc, onadd_sn desc limit $offset, $rows";
	else
		$sql="select * from onliine_add_data where onadd_status > 0 and onadd_plant_st=1 and ( $where ) order by onadd_add_date desc, onadd_sn desc limit $offset, $rows";
	// printr($sql);
	// exit;
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

	$sql="select onproduct_part_no,onproduct_part_name from  onliine_product_data where onproduct_status>=0 and onproduct_plant_st = 1";
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
			}
			else{
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

function getUserQty($where='') {
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

function getProductAllNowQty($onadd_sn) {
	$ret_data = 0;
	$conn = getDB();	
	$sql="select SUM(onadd_quantity) as now_total from onliine_add_data where onadd_status>=1 and onadd_sn like '$onadd_sn' or onadd_newpot_sn like '$onadd_sn'  or onadd_ml like '$onadd_sn'";
	$sql2="select SUM(onshda_quantity) as ship_total from online_shipment_data where onshda_status>=1 and onadd_sn like '$onadd_sn'";
	// echo $sql;
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data = $row['now_total'];
		}
		$qresult->free();

		$sql2="select SUM(onshda_quantity) as ship_total from online_shipment_data where onshda_status>=1 and onadd_sn like '$onadd_sn'";
		// echo $sql;
		$temp_n = 0;
		$qresult2 = $conn->query($sql2);
		if ($qresult2->num_rows > 0) {
			while($row2 = $qresult2->fetch_assoc()) {
				$temp_n = $temp_n + intval($row2['ship_total']);
			}
			$qresult2->free();			
		}
		$ret_data = $ret_data + $temp_n;
	}
	else{
		$ret_data = 1;
	}


	$conn->close();
	return $ret_data;
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

function getUserBySn($onadd_sn) {
	$ret_data = array();
	$conn = getDB();
	$sql="select * from onliine_add_data where onadd_sn='{$onadd_sn}' and onadd_plant_st = 1";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		if ($row = $qresult->fetch_assoc()) {
			$ret_data = $row;
			$ret_data['onadd_planting_date'] = date('Y-m-d',$ret_data['onadd_planting_date']);
		}
		$qresult->free();
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
	$sql="select * from onliine_product_data where onproduct_part_no='{$onproduct_part_no}'";

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
	$sql="SELECT onchba_sn FROM `online_change_basin` WHERE onchba_size like {$onchba_size} and onchba_tsize like {$onchba_tsize}";
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
		8=>"瓶苗開瓶"
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

function getExpectedShipByMonth($year,$onadd_part_no,$onadd_growing) {
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
	$year_start = strtotime($year."/1/1");
    $year_end = strtotime(($year)."/12/31");
	$ret_data = array();
	$conn = getDB();
	$sql="select * from onliine_add_data where onadd_part_no='$onadd_part_no' AND onadd_planting_date and onadd_plant_st = 1";
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {			
			$onchba_cycle = getSettingBySn($DEVICE_SYSTEM[$row['onadd_cur_size']],$DEVICE_SYSTEM[$row['onadd_growing']])['onchba_cycle'];;
        	if($row['onadd_plant_st']==2){
        		$onchba_cycle=1;
        		$test = date("Y/m/d", strtotime("+$onchba_cycle days", $row['onadd_planting_date']));
        	}else{
        		$test = date("Y/m/d", strtotime("+$onchba_cycle days", $row['onadd_planting_date']));
        	}
			
        	if(strtotime($test) > $year_start && strtotime($test) < $year_end){
        		$row['month'] = date("m", strtotime("+$onchba_cycle days", $row['onadd_planting_date']));
        		$row['count'] = $row['onadd_quantity'];
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
				$expected_number[$ret_data[$i]['onadd_growing']]['1'] += $ret_data[$i]['count'];
				break;
			case '02':
				$expected_number[$ret_data[$i]['onadd_growing']]['2'] += $ret_data[$i]['count'];
				break;
			case '03':
				$expected_number[$ret_data[$i]['onadd_growing']]['3'] += $ret_data[$i]['count'];
				break;
			case '04':
				$expected_number[$ret_data[$i]['onadd_growing']]['4'] += $ret_data[$i]['count'];
				break;
			case '05':
				$expected_number[$ret_data[$i]['onadd_growing']]['5'] += $ret_data[$i]['count'];
				break;
			case '06':
				$expected_number[$ret_data[$i]['onadd_growing']]['6'] += $ret_data[$i]['count'];
				break;
			case '07':
				$expected_number[$ret_data[$i]['onadd_growing']]['7'] += $ret_data[$i]['count'];
				break;
			case '08':
				$expected_number[$ret_data[$i]['onadd_growing']]['8'] += $ret_data[$i]['count'];
				break;
			case '09':
				$expected_number[$ret_data[$i]['onadd_growing']]['9'] += $ret_data[$i]['count'];
				break;
			case '10':
				$expected_number[$ret_data[$i]['onadd_growing']]['10'] += $ret_data[$i]['count'];
				break;
			case '11':
				$expected_number[$ret_data[$i]['onadd_growing']]['11'] += $ret_data[$i]['count'];
				break;
			case '12':
				$expected_number[$ret_data[$i]['onadd_growing']]['12'] += $ret_data[$i]['count'];
				break;
		}
	}

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

	$sql="SELECT * FROM `onliine_product_data` WHERE onproduct_part_no like '{$onproduct_part_no}' and onproduct_part_name like '{$onproduct_part_name}' and onproduct_plant_st = 1";
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

	$sql="select *,SUM(onadd_quantity) from onliine_add_data where onadd_status>=0 and onadd_part_no like '$part_no' and onadd_part_name like '$part_name' and onadd_status = 1 and onadd_cur_size not in(0,8) group by onadd_cur_size order by onadd_planting_date";
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
function getMapping_size() {
	$ret_data = array();
	$conn = getDB();
	$sql="select onchba_sn,onchba_tsize from online_change_basin";
	// echo $sql;
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data[$row['onchba_sn']] = '<font color="#666666">'.$row['onchba_tsize'].'</font>';
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}
?>