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

function permission_level($level) {
	switch ($level) {
		case '0':
			return "系統管理員";
			break;
		
		case '1':
			return "老闆";
			break;

		case '2':
			return "員工";
			break;

		case '3':
			return "業務";
			break;

		case '4':
			return "訪客";
			break;
	}
}

function permission_option($option) {
	$text = "";
	if(strpos($option, "1") !== false){
		$text .= '<span class="glyphicon glyphicon-ok"></span>新增';
	}
	if(strpos($option, "2") !== false){
		$text .= '<span class="glyphicon glyphicon-ok"></span>汰除';
	}
	if(strpos($option, "3") !== false){
		$text .= '<span class="glyphicon glyphicon-ok"></span>修改';
	}
	if(strpos($option, "4") !== false){
		$text .= '<span class="glyphicon glyphicon-ok"></span>圖片上傳';
	}
	if(strpos($option, "5") !== false){
		$text .= '<span class="glyphicon glyphicon-ok"></span>出貨';
	}
	if(strpos($option, "6") !== false){
		$text .= '<span class="glyphicon glyphicon-ok"></span>移倉';
	}

	if(strlen($text))
		return $text;	
	else
		return "尚未設定其他權限";
}

//================================
// onliine_add_data.php
//================================
function getUser($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="SELECT * FROM `js_user` ORDER BY `jsuser_sn` ASC limit $offset, $rows";
	else
		$sql="SELECT * FROM `js_user` WHERE {$where} ORDER BY `jsuser_sn` limit $offset, $rows";
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

function getProducts($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select * from  onliine_product_data where onproduct_status>=0 and onproduct_plant_st = 2 GROUP BY onproduct_part_no";
	else
		$sql="select * from  onliine_product_data where onproduct_status>=0 and onproduct_plant_st = 2 and $where GROUP BY onproduct_part_no";
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

function getAllProductsNo() {
	$ret_data = array();
	$conn = getDB();

	$sql="select onproduct_part_no,onproduct_part_name from  onliine_product_data where onproduct_status>=0 and onproduct_plant_st = 2";
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
	$sql="select * from onliine_product_data where onproduct_part_no='{$onproduct_part_no}'' and onproduct_part_name='{$onproduct_part_name}'";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		$ret_data = "1";
	}
	$conn->close();
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
function getUseradd($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select * from onliine_add_data where onadd_status>=0 and onadd_plant_st=2 GROUP BY onadd_part_no";
	else
		$sql="select * from onliine_add_data where onadd_status>=0 and onadd_plant_st=2 and ( $where ) GROUP BY onadd_part_no";
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

function getUserQty($where='') {
	$ret_data = 0;
	$conn = getDB();
	if(empty($where))
		$sql="select count(*) from onliine_add_data where onadd_status>=0 and onadd_plant_st=2";
	else
		$sql="select count(*) from onliine_add_data where onadd_status>=0 and onadd_plant_st=2 and ( $where )";

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
function getProductsQty($where='') {
	$ret_data = 0;
	$conn = getDB();
	if(empty($where))
		$sql="select count(DISTINCT onproduct_part_no) as count from onliine_product_data where onproduct_status>=0 AND onproduct_plant_st = 2";
	else
		$sql="select count(DISTINCT onproduct_part_no) as count from onliine_product_data where onproduct_status>=0 AND onproduct_plant_st = 2 AND $where";
	// echo $sql;
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

function getUserBySn($jsuser_sn) {
	$ret_data = array();
	$conn = getDB();
	$sql="SELECT * FROM `js_user` where jsuser_sn='{$jsuser_sn}'";

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
function getSettingBySn($onchba_size) {
	$ret_data = array();
	$conn = getDB();
	$sql="select * from online_change_basin where onchba_size='{$onchba_size}'";

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

function getProductAllNowQty($onadd_sn) {
	$ret_data = 0;
	$conn = getDB();	
	$sql="select SUM(onadd_quantity) as now_total from onliine_add_data where onadd_status>=1 and onadd_sn like '$onadd_sn' or onadd_newpot_sn like '$onadd_sn'";
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

function IsNewProduct($onproduct_part_no,$onproduct_part_name) {
	$ret_data = "0";
	$conn = getDB();

	$sql="SELECT * FROM `onliine_product_data` WHERE onproduct_part_no like '{$onproduct_part_no}' and onproduct_part_name like '{$onproduct_part_name}' and onproduct_plant_st = 2";
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
?>